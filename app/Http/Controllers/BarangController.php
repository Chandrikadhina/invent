<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\BarangKeluar;
use App\Models\BarangMasuk;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        // $rsetBarang = Barang::with('kategori')->latest()->paginate(10);

        // return view('barang.index', compact('rsetBarang'))
        //     ->with('i', (request()->input('page', 1) - 1) * 10);
        $keyword = $request->input('keyword');

        // Query untuk mencari barang berdasarkan keyword
        $rsetBarang = Barang::where('merk', 'LIKE', "%$keyword%")
            ->orWhere('seri', 'LIKE', "%$keyword%")
            ->orWhere('spesifikasi', 'LIKE', "%$keyword%")
            ->orWhere('stok', 'LIKE', "%$keyword%")
            ->orWhereHas('kategori', function ($query) use ($keyword) {
                $query->where('deskripsi', 'LIKE', "%$keyword%");
            })
            ->paginate(10);
    
        return view('barang.index', compact('rsetBarang'))
        ->with('i', (request()->input('page', 1) - 1) * 10);
    }


    public function create()
    {
        // Get all categories
        $akategori = Kategori::all();
        return view('barang.create', compact('akategori'));
    }

    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'merk'        => 'required|string|max:255',
            'seri'        => 'required|string|max:255',
            'spesifikasi' => 'required|string',
            'kategori_id' => 'required|exists:kategori,id',
        ]);

        // Create a new Barang record
        Barang::create([
            'merk'        => $request->merk,
            'seri'        => $request->seri,
            'spesifikasi' => $request->spesifikasi,
            'kategori_id' => $request->kategori_id,
        ]);

        return redirect()->route('barang.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function show(string $id)
    {
        // Find the Barang by ID or fail
        $rsetBarang = Barang::findOrFail($id);
        return view('barang.show', compact('rsetBarang'));
    }

    public function edit(string $id)
    {
        // Find the Barang and get all categories
        $rsetBarang = Barang::findOrFail($id);
        $akategori = Kategori::all();
        $selectedKategori = Kategori::findOrFail($rsetBarang->kategori_id);

        return view('barang.edit', compact('rsetBarang', 'akategori', 'selectedKategori'));
    }

    public function update(Request $request, string $id)
    {
        // Validate the request data
        $request->validate([
            'merk'        => 'required|string|max:255',
            'seri'        => 'required|string|max:255',
            'spesifikasi' => 'required|string',
            'kategori_id' => 'required|exists:kategori,id',
        ]);

        // Find the Barang by ID or fail
        $rsetBarang = Barang::findOrFail($id);

        // Update the Barang record
        $rsetBarang->update([
            'merk'        => $request->merk,
            'seri'        => $request->seri,
            'spesifikasi' => $request->spesifikasi,
            'kategori_id' => $request->kategori_id,
        ]);

        return redirect()->route('barang.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    public function destroy(string $id)
    {
        // Find the Barang by ID or fail
        $rsetBarang = Barang::findOrFail($id);

        // Check if the stock is greater than 0 before deleting
        if ($rsetBarang->stok > 0) {
            return redirect()->route('barang.index')->with(['error' => 'Barang dengan stok lebih dari 0 tidak dapat dihapus!']);
        }

        // Check relationships with BarangKeluar
        if (BarangKeluar::where('barang_id', $id)->exists()) {
            return redirect()->route('barang.index')->with(['gagal' => 'Data Gagal Dihapus! Data masih digunakan dalam tabel Barang Keluar']);
        }

        // Check relationships with BarangMasuk
        if (BarangMasuk::where('barang_id', $id)->exists()) {
            return redirect()->route('barang.index')->with(['gagal' => 'Data Gagal Dihapus! Data masih digunakan dalam tabel Barang Masuk']);
        }

        // Delete the Barang record
        $rsetBarang->delete();
        return redirect()->route('barang.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }
}