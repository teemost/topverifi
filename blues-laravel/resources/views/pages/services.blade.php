@extends('layouts.app')
@section('title', 'All Services — TopVerifi')
@section('meta_description', 'Virtual phone numbers, Telegram Premium, SMM boosting, and digital accounts — everything on TopVerifi.')

@push('head')
<style>
@keyframes float-slow { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-16px)} }
@keyframes shimmer { 0%{background-position:-200% center} 100%{background-position:200% center} }
.orb { position:absolute;border-radius:9999px;filter:blur(80px);pointer-events:none; }
.shimmer-text { background:linear-gradient(90deg,#f97316 20%,#fb923c 40%,#fbbf24 60%,#f97316 80%);background-size:200% auto;-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;animation:shimmer 4s linear infinite; }
.svc-card { transition:all .3s ease; }
.svc-card:hover { transform:translateY(-6px); }
.reveal { opacity:0;transform:translateY(24px);transition:opacity .6s ease,transform .6s ease; }
.reveal.visible { opacity:1;transform:translateY(0); }
.step-line::after { content:'';position:absolute;left:50%;top:100%;width:2px;height:2rem;background:linear-gradient(to bottom,rgba(249,115,22,.4),transparent);transform:translateX(-50%); }
</style>
@endpush

@section('content')

{{-- Hero --}}
<section class="relative bg-slate-950 overflow-hidden pt-20 pb-24">
    <div class="orb" style="width:400px;height:400px;background:rgba(249,115,22,.1);top:-80px;right:-80px;animation:float-slow 8s ease-in-out infinite"></div>
    <div class="orb" style="width:300px;height:300px;background:rgba(99,102,241,.08);bottom:0;left:-60px;animation:float-slow 6s ease-in-out infinite 2s"></div>
    <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 bg-orange-500/10 border border-orange-500/20 rounded-full px-4 py-1.5 mb-6">
            <span class="w-2 h-2 bg-brand rounded-full animate-pulse"></span>
            <span class="text-orange-300 text-sm font-medium">All Services</span>
        </div>
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white mb-5 leading-tight">
            Everything You Need<br><span class="shimmer-text">In One Platform</span>
        </h1>
        <p class="text-slate-400 text-lg max-w-2xl mx-auto mb-8 leading-relaxed">
            From SMS verification to Telegram Premium to social media growth — TopVerifi covers it all, powered by your secure naira wallet.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('register') }}" class="btn-primary !px-8 !py-3.5 !text-base">Get Started Free →</a>
            <a href="{{ route('login') }}" class="btn-outline !px-8 !py-3.5 !text-base">Sign In</a>
        </div>

        {{-- Stats strip --}}
        <div class="grid grid-cols-3 gap-4 mt-12 max-w-md mx-auto">
            <div class="text-center">
                <p class="text-2xl font-black text-white">{{ number_format($stats['users']) }}+</p>
                <p class="text-[11px] text-slate-500 uppercase tracking-wider mt-0.5">Users</p>
            </div>
            <div class="text-center border-x border-slate-800">
                <p class="text-2xl font-black text-white">{{ number_format($stats['orders'] + $stats['numbers']) }}+</p>
                <p class="text-[11px] text-slate-500 uppercase tracking-wider mt-0.5">Orders</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-black text-white">500+</p>
                <p class="text-[11px] text-slate-500 uppercase tracking-wider mt-0.5">Services</p>
            </div>
        </div>
    </div>
</section>

