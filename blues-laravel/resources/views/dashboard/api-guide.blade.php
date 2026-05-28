@extends('layouts.dashboard')
@section('title', 'Integration Guide')
@section('page-title', 'Integration Guide')

@section('content')

{{-- Hero --}}
<div class="rounded-2xl p-6 mb-6 relative overflow-hidden" style="background:linear-gradient(135deg,#1e293b 0%,#0f172a 100%);border:1px solid rgba(249,115,22,0.2)">
    <div class="absolute top-0 right-0 w-56 h-56 rounded-full opacity-5" style="background:radial-gradient(circle,#f97316,transparent 70%);transform:translate(30%,-30%)"></div>
    <div class="relative flex flex-col sm:flex-row sm:items-start gap-4">
        <div class="flex-1">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-xl bg-orange-500/10 border border-orange-500/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <h2 class="text-white font-bold text-lg">Reseller Integration Guide</h2>
            </div>
            <p class="text-slate-400 text-sm leading-relaxed">Everything you need to plug TopVerifi services into your own platform — code examples in PHP, Python, and JavaScript, plus a step-by-step walkthrough.</p>
        </div>
        <a href="{{ route('dashboard.api-keys') }}"
           class="shrink-0 inline-flex items-center gap-1.5 border border-slate-600 hover:border-brand text-slate-300 hover:text-white text-xs font-semibold px-4 py-2 rounded-xl transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
            Manage Keys
        </a>
    </div>
</div>

{{-- Language tabs --}}
<div class="flex gap-1 mb-6 bg-slate-800 border border-slate-700 rounded-xl p-1 w-fit">
    @foreach(['php'=>'PHP','python'=>'Python','js'=>'JavaScript','curl'=>'cURL'] as $lang => $label)
    <button onclick="setLang('{{ $lang }}')" id="lang-btn-{{ $lang }}"
        class="lang-btn px-4 py-2 rounded-lg text-xs font-semibold transition-colors {{ $lang==='php' ? 'bg-brand text-white' : 'text-slate-400 hover:text-white' }}">
        {{ $label }}
    </button>
    @endforeach
</div>

{{-- ── Step 1 ──────────────────────────────────────────────────────────────── --}}
<div class="space-y-5">

<div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-700 flex items-center gap-3">
        <div class="w-7 h-7 rounded-full bg-brand/20 border border-brand/30 flex items-center justify-center shrink-0">
            <span class="text-brand font-bold text-xs">1</span>
        </div>
        <h3 class="text-white font-semibold text-sm">Get your API key</h3>
    </div>
    <div class="px-5 py-4 text-slate-400 text-sm leading-relaxed">
        <p>Go to <a href="{{ route('dashboard.api-keys') }}" class="text-brand hover:underline">API Access</a>, create a key, and copy it. Every request must include your key in the <code class="text-orange-300 bg-slate-900 px-1.5 py-0.5 rounded text-xs font-mono">X-API-Key</code> header.</p>
        <div class="mt-4 bg-slate-900 rounded-xl p-4 border border-slate-700">
            @foreach(['php'=>'$apiKey = "tv_your_key_here";', 'python'=>'api_key = "tv_your_key_here"', 'js'=>'const apiKey = "tv_your_key_here";', 'curl'=>'# Use -H "X-API-Key: tv_your_key_here" on every request'] as $lang => $code)
            <pre class="lang-block {{ $lang!=='php' ? 'hidden' : '' }} text-xs text-slate-300 font-mono overflow-x-auto" data-lang="{{ $lang }}">{{ $code }}</pre>
            @endforeach
        </div>
    </div>
</div>

{{-- ── Step 2: Check balance ────────────────────────────────────────────── --}}
<div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-700 flex items-center gap-3">
        <div class="w-7 h-7 rounded-full bg-brand/20 border border-brand/30 flex items-center justify-center shrink-0">
            <span class="text-brand font-bold text-xs">2</span>
        </div>
        <h3 class="text-white font-semibold text-sm">Check wallet balance</h3>
        <code class="ml-auto text-xs text-green-400 font-mono bg-green-500/10 px-2 py-0.5 rounded">GET /api/v1/balance</code>
    </div>
    <div class="px-5 py-4">
        <div class="bg-slate-900 rounded-xl p-4 border border-slate-700 mb-3">
            <pre class="lang-block text-xs text-slate-300 font-mono overflow-x-auto" data-lang="php"><?php
