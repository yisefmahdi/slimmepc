@extends('landing.layouts.app')

@section('content')
    @include('landing.partials.header')

    <main class="min-h-screen bg-[#FAFCFF] text-navy">
        <div class="max-w-[680px] mx-auto px-4 sm:px-6 py-14">
            <div class="rounded-3xl border border-slate-100 bg-white p-6 sm:p-10 text-center shadow-floating">
                <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-green-100 text-green-600">
                    <i data-lucide="check-circle-2" class="h-9 w-9"></i>
                </div>

                <h1 class="text-2xl sm:text-3xl font-black text-slate-900">Betaling gelukt!</h1>
                <p class="mx-auto mt-2 max-w-md text-slate-600">
                    Bedankt! De betaling voor de werkzaamheden is gelukt.
                    @php $inv = $form->technicianInvoice; @endphp
                    @if($inv)
                        Factuurnummer: <span class="font-black text-[#155EEF]">{{ $inv->invoice_number }}</span>.
                    @endif
                </p>

                <div class="mt-6 rounded-2xl border border-slate-100 bg-slate-50/60 p-4 text-left text-sm">
                    <div class="flex items-center justify-between gap-3">
                        <span class="font-bold text-slate-800">Totaal betaald (incl. btw)</span>
                        <span class="font-black text-[#155EEF]">€{{ number_format($form->total, 2, ',', '.') }}</span>
                    </div>
                </div>
                <p class="mt-3 text-xs text-slate-500">De factuur is per e-mail verzonden.</p>

                <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="{{ route('technician.login') }}"
                       class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-2xl bg-[#155EEF] px-6 py-3 text-sm font-black text-white shadow-blue transition hover:bg-[#0C4CD9]">
                        Nieuwe betaling
                    </a>
                    <a href="{{ route('home') }}"
                       class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                        Naar home
                    </a>
                </div>
            </div>
        </div>
    </main>

    @include('landing.partials.footer')
    @include('landing.partials.floating')
    @include('landing.partials.ai-chat')
@endsection