{{-- Services Catalog --}}
<section class="py-20 bg-slate-900">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14 reveal">
            <p class="text-brand text-sm font-semibold uppercase tracking-wider mb-2">What We Offer</p>
            <h2 class="text-3xl sm:text-4xl font-black text-white">Our Service Catalog</h2>
        </div>

        <div class="space-y-6">

            {{-- 1. Virtual Numbers --}}
            <div class="svc-card bg-slate-800 border border-slate-700 hover:border-green-500/40 rounded-2xl p-6 sm:p-8 reveal">
                <div class="flex flex-col lg:flex-row gap-8">
                    <div class="flex-1">
                        <div class="flex items-center gap-4 mb-5">
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0" style="background:rgba(34,197,94,.12);border:1px solid rgba(34,197,94,.2)">
                                <svg class="w-7 h-7 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-xl font-bold text-white">Virtual Phone Numbers</h3>
                                    <span class="text-[11px] bg-green-500/15 text-green-400 border border-green-500/25 px-2 py-0.5 rounded-full font-semibold">SMS Verification</span>
                                </div>
                                <p class="text-slate-400 text-sm mt-0.5">Temporary numbers from 80+ countries</p>
                            </div>
                        </div>
                        <p class="text-slate-300 leading-relaxed mb-5">Get a real phone number from any country and receive the SMS verification code for any app or platform. Numbers are one-time use and delivered instantly — no SIM card needed.</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mb-6">
                            @foreach(['WhatsApp','Telegram','Instagram','TikTok','Facebook','Twitter / X','Google','Snapchat','Discord','Uber','Amazon','Any Platform'] as $p)
                            <div class="flex items-center gap-2 text-sm text-slate-300">
                                <svg class="w-3.5 h-3.5 text-green-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                {{ $p }}
                            </div>
                            @endforeach
                        </div>
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-500 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors">
                            Get a Number →
                        </a>
                    </div>
                    <div class="lg:w-64 shrink-0 space-y-3">
                        <div class="bg-slate-900 rounded-xl p-4 border border-slate-700">
                            <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Starting from</p>
                            <p class="text-2xl font-black text-green-400">₦80</p>
                            <p class="text-xs text-slate-400 mt-0.5">per number</p>
                        </div>
                        <div class="bg-slate-900 rounded-xl p-4 border border-slate-700 space-y-2">
                            @foreach(['Instant delivery','80+ countries','Pay per use','No monthly fee'] as $f)
                            <div class="flex items-center gap-2 text-sm text-slate-300">
                                <svg class="w-3.5 h-3.5 text-green-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                {{ $f }}
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Telegram Premium --}}
            <div class="svc-card bg-slate-800 border border-blue-500/30 hover:border-blue-400/50 rounded-2xl p-6 sm:p-8 reveal" style="background:linear-gradient(135deg,#0f172a 0%,#1e1b4b 100%)">
                <div class="flex flex-col lg:flex-row gap-8">
                    <div class="flex-1">
                        <div class="flex items-center gap-4 mb-5">
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0" style="background:rgba(99,102,241,.15);border:1px solid rgba(99,102,241,.3)">
                                <svg class="w-7 h-7 text-indigo-400" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.248l-2.038 9.586c-.145.658-.537.818-1.084.508l-3-2.21-1.447 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12L6.32 14.902l-2.96-.924c-.643-.203-.658-.643.136-.953l11.56-4.461c.537-.194 1.006.131.506.684z"/></svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-xl font-bold text-white">Telegram Premium</h3>
                                    <span class="text-[11px] bg-indigo-500/15 text-indigo-400 border border-indigo-500/25 px-2 py-0.5 rounded-full font-semibold">✨ Auto-Delivery</span>
                                    <span class="text-[11px] bg-yellow-500/15 text-yellow-400 border border-yellow-500/25 px-2 py-0.5 rounded-full font-semibold">🔥 Popular</span>
                                </div>
                                <p class="text-slate-400 text-sm mt-0.5">Gift Telegram Premium to any username instantly</p>
                            </div>
                        </div>
                        <p class="text-slate-300 leading-relaxed mb-5">
                            Yes — <strong class="text-white">users can automatically receive Telegram Premium</strong> from our platform. Simply provide your Telegram username, choose how many months, and the subscription is delivered automatically through our JAP panel integration. No manual steps needed.
                        </p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mb-6">
                            @foreach(['No Ads','4GB File Uploads','Voice-to-Text','Animated Emoji','Premium Stickers','Faster Downloads','Exclusive Reactions','Stories','Auto-Delivery'] as $f)
                            <div class="flex items-center gap-2 text-sm text-slate-300">
                                <svg class="w-3.5 h-3.5 text-indigo-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                {{ $f }}
                            </div>
                            @endforeach
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors">
                                Get Telegram Premium →
                            </a>
                            <div class="inline-flex items-center gap-2 bg-slate-700/50 text-slate-300 px-4 py-2.5 rounded-xl text-sm border border-slate-600">
                                <span class="text-indigo-400 font-bold">How:</span> Login → SMM Boosting → Search "Telegram Premium"
                            </div>
                        </div>
                    </div>
                    <div class="lg:w-64 shrink-0 space-y-3">
                        <div class="bg-indigo-900/30 rounded-xl p-4 border border-indigo-500/20">
                            <p class="text-xs text-indigo-300 uppercase tracking-wider mb-1">Starting from</p>
                            <p class="text-2xl font-black text-indigo-400">₦2,500</p>
                            <p class="text-xs text-slate-400 mt-0.5">per month subscription</p>
                        </div>
                        <div class="bg-slate-900/60 rounded-xl p-4 border border-slate-700 space-y-2">
                            @foreach(['Instant auto-delivery','Gift to any username','1, 3 or 6 months','No Telegram account needed','Works worldwide'] as $f)
                            <div class="flex items-center gap-2 text-sm text-slate-300">
                                <svg class="w-3.5 h-3.5 text-indigo-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                {{ $f }}
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. SMM Boosting --}}
            <div class="svc-card bg-slate-800 border border-slate-700 hover:border-brand/40 rounded-2xl p-6 sm:p-8 reveal">
                <div class="flex flex-col lg:flex-row gap-8">
                    <div class="flex-1">
                        <div class="flex items-center gap-4 mb-5">
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0" style="background:rgba(249,115,22,.12);border:1px solid rgba(249,115,22,.2)">
                                <svg class="w-7 h-7 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-xl font-bold text-white">Social Media Boosting</h3>
                                    <span class="text-[11px] bg-orange-500/15 text-brand border border-orange-500/25 px-2 py-0.5 rounded-full font-semibold">1000+ Services</span>
                                </div>
                                <p class="text-slate-400 text-sm mt-0.5">Powered by JustAnotherPanel</p>
                            </div>
                        </div>
                        <p class="text-slate-300 leading-relaxed mb-5">Grow your social media presence with real, high-quality engagement. Choose from over 1,000 services across all major platforms — followers, likes, views, comments, and more.</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mb-6">
                            @foreach(['Instagram Followers','YouTube Views','TikTok Likes','Twitter Followers','Facebook Likes','Spotify Streams','LinkedIn Connections','Telegram Members','Pinterest Saves','SoundCloud Plays','Twitch Viewers','Any Platform'] as $p)
                            <div class="flex items-center gap-2 text-sm text-slate-300">
                                <svg class="w-3.5 h-3.5 text-brand shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                {{ $p }}
                            </div>
                            @endforeach
                        </div>
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-brand hover:bg-brand-dark text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors">
                            Start Boosting →
                        </a>
                    </div>
                    <div class="lg:w-64 shrink-0 space-y-3">
                        <div class="bg-slate-900 rounded-xl p-4 border border-slate-700">
                            <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Starting from</p>
                            <p class="text-2xl font-black text-brand">₦50</p>
                            <p class="text-xs text-slate-400 mt-0.5">per 1,000 followers/views</p>
                        </div>
                        <div class="bg-slate-900 rounded-xl p-4 border border-slate-700 space-y-2">
                            @foreach(['1000+ services','Fast delivery','Real quality','Bulk discounts','Order tracking'] as $f)
                            <div class="flex items-center gap-2 text-sm text-slate-300">
                                <svg class="w-3.5 h-3.5 text-brand shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                {{ $f }}
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. Digital Accounts --}}
            <div class="svc-card bg-slate-800 border border-slate-700 hover:border-purple-500/40 rounded-2xl p-6 sm:p-8 reveal">
                <div class="flex flex-col lg:flex-row gap-8">
                    <div class="flex-1">
                        <div class="flex items-center gap-4 mb-5">
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0" style="background:rgba(168,85,247,.12);border:1px solid rgba(168,85,247,.2)">
                                <svg class="w-7 h-7 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-xl font-bold text-white">Digital Accounts Marketplace</h3>
                                    <span class="text-[11px] bg-purple-500/15 text-purple-400 border border-purple-500/25 px-2 py-0.5 rounded-full font-semibold">Verified Sellers</span>
                                </div>
                                <p class="text-slate-400 text-sm mt-0.5">Buy verified digital accounts instantly</p>
                            </div>
                        </div>
                        <p class="text-slate-300 leading-relaxed mb-5">Browse our marketplace for verified social media accounts, streaming subscriptions, gaming accounts, and digital goods — all delivered instantly to your email on purchase.</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mb-6">
                            @foreach(['Instagram Accounts','Netflix Subscriptions','Spotify Premium','Gaming Accounts','YouTube Channels','Facebook Pages','Twitter Accounts','Verified Accounts','More Added Daily'] as $p)
                            <div class="flex items-center gap-2 text-sm text-slate-300">
                                <svg class="w-3.5 h-3.5 text-purple-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                {{ $p }}
                            </div>
                            @endforeach
                        </div>
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-500 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors">
                            Browse Marketplace →
                        </a>
                    </div>
                    <div class="lg:w-64 shrink-0 space-y-3">
                        <div class="bg-slate-900 rounded-xl p-4 border border-slate-700">
                            <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Starting from</p>
                            <p class="text-2xl font-black text-purple-400">₦500</p>
                            <p class="text-xs text-slate-400 mt-0.5">per account</p>
                        </div>
                        <div class="bg-slate-900 rounded-xl p-4 border border-slate-700 space-y-2">
                            @foreach(['Instant delivery','Verified listings','Wallet payment','Buyer protection','24/7 support'] as $f)
                            <div class="flex items-center gap-2 text-sm text-slate-300">
                                <svg class="w-3.5 h-3.5 text-purple-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                {{ $f }}
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- How to Order --}}
<section class="py-20 bg-slate-950">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14 reveal">
            <p class="text-brand text-sm font-semibold uppercase tracking-wider mb-2">Simple Process</p>
            <h2 class="text-3xl sm:text-4xl font-black text-white">Order in Under 2 Minutes</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-6">
            @foreach([
                ['1','Create Account','Sign up free. No credit card needed.','M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                ['2','Fund Wallet','Add ₦ via card, bank transfer, or USSD.','M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                ['3','Pick a Service','Virtual numbers, Telegram Premium, SMM, or marketplace.','M4 6h16M4 12h16M4 18h16'],
                ['4','Instant Delivery','Get your number, account, or SMM order immediately.','M13 10V3L4 14h7v7l9-11h-7z'],
            ] as [$num, $title, $desc, $icon])
            <div class="text-center reveal">
                <div class="w-14 h-14 rounded-2xl mx-auto mb-4 flex items-center justify-center" style="background:rgba(249,115,22,.12);border:1px solid rgba(249,115,22,.2)">
                    <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                </div>
                <div class="text-brand font-black text-3xl mb-1">{{ $num }}</div>
                <h3 class="text-white font-bold text-base mb-1.5">{{ $title }}</h3>
                <p class="text-slate-400 text-sm leading-relaxed">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Payment Methods --}}