$ch = curl_init('{{ url('/api/v1/balance') }}');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => ['X-API-Key: ' . $apiKey, 'Accept: application/json'],
]);
$data = json_decode(curl_exec($ch), true);
// $data = ['balance' => 12450.00, 'currency' => 'NGN']
echo "Balance: " . $data['currency'] . " " . $data['balance'];</pre>
            <pre class="lang-block hidden text-xs text-slate-300 font-mono overflow-x-auto" data-lang="python">import requests

BASE = "{{ url('/api/v1') }}"
HEADERS = {"X-API-Key": api_key, "Accept": "application/json"}

r = requests.get(f"{BASE}/balance", headers=HEADERS)
data = r.json()
# data = {"balance": 12450.0, "currency": "NGN"}
print(f"Balance: {data['currency']} {data['balance']}")</pre>
            <pre class="lang-block hidden text-xs text-slate-300 font-mono overflow-x-auto" data-lang="js">const BASE = "{{ url('/api/v1') }}";
const HEADERS = { "X-API-Key": apiKey, "Accept": "application/json" };

const res  = await fetch(`${BASE}/balance`, { headers: HEADERS });
const data = await res.json();
// data = { balance: 12450.0, currency: "NGN" }
console.log(`Balance: ${data.currency} ${data.balance}`);</pre>
            <pre class="lang-block hidden text-xs text-slate-300 font-mono overflow-x-auto" data-lang="curl">curl -X GET "{{ url('/api/v1/balance') }}" \
  -H "X-API-Key: tv_your_key_here" \
  -H "Accept: application/json"

# Response: {"balance":12450.00,"currency":"NGN"}</pre>
        </div>
    </div>
</div>

{{-- ── Step 3: Order virtual number ────────────────────────────────────── --}}
<div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-700 flex items-center gap-3">
        <div class="w-7 h-7 rounded-full bg-brand/20 border border-brand/30 flex items-center justify-center shrink-0">
            <span class="text-brand font-bold text-xs">3</span>
        </div>
        <h3 class="text-white font-semibold text-sm">Order a virtual number</h3>
        <code class="ml-auto text-xs text-blue-400 font-mono bg-blue-500/10 px-2 py-0.5 rounded">POST /api/v1/numbers/order</code>
    </div>
    <div class="px-5 py-4 text-sm text-slate-400">
        <p class="mb-3">First fetch the service list, then place the order. The cost is deducted from your wallet automatically.</p>
        <div class="bg-slate-900 rounded-xl p-4 border border-slate-700">
            <pre class="lang-block text-xs text-slate-300 font-mono overflow-x-auto" data-lang="php">// Step A: fetch services for a country
$ch = curl_init('{{ url('/api/v1/numbers/services') }}?provider=grizzlysms&country=russia');
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_HTTPHEADER=>['X-API-Key: '.$apiKey,'Accept: application/json']]);
$services = json_decode(curl_exec($ch), true)['services'];

// Step B: pick the WhatsApp service
$wa = collect($services)->firstWhere('name', 'whatsapp');

// Step C: place the order
$ch = curl_init('{{ url('/api/v1/numbers/order') }}');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => ['X-API-Key: '.$apiKey,'Content-Type: application/json','Accept: application/json'],
    CURLOPT_POSTFIELDS     => json_encode([
        'provider'     => 'grizzlysms',
        'service_id'   => $wa['serviceId'],
        'country'      => 'russia',
        'price'        => $wa['cost_ngn'],
        'service_name' => $wa['name'],
    ]),
]);
$order = json_decode(curl_exec($ch), true);
// $order = ['order_id'=>7, 'phone_number'=>'+79161234567', 'status'=>'active', 'cost'=>450.00]
echo "Your number: " . $order['phone_number'];</pre>
            <pre class="lang-block hidden text-xs text-slate-300 font-mono overflow-x-auto" data-lang="python">import requests, json

