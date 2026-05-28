@extends('layouts.app')
@section('title', 'TopVerifi — Virtual Numbers & Social Media Boosting')
@section('meta_description', 'TopVerifi — Get virtual phone numbers for instant SMS verification and grow your social media with premium boosting services. Fast, secure, and affordable.')
@section('meta_keywords', 'virtual phone numbers, SMS verification, buy virtual number, social media boosting, SMM panel, instagram followers, TopVerifi')
@section('canonical', url('/'))
@section('og_title', 'TopVerifi — Virtual Numbers & Social Media Boosting')
@section('og_description', 'Get virtual phone numbers for instant SMS verification and grow your social media with premium boosting services. Fast, secure, and affordable.')
@push('schema')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebSite",
    "name": "TopVerifi",
    "url": "{{ url('/') }}",
    "description": "Virtual phone numbers for SMS verification and premium social media boosting services.",
    "potentialAction": {
        "@@type": "SearchAction",
        "target": "{{ url('/services') }}?q={search_term_string}",
        "query-input": "required name=search_term_string"
    }
}
</script>
@endpush

@push('head')
<style>
@keyframes float-slow   { 0%,100%{transform:translateY(0) scale(1)} 50%{transform:translateY(-20px) scale(1.03)} }
@keyframes float-medium { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-12px)} }
@keyframes shimmer      { 0%{background-position:-200% center} 100%{background-position:200% center} }
@keyframes pulse-glow   { 0%,100%{box-shadow:0 0 20px rgba(249,115,22,.3)} 50%{box-shadow:0 0 40px rgba(249,115,22,.6)} }
@keyframes gradient-x   { 0%,100%{background-position:0% 50%} 50%{background-position:100% 50%} }
@keyframes marquee      { 0%{transform:translateX(0)} 100%{transform:translateX(-50%)} }
@keyframes count-up     { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
@keyframes tg-spin      { 0%,100%{transform:rotate(-5deg) scale(1)} 50%{transform:rotate(5deg) scale(1.05)} }

.orb { position:absolute; border-radius:9999px; filter:blur(80px); pointer-events:none; }
.orb-1 { width:500px;height:500px;background:rgba(249,115,22,.12);top:-100px;right:-100px;animation:float-slow 8s ease-in-out infinite; }
.orb-2 { width:350px;height:350px;background:rgba(234,88,12,.10);bottom:0;left:-80px;animation:float-medium 6s ease-in-out infinite 2s; }
.orb-3 { width:220px;height:220px;background:rgba(251,146,60,.08);top:40%;left:45%;animation:float-slow 10s ease-in-out infinite 1s; }

.shimmer-text {
  background: linear-gradient(90deg,#f97316 20%,#fb923c 40%,#fbbf24 60%,#f97316 80%);
  background-size:200% auto;
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
  animation:shimmer 4s linear infinite;
}

.service-card { transition:all .3s ease; }
.service-card:hover { transform:translateY(-8px); box-shadow:0 0 0 1px rgba(249,115,22,.3),0 20px 40px rgba(0,0,0,.4); }

.tg-card { transition:all .3s ease; }
.tg-card:hover { transform:translateY(-6px); box-shadow:0 0 0 1px rgba(99,102,241,.4),0 20px 50px rgba(0,0,0,.5); }

.reveal { opacity:0;transform:translateY(28px);transition:opacity .65s ease,transform .65s ease; }
.reveal.visible { opacity:1;transform:translateY(0); }

.marquee-track { display:flex;width:max-content;animation:marquee 28s linear infinite; }
.marquee-track:hover { animation-play-state:paused; }

.faq-body { max-height:0;overflow:hidden;transition:max-height .4s ease,padding .3s ease; }
.faq-body.open { max-height:400px; }

.feature-icon { transition:transform .3s ease; }
.feature-wrap:hover .feature-icon { transform:scale(1.15) rotate(-3deg); }

.tg-glow { box-shadow:0 0 60px rgba(99,102,241,.25),0 0 0 1px rgba(99,102,241,.2); }
</style>
@endpush

@section('content')

{{-- Promo Banner --}}
@if($promoBannerEnabled && $promoBannerText)
<div class="w-full py-2.5 px-4 text-center text-sm font-semibold
    @if($promoBannerColor==='green') bg-green-600 text-white
    @elseif($promoBannerColor==='yellow') bg-yellow-500 text-slate-900
    @elseif($promoBannerColor==='red') bg-red-600 text-white
    @elseif($promoBannerColor==='purple') bg-purple-600 text-white
    @else bg-brand text-white @endif">
    {{ $promoBannerText }}
</div>
@endif

{{-- Hero --}}
<section class="relative bg-slate-950 overflow-hidden pt-24 pb-32">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 bg-orange-500/10 border border-orange-500/20 rounded-full px-4 py-1.5 mb-8">
            <span class="w-2 h-2 bg-brand rounded-full animate-pulse"></span>
            <span class="text-orange-300 text-sm font-medium">#1 Digital Services Platform</span>
        </div>
        <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black text-white mb-6 leading-none tracking-tight">
            Grow Faster.<br>
            <span class="shimmer-text">Verify Smarter.</span>
        </h1>
        <p class="text-slate-400 text-lg sm:text-xl max-w-2xl mx-auto mb-10 leading-relaxed">
            Virtual phone numbers, social media boosting, and digital accounts — all from your secure TopVerifi wallet.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 bg-brand hover:bg-brand-dark text-white font-bold px-8 py-4 rounded-xl text-base transition-all hover:scale-105" style="animation:pulse-glow 3s ease-in-out infinite">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Get Started Free
            </a>
            <a href="{{ route('services') }}" class="inline-flex items-center justify-center gap-2 border border-slate-600 hover:border-brand text-slate-300 hover:text-white font-semibold px-8 py-4 rounded-xl text-base transition-all">
                View All Services →
            </a>
        </div>

        {{-- Trust badges --}}
        <div class="flex flex-wrap justify-center gap-3 mt-10">
            @foreach(['🔒 SSL Secured','⚡ Instant Delivery','💳 Fast Payments','💬 24/7 Support'] as $badge)
            <span class="text-xs text-slate-400 bg-slate-800/60 border border-slate-700 px-3 py-1.5 rounded-full">{{ $badge }}</span>
            @endforeach
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-3 gap-6 mt-14 max-w-lg mx-auto">
            <div class="text-center">
                <p class="text-2xl sm:text-3xl font-black text-white stat-counter" data-target="{{ $stats['users'] }}">0+</p>
                <p class="text-xs text-slate-500 mt-1 uppercase tracking-wider">Users</p>
            </div>
            <div class="text-center border-x border-slate-800">
                <p class="text-2xl sm:text-3xl font-black text-white stat-counter" data-target="{{ $stats['orders'] }}">0+</p>
                <p class="text-xs text-slate-500 mt-1 uppercase tracking-wider">Orders</p>
            </div>
            <div class="text-center">
                <p class="text-2xl sm:text-3xl font-black text-white stat-counter" data-target="{{ $stats['numbers'] }}">0+</p>
                <p class="text-xs text-slate-500 mt-1 uppercase tracking-wider">Numbers</p>
            </div>
        </div>
    </div>
</section>

{{-- Services --}}
<section class="py-24 bg-slate-900">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 reveal">
            <p class="text-brand text-sm font-semibold uppercase tracking-wider mb-3">What We Offer</p>
            <h2 class="text-3xl sm:text-4xl font-black text-white mb-4">Everything You Need In One Place</h2>
            <p class="text-slate-400 text-lg max-w-xl mx-auto">Virtual numbers, social media growth, and digital accounts.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            {{-- Virtual Numbers --}}
            <div class="service-card bg-slate-800 border border-slate-700 rounded-2xl p-8 reveal flex flex-col">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-6 feature-icon" style="background:rgba(34,197,94,.12)">
                    <svg class="w-7 h-7 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Virtual Numbers</h3>
                <p class="text-slate-400 mb-6 leading-relaxed flex-1">Temporary phone numbers for SMS verification on WhatsApp, Telegram, Instagram, TikTok, and 500+ other platforms.</p>
                <ul class="space-y-2 mb-8">
                    @foreach(['Numbers from 80+ countries','Instant SMS delivery','Pay per use — from ₦80','New number every time'] as $f)
                    <li class="flex items-center gap-2 text-sm text-slate-300">
                        <svg class="w-4 h-4 text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $f }}
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-500 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors mt-auto">
                    Get a Number →
                </a>
            </div>

            {{-- SMM Boosting --}}
            <div class="service-card bg-slate-800 border border-orange-500/30 rounded-2xl p-8 reveal flex flex-col" style="box-shadow:0 0 0 1px rgba(249,115,22,.1)">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-6 feature-icon" style="background:rgba(249,115,22,.12)">
                    <svg class="w-7 h-7 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Social Media Boosting</h3>
                <p class="text-slate-400 mb-6 leading-relaxed flex-1">Grow your Instagram, YouTube, TikTok, Twitter, and more with real, high-quality engagement at the best prices.</p>
                <ul class="space-y-2 mb-8">
                    @foreach(['Instagram followers & likes','YouTube views & subscribers','TikTok growth services','Twitter/X engagement','1000+ services available'] as $f)
                    <li class="flex items-center gap-2 text-sm text-slate-300">
                        <svg class="w-4 h-4 text-brand shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $f }}
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-brand hover:bg-brand-dark text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors mt-auto">
                    Boost Now →
                </a>
            </div>
        </div>

        <div class="text-center mt-8">
            <a href="{{ route('services') }}" class="inline-flex items-center gap-2 text-slate-400 hover:text-white text-sm transition-colors border border-slate-700 hover:border-slate-500 px-5 py-2.5 rounded-xl">
                View All Services Including Digital Accounts →
            </a>
        </div>
    </div>
</section>

{{-- Platform logos marquee --}}
<section class="py-12 bg-slate-950 border-y border-slate-800 overflow-hidden">
    <p class="text-center text-slate-500 text-xs uppercase tracking-widest mb-6">Supports all major platforms</p>
    <div class="relative">
        <div class="marquee-track">
            @foreach(['Instagram','TikTok','YouTube','Twitter','Facebook','WhatsApp','Snapchat','LinkedIn','Pinterest','Discord','Reddit','Twitch','Spotify','Netflix','Instagram','TikTok','YouTube','Twitter','Facebook','WhatsApp','Snapchat','LinkedIn','Pinterest','Discord','Reddit','Twitch','Spotify','Netflix'] as $p)
            <div class="mx-6 flex items-center gap-2 text-slate-500 whitespace-nowrap text-sm font-medium">
                <span class="w-2 h-2 bg-brand rounded-full opacity-50"></span>
                {{ $p }}
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Why TopVerifi --}}
<section class="py-24 bg-slate-900">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 reveal">
            <p class="text-brand text-sm font-semibold uppercase tracking-wider mb-3">Why Choose Us</p>
            <h2 class="text-3xl sm:text-4xl font-black text-white mb-4">Built for Speed & Reliability</h2>
            <p class="text-slate-400 max-w-xl mx-auto">We partner with the best providers so you always get fast, reliable service.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach([
                ['⚡','Instant Delivery','Numbers and SMM orders start within seconds of payment.','rgba(249,115,22,.12)','rgba(249,115,22,.2)','text-brand'],
                ['🔒','Secure Wallet','Your funds are safe. Top up with card, bank transfer, or USSD.','rgba(34,197,94,.12)','rgba(34,197,94,.2)','text-green-400'],
                ['📦','500+ Services','From WhatsApp verification to Instagram followers — one platform for everything.','rgba(99,102,241,.12)','rgba(99,102,241,.2)','text-indigo-400'],
                ['🌍','Global Access','Prices in your local currency. Fast payments. Built for everyone, everywhere.','rgba(239,68,68,.12)','rgba(239,68,68,.2)','text-red-400'],
            ] as [$icon, $title, $desc, $bg, $border, $tc])
            <div class="feature-wrap bg-slate-800 border border-slate-700 rounded-2xl p-6 text-center reveal hover:border-slate-600 transition-colors">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4 feature-icon text-2xl" style="background:{{ $bg }};border:1px solid {{ $border }}">
                    {{ $icon }}
                </div>
                <h3 class="text-white font-bold mb-2">{{ $title }}</h3>
                <p class="text-slate-400 text-sm leading-relaxed">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- How it works --}}
