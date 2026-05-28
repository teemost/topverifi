<?php
namespace App\Observers;

use App\Models\Listing;

class ListingObserver
{
    public function created(Listing $listing): void
    {
        if ($listing->is_active) {
            $this->pingGoogle();
        }
    }

    public function updated(Listing $listing): void
    {
        if ($listing->wasChanged('is_active') && $listing->is_active) {
            $this->pingGoogle();
        }
    }

    private function pingGoogle(): void
    {
        try {
            $sitemapUrl = urlencode(url('/sitemap.xml'));
            $ch = curl_init('https://www.google.com/ping?sitemap=' . $sitemapUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 5,
                CURLOPT_USERAGENT      => 'TopVerifi-SitemapPing/1.0',
            ]);
            curl_exec($ch);
            curl_close($ch);
        } catch (\Throwable) {
        }
    }
}