BASE    = "{{ url('/api/v1') }}"
HEADERS = {"X-API-Key": api_key, "Accept": "application/json"}

# Step A: fetch services
svcs = requests.get(f"{BASE}/numbers/services",
    params={"provider": "grizzlysms", "country": "russia"},
    headers=HEADERS).json()["services"]

# Step B: pick WhatsApp
wa = next(s for s in svcs if "whatsapp" in s["name"].lower())

# Step C: order
order = requests.post(f"{BASE}/numbers/order",
    headers={**HEADERS, "Content-Type": "application/json"},
    json={
        "provider":     "grizzlysms",
        "service_id":   str(wa["serviceId"]),
        "country":      "russia",
        "price":        wa["cost_ngn"],
        "service_name": wa["name"],
    }).json()

print(f"Your number: {order['phone_number']}")</pre>
            <pre class="lang-block hidden text-xs text-slate-300 font-mono overflow-x-auto" data-lang="js">// Step A: services
const { services } = await fetch(`${BASE}/numbers/services?provider=grizzlysms&country=russia`,
    { headers: HEADERS }).then(r => r.json());

// Step B: pick WhatsApp
const wa = services.find(s => s.name.toLowerCase().includes('whatsapp'));

// Step C: order
const order = await fetch(`${BASE}/numbers/order`, {
    method: 'POST',
    headers: { ...HEADERS, 'Content-Type': 'application/json' },
    body: JSON.stringify({
        provider: 'grizzlysms', service_id: String(wa.serviceId),
        country: 'russia', price: wa.cost_ngn, service_name: wa.name,
    })
}).then(r => r.json());

console.log('Your number:', order.phone_number);</pre>
            <pre class="lang-block hidden text-xs text-slate-300 font-mono overflow-x-auto" data-lang="curl">curl -X POST "{{ url('/api/v1/numbers/order') }}" \
  -H "X-API-Key: tv_your_key_here" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"provider":"grizzlysms","service_id":"24","country":"russia","price":450,"service_name":"WhatsApp"}'

# Response: {"order_id":7,"phone_number":"+79161234567","status":"active","cost":450.0}</pre>
        </div>
    </div>
</div>

{{-- ── Step 4: Poll for SMS ────────────────────────────────────────────── --}}
<div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-700 flex items-center gap-3">
        <div class="w-7 h-7 rounded-full bg-brand/20 border border-brand/30 flex items-center justify-center shrink-0">
            <span class="text-brand font-bold text-xs">4</span>
        </div>
        <h3 class="text-white font-semibold text-sm">Poll for SMS code</h3>
        <code class="ml-auto text-xs text-green-400 font-mono bg-green-500/10 px-2 py-0.5 rounded">GET /api/v1/numbers/{id}/sms</code>
    </div>
    <div class="px-5 py-4 text-sm text-slate-400">
        <p class="mb-3">Poll every few seconds until <code class="text-orange-300 bg-slate-900 px-1 rounded text-xs">sms_code</code> is not null. Numbers typically expire after a few minutes — cancel unused ones for a refund.</p>
        <div class="bg-slate-900 rounded-xl p-4 border border-slate-700">
            <pre class="lang-block text-xs text-slate-300 font-mono overflow-x-auto" data-lang="php">$orderId = $order['order_id'];
$smsCode = null;

for ($i = 0; $i < 30; $i++) {          // poll for up to 5 minutes
    sleep(10);
    $ch = curl_init('{{ url('/api/v1/numbers') }}/' . $orderId . '/sms');
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_HTTPHEADER=>['X-API-Key: '.$apiKey,'Accept: application/json']]);
    $result = json_decode(curl_exec($ch), true);

    if (!empty($result['sms_code'])) {
        $smsCode = $result['sms_code'];
        echo "Code received: " . $smsCode;
        break;
    }
}