<section class="py-24 bg-slate-950">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 reveal">
            <p class="text-brand text-sm font-semibold uppercase tracking-wider mb-3">Quick Start</p>
            <h2 class="text-3xl sm:text-4xl font-black text-white mb-4">Get Started in 2 Minutes</h2>
            <p class="text-slate-400">No complex setup. No credit cards required to sign up.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
            @foreach([
                ['Create Account','Sign up for free in seconds. Verify your email and you\'re in.','M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                ['Fund Wallet','Add funds via card, bank transfer, or USSD. Instant credit to your naira wallet.','M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                ['Order Services','Buy virtual numbers, boost your social media, or browse digital accounts.','M13 10V3L4 14h7v7l9-11h-7z'],
            ] as $i => [$title, $desc, $path])
            <div class="text-center reveal">
                <div class="w-16 h-16 rounded-2xl mx-auto mb-5 flex items-center justify-center" style="background:rgba(249,115,22,.12);border:1px solid rgba(249,115,22,.2)">
                    <svg class="w-7 h-7 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"/></svg>
                </div>
                <div class="text-brand font-black text-4xl mb-2">{{ $i + 1 }}</div>
                <h3 class="text-white font-bold text-lg mb-2">{{ $title }}</h3>
                <p class="text-slate-400 text-sm leading-relaxed">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Social Proof / Testimonials --}}
