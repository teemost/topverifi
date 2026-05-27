<?php
namespace App\Http\Controllers;

use App\Models\{User, Setting, BoostingOrder};

class HomeController extends Controller
{
    public function index()
    {
        $stats = [
            'users'   => User::count(),
            'orders'  => BoostingOrder::count(),
            'numbers' => \App\Models\VirtualNumberOrder::count(),
        ];

        $promoBannerEnabled = Setting::get('promo_banner_enabled', '0') === '1';
        $promoBannerText    = Setting::get('promo_banner_text', '');
        $promoBannerColor   = Setting::get('promo_banner_color', 'brand');

        return view('home.index', compact('stats', 'promoBannerEnabled', 'promoBannerText', 'promoBannerColor'));
    }
}
