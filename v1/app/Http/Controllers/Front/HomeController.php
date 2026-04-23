<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Service;

class HomeController extends Controller
{
    public function index()
    {
        $services = Service::where('active', true)
            ->orderBy('name')
            ->get();

        return view('front.pages.home', compact('services'));
    }
}
