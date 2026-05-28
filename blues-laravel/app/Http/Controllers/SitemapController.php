<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\ListingCategory;

class SitemapController extends Controller
{
    public function index()
    {
        $staticUrls = [
            ['loc' => url('/'),          'priority' => '1.0',  'changefreq' => 'daily'],
            ['loc' => url('/services'),  'priority' => '0.9',  'changefreq' => 'weekly'],
            ['loc' => url('/login'),     'priority' => '0.6',  'changefreq' => 'monthly'],
            ['loc' => url('/register'),  'priority' => '0.7',  'changefreq' => 'monthly'],
            ['loc' => url('/terms'),     'priority' => '0.4',  'changefreq' => 'yearly'],
            ['loc' => url('/privacy'),   'priority' => '0.4',  'changefreq' => 'yearly'],
        ];

        $listings = Listing::where('is_active', true)
            ->select('id', 'updated_at')
            ->latest('updated_at')
            ->get();

        $categories = ListingCategory::select('id', 'updated_at')->get();

        return response()
            ->view('sitemap', compact('staticUrls', 'listings', 'categories'))
            ->header('Content-Type', 'application/xml');
    }
}
