<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function home()
    {
        return view('frontend.home');
    }

    public function how_it_works()
    {
        return view('frontend.how_it_works');
    }

    public function pricing()
    {
        return view('frontend.pricing');
    }

    public function safety()
    {
        return view('frontend.safety');
    }

    public function contact()
    {
        return view('frontend.contact');
    }
}
