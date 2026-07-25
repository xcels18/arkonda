<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Opd;
use Illuminate\Http\Request;

class OpdController extends Controller
{
    public function index()
    {
        $opds = Opd::orderBy('kode')->paginate(20);
        return view('admin.opds.index', compact('opds'));
    }

    public function create()
    {
        return view('admin.opds.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:10', 'unique:opds'],
            'nama' => ['required', 'string', 'max:255'],
            'singkatan' => ['required', 'string', 'max:50'],
        ]);

        Opd::create($validated);

        return redirect()->route('admin.opds.index')->with('success', 'OPD berhasil ditambahkan.');
    }

    public function edit(Opd $opd)
    {
        return view('admin.opds.edit', compact('opd'));
    }

    public function update(Request $request, Opd $opd)
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:10', 'unique:opds,kode,' . $opd->id],
            'nama' => ['required', 'string', 'max:255'],
            'singkatan' => ['required', 'string', 'max:50'],
        ]);

        $opd->update($validated);

        return redirect()->route('admin.opds.index')->with('success', 'OPD berhasil diperbarui.');
    }

    public function destroy(Opd $opd)
    {
        if ($opd->contracts()->count() > 0) {
            return redirect()->route('admin.opds.index')->with('error', 'OPD tidak dapat dihapus karena masih memiliki kontrak terkait.');
        }

        $opd->delete();

        return redirect()->route('admin.opds.index')->with('success', 'OPD berhasil dihapus.');
    }
}