<section class="py-20 bg-slate-900">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 reveal">
            <p class="text-brand text-sm font-semibold uppercase tracking-wider mb-3">What Users Say</p>
            <h2 class="text-3xl font-black text-white">Trusted by Thousands</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            @foreach([
                ['Chukwuemeka O.','Lagos','Virtual Numbers','★★★★★','TopVerifi is the only platform I trust for getting cheap WhatsApp numbers. Instant delivery, never failed me.'],
                ['Fatima A.','Abuja','SMM Boosting','★★★★★','Grew my business page significantly in just days. Affordable, fast, and the quality is excellent!'],
                ['Tunde M.','Port Harcourt','SMM Boosting','★★★★★','My Instagram page went from 500 to 8,000 followers in a week. Very affordable and real followers too.'],
            ] as [$name, $city, $service, $stars, $review])
            <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 reveal">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-brand/20 border border-brand/30 flex items-center justify-center text-brand font-bold text-sm">
                        {{ substr($name, 0, 1) }}
                    </div>
                    <div>
                        <p class="text-white font-semibold text-sm">{{ $name }}</p>
                        <p class="text-slate-500 text-xs">{{ $city }} · {{ $service }}</p>
                    </div>
                </div>
                <p class="text-yellow-400 text-sm mb-3">{{ $stars }}</p>
                <p class="text-slate-300 text-sm leading-relaxed">"{{ $review }}"</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- FAQ --}}
