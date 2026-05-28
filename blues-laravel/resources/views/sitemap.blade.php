<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    {{-- Static pages --}}
    @foreach($staticUrls as $url)
    <url>
        <loc>{{ $url['loc'] }}</loc>
        <changefreq>{{ $url['changefreq'] }}</changefreq>
        <priority>{{ $url['priority'] }}</priority>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
    </url>
    @endforeach

    {{-- Listing detail pages --}}
    @foreach($listings as $listing)
    <url>
        <loc>{{ url('/marketplace/' . $listing->id) }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
        <lastmod>{{ $listing->updated_at->toAtomString() }}</lastmod>
    </url>
    @endforeach

</urlset>
