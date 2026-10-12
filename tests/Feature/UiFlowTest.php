<?php

namespace Tests\Feature;

use App\Models\Contract;
use App\Models\Opd;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UiFlowTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_verified' => true]);
    }

    public function test_all_admin_workspaces_render_with_the_shared_layout(): void
    {
        $opd = Opd::create(['kode' => 'INS', 'nama' => 'Inspektorat', 'singkatan' => 'INS']);
        $user = $this->admin();
        $contract = Contract::create([
            'nomor_kontrak' => 'UI-001', 'nama_kontrak' => 'Arsip contoh', 'nama_penyedia' => 'Penyedia',
            'nilai_kontrak' => 1000, 'tanggal_kontrak' => '2026-10-10', 'tahun_anggaran' => 2026, 'jenis_kontrak' => 'Kontrak', 'opd_id' => $opd->id,
        ]);
        $this->actingAs($user);
        foreach (['/dashboard', '/admin/contracts', '/admin/contracts/create', '/admin/contracts/'.$contract->id, '/admin/contracts/'.$contract->id.'/edit', '/admin/opds', '/admin/opds/create', '/admin/opds/'.$opd->id.'/edit', '/admin/users', '/admin/users/'.$user->id.'/edit', '/admin/reports', '/admin/downloads'] as $url) {
            $this->get($url)->assertOk()->assertSee('id="main-content"', false);
        }
    }

    public function test_opd_search_limits_the_list(): void
    {
        Opd::create(['kode' => 'INS', 'nama' => 'Inspektorat', 'singkatan' => 'INS']);
        Opd::create(['kode' => 'KES', 'nama' => 'Dinas Kesehatan', 'singkatan' => 'DINKES']);
        $this->actingAs($this->admin())->get('/admin/opds?search=INS')
            ->assertOk()->assertViewHas('opds', fn ($opds) => $opds->total() === 1 && $opds->first()->kode === 'INS');
    }

    public function test_invalid_opd_modal_reopens_with_entered_values(): void
    {
        $this->actingAs($this->admin());
        $this->from('/admin/opds')->post('/admin/opds', ['_modal' => 'opd', 'kode' => 'INS', 'nama' => 'Inspektorat', 'singkatan' => ''])
            ->assertRedirect('/admin/opds')->assertSessionHasErrors('singkatan');
        $this->get('/admin/opds')->assertOk()->assertSee('data-auto-open', false)->assertSee('value="INS"', false)->assertSee('value="Inspektorat"', false);
    }

    public function test_invalid_user_modal_keeps_update_target_and_values(): void
    {
        $admin = $this->admin();
        $user = User::factory()->create();
        $this->actingAs($admin);
        $this->from('/admin/users')->put('/admin/users/'.$user->id, [
            '_modal' => 'user', '_record_id' => $user->id, 'name' => 'Nama diperbarui', 'email' => 'invalid',
        ])->assertRedirect('/admin/users')->assertSessionHasErrors('email');
        $this->get('/admin/users')->assertOk()->assertSee('data-auto-open', false)
            ->assertSee('value="Nama diperbarui"', false)->assertSee('action="'.route('admin.users.update', $user).'"', false);
        $this->assertSame($user->name, $user->fresh()->name);
    }

    public function test_ajax_contract_creation_returns_redirect_and_validation_errors(): void
    {
        $this->actingAs($this->admin());
        $this->postJson('/admin/contracts', [])->assertUnprocessable()->assertJsonValidationErrors('nomor_kontrak');
        $this->postJson('/admin/contracts', [
            'nomor_kontrak' => 'UI-001', 'nama_kontrak' => 'Arsip contoh', 'nama_penyedia' => 'Penyedia',
            'nilai_kontrak' => 1000, 'tanggal_kontrak' => '2026-10-10', 'tahun_anggaran' => 2026, 'jenis_kontrak' => 'Kontrak',
        ])->assertCreated()->assertJsonPath('redirect', route('admin.contracts.index'));
        $this->assertDatabaseCount('contracts', 1);
    }

    public function test_empty_zip_selection_is_rejected_instead_of_downloading_everything(): void
    {
        $this->actingAs($this->admin())->postJson('/admin/downloads/zip', ['selection_mode' => 'selected'])
            ->assertUnprocessable()->assertJsonValidationErrors('contract_ids');
    }

    public function test_public_auth_pages_render(): void
    {
        $this->get('/login')->assertOk()->assertSee('Masuk ke ARKONDA')->assertSee('images/logo-inspektorat.png');
        $this->get('/register')->assertOk()->assertSee('Buat Akun Baru');
    }
}
