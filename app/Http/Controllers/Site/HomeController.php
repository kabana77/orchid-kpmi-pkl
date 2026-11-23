<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Services\HomeService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(private HomeService $home)
    {
    }

    public function __invoke(): View
    {
        return view('public.home', $this->home->data());
    }
}
