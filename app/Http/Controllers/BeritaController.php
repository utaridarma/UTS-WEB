<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BeritaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $berita = Berita::all();
        $title = 'Daftar Buku Novel Restock';
        return view('admin.berandaberita', compact('title', 'berita'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = "Input data";
        return view('admin.inputberita', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //memberikan pesan default ketika terjadi error validasi
        $message = [
            'required' => 'Kolom :attribute Harus lengkap',
            'date' => 'Kolom :attribute Harus Tanggal',
            'numeric' => 'Kolom :attribute Harus Angka',
        ];
        //memberikan validasi menggunakan helper validate pada setiap input field
        //validasi error disertai dengan pesan yang telah didefinisikan sebelumnya
        $validasi = $request->validate([
            'title' => 'required|unique:beritas|max:255',
            'description' => 'required',
            'cover' => 'required|mimes:jpg,bmp,png|max:512'
        ], $message);
        //membuat nama fiile untuk file cover
        $fileName = time() . $request->file('cover')->getClientOriginalName();
        //funsgi upload file menggunakan storeAs agar nama file bisa disesuaikan dengan k ebutuhan
        $path = $request->file('cover')->storeAs('covers', $fileName);
        //mengambil id user menggunakan helper auth
        //id user akan disimpan pada setiap baris data
        $validasi['user_id'] = Auth::id();
        //menyimpan nama cover berupa path upload
        $validasi['cover'] = $path;
        //fungsi simpan menggunakan eloquent create
        //dengan isian data dari variabel validasi 
        Berita::create($validasi);
        //redirect ke index lengkap dengan pesan sukses
        return redirect('berita')->with('success', 'Data berhasil tersimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //memanggil data berita sesuai parameter id berupa primarykey
        $berita = Berita::find($id);
        $title = "Edit data";
        //mengirim data berita menggunakan helpe compact
        return view('admin.inputberita', compact('title', 'berita'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $message = [
            'required' => 'Kolom :attribute Harus lengkap',
            'date' => 'Kolom :attribute Harus Tanggal',
            'numeric' => 'Kolom :attribute Harus Angka',
        ];
        //pastikan validasi sesuai kebutuhan
        $validasi = $request->validate([
            'title' => 'required|unique:beritas|max:255',
            'description' => 'required'
        ], $message);
        //jika ditemukan berupa upload file 
        if ($request->hasFile('cover')) {
            $fileName = time() . $request->file('cover')->getClientOriginalName();
            $path = $request->file('cover')->storeAs('covers', $fileName);
            $validasi['cover'] = $path;
            //mengambil data berita untuk menemukan path cover
            $berita = Berita::find($id);
            //menghapus cover sesuai path 
            Storage::delete($berita->cover);
        }
        $validasi['user_id'] = Auth::id();
        //update data berita berdasarkan id dengan isian berupa data dari variabel validasi
        Berita::where('id', $id)->update($validasi);
        return redirect('berita')->with('success', 'Data berhasil Terupdate');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //mengambil data berita untuk mengetahui path cover
        $berita = Berita::find($id);
        //jika berita ditemukan 
        if ($berita != null) {
            //detele cover berdasarkan path cover 
            Storage::delete($berita->cover);
            //delete berita 
            Berita::where('id', $berita->id)->delete();
        }
        return redirect('berita')->with('success', 'Data berhasil terhapus');
    }
}
