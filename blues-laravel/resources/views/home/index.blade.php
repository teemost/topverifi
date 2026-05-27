@extends('layouts.app')
@section('title', 'TopVerifi — Virtual Numbers & Social Media Boosting')
@section('meta_description', 'TopVerifi — Get virtual phone numbers for SMS verification and grow your social media with premium boosting services.')

@push('head')
<style>
@keyframes float-slow   { 0%,100%{transform:translateY(0) scale(1)} 50%{transform:translateY(-20px) scale(1.03)} }
@keyframes float-medium { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-12px)} }
@keyframes shimmer      { 0%{background-position:-200% center} 100%{background-position:200% center} }
@keyframes pulse-glow   { 0%,100%{box-shadow:0 0 20px rgba(249,115,22,.3)} 50%{box-shadow:0 0 40px rgba(249,115,22,.6)} }
@keyframes gradient-x   { 0%,100%{background-position:0% 50%} 50%{background-position:100% 50%} }
@keyframes marquee      { 0%{transform:translateX(0)} 100%{transform:translateX(-50%)} }

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

.reveal { opacity:0;transform:translateY(28px);transition:opacity .65s ease,transform .65s ease; }
.reveal.visible { opacity:1;transform:translateY(0); }

.marquee-track { display:flex;width:max-content;animation:marquee 28s linear infinite; }
.marquee-track:hover { animation-play-state:paused; }

.faq-body { max-height:0;overflow:hidden;transition:max-height .4s ease,padding .3s ease; }
.faq-body.open { max-height:300px; }
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
            Virtual phone numbers for any platform, plus premium social media boosting — all from your secure TopVerifi wallet.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 bg-brand hover:bg-brand-dark text-white font-bold px-8 py-4 rounded-xl text-base transition-all hover:scale-105" style="animation:pulse-glow 3s ease-in-out infinite">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Get Started Free
            </a>
            <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 border border-slate-600 hover:border-brand text-slate-300 hover:text-white font-semibold px-8 py-4 rounded-xl text-base transition-all">
                Sign In →
            </a>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-3 gap-6 mt-16 max-w-lg mx-auto">
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
            <h2 class="text-3xl sm:text-4xl font-black text-white mb-4">Two Powerful Services</h2>
            <p class="text-slate-400 text-lg max-w-xl mx-auto">Everything you need to grow and verify — in one platform.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Virtual Numbers --}}
            <div class="service-card bg-slate-800 border border-slate-700 rounded-2xl p-8 reveal">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-6" style="background:rgba(34,197,94,.12)">
                    <svg class="w-7 h-7 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Virtual Numbers</h3>
                <p class="text-slate-400 mb-6 leading-relaxed">Get temporary phone numbers for SMS verification on WhatsApp, Telegram, Instagram, TikTok, and 500+ other platforms.</p>
                <ul class="space-y-2 mb-8">
                    @foreach(['Numbers from multiple countries','Instant SMS delivery','Pay per use from wallet','New number every time'] as $f)
                    <li class="flex items-center gap-2 text-sm text-slate-300">
                        <svg class="w-4 h-4 text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $f }}
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-500 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors">
                    Get a Number →
                </a>
            </div>

            {{-- SMM Boosting --}}
            <div class="service-card bg-slate-800 border border-orange-500/30 rounded-2xl p-8 reveal" style="box-shadow:0 0 0 1px rgba(249,115,22,.1)">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-6" style="background:rgba(249,115,22,.12)">
                    <svg class="w-7 h-7 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <div class="inline-flex items-center gap-1.5 bg-orange-500/10 border border-orange-500/20 rounded-full px-3 py-0.5 mb-4">
                    <span class="w-1.5 h-1.5 bg-brand rounded-full"></span>
                    <span class="text-brand text-xs font-semibold">Powered by JustAnotherPanel</span>
                </div>
                <h3 class="text-xl font-bold text-white mb-3">Social Media Boosting</h3>
                <p class="text-slate-400 mb-6 leading-relaxed">Grow your Instagram, YouTube, TikTok, Twitter, and more with real, high-quality engagement at the best prices.</p>
                <ul class="space-y-2 mb-8">
                    @foreach(['Instagram followers & likes','YouTube views & subscribers','TikTok growth services','Twitter/X engagement','1000+ services available'] as $f)
                    <li class="flex items-center gap-2 text-sm text-slate-300">
                        <svg class="w-4 h-4 text-brand shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $f }}
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-brand hover:bg-brand-dark text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors">
                    Boost Now →
                </a>
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

{{-- How it works --}}
<section class="py-24 bg-slate-900">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 reveal">
            <h2 class="text-3xl sm:text-4xl font-black text-white mb-4">How It Works</h2>
            <p class="text-slate-400">Get started in under 2 minutes.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
            @foreach([
                ['Create Account','Sign up for free in seconds. No credit card required.','M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                ['Fund Wallet','Add funds via card, bank transfer, or USSD. Instant credit.','M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                ['Order Services','Buy virtual numbers or boost your social media instantly.','M13 10V3L4 14h7v7l9-11h-7z'],
            ] as $i => [$title, $desc, $path])
            <div class="text-center reveal">
                <div class="w-16 h-16 rounded-2xl mx-auto mb-5 flex items-center justify-center" style="background:rgba(249,115,22,.12);border:1px solid rgba(249,115,22,.2)">
                    <svg class="w-7 h-7 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"/></svg>
                </div>
                <div class="text-brand font-black text-4xl mb-2">{{ $i + 1 }}</div>
                <h3 class="text-white font-bold text-lg mb-2">{{ $title }}</h3>
                <p class="text-slate-400 text-sm">{{ $desc }}</p>
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
                ['What is TopVerifi?','TopVerifi is a Nigerian digital services platform offering virtual phone numbers for SMS verification and social media growth services powered by JustAnotherPanel.'],
                ['How do I fund my wallet?','You can add funds using Paystack (card, bank transfer, USSD) or via manual bank transfer. Your balance is credited instantly on payment confirmation.'],
                ['Are the virtual numbers real?','Yes. We use GrizzlySMS to provide real temporary numbers from multiple countries that can receive SMS messages.'],
                ['How fast are the SMM orders?','Most orders start within minutes. Completion time depends on the service and quantity ordered.'],
                ['Is my data safe?','Absolutely. We never share your data and all payments are processed through secure, encrypted channels.'],
            ] as [$q, $a])
            <div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden reveal">
                <button onclick="this.parentElement.querySelector('.faq-body').classList.toggle('open');this.querySelector('svg').classList.toggle('rotate-180')"
                    class="w-full flex items-center justify-between p-5 text-left text-white font-semibold text-sm hover:bg-slate-700/50 transition-colors">
                    {{ $q }}
                    <svg class="w-4 h-4 text-slate-400 shrink-0 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="faq-body">
                    <p class="text-slate-400 text-sm px-5 pb-5">{{ $a }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-24 bg-slate-900">
    <div class="max-w-2xl mx-auto px-4 text-center reveal">
        <div class="bg-gradient-to-br from-orange-500/10 to-orange-900/20 border border-orange-500/20 rounded-3xl p-12">
            <h2 class="text-3xl sm:text-4xl font-black text-white mb-4">Ready to get started?</h2>
            <p class="text-slate-400 mb-8">Join thousands of Nigerians using TopVerifi for virtual numbers and social growth.</p>
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
const observer = new IntersectionObserver(entries => {
    entries.forEach(e => { if(e.isIntersecting) { e.target.classList.add('visible'); observer.unobserve(e.target); } });
}, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>
@endpush