<section class="py-16 bg-slate-900 border-y border-slate-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-slate-500 text-xs uppercase tracking-widest mb-8">Accepted Payment Methods</p>
        <div class="flex flex-wrap justify-center gap-4">
            @foreach([
                ['💳','Card Payment','Visa, Mastercard via Paystack'],
                ['🏦','Bank Transfer','Direct transfer, instant credit'],
                ['📱','USSD','*737#, *822# and more'],
                ['👛','Wallet Balance','Spend from your TopVerifi wallet'],
            ] as [$icon, $name, $desc])
            <div class="flex items-center gap-3 bg-slate-800 border border-slate-700 rounded-xl px-5 py-3.5">
                <span class="text-2xl">{{ $icon }}</span>
                <div class="text-left">
                    <p class="text-white font-semibold text-sm">{{ $name }}</p>
                    <p class="text-slate-400 text-xs">{{ $desc }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- FAQ --}}
<section class="py-20 bg-slate-950">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-black text-white text-center mb-10 reveal">Common Questions</h2>
        <div class="space-y-3">
            @foreach([
                ['Can I buy Telegram Premium automatically?','Yes! Through our SMM Boosting section (powered by JustAnotherPanel), simply search for "Telegram Premium", enter your Telegram username, select how many months, and submit. The subscription is delivered automatically — no manual steps.'],
                ['What countries are virtual numbers available from?','We offer numbers from 80+ countries including USA, UK, Russia, India, Indonesia, Philippines, Nigeria, Brazil, Germany, France, and many more.'],
                ['How fast is delivery?','Virtual numbers are instant. Telegram Premium is typically delivered within minutes. SMM orders start within minutes, completion depends on quantity.'],
                ['Is there a minimum order?','No minimum. Buy one number for ₦80, or order 1,000 Instagram followers starting from ₦50. You only pay for what you use.'],
                ['What if the SMS code doesn\'t arrive?','For virtual numbers, you can wait and retry — the number stays active for several minutes. If no SMS arrives you can cancel for a refund to your wallet.'],
                ['How do I fund my wallet?','Go to Dashboard → Wallet and choose from card payment (Paystack), bank transfer, or USSD. Funds are credited instantly.'],
            ] as [$q, $a])
            <div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden reveal">
                <button onclick="this.parentElement.querySelector('.faq-b').classList.toggle('open');this.querySelector('svg').classList.toggle('rotate-180')"
                    class="w-full flex items-center justify-between p-5 text-left text-white font-semibold text-sm hover:bg-slate-700/50 transition-colors">
                    {{ $q }}
                    <svg class="w-4 h-4 text-slate-400 shrink-0 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="faq-b" style="max-height:0;overflow:hidden;transition:max-height .4s ease">
                    <p class="text-slate-400 text-sm px-5 pb-5 leading-relaxed">{{ $a }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-20 bg-slate-900">
    <div class="max-w-2xl mx-auto px-4 text-center reveal">
        <div class="bg-gradient-to-br from-orange-500/10 to-indigo-900/20 border border-orange-500/20 rounded-3xl p-12">
            <h2 class="text-3xl sm:text-4xl font-black text-white mb-4">Ready to get started?</h2>
            <p class="text-slate-400 mb-8 leading-relaxed">Create your free account, fund your wallet, and access all services in under 2 minutes.</p>
            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-brand hover:bg-brand-dark text-white font-bold px-8 py-4 rounded-xl text-base transition-all hover:scale-105">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Create Free Account
            </a>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
const obs = new IntersectionObserver(entries => {
    entries.forEach(e => { if(e.isIntersecting){e.target.classList.add('visible');obs.unobserve(e.target);} });
},{threshold:0.08,rootMargin:'0px 0px -30px 0px'});
document.querySelectorAll('.reveal').forEach(el => obs.observe(el));

document.querySelectorAll('.faq-b').forEach(el => {
    const btn = el.previousElementSibling;
    if (btn) btn.addEventListener('click', () => {
        const isOpen = el.style.maxHeight && el.style.maxHeight !== '0px';
        el.style.maxHeight = isOpen ? '0px' : el.scrollHeight + 'px';
        btn.querySelector('svg').style.transform = isOpen ? '' : 'rotate(180deg)';
    });
});
</script>
@endpush
