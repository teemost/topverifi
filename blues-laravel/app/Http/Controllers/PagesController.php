<?php
namespace App\Http\Controllers;

use App\Models\Setting;

class PagesController extends Controller
{
    public function terms()   { return view('pages.terms'); }
    public function privacy() { return view('pages.privacy'); }

    public function services()
    {
        $stats = [
            'users'   => \App\Models\User::count(),
            'orders'  => \App\Models\BoostingOrder::count(),
            'numbers' => \App\Models\VirtualNumberOrder::count(),
        ];
        return view('pages.services', compact('stats'));
    }
}