if (!$smsCode) {
    // Cancel for automatic refund
    $ch = curl_init('{{ url('/api/v1/numbers') }}/' . $orderId);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_CUSTOMREQUEST=>'DELETE', CURLOPT_HTTPHEADER=>['X-API-Key: '.$apiKey,'Accept: application/json']]);
    curl_exec($ch);
    echo "No SMS received — number cancelled and refunded.";
}</pre>
            <pre class="lang-block hidden text-xs text-slate-300 font-mono overflow-x-auto" data-lang="python">import time

order_id = order["order_id"]
sms_code = None

for _ in range(30):
    time.sleep(10)
    r = requests.get(f"{BASE}/numbers/{order_id}/sms", headers=HEADERS).json()
    if r.get("sms_code"):
        sms_code = r["sms_code"]
        print(f"Code: {sms_code}")
        break

if not sms_code:
    # cancel for refund
    requests.delete(f"{BASE}/numbers/{order_id}", headers=HEADERS)
    print("No SMS — number cancelled and refunded.")</pre>
            <pre class="lang-block hidden text-xs text-slate-300 font-mono overflow-x-auto" data-lang="js">const orderId = order.order_id;
let smsCode   = null;

for (let i = 0; i < 30; i++) {
    await new Promise(r => setTimeout(r, 10_000));
    const result = await fetch(`${BASE}/numbers/${orderId}/sms`, { headers: HEADERS }).then(r => r.json());
    if (result.sms_code) { smsCode = result.sms_code; break; }
}

if (!smsCode) {
    await fetch(`${BASE}/numbers/${orderId}`, { method: 'DELETE', headers: HEADERS });
    console.log('No SMS — number cancelled and refunded.');
} else {
    console.log('Code:', smsCode);
}</pre>
            <pre class="lang-block hidden text-xs text-slate-300 font-mono overflow-x-auto" data-lang="curl"># Poll until sms_code is non-null
curl -X GET "{{ url('/api/v1/numbers/7/sms') }}" \
  -H "X-API-Key: tv_your_key_here"

# {"order_id":7,"phone_number":"+79161234567","sms_code":"123456","status":"active"}

# Cancel if no SMS
curl -X DELETE "{{ url('/api/v1/numbers/7') }}" \
  -H "X-API-Key: tv_your_key_here"</pre>
        </div>
    </div>
</div>

{{-- ── Step 5: SMM Boosting ─────────────────────────────────────────────── --}}
<div class="bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-700 flex items-center gap-3">
        <div class="w-7 h-7 rounded-full bg-brand/20 border border-brand/30 flex items-center justify-center shrink-0">
            <span class="text-brand font-bold text-xs">5</span>
        </div>
        <h3 class="text-white font-semibold text-sm">Place an SMM boosting order</h3>
        <code class="ml-auto text-xs text-blue-400 font-mono bg-blue-500/10 px-2 py-0.5 rounded">POST /api/v1/smm/order</code>
    </div>
    <div class="px-5 py-4 text-sm text-slate-400">
        <p class="mb-3">Fetch the service list, calculate the total cost, then place the order. The charge is <code class="text-orange-300 bg-slate-900 px-1 rounded text-xs">(quantity / 1000) × rate_per_1k</code>.</p>
        <div class="bg-slate-900 rounded-xl p-4 border border-slate-700">
            <pre class="lang-block text-xs text-slate-300 font-mono overflow-x-auto" data-lang="php">// Fetch services
$ch = curl_init('{{ url('/api/v1/smm/services') }}');
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_HTTPHEADER=>['X-API-Key: '.$apiKey,'Accept: application/json']]);
$services = json_decode(curl_exec($ch), true)['services'];

// Pick a service
$svc = $services[0]; // e.g. Instagram Followers

// Place order
$ch = curl_init('{{ url('/api/v1/smm/order') }}');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => ['X-API-Key: '.$apiKey,'Content-Type: application/json','Accept: application/json'],
    CURLOPT_POSTFIELDS     => json_encode([
        'service_id' => $svc['service_id'],
        'link'       => 'https://instagram.com/yourpage',
        'quantity'   => 1000,
    ]),
]);
$result = json_decode(curl_exec($ch), true);
echo "Order #" . $result['order_id'] . " placed. Charge: ₦" . $result['charge'];</pre>
            <pre class="lang-block hidden text-xs text-slate-300 font-mono overflow-x-auto" data-lang="python">services = requests.get(f"{BASE}/smm/services", headers=HEADERS).json()["services"]