<section class="py-24 bg-slate-950">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-black text-white text-center mb-12 reveal">Frequently Asked Questions</h2>
        <div class="space-y-3">
            @foreach([
                ['What is TopVerifi?','TopVerifi is a digital services platform offering virtual phone numbers for SMS verification, social media growth services, and a digital accounts marketplace — all powered by your TopVerifi wallet.'],
                ['How do I fund my wallet?','Add funds using card payment, bank transfer, or USSD. Your balance is credited instantly on payment confirmation.'],
                ['Are the virtual numbers real?','Yes. We provide real temporary numbers from 80+ countries that can receive SMS messages for any platform.'],
                ['How fast are SMM orders?','Most orders start within minutes of placing. SMS numbers are instant.'],
                ['What if no SMS arrives on my number?','Numbers remain active for several minutes. If no SMS arrives you can cancel the order and the cost is refunded to your wallet.'],
                ['Is my data safe?','Absolutely. We never share your data and all payments are processed through secure, encrypted channels via Paystack.'],
            ] as [$q, $a])
            <div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden reveal">
                <button onclick="this.parentElement.querySelector('.faq-body').classList.toggle('open');this.querySelector('svg').classList.toggle('rotate-180')"
                    class="w-full flex items-center justify-between p-5 text-left text-white font-semibold text-sm hover:bg-slate-700/50 transition-colors">
                    {{ $q }}
                    <svg class="w-4 h-4 text-slate-400 shrink-0 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="faq-body">
                    <p class="text-slate-400 text-sm px-5 pb-5 leading-relaxed">{{ $a }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Mobile Preview --}}
