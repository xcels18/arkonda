<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_kontrak')->unique();
            $table->string('nama_kontrak');
            $table->string('nama_penyedia');
            $table->decimal('nilai_kontrak', 15, 2);
            $table->date('tanggal_kontrak');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->string('jenis_kontrak')->nullable();
            $table->string('bidang')->default('Aset');
            $table->year('tahun_anggaran');
            $table->text('keterangan')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->integer('file_size')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();
            
            $table->index('tahun_anggaran');
            $table->index('nama_penyedia');
            $table->index('jenis_kontrak');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};

