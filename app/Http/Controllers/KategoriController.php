<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        // $rsetKategori = DB::table('kategori')
        // ->select('id', 'deskripsi', DB::raw('ketKategorik(kategori) as ketkategorik'))
        // ->orderBy('kategori', 'asc') // Menambahkan orderBy untuk mengurutkan berdasarkan deskripsi (kategori) secara ascending
        // ->paginate(10);

        // return view('kategori.index', compact('rsetKategori'))
        // ->with('i', ($request->input('page', 1) - 1) * 10);

        $keyword = $request->input('keyword');

        // Query untuk mencari kategori berdasarkan keyword
        $query = DB::table('kategori')
            ->select('id', 'deskripsi', DB::raw('ketKategorik(kategori) as ketkategorik'))
            ->orderBy('kategori', 'asc');
    
        if (!empty($keyword)) {
            $query->where('deskripsi', 'LIKE', "%$keyword%")
                  ->orWhereRaw('ketKategorik(kategori) COLLATE utf8mb4_unicode_ci LIKE ?', ["%$keyword%"]);
        }
    
        $rsetKategori = $query->paginate(10);
    
        return view('kategori.index', compact('rsetKategori'))
            ->with('i', ($request->input('page', 1) - 1) * 10);
        }

    public function create()
    {
        $akategori = array('blank'=>'Pilih Kategori',
                            'M'=>'Barang Modal',
                            'A'=>'Alat',
                            'BHP'=>'Bahan Habis Pakai',
                            'BTHP'=>'Bahan Tidak Habis Pakai'
                            );
        return view('kategori.create',compact('akategori'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'deskripsi'   => 'required | unique:kategori',
            'kategori'     => 'required | in:M,A,BHP,BTHP',
        ]);

        Kategori::create([
            'deskripsi'  => $request->deskripsi,
	        'kategori'   => $request->kategori,
        ]);

        return redirect()->route('kategori.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $rsetKategori = Kategori::find($id);

        // $rsetKategori = Kategori::select('id','deskripsi','kategori',
        //     \DB::raw('(CASE
        //         WHEN kategori = "M" THEN "Modal"
        //         WHEN kategori = "A" THEN "Alat"
        //         WHEN kategori = "BHP" THEN "Bahan Habis Pakai"
        //         ELSE "Bahan Tidak Habis Pakai"
        //         END) AS ketKategori'))->where('id', '=', $id);

        return view('kategori.show', compact('rsetKategori'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
        {
        $rsetKategori = Kategori::find($id);

        // $selectedKategori = Kategori::find($kategori->kategori_id);
        return view('kategori.edit', compact('rsetKategori'));
        }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $request->validate([
            'deskripsi'           => 'required',
            'kategori'              => 'required',
            // 'spesifikasi'       => 'required',
            // 'stok'              => 'required',
            // 'kategori_id'       => 'required',
        ]);

        $rsetKategori = Kategori::find($id);
            $rsetKategori->update([
                'deskripsi'              => $request->deskripsi,
                'kategori'              => $request->kategori,
                // 'spesifikasi'       => $request->spesifikasi,
                // 'stok'              => $request->stok,
                // 'kategori_id'       => $request->kategori_id
            ]);

        return redirect()->route('kategori.index')->with(['success' => 'Data Kategori Berhasil Diubah!']);
    }

    public function destroy(string $id)

    {

        // cek apakah kategori_id ada di tabel barang.kategori_id ?

        if (DB::table('barang')->where('kategori_id', $id)->exists()){

            return redirect()->route('kategori.index')->with(['gagal' => 'Data Gagal Dihapus!']);


        } else {

            $rsetKategori = Kategori::find($id);

            $rsetKategori->delete();

            return redirect()->route('kategori.index')->with(['success' => 'Data Berhasil Dihapus!']);

        }

    }


}