<section class="py-24 bg-slate-950 overflow-hidden">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-16">

            {{-- Text side --}}
            <div class="flex-1 text-center lg:text-left reveal">
                <div class="inline-flex items-center gap-2 bg-orange-500/10 border border-orange-500/20 rounded-full px-4 py-1.5 mb-6">
                    <span class="w-2 h-2 bg-brand rounded-full animate-pulse"></span>
                    <span class="text-orange-300 text-sm font-medium">Works on any device</span>
                </div>
                <h2 class="text-3xl sm:text-4xl font-black text-white mb-5 leading-tight">
                    Everything in your<br><span class="shimmer-text">pocket</span>
                </h2>
                <p class="text-slate-400 text-lg leading-relaxed mb-8 max-w-lg">
                    TopVerifi works seamlessly on mobile, tablet, and desktop. Order virtual numbers, track boosting orders, and manage your wallet — wherever you are.
                </p>
                <div class="space-y-3 mb-10">
                    @foreach([
                        ['📱','Mobile-first design','Fast and responsive on every screen size'],
                        ['⚡','Instant notifications','Get alerted the moment your SMS arrives'],
                        ['💼','Full wallet control','Top up, spend, and track your balance on the go'],
                    ] as [$icon, $title, $desc])
                    <div class="flex items-start gap-3 text-left">
                        <span class="text-xl mt-0.5">{{ $icon }}</span>
                        <div>
                            <p class="text-white font-semibold text-sm">{{ $title }}</p>
                            <p class="text-slate-400 text-sm">{{ $desc }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-brand hover:bg-brand-dark text-white font-bold px-7 py-3.5 rounded-xl text-sm transition-all hover:scale-105">
                    Get Started Free →
                </a>
            </div>

            {{-- Phone mockup --}}
            <div class="shrink-0 flex justify-center reveal">
                <div class="relative">
                    {{-- Glow --}}
                    <div class="absolute inset-0 rounded-[3rem] blur-3xl opacity-30" style="background:radial-gradient(ellipse,#f97316 0%,transparent 70%)"></div>

                    {{-- Phone frame --}}
                    <div class="relative w-64 sm:w-72 rounded-[2.8rem] border-[6px] border-slate-700 bg-slate-900 shadow-2xl overflow-hidden" style="box-shadow:0 0 0 1px rgba(249,115,22,.15),0 40px 80px rgba(0,0,0,.6)">

                        {{-- Status bar --}}
                        <div class="bg-slate-950 px-5 pt-3 pb-1 flex items-center justify-between">
                            <span class="text-white text-[10px] font-semibold">9:41</span>
                            <div class="w-16 h-4 bg-slate-950 rounded-full border border-slate-800 mx-auto absolute left-1/2 -translate-x-1/2 top-0"></div>
                            <div class="flex items-center gap-1">
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M1.5 8.5a13 13 0 0121 0M5 12a10 10 0 0114 0M8.5 15.5a6 6 0 017 0M12 19h.01"/></svg>
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M17 7H7a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2V9a2 2 0 00-2-2z"/><path d="M19 9h1a1 1 0 011 1v4a1 1 0 01-1 1h-1"/></svg>
                            </div>
                        </div>

                        {{-- App header --}}
                        <div class="bg-slate-900 px-4 py-3 border-b border-slate-800 flex items-center justify-between">
                            <span class="font-bold text-white text-sm">Top<span class="text-brand">Verifi</span></span>
                            <div class="w-7 h-7 rounded-full bg-brand/20 border border-brand/30 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                        </div>

                        {{-- Wallet card --}}
                        <div class="px-4 pt-4 pb-2">
                            <div class="rounded-2xl p-4 mb-3" style="background:linear-gradient(135deg,#ea580c,#f97316)">
                                <p class="text-white/70 text-[10px] font-medium uppercase tracking-wider mb-1">Wallet Balance</p>
                                <p class="text-white font-black text-2xl">₦12,450.00</p>
                                <div class="flex justify-between items-end mt-3">
                                    <p class="text-white/70 text-[9px]">TopVerifi Wallet</p>
                                    <div class="flex gap-1">
                                        <div class="w-5 h-5 rounded-full bg-white/20"></div>
                                        <div class="w-5 h-5 rounded-full bg-white/30 -ml-2"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Quick actions --}}
                            <div class="grid grid-cols-3 gap-2 mb-3">
                                @foreach([['📱','Numbers'],['📈','Boost'],['🛒','Market']] as [$i,$l])
                                <div class="bg-slate-800 rounded-xl p-2.5 text-center border border-slate-700">
                                    <span class="text-lg">{{ $i }}</span>
                                    <p class="text-slate-400 text-[9px] mt-1 font-medium">{{ $l }}</p>
                                </div>
                                @endforeach
                            </div>

                            {{-- Recent orders --}}
                            <p class="text-slate-500 text-[9px] uppercase tracking-wider font-semibold mb-2">Recent Activity</p>
                            @foreach([
                                ['WhatsApp Number','Delivered','text-green-400','bg-green-500/10'],
                                ['Instagram Boost','Processing','text-brand','bg-orange-500/10'],
                                ['WhatsApp Number','Delivered','text-green-400','bg-green-500/10'],
                            ] as [$name, $status, $tc, $bg])
                            <div class="flex items-center justify-between py-1.5 border-b border-slate-800 last:border-0">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-lg {{ $bg }} flex items-center justify-center">
                                        <svg class="w-3 h-3 {{ $tc }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <span class="text-slate-300 text-[10px] font-medium">{{ $name }}</span>
                                </div>
                                <span class="{{ $tc }} text-[9px] font-semibold">{{ $status }}</span>
                            </div>
                            @endforeach
                        </div>

                        {{-- Bottom nav --}}
                        <div class="bg-slate-950 border-t border-slate-800 px-4 py-3 flex justify-around">
                            @foreach([
                                ['M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6','text-brand'],
                                ['M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z','text-slate-600'],
                                ['M13 7h8m0 0v8m0-8l-8 8-4-4-6 6','text-slate-600'],
                                ['M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z','text-slate-600'],
                            ] as [$path, $color])
                            <svg class="w-5 h-5 {{ $color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"/></svg>
                            @endforeach
                        </div>
                    </div>

                    {{-- Floating badge --}}
                    <div class="absolute -right-4 top-16 bg-green-500 text-white text-[10px] font-bold px-3 py-1.5 rounded-full shadow-lg animate-bounce">
                        SMS Received! ✓
                    </div>
                    <div class="absolute -left-4 bottom-24 bg-slate-800 border border-slate-700 text-white text-[10px] font-semibold px-3 py-1.5 rounded-xl shadow-lg">
                        ⚡ Order placed
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-24 bg-slate-900">
    <div class="max-w-3xl mx-auto px-4 text-center reveal">
        <div class="bg-gradient-to-br from-orange-500/10 to-indigo-900/20 border border-orange-500/20 rounded-3xl p-12">
            <h2 class="text-3xl sm:text-4xl font-black text-white mb-4">Ready to get started?</h2>
            <p class="text-slate-400 mb-8 leading-relaxed">Join thousands of users on TopVerifi for virtual numbers and social media growth.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 bg-brand hover:bg-brand-dark text-white font-bold px-8 py-4 rounded-xl text-base transition-all hover:scale-105">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Create Free Account
                </a>
                <a href="{{ route('services') }}" class="inline-flex items-center justify-center gap-2 border border-slate-600 hover:border-brand text-slate-300 hover:text-white font-semibold px-8 py-4 rounded-xl text-base transition-all">
                    Explore All Services →
                </a>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
const observer = new IntersectionObserver(entries => {
    entries.forEach(e => { if(e.isIntersecting) { e.target.classList.add('visible'); observer.unobserve(e.target); } });
}, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

// Count-up animation for stats
function animateCounter(el) {
    const target = parseInt(el.dataset.target, 10) || 0;
    const duration = 1800;
    const start = performance.now();
    const easeOut = t => 1 - Math.pow(1 - t, 3);
    function step(now) {
        const elapsed = Math.min((now - start) / duration, 1);
        const value = Math.round(easeOut(elapsed) * target);
        el.textContent = value.toLocaleString() + '+';
        if (elapsed < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
}

const statObserver = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (e.isIntersecting) {
            animateCounter(e.target);
            statObserver.unobserve(e.target);
        }
    });
}, { threshold: 0.5 });
document.querySelectorAll('.stat-counter').forEach(el => statObserver.observe(el));
</script>
@endpush
