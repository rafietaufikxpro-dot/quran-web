<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman beranda "Cahaya Harian".
     */
    public function index(): View
    {
        return view('home');
    }
}
