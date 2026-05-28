@extends('layouts.app')
@section('title', 'Privacy Policy & Refund Policy')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

    {{-- Page nav tabs --}}
    <div class="flex gap-2 mb-10">
        <button onclick="showTab('privacy')" id="tab-privacy"
            class="tab-btn px-5 py-2 rounded-lg text-sm font-semibold transition-colors bg-brand text-white">
            Privacy Policy
        </button>
        <button onclick="showTab('refund')" id="tab-refund"
            class="tab-btn px-5 py-2 rounded-lg text-sm font-semibold transition-colors bg-slate-800 text-slate-400 hover:text-white border border-slate-700">
            Refund Policy
        </button>
    </div>

    {{-- Privacy Policy --}}
    <div id="section-privacy">
        <h1 class="text-3xl font-bold text-white mb-2">Privacy Policy</h1>
        <p class="text-slate-400 text-sm mb-10">Last updated: {{ date('F j, Y') }}</p>

        <div class="prose prose-invert max-w-none space-y-8 text-slate-300 text-sm leading-relaxed">
            <section>
                <h2 class="text-lg font-semibold text-white mb-3">1. Information We Collect</h2>
                <p>We collect information you provide directly: your name, email address, and password when registering. We also collect transactional data such as purchases, wallet activity, and support interactions. We do not collect payment card data.</p>
            </section>
            <section>
                <h2 class="text-lg font-semibold text-white mb-3">2. How We Use Your Information</h2>
                <p>We use your information to: provide and improve our services; process transactions; send account notifications; respond to support requests; and detect and prevent fraud.</p>
            </section>
            <section>
                <h2 class="text-lg font-semibold text-white mb-3">3. Data Storage and Security</h2>
                <p>Your data is stored securely on our servers. Passwords are hashed and never stored in plain text. We implement industry-standard security measures to protect against unauthorized access, alteration, or disclosure.</p>
            </section>
            <section>
                <h2 class="text-lg font-semibold text-white mb-3">4. Data Sharing</h2>
                <p>We do not sell, trade, or rent your personal information to third parties. We may share data with service providers who assist in operating the Platform, subject to confidentiality agreements.</p>
            </section>
            <section>
                <h2 class="text-lg font-semibold text-white mb-3">5. Cookies</h2>
                <p>We use session cookies to keep you logged in. We do not use tracking cookies for advertising purposes.</p>
            </section>
            <section>
                <h2 class="text-lg font-semibold text-white mb-3">6. Your Rights</h2>
                <p>You have the right to access, correct, or delete your personal data. To exercise these rights, contact us through the support system. Account deletion will remove all your personal data within 30 days.</p>
            </section>
            <section>
                <h2 class="text-lg font-semibold text-white mb-3">7. Changes to This Policy</h2>
                <p>We may update this policy periodically. Significant changes will be notified via your account notifications.</p>
            </section>
            <section>
                <h2 class="text-lg font-semibold text-white mb-3">8. Contact</h2>
                <p>Privacy-related inquiries can be submitted via our <a href="{{ route('dashboard.support') }}" class="text-brand hover:underline">support system</a>.</p>
            </section>
        </div>
    </div>

    {{-- Refund Policy --}}
    <div id="section-refund" class="hidden">
        <h1 class="text-3xl font-bold text-white mb-2">Refund Policy</h1>
        <p class="text-slate-400 text-sm mb-10">Last updated: {{ date('F j, Y') }}</p>

        <div class="prose prose-invert max-w-none space-y-8 text-slate-300 text-sm leading-relaxed">

            <div class="bg-orange-500/10 border border-orange-500/20 rounded-xl p-5">
                <p class="text-orange-300 font-semibold text-sm">Important Notice</p>
                <p class="text-slate-300 text-sm mt-1">All wallet top-ups and purchases are subject to the terms below. Please read carefully before making any payment.</p>
            </div>

            <section>
                <h2 class="text-lg font-semibold text-white mb-3">1. Wallet Top-Ups</h2>
                <p>Wallet funds are non-refundable once credited to your account. If a payment was processed but your wallet was not credited, please contact support within <strong class="text-white">24 hours</strong> with proof of payment and we will investigate and resolve the issue promptly.</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-white mb-3">2. Virtual Number Orders</h2>
                <p>If you purchase a virtual number and <strong class="text-white">no SMS is received</strong> within the active window, the cost of that number will be automatically refunded to your wallet balance. Refunds are not issued if an SMS was successfully delivered to your number.</p>
                <ul class="list-disc list-inside space-y-1 mt-3 text-slate-400">
                    <li>Automatic wallet refund if no SMS arrives</li>
                    <li>No refund once an SMS code has been delivered</li>
                    <li>Numbers are one-time use and non-transferable</li>
                </ul>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-white mb-3">3. SMM Boosting Orders</h2>
                <p>SMM orders begin processing immediately after placement. Refunds for boosting orders are handled on a case-by-case basis:</p>
                <ul class="list-disc list-inside space-y-1 mt-3 text-slate-400">
                    <li><strong class="text-white">Not started:</strong> Full wallet refund issued</li>
                    <li><strong class="text-white">Partially completed:</strong> Refund for the undelivered portion only</li>
                    <li><strong class="text-white">Fully completed:</strong> No refund applicable</li>
                </ul>
                <p class="mt-3">To request a refund for an SMM order, open a support ticket with your order ID within <strong class="text-white">72 hours</strong> of placing the order.</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-white mb-3">4. Digital Account Purchases</h2>
                <p>Digital accounts are delivered instantly and all sales are <strong class="text-white">final once login credentials have been viewed</strong>. A refund may be considered only if the account is provably non-functional at the time of delivery — report within <strong class="text-white">1 hour</strong> of purchase with evidence via the support system.</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-white mb-3">5. Duplicate Payments</h2>
                <p>If you were charged more than once for the same transaction due to a technical error, contact support immediately with proof of duplicate charges. Confirmed duplicate payments will be refunded in full to your original payment method or wallet within <strong class="text-white">3–5 business days</strong>.</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-white mb-3">6. How to Request a Refund</h2>
                <p>All refund requests must be submitted through our support system. Include your order ID, the service in question, and a clear description of the issue. Our team responds within <strong class="text-white">24 hours</strong> on business days.</p>
                <div class="mt-4">
                    <a href="{{ route('dashboard.support') }}" class="inline-flex items-center gap-2 bg-brand hover:bg-brand-dark text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors">
                        Open a Support Ticket →
                    </a>
                </div>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-white mb-3">7. Non-Refundable Items</h2>
                <p>The following are strictly non-refundable under all circumstances:</p>
                <ul class="list-disc list-inside space-y-1 mt-3 text-slate-400">
                    <li>Wallet funds already spent on completed orders</li>
                    <li>Virtual numbers where an SMS was successfully received</li>
                    <li>SMM orders that have been fully delivered</li>
                    <li>Digital accounts where credentials have been accessed</li>
                    <li>Referral bonuses and promotional credits</li>
                </ul>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-white mb-3">8. Changes to This Policy</h2>
                <p>We reserve the right to update this refund policy at any time. Continued use of the platform after changes constitutes acceptance of the updated policy.</p>
            </section>
        </div>
    </div>

</div>

<script>
function showTab(tab) {
    document.getElementById('section-privacy').classList.toggle('hidden', tab !== 'privacy');
    document.getElementById('section-refund').classList.toggle('hidden', tab !== 'refund');
    document.getElementById('tab-privacy').className = tab === 'privacy'
        ? 'tab-btn px-5 py-2 rounded-lg text-sm font-semibold transition-colors bg-brand text-white'
        : 'tab-btn px-5 py-2 rounded-lg text-sm font-semibold transition-colors bg-slate-800 text-slate-400 hover:text-white border border-slate-700';
    document.getElementById('tab-refund').className = tab === 'refund'
        ? 'tab-btn px-5 py-2 rounded-lg text-sm font-semibold transition-colors bg-brand text-white'
        : 'tab-btn px-5 py-2 rounded-lg text-sm font-semibold transition-colors bg-slate-800 text-slate-400 hover:text-white border border-slate-700';
}
@if(request('tab') === 'refund')
document.addEventListener('DOMContentLoaded', () => showTab('refund'));
@endif
</script>
@endsection
