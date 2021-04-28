<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $title = "Halaman home";
        $content['mahasiswa'] = array(
            'nama' => 'Kadek Utari Darma Putri',
            'nim' => '1915101033'
        );

        return view('admin/beranda', compact('title', 'content'));
    }

    public function dashboard()
    {
        $title = "Buku Novel Dia Dilanku";

        return view('admin/dashboard', compact('title'));
    }
}
