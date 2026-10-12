<?php

namespace Tests\Feature;

use App\Exports\ContractsExport;
use App\Models\AuditLog;
use App\Models\Contract;
use App\Models\User;
use App\Services\ContractDocumentService;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class DataSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_verified' => true]);
    }

    private function contract(): Contract
    {
        return Contract::create([
            'nomor_kontrak' => '=1+1', 'nama_kontrak' => 'Contoh', 'nama_penyedia' => '=2+2',
            'nilai_kontrak' => 1234, 'tanggal_kontrak' => '2026-10-11', 'tahun_anggaran' => 2026,
            'jenis_kontrak' => 'Kontrak', 'file_path' => 'contracts/old.pdf', 'file_name' => 'old.pdf',
        ]);
    }

    public function test_seeding_without_explicit_credentials_creates_no_accounts(): void
    {
        config(['admin_bootstrap.email' => null, 'admin_bootstrap.password' => null]);
        try {
            (new AdminUserSeeder)->run();
            $this->fail('Expected explicit credentials to be required.');
        } catch (\InvalidArgumentException $exception) {
            $this->assertDatabaseCount('users', 0);
        }
    }

    public function test_seeder_uses_explicit_credentials_and_does_not_overwrite_accounts(): void
    {
        config(['admin_bootstrap.email' => 'setup@example.test', 'admin_bootstrap.password' => 'Unique-Bootstrap-2026']);
        (new AdminUserSeeder)->run();
        $user = User::firstOrFail();
        $this->assertTrue(Hash::check('Unique-Bootstrap-2026', $user->password));
        $this->assertTrue($user->isAdmin());
        $user->update(['role' => 'user', 'password' => 'Existing-Password-2026']);
        (new AdminUserSeeder)->run();
        $this->assertDatabaseCount('users', 1);
        $this->assertSame('user', $user->fresh()->role);
        $this->assertTrue(Hash::check('Existing-Password-2026', $user->fresh()->password));
    }

    public function test_password_reset_rotates_remember_token_and_rejects_old_session(): void
    {
        $admin = $this->admin();
        $user = User::factory()->create(['is_verified' => true, 'remember_token' => 'old-token']);
        $oldHash = $user->password;
        $this->actingAs($admin)->put('/admin/users/'.$user->id, [
            'name' => $user->name, 'email' => $user->email,
            'password' => 'New-Secure-Password-2026', 'password_confirmation' => 'New-Secure-Password-2026',
        ])->assertRedirect('/admin/users');
        $user->refresh();
        $this->assertNotSame('old-token', $user->remember_token);
        $this->assertTrue(Hash::check('New-Secure-Password-2026', $user->password));
        $this->assertDatabaseHas('audit_logs', ['action' => 'reset_password', 'model_id' => $user->id, 'user_id' => $admin->id]);
        $this->actingAs($user)->withSession(['password_hash_web' => $oldHash])
            ->get('/dashboard')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_password_reset_removes_database_sessions(): void
    {
        config(['session.driver' => 'database']);
        $admin = $this->admin();
        $user = User::factory()->create();
        \Illuminate\Support\Facades\DB::table('sessions')->insert([
            'id' => 'compromised-session', 'user_id' => $user->id,
            'payload' => base64_encode(serialize([])), 'last_activity' => time(),
        ]);
        $this->actingAs($admin)->put('/admin/users/'.$user->id, [
            'name' => $user->name, 'email' => $user->email,
            'password' => 'New-Secure-Password-2026', 'password_confirmation' => 'New-Secure-Password-2026',
        ])->assertRedirect('/admin/users');
        $this->assertDatabaseMissing('sessions', ['id' => 'compromised-session']);
    }

    public function test_login_records_session_password_baseline_before_first_dashboard_visit(): void
    {
        $user = User::factory()->create(['password' => 'Login-Secure-2026', 'is_verified' => true]);
        $this->post('/login', ['email' => $user->email, 'password' => 'Login-Secure-2026'])
            ->assertRedirect('/dashboard')->assertSessionHas('password_hash_web');
        $user->update(['password' => 'Changed-Secure-2026']);
        Auth::forgetGuards();
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_xlsx_stores_untrusted_text_literally_and_preserves_numeric_values(): void
    {
        $this->contract();
        $path = tempnam(sys_get_temp_dir(), 'security-xlsx-');
        try {
            file_put_contents($path, Excel::raw(new ContractsExport, ExcelFormat::XLSX));
            $sheet = IOFactory::load($path)->getActiveSheet();
            $this->assertSame('=1+1', $sheet->getCell('B2')->getValue());
            $this->assertSame(DataType::TYPE_STRING, $sheet->getCell('B2')->getDataType());
            $this->assertSame('=2+2', $sheet->getCell('F2')->getValue());
            $this->assertSame(DataType::TYPE_STRING, $sheet->getCell('F2')->getDataType());
            $this->assertEquals(1234, $sheet->getCell('G2')->getValue());
            $this->assertSame(DataType::TYPE_NUMERIC, $sheet->getCell('G2')->getDataType());
        } finally {
            unlink($path);
        }
    }

    public function test_delete_archives_document_and_records_recovery_metadata(): void
    {
        Storage::fake('contracts');
        Storage::disk('contracts')->put('old.pdf', '%PDF original');
        $contract = $this->contract();
        $admin = $this->admin();
        $this->actingAs($admin)->delete('/admin/contracts/'.$contract->id)->assertRedirect('/admin/contracts');
        $this->assertDatabaseMissing('contracts', ['id' => $contract->id]);
        $log = AuditLog::where('action', 'delete')->firstOrFail();
        $this->assertSame($admin->id, $log->user_id);
        $this->assertSame('contracts/old.pdf', $log->old_values['file_path']);
        $archive = $log->new_values['archived_document'];
        Storage::disk('contracts')->assertExists($archive);
        $this->assertSame('%PDF original', Storage::disk('contracts')->get($archive));
        Storage::disk('contracts')->assertMissing('old.pdf');
    }

    public function test_failed_archive_copy_never_deletes_record_or_original(): void
    {
        $contract = $this->contract();
        $disk = \Mockery::mock();
        $disk->shouldReceive('exists')->with('old.pdf')->once()->andReturn(true);
        $disk->shouldReceive('copy')->once()->andReturn(false);
        $disk->shouldReceive('delete')->with(\Mockery::on(fn ($path) => str_starts_with($path, '.deleted/')))->once()->andReturn(true);
        Storage::shouldReceive('disk')->with('contracts')->once()->andReturn($disk);
        try {
            app(ContractDocumentService::class)->destroy($contract);
            $this->fail('Expected archive failure.');
        } catch (\RuntimeException $exception) {
            $this->assertDatabaseHas('contracts', ['id' => $contract->id]);
            $this->assertDatabaseCount('audit_logs', 0);
        }
    }

    public function test_failed_delete_transaction_preserves_original_document_and_record(): void
    {
        Storage::fake('contracts');
        Storage::disk('contracts')->put('old.pdf', '%PDF original');
        $contract = $this->contract();
        $this->actingAs($this->admin());
        $dispatcher = AuditLog::getEventDispatcher();
        AuditLog::setEventDispatcher(clone $dispatcher);
        AuditLog::creating(fn () => throw new \RuntimeException('Audit unavailable'));
        try {
            app(ContractDocumentService::class)->destroy($contract);
            $this->fail('Expected audit failure.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Audit unavailable', $exception->getMessage());
            $this->assertDatabaseHas('contracts', ['id' => $contract->id]);
            $this->assertSame(['old.pdf'], Storage::disk('contracts')->allFiles());
        } finally {
            AuditLog::setEventDispatcher($dispatcher);
        }
    }

    public function test_role_and_verification_changes_record_actor_and_before_after_values(): void
    {
        $admin = $this->admin();
        $user = User::factory()->create(['is_verified' => true]);
        $this->actingAs($admin)->post('/admin/users/'.$user->id.'/unverify')->assertRedirect();
        $log = AuditLog::where('action', 'unverify_user')->firstOrFail();
        $this->assertSame($admin->id, $log->user_id);
        $this->assertTrue($log->old_values['is_verified']);
        $this->assertFalse($log->new_values['is_verified']);
        $this->patch('/admin/users/'.$user->id.'/role', ['role' => 'admin'])->assertRedirect();
        $log = AuditLog::where('action', 'change_role')->firstOrFail();
        $this->assertSame($admin->id, $log->user_id);
        $this->assertSame('user', $log->old_values['role']);
        $this->assertSame('admin', $log->new_values['role']);
    }
}
