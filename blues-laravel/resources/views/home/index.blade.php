@extends('layouts.app')
@section('title', 'TopVerifi — Virtual Numbers, Telegram Premium & Social Media Boosting')
@section('meta_description', 'TopVerifi — Get virtual phone numbers for SMS verification, buy Telegram Premium, and grow your social media with premium boosting services.')

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
            <span class="text-orange-300 text-sm font-medium">Nigeria's #1 Digital Services Platform</span>
        </div>
        <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black text-white mb-6 leading-none tracking-tight">
            Grow Faster.<br>
            <span class="shimmer-text">Verify Smarter.</span>
        </h1>
        <p class="text-slate-400 text-lg sm:text-xl max-w-2xl mx-auto mb-10 leading-relaxed">
            Virtual phone numbers, Telegram Premium, social media boosting, and digital accounts — all from your secure TopVerifi wallet.
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
            @foreach(['🔒 SSL Secured','⚡ Instant Delivery','🇳🇬 Naira Payments','💬 24/7 Support'] as $badge)
            <span class="text-xs text-slate-400 bg-slate-800/60 border border-slate-700 px-3 py-1.5 rounded-full">{{ $badge }}</span>
            @endforeach
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-3 gap-6 mt-14 max-w-lg mx-auto">
            <div class="text-center">
                <p class="text-2xl sm:text-3xl font-black text-white">{{ number_format($stats['users']) }}+</p>
                <p class="text-xs text-slate-500 mt-1 uppercase tracking-wider">Users</p>
            </div>
            <div class="text-center border-x border-slate-800">
                <p class="text-2xl sm:text-3xl font-black text-white">{{ number_format($stats['orders']) }}+</p>
                <p class="text-xs text-slate-500 mt-1 uppercase tracking-wider">Orders</p>
            </div>
            <div class="text-center">
                <p class="text-2xl sm:text-3xl font-black text-white">{{ number_format($stats['numbers']) }}+</p>
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
            <p class="text-slate-400 text-lg max-w-xl mx-auto">Virtual numbers, Telegram Premium, social media growth, and digital accounts.</p>
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

            {{-- Telegram Premium --}}
            <div class="tg-card tg-glow rounded-2xl p-8 reveal flex flex-col" style="background:linear-gradient(145deg,#1e1b4b 0%,#0f172a 60%,#1e1b4b 100%);border:1px solid rgba(99,102,241,.25)">
                <div class="flex items-start justify-between mb-6">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center" style="background:rgba(99,102,241,.2);animation:tg-spin 4s ease-in-out infinite">
                        <svg class="w-7 h-7 text-indigo-400" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.248l-2.038 9.586c-.145.658-.537.818-1.084.508l-3-2.21-1.447 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12L6.32 14.902l-2.96-.924c-.643-.203-.658-.643.136-.953l11.56-4.461c.537-.194 1.006.131.506.684z"/></svg>
                    </div>
                    <div class="flex flex-col gap-1 items-end">
                        <span class="text-[11px] bg-indigo-500/15 text-indigo-300 border border-indigo-500/25 px-2 py-0.5 rounded-full font-semibold">✨ Auto-Delivery</span>
                        <span class="text-[11px] bg-yellow-500/15 text-yellow-400 border border-yellow-500/25 px-2 py-0.5 rounded-full font-semibold">🔥 Popular</span>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Telegram Premium</h3>
                <p class="text-slate-300 mb-6 leading-relaxed flex-1">Buy Telegram Premium for yourself or as a gift. Automatically delivered to any Telegram username — no manual steps.</p>
                <ul class="space-y-2 mb-8">
                    @foreach(['No ads, faster downloads','4 GB file uploads','Animated emoji & stickers','Gift to any username','From ₦2,500/month'] as $f)
                    <li class="flex items-center gap-2 text-sm text-slate-300">
                        <svg class="w-4 h-4 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $f }}
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors mt-auto">
                    Get Telegram Premium →
                </a>
            </div>

            {{-- SMM Boosting --}}
            <div class="service-card bg-slate-800 border border-orange-500/30 rounded-2xl p-8 reveal flex flex-col" style="box-shadow:0 0 0 1px rgba(249,115,22,.1)">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-6 feature-icon" style="background:rgba(249,115,22,.12)">
                    <svg class="w-7 h-7 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <div class="inline-flex items-center gap-1.5 bg-orange-500/10 border border-orange-500/20 rounded-full px-3 py-0.5 mb-4 self-start">
                    <span class="w-1.5 h-1.5 bg-brand rounded-full"></span>
                    <span class="text-brand text-xs font-semibold">Powered by JustAnotherPanel</span>
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

