<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\FormEngine\Form;


class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::where('active', true)
            ->orderBy('name')
            ->paginate(12);

        return view('front.pages.services.index', compact('services'));
    }

    public function show($slug)
    {
        $service = Service::where('slug', $slug)
            ->where('active', true)
            ->firstOrFail();

        $form = Form::fromService($service);

        return view('front.pages.services.show', compact('service', 'form'));
    }
}
