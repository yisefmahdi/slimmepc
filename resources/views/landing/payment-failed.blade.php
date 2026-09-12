@extends('landing.layouts.app')

@section('content')
    @include('landing.partials.header')

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { brand: '#155EEF', brandDark: '#0A45C2', navy: '#07172F', softBlue: '#F4F8FF', borderBlue: '#DCE6F5' },
                    boxShadow: { card: '0 8px 30px rgba(11,37,80,.06)', floating: '0 20px 55px rgba(15,54,115,.10)', blue: '0 12px 30px rgba(21,94,239,.20)' }
                }
            }
        }
    </script>
    <style>
        body { font-family: Inter, ui-sans-serif, system-ui, sans-serif; }
    </style>

    <main class="min-h-screen bg-[#FAFCFF] flex items-center justify-center px-4 py-16">
        <div class="max-w-md w-full bg-white border border-slate-200 rounded-2xl shadow-card p-8 text-center">
            <div class="mx-auto w-16 h-16 rounded-full bg-red-50 text-red-600 flex items-center justify-center">
                <i data-lucide="x" class="w-8 h-8"></i>
            </div>
            <h1 class="mt-4 text-2xl font-bold">Betaling niet gelukt</h1>
            @if(!empty($orderModel))
                <p class="mt-2 text-sm text-slate-500">Bestelnummer: <strong>{{ $orderModel->order_number }}</strong></p>
            @endif
            <p class="mt-2 text-sm text-slate-500">De betaling is geannuleerd of mislukt. Je winkelwagen is bewaard — probeer het opnieuw.</p>
            <div class="mt-6 flex gap-3 justify-center">
                <a href="{{ route('checkout.index') }}" class="inline-flex h-11 items-center gap-2 rounded-xl bg-[#155EEF] px-6 text-sm font-semibold text-white hover:bg-[#0C4CD9]">Opnieuw proberen</a>
                <a href="{{ route('cart.index') }}" class="inline-flex h-11 items-center gap-2 rounded-xl border px-6 text-sm font-semibold hover:bg-slate-50">Winkelwagen</a>
            </div>
        </div>
    </main>
    @include('landing.partials.footer')
    @include('landing.partials.floating')
    @include('landing.partials.ai-chat')
@endsection
