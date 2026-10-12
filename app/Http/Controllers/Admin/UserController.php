<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Opd;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display list of users
     */
    public function index(Request $request)
    {
        $query = User::with('opd');

        // Filter by verification status
        if ($request->filled('status')) {
            if ($request->status === 'verified') {
                $query->verified();
            } elseif ($request->status === 'unverified') {
                $query->unverified();
            }
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        $opds = Opd::orderBy('nama')->get();

        return view('admin.users.index', compact('users', 'opds'));
    }

    /**
     * Show edit form for a user
     */
    public function edit(User $user)
    {
        $opds = Opd::orderBy('nama')->get();

        return view('admin.users.edit', compact('user', 'opds'));
    }

    /**
     * Update user data and (optionally) password
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'opd_id' => ['nullable', 'exists:opds,id'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $oldValues = $user->toArray();

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->opd_id = $validated['opd_id'] ?? null;

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
            $user->remember_token = Str::random(60);
        }

        DB::transaction(function () use ($user, $validated, $oldValues) {
            $user->save();
            if (! empty($validated['password']) && config('session.driver') === 'database') {
                DB::connection(config('session.connection'))->table(config('session.table'))
                    ->where('user_id', $user->id)->delete();
            }
            AuditLog::log('update', "Admin mengupdate data user: {$user->name}", $user, $oldValues, $user->toArray());
            if (! empty($validated['password'])) {
                AuditLog::log('reset_password', "Admin mereset kata sandi user: {$user->name}", $user);
            }
        });

        return redirect()->route('admin.users.index')->with('success', "Data user {$user->name} berhasil diperbarui.");
    }

    /**
     * Verify a user
     */
    public function verify(User $user)
    {
        if ($user->isVerified()) {
            return back()->with('info', 'User sudah terverifikasi.');
        }

        $user->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verified_by' => auth()->id(),
        ]);

        // Log verification
        AuditLog::logVerifyUser($user, auth()->user());

        return back()->with('success', "User {$user->name} berhasil diverifikasi.");
    }

    /**
     * Unverify a user
     */
    public function unverify(User $user)
    {
        if (! $user->isVerified()) {
            return back()->with('info', 'User belum terverifikasi.');
        }

        // Prevent unverifying admin
        if ($user->isAdmin()) {
            return back()->with('error', 'Tidak dapat membatalkan verifikasi admin.');
        }

        DB::transaction(function () use ($user) {
            $oldValues = $user->only(['is_verified', 'verified_at', 'verified_by']);
            $user->update(['is_verified' => false, 'verified_at' => null, 'verified_by' => null]);
            AuditLog::log('unverify_user', "Admin membatalkan verifikasi user: {$user->name}", $user, $oldValues,
                $user->only(['is_verified', 'verified_at', 'verified_by']));
        });

        return back()->with('success', "Verifikasi {$user->name} dibatalkan.");
    }

    /**
     * Change user role
     */
    public function changeRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => ['required', 'in:admin,user'],
        ]);

        // Prevent changing own role
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat mengubah role sendiri.');
        }

        DB::transaction(function () use ($user, $validated) {
            $oldValues = $user->only(['role']);
            $user->update(['role' => $validated['role']]);
            AuditLog::log('change_role', "Admin mengubah role user: {$user->name}", $user, $oldValues, $user->only(['role']));
        });

        return back()->with('success', "Role {$user->name} diubah menjadi {$validated['role']}.");
    }

    /**
     * Delete a user
     */
    public function destroy(User $user)
    {
        // Prevent deleting self
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $userName = $user->name;
        $user->delete();

        AuditLog::log('delete', "Admin menghapus user {$userName}");

        return back()->with('success', "User {$userName} berhasil dihapus.");
    }
}
