<?php

namespace Tests\Feature;

use App\Models\Contract;
use App\Models\Opd;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;
use ZipArchive;

class ContractSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function opd(string $code = 'A'): Opd
    {
        return Opd::create(['kode' => $code, 'nama' => 'OPD '.$code, 'singkatan' => $code]);
    }

    private function user(?Opd $opd = null, string $role = 'user'): User
    {
        return User::factory()->create(['role' => $role, 'is_verified' => true, 'opd_id' => $opd?->id]);
    }

    private function data(array $overrides = []): array
    {
        return array_merge([
            'nomor_kontrak' => '001/2026', 'nama_kontrak' => 'Pengadaan peralatan',
            'nama_penyedia' => 'PT Penyedia', 'nilai_kontrak' => 1000000,
            'tanggal_kontrak' => '2026-10-10', 'jenis_kontrak' => 'Kontrak',
            'tahun_anggaran' => 2026, 'bidang' => 'Aset',
        ], $overrides);
    }

    public function test_verified_user_without_opd_cannot_read_any_contract(): void
    {
        $contract = Contract::create($this->data(['opd_id' => $this->opd()->id]));
        $this->actingAs($this->user());
        $this->get('/contracts')->assertOk()->assertDontSee($contract->nama_kontrak);
        $this->get('/dashboard')->assertOk()->assertDontSee($contract->nama_kontrak);
        foreach (['', '/preview', '/download'] as $suffix) {
            $this->get('/contracts/'.$contract->id.$suffix)->assertForbidden();
        }
        $this->postJson('/api/validate-contract-number', ['nomor_kontrak' => '001/2026'])->assertForbidden();
    }

    public function test_opd_user_can_read_own_contract_but_not_other_opd(): void
    {
        $ownOpd = $this->opd();
        $own = Contract::create($this->data(['opd_id' => $ownOpd->id]));
        $other = Contract::create($this->data(['opd_id' => $this->opd('B')->id, 'nomor_kontrak' => '002', 'nama_kontrak' => 'Rahasia OPD B']));
        $this->actingAs($this->user($ownOpd));
        $this->get('/contracts')->assertOk()->assertSee($own->nama_kontrak)->assertDontSee($other->nama_kontrak);
        $this->get('/dashboard')->assertOk()->assertDontSee($other->nama_kontrak);
        $this->get('/contracts/'.$own->id)->assertOk();
        foreach (['', '/preview', '/download'] as $suffix) {
            $this->get('/contracts/'.$other->id.$suffix)->assertForbidden();
        }
    }

    public function test_admin_without_opd_can_read_all_contracts(): void
    {
        $contract = Contract::create($this->data(['opd_id' => $this->opd()->id]));
        $this->actingAs($this->user(null, 'admin'));
        $this->get('/contracts')->assertOk()->assertSee($contract->nama_kontrak);
        $this->get('/contracts/'.$contract->id)->assertOk();
    }

    public function test_user_validation_reports_duplicates_without_other_opd_metadata(): void
    {
        Contract::create($this->data(['opd_id' => $this->opd('B')->id]));
        $this->actingAs($this->user($this->opd()));
        $this->postJson('/api/validate-contract-number', ['nomor_kontrak' => '001/2026'])
            ->assertOk()->assertJsonPath('valid', false)->assertJsonMissingPath('existing');
        $this->postJson('/api/validate-contract-number', ['nomor_kontrak' => 'NEW'])
            ->assertOk()->assertJsonPath('valid', true);
        $this->postJson('/api/validate-contract-number', ['nomor_kontrak' => ['invalid']])
            ->assertUnprocessable();
    }

    public function test_blank_bidang_uses_default_and_opd_cannot_be_spoofed(): void
    {
        $ownOpd = $this->opd();
        $other = $this->opd('B');
        $this->actingAs($this->user($ownOpd));
        $this->post('/contracts', $this->data(['bidang' => '', 'opd_id' => $other->id]))
            ->assertRedirect('/contracts')->assertSessionHasNoErrors();
        $this->assertDatabaseHas('contracts', ['nomor_kontrak' => '001/2026', 'bidang' => 'Aset', 'opd_id' => $ownOpd->id]);
    }

    public function test_failed_replacement_preserves_old_document_and_metadata(): void
    {
        Storage::fake('contracts');
        Storage::disk('contracts')->put('old.pdf', '%PDF old');
        $contract = Contract::create($this->data(['file_path' => 'contracts/old.pdf', 'file_name' => 'old.pdf']));
        $this->actingAs($this->user(null, 'admin'));
        $file = Mockery::mock(UploadedFile::class)->makePartial();
        $file->shouldReceive('storeAs')->once()->andReturn(false);

        try {
            $contract->fill(['nama_kontrak' => 'Changed']);
            app(\App\Services\ContractDocumentService::class)->save($contract, $file);
            $this->fail('Expected upload validation failure');
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->assertArrayHasKey('dokumen', $exception->errors());
        }
        Storage::disk('contracts')->assertExists('old.pdf');
        $this->assertSame('contracts/old.pdf', $contract->fresh()->file_path);
        $this->assertSame('Pengadaan peralatan', $contract->fresh()->nama_kontrak);
    }

    public function test_successful_replacement_is_readable_and_removes_old_file(): void
    {
        Storage::fake('contracts');
        Storage::disk('contracts')->put('old.pdf', '%PDF old');
        $contract = Contract::create($this->data(['file_path' => 'contracts/old.pdf', 'file_name' => 'old.pdf']));
        $this->actingAs($this->user(null, 'admin'));
        $pdf = UploadedFile::fake()->createWithContent('new.pdf', "%PDF-1.4\nNew document");
        $this->put('/admin/contracts/'.$contract->id, $this->data(['dokumen' => $pdf]))
            ->assertRedirect('/admin/contracts')->assertSessionHasNoErrors();
        $contract->refresh();
        Storage::disk('contracts')->assertMissing('old.pdf');
        Storage::disk('contracts')->assertExists($contract->file_name);
        $this->get('/contracts/'.$contract->id.'/preview')->assertOk();
        $this->get('/contracts/'.$contract->id.'/download')->assertDownload($contract->file_name);
    }

    public function test_failed_database_save_cleans_new_file_and_preserves_old_file(): void
    {
        Storage::fake('contracts');
        Storage::disk('contracts')->put('old.pdf', '%PDF old');
        $existing = Contract::create($this->data(['file_path' => 'contracts/old.pdf', 'file_name' => 'old.pdf']));
        Contract::create($this->data(['nomor_kontrak' => '002']));
        $this->actingAs($this->user(null, 'admin'));
        $existing->nomor_kontrak = '002';
        try {
            app(\App\Services\ContractDocumentService::class)->save($existing, UploadedFile::fake()->createWithContent('new.pdf', '%PDF-1.4 new'));
            $this->fail('Expected unique constraint failure');
        } catch (\Illuminate\Database\QueryException $exception) {
            $this->assertSame(['old.pdf'], Storage::disk('contracts')->allFiles());
            $this->assertSame('001/2026', $existing->fresh()->nomor_kontrak);
            $this->assertSame('contracts/old.pdf', $existing->fresh()->file_path);
        }
    }

    public function test_failed_new_upload_does_not_leave_contract_row(): void
    {
        Storage::fake('contracts');
        $this->actingAs($this->user($this->opd()));
        $file = Mockery::mock(UploadedFile::class)->makePartial();
        $file->shouldReceive('storeAs')->once()->andReturn(false);
        try {
            app(\App\Services\ContractDocumentService::class)->save(new Contract($this->data()), $file);
            $this->fail('Expected upload validation failure');
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->assertDatabaseCount('contracts', 0);
            $this->assertSame([], Storage::disk('contracts')->allFiles());
        }
    }

    public function test_zip_keeps_both_documents_with_identical_metadata(): void
    {
        Storage::fake('contracts');
        $opd = $this->opd();
        $ids = [];
        foreach (['first', 'second'] as $name) {
            Storage::disk('contracts')->put($name.'.pdf', '%PDF '.$name);
            $ids[] = Contract::create($this->data([
                'nomor_kontrak' => $name, 'opd_id' => $opd->id,
                'file_path' => 'contracts/'.$name.'.pdf', 'file_name' => $name.'.pdf',
            ]))->id;
        }
        $this->actingAs($this->user(null, 'admin'));
        $response = $this->post('/admin/downloads/zip', ['contract_ids' => $ids])->assertOk();
        $path = $response->baseResponse->getFile()->getPathname();
        try {
            $zip = new ZipArchive;
            $this->assertTrue($zip->open($path));
            $this->assertSame(2, $zip->numFiles);
            $contents = [$zip->getFromIndex(0), $zip->getFromIndex(1)];
            $this->assertEqualsCanonicalizing(['%PDF first', '%PDF second'], $contents);
            $zip->close();
        } finally {
            @unlink($path);
        }
    }

    public function test_concurrent_zip_requests_use_different_temporary_paths(): void
    {
        Storage::fake('contracts');
        Storage::disk('contracts')->put('one.pdf', '%PDF one');
        $contract = Contract::create($this->data(['file_path' => 'contracts/one.pdf']));
        $this->actingAs($this->user(null, 'admin'));
        $paths = [];
        try {
            for ($i = 0; $i < 2; $i++) {
                $response = $this->post('/admin/downloads/zip', ['contract_ids' => [$contract->id]])->assertOk();
                $paths[] = $response->baseResponse->getFile()->getPathname();
            }
            $this->assertNotSame($paths[0], $paths[1]);
        } finally {
            foreach ($paths as $path) {
                @unlink($path);
            }
        }
    }

    public function test_user_upload_is_saved_with_private_document_metadata(): void
    {
        Storage::fake('contracts');
        $opd = $this->opd();
        $user = $this->user($opd);
        $this->actingAs($user);
        $this->post('/contracts', $this->data([
            'dokumen' => UploadedFile::fake()->createWithContent('original.pdf', "%PDF-1.4\nDocument"),
        ]))->assertRedirect('/contracts')->assertSessionHasNoErrors();

        $contract = Contract::firstOrFail();
        $this->assertSame($user->id, $contract->uploaded_by);
        $this->assertSame($opd->id, $contract->opd_id);
        $this->assertNotNull($contract->uploaded_at);
        Storage::disk('contracts')->assertExists($contract->file_name);
        $this->get('/contracts/'.$contract->id.'/download')->assertDownload($contract->file_name);
    }

    public function test_admin_validation_can_exclude_current_contract(): void
    {
        $contract = Contract::create($this->data());
        $this->actingAs($this->user(null, 'admin'));
        $this->postJson('/admin/contracts/validate-number', ['nomor_kontrak' => $contract->nomor_kontrak])
            ->assertOk()->assertJsonPath('valid', false);
        $this->postJson('/admin/contracts/validate-number', [
            'nomor_kontrak' => $contract->nomor_kontrak, 'contract_id' => $contract->id,
        ])->assertOk()->assertJsonPath('valid', true);
    }

    public function test_regular_user_cannot_download_admin_zip(): void
    {
        $this->actingAs($this->user($this->opd()));
        $this->post('/admin/downloads/zip')->assertForbidden();
    }

    public function test_login_is_throttled_after_five_attempts(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => 'missing@example.test', 'password' => 'wrong'])->assertRedirect();
        }
        $this->post('/login', ['email' => 'missing@example.test', 'password' => 'wrong'])->assertStatus(429);
    }
}