svc = services[0]

order = requests.post(f"{BASE}/smm/order",
    headers={**HEADERS, "Content-Type": "application/json"},
    json={"service_id": svc["service_id"], "link": "https://instagram.com/yourpage", "quantity": 1000}
).json()

print(f"Order #{order['order_id']} placed. Charge: ₦{order['charge']}")</pre>
            <pre class="lang-block hidden text-xs text-slate-300 font-mono overflow-x-auto" data-lang="js">const { services } = await fetch(`${BASE}/smm/services`, { headers: HEADERS }).then(r => r.json());
const svc = services[0];

const order = await fetch(`${BASE}/smm/order`, {
    method: 'POST',
    headers: { ...HEADERS, 'Content-Type': 'application/json' },
    body: JSON.stringify({ service_id: svc.service_id, link: 'https://instagram.com/yourpage', quantity: 1000 })
}).then(r => r.json());

console.log(`Order #${order.order_id} placed. Charge: ₦${order.charge}`);</pre>
            <pre class="lang-block hidden text-xs text-slate-300 font-mono overflow-x-auto" data-lang="curl">curl -X POST "{{ url('/api/v1/smm/order') }}" \
  -H "X-API-Key: tv_your_key_here" \
  -H "Content-Type: application/json" \
  -d '{"service_id":1,"link":"https://instagram.com/yourpage","quantity":1000}'

# {"order_id":42,"status":"pending","charge":250.00,"currency":"NGN"}</pre>
        </div>
    </div>
</div>

{{-- Security notes --}}
<div class="bg-orange-500/5 border border-orange-500/20 rounded-2xl p-5">
    <h3 class="text-orange-300 font-semibold text-sm mb-3 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        Security Best Practices
    </h3>
    <ul class="space-y-2 text-slate-400 text-sm">
        <li class="flex items-start gap-2"><span class="text-orange-400 mt-0.5">•</span><span><strong class="text-white">Never expose your API key in client-side code</strong> (browser JavaScript, mobile apps). Always make API calls from your server.</span></li>
        <li class="flex items-start gap-2"><span class="text-orange-400 mt-0.5">•</span><span><strong class="text-white">Store keys as environment variables</strong> — never hardcode them in source files.</span></li>
        <li class="flex items-start gap-2"><span class="text-orange-400 mt-0.5">•</span><span><strong class="text-white">Rotate keys regularly</strong> — create a new key, update your integration, then revoke the old one.</span></li>
        <li class="flex items-start gap-2"><span class="text-orange-400 mt-0.5">•</span><span><strong class="text-white">Monitor the request log</strong> on your API Access page — unexpected calls may indicate a compromised key.</span></li>
        <li class="flex items-start gap-2"><span class="text-orange-400 mt-0.5">•</span><span><strong class="text-white">Rate limit:</strong> 60 requests per minute per key. Implement exponential backoff on 429 responses.</span></li>
        <li class="flex items-start gap-2"><span class="text-orange-400 mt-0.5">•</span><span><strong class="text-white">Always use HTTPS</strong> when calling the API in production.</span></li>
    </ul>
</div>

</div>{{-- end space-y-5 --}}

@endsection

@push('scripts')
<script>
let currentLang = 'php';
function setLang(lang) {
    currentLang = lang;
    document.querySelectorAll('.lang-block').forEach(el => {
        el.classList.toggle('hidden', el.dataset.lang !== lang);
    });
    document.querySelectorAll('.lang-btn').forEach(btn => {
        const isActive = btn.id === 'lang-btn-' + lang;
        btn.className = 'lang-btn px-4 py-2 rounded-lg text-xs font-semibold transition-colors ' +
            (isActive ? 'bg-brand text-white' : 'text-slate-400 hover:text-white');
    });
}
</script>
@endpush
