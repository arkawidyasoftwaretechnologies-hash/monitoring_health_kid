<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Anak;

class AnakController extends Controller
{
    public function index()
    {
        return response()->json(Anak::with('puskesmas')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string',
            'nik' => 'nullable|string|unique:anaks',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'nama_ortu' => 'nullable|string',
            'alamat' => 'nullable|string',
            'puskesmas_id' => 'nullable|exists:puskesmas,id',
        ]);

        $anak = Anak::create($validated);
        return response()->json($anak, 201);
    }

    public function show(string $id)
    {
        $anak = Anak::with(['puskesmas', 'pengukurans.hasilStatusGizi'])->findOrFail($id);
        return response()->json($anak);
    }

    public function update(Request $request, string $id)
    {
        $anak = Anak::findOrFail($id);
        $anak->update($request->all());
        return response()->json($anak);
    }

    public function destroy(string $id)
    {
        $anak = Anak::findOrFail($id);
        $anak->delete();
        return response()->json(null, 204);
    }
}
