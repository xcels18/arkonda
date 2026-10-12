<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Contract;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class ContractDocumentService
{
    public function save(Contract $contract, ?UploadedFile $file = null): void
    {
        $oldPath = $contract->file_path;
        $newName = null;

        try {
            if ($file !== null) {
                $newName = $this->generateFileName($contract);
                $stored = $file->storeAs('', $newName, 'contracts');

                if ($stored === false || ! Storage::disk('contracts')->exists($newName)) {
                    throw ValidationException::withMessages([
                        'dokumen' => 'Dokumen gagal disimpan. Silakan coba kembali.',
                    ]);
                }

                $contract->fill([
                    'file_path' => 'contracts/'.$newName,
                    'file_name' => $newName,
                    'file_size' => $file->getSize(),
                    'uploaded_by' => auth()->id(),
                    'uploaded_at' => now(),
                ]);
            }

            DB::transaction(function () use ($contract, $file) {
                $contract->save();
                if ($file !== null) {
                    AuditLog::logUpload($contract, auth()->user());
                }
            });
        } catch (Throwable $exception) {
            if ($newName !== null) {
                Storage::disk('contracts')->delete($newName);
            }
            throw $exception;
        }

        // Only remove the previous document after the replacement and metadata succeed.
        if ($file !== null && $oldPath) {
            $this->delete($oldPath);
        }
    }

    public function destroy(Contract $contract): void
    {
        $disk = Storage::disk('contracts');
        $relativePath = $contract->file_path
            ? preg_replace('/^contracts\//', '', $contract->file_path) : null;
        $archivePath = null;

        try {
            if ($relativePath && $disk->exists($relativePath)) {
                $archivePath = '.deleted/'.Str::uuid().'/'.basename($relativePath);
                if (! $disk->copy($relativePath, $archivePath)) {
                    throw new RuntimeException('Dokumen tidak dapat diarsipkan sebelum penghapusan.');
                }
            }

            DB::transaction(function () use ($contract, $archivePath) {
                $oldValues = $contract->toArray();
                $number = $contract->nomor_kontrak;
                if (! $contract->delete()) {
                    throw new RuntimeException('Kontrak gagal dihapus.');
                }
                AuditLog::log('delete', "Admin menghapus kontrak: {$number}", $contract, $oldValues,
                    ['archived_document' => $archivePath]);
            });
        } catch (Throwable $exception) {
            if ($archivePath !== null) {
                $disk->delete($archivePath);
            }
            throw $exception;
        }

        // A private recovery copy and the original metadata survive the deletion.
        // Failed cleanup must not undo an already committed database transaction.
        if ($archivePath !== null) {
            try {
                if (! $disk->delete($relativePath)) {
                    Log::warning('Deleted contract document needs cleanup.', ['contract_id' => $contract->id]);
                }
            } catch (Throwable $exception) {
                report($exception);
            }
        }
    }

    public function delete(string $path): void
    {
        Storage::disk('contracts')->delete(preg_replace('/^contracts\//', '', $path));
    }

    private function generateFileName(Contract $contract): string
    {
        // Get OPD singkatan or 'TANPA-OPD'
        $opd = $contract->opd?->singkatan ?? 'TANPA-OPD';

        // Get jenis kontrak or 'UMUM'
        $jenis = $contract->jenis_kontrak ?? 'UMUM';

        // Get nama kontrak (truncate if too long)
        $nama = $contract->nama_kontrak;

        // Get tahun
        $tahun = $contract->tahun_anggaran;

        // Sanitize each part (remove special characters, replace spaces with dash)
        $opd = $this->sanitizeFileName($opd);
        $jenis = $this->sanitizeFileName($jenis);
        $nama = $this->sanitizeFileName($nama);

        // Truncate nama if too long (max 50 chars)
        if (strlen($nama) > 50) {
            $nama = substr($nama, 0, 50);
        }

        // Build filename
        $baseName = "{$opd}_{$jenis}_{$nama}_{$tahun}";

        // Add unique suffix to prevent duplicates
        $uniqueSuffix = '_'.(string) Str::uuid();

        return $baseName.$uniqueSuffix.'.pdf';
    }

    /**
     * Sanitize string for use in filename
     */
    private function sanitizeFileName(string $string): string
    {
        // Replace spaces with dash
        $string = str_replace(' ', '-', $string);

        // Remove special characters except dash and underscore
        $string = preg_replace('/[^A-Za-z0-9\-_]/', '', $string);

        // Remove multiple dashes
        $string = preg_replace('/-+/', '-', $string);

        // Trim dashes from start and end
        $string = trim($string, '-');

        return $string ?: 'UNKNOWN';
    }
}
