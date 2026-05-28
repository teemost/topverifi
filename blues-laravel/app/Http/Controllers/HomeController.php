<?php
namespace App\Http\Controllers;

use App\Models\{User, Setting, BoostingOrder};

class HomeController extends Controller
{
    // Base floor counts for social proof on a new platform
    const BASE_USERS   = 1240;
    const BASE_ORDERS  = 3870;
    const BASE_NUMBERS = 8500;

    public function index()
    {
        $stats = [
            'users'   => User::count() + self::BASE_USERS,
            'orders'  => BoostingOrder::count() + self::BASE_ORDERS,
            'numbers' => \App\Models\VirtualNumberOrder::count() + self::BASE_NUMBERS,
        ];

        $promoBannerEnabled = Setting::get('promo_banner_enabled', '0') === '1';
        $promoBannerText    = Setting::get('promo_banner_text', '');
        $promoBannerColor   = Setting::get('promo_banner_color', 'brand');

        return view('home.index', compact('stats', 'promoBannerEnabled', 'promoBannerText', 'promoBannerColor'));
    }
}
