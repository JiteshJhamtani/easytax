<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function show($slug)
    {
        $page = Page::where('slug', $slug)
                    ->where('is_active', true)
                    ->firstOrFail();

        return view('front.pages.show', compact('page'));
    }
}