{{-- Telegram Premium Spotlight --}}
<section class="py-20 bg-slate-950 overflow-hidden">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative rounded-3xl overflow-hidden reveal" style="background:linear-gradient(135deg,#1e1b4b 0%,#0f172a 50%,#1e1b4b 100%);border:1px solid rgba(99,102,241,.25);box-shadow:0 0 80px rgba(99,102,241,.15)">
            <div class="absolute inset-0 pointer-events-none" style="background:radial-gradient(ellipse at 80% 50%,rgba(99,102,241,.12) 0%,transparent 60%)"></div>
            <div class="relative z-10 flex flex-col lg:flex-row items-center gap-10 p-8 sm:p-12">
                <div class="flex-1">
                    <div class="inline-flex items-center gap-2 bg-indigo-500/10 border border-indigo-500/20 rounded-full px-4 py-1.5 mb-6">
                        <span class="w-2 h-2 bg-indigo-400 rounded-full animate-pulse"></span>
                        <span class="text-indigo-300 text-sm font-medium">Auto-Delivery Available</span>
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-black text-white mb-4 leading-tight">
                        Telegram Premium<br><span style="background:linear-gradient(90deg,#818cf8,#a5b4fc);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">Delivered Instantly</span>
                    </h2>
                    <p class="text-slate-300 text-lg leading-relaxed mb-8 max-w-lg">
                        Yes — users can <strong class="text-white">automatically buy Telegram Premium</strong> from TopVerifi. Enter a Telegram username, choose duration, and the subscription is gifted automatically through our panel. No manual steps.
                    </p>
                    <div class="flex flex-wrap gap-3 mb-8">
                        @foreach(['No Ads','4GB Uploads','Voice to Text','Animated Emoji','Premium Stickers','Story Priority'] as $f)
                        <span class="text-xs bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 px-3 py-1.5 rounded-full">{{ $f }}</span>
                        @endforeach
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-6 py-3 rounded-xl text-sm transition-all hover:scale-105">
                            Get Telegram Premium →
                        </a>
                        <a href="{{ route('services') }}" class="inline-flex items-center gap-2 border border-indigo-500/30 hover:border-indigo-400 text-indigo-300 hover:text-white font-semibold px-6 py-3 rounded-xl text-sm transition-colors">
                            Learn More
                        </a>
                    </div>
                </div>
                <div class="shrink-0">
                    <div class="w-48 h-48 sm:w-56 sm:h-56 rounded-3xl flex items-center justify-center" style="background:rgba(99,102,241,.15);border:1px solid rgba(99,102,241,.25)">
                        <svg class="w-24 h-24 text-indigo-400" viewBox="0 0 24 24" fill="currentColor" style="animation:tg-spin 4s ease-in-out infinite"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.248l-2.038 9.586c-.145.658-.537.818-1.084.508l-3-2.21-1.447 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12L6.32 14.902l-2.96-.924c-.643-.203-.658-.643.136-.953l11.56-4.461c.537-.194 1.006.131.506.684z"/></svg>
                    </div>
                    <div class="mt-4 text-center">
                        <p class="text-indigo-300 font-black text-2xl">from ₦2,500</p>
                        <p class="text-slate-400 text-xs mt-0.5">per month</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Platform logos marquee --}}
<section class="py-12 bg-slate-950 border-y border-slate-800 overflow-hidden">
    <p class="text-center text-slate-500 text-xs uppercase tracking-widest mb-6">Supports all major platforms</p>
    <div class="relative">
        <div class="marquee-track">
            @foreach(['Instagram','TikTok','YouTube','Twitter','Facebook','WhatsApp','Telegram','Snapchat','LinkedIn','Pinterest','Discord','Reddit','Twitch','Spotify','Netflix','Instagram','TikTok','YouTube','Twitter','Facebook','WhatsApp','Telegram','Snapchat','LinkedIn','Pinterest','Discord','Reddit','Twitch','Spotify','Netflix'] as $p)
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
                ['⚡','Instant Delivery','Numbers, Telegram Premium, and SMM orders start within seconds of payment.','rgba(249,115,22,.12)','rgba(249,115,22,.2)','text-brand'],
                ['🔒','Secure Wallet','Your funds are safe. Top up with card, bank transfer, or USSD — all via Paystack.','rgba(34,197,94,.12)','rgba(34,197,94,.2)','text-green-400'],
                ['📦','500+ Services','From WhatsApp verification to Instagram followers to Telegram Premium — one platform.','rgba(99,102,241,.12)','rgba(99,102,241,.2)','text-indigo-400'],
                ['🇳🇬','Made for Nigeria','Prices in naira. Payments via Nigerian banks. Built for the Nigerian market.','rgba(239,68,68,.12)','rgba(239,68,68,.2)','text-red-400'],
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
                ['Order Services','Buy virtual numbers, Telegram Premium, boost your social media, or browse digital accounts.','M13 10V3L4 14h7v7l9-11h-7z'],
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
                ['Fatima A.','Abuja','Telegram Premium','★★★★★','Bought Telegram Premium for my whole team. Delivered within 2 minutes to each username. Incredible service!'],
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
                ['Can I buy Telegram Premium automatically?','Yes! Through our SMM Boosting section, search for "Telegram Premium", enter your Telegram username, select how many months, and submit. The subscription is automatically delivered — no manual steps required.'],
                ['What is TopVerifi?','TopVerifi is a Nigerian digital services platform offering virtual phone numbers for SMS verification, Telegram Premium, social media growth services, and a digital accounts marketplace — all powered by your naira wallet.'],
                ['How do I fund my wallet?','Add funds using Paystack (card, bank transfer, USSD) or via manual bank transfer. Your balance is credited instantly on payment confirmation.'],
                ['Are the virtual numbers real?','Yes. We use GrizzlySMS, 5SIM, and HeroSMS to provide real temporary numbers from 80+ countries that can receive SMS messages.'],
                ['How fast are SMM orders?','Most orders start within minutes of placing. Telegram Premium is typically delivered within minutes. SMS numbers are instant.'],
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

{{-- CTA --}}
<section class="py-24 bg-slate-900">
    <div class="max-w-3xl mx-auto px-4 text-center reveal">
        <div class="bg-gradient-to-br from-orange-500/10 to-indigo-900/20 border border-orange-500/20 rounded-3xl p-12">
            <h2 class="text-3xl sm:text-4xl font-black text-white mb-4">Ready to get started?</h2>
            <p class="text-slate-400 mb-8 leading-relaxed">Join thousands of Nigerians using TopVerifi for virtual numbers, Telegram Premium, and social media growth.</p>
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
</script>
@endpush
