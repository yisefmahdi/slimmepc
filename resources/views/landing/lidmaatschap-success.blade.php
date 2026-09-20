@extends('landing.layouts.app')

@section('content')
    @include('landing.partials.header')

    @php
        $contactRows = $c['footer']['contact'] ?? [];
    @endphp

    <main class="min-h-screen bg-[#FAFCFF] text-navy">
        <div class="max-w-[1100px] mx-auto px-4 sm:px-6 py-14">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

                {{-- LEFT: success + membership --}}
                <div class="rounded-3xl border border-slate-100 bg-white p-6 sm:p-10 text-center shadow-floating">
                    <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-green-100 text-green-600">
                        <i data-lucide="check-circle-2" class="h-9 w-9"></i>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900">Welkom als lid!</h1>
                    <p class="mx-auto mt-2 max-w-md text-slate-600">
                        Bedankt {{ $lidmaatschap->name }}! Je betaling is gelukt en je lidmaatschap is actief.
                        Je klantnummer is <span class="font-black text-[#155EEF]">{{ $lidmaatschap->klantnummer }}</span>.
                    </p>

                    <div class="mt-6 rounded-2xl border border-slate-100 bg-slate-50/60 p-4 text-left text-sm">
                        <p class="font-bold text-slate-800">Jouw lidmaatschap</p>
                        <ul class="mt-2 divide-y divide-slate-100">
                            <li class="flex items-center justify-between gap-3 py-2">
                                <span class="text-slate-600">Looptijd</span>
                                <span class="font-semibold text-slate-800">{{ $lidmaatschap->start_date->format('d-m-Y') }} – {{ $lidmaatschap->end_date->format('d-m-Y') }}</span>
                            </li>
                            <li class="flex items-center justify-between gap-3 py-2">
                                <span class="text-slate-600">Type</span>
                                <span class="font-semibold text-slate-800">{{ $lidmaatschap->customer_type === 'business' ? 'Zakelijk' : 'Particulier' }}</span>
                            </li>
                        </ul>
                        <div class="mt-2 flex items-center justify-between border-t border-slate-100 pt-3">
                            <span class="font-bold text-slate-800">Totaal betaald (incl. btw)</span>
                            <span class="font-black text-[#155EEF]">€{{ number_format($lidmaatschap->total, 2, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-3">
                        <a href="{{ route('home') }}"
                           class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-2xl bg-[#155EEF] px-6 py-3 text-sm font-black text-white shadow-blue transition hover:bg-[#0C4CD9]">
                            Terug naar home
                        </a>
                        <a href="{{ route('contact') }}"
                           class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                            Contact
                        </a>
                    </div>
                </div>

                {{-- RIGHT: next steps + company --}}
                <div class="space-y-6">
                    <div class="rounded-3xl border border-slate-100 bg-slate-50 p-6 sm:p-8 text-left text-sm text-slate-600">
                        <p class="font-bold text-slate-800 text-base">Wat gebeurt er nu?</p>
                        <ul class="mt-3 list-disc space-y-2 pl-5">
                            <li>Je ontvangt de factuur per e-mail op {{ $lidmaatschap->customer_email }}.</li>
                            <li>Je lidmaatschap is geldig tot {{ $lidmaatschap->end_date->format('d-m-Y') }}.</li>
                            <li>Vragen? Neem gerust contact met ons op — we helpen je graag.</li>
                        </ul>
                    </div>

                    <div class="rounded-3xl border border-blue-100 bg-white p-6 sm:p-8 text-left text-sm shadow-card">
                        <p class="font-bold text-slate-800 text-base">Slimme-PC · Apeldoorn</p>
                        @if(!empty($contactRows))
                            <ul class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3">
                                @foreach($contactRows as $row)
                                    <li class="flex items-start gap-2.5 text-slate-600">
                                        <i data-lucide="{{ $row['icon'] ?? 'circle' }}" class="mt-0.5 h-4 w-4 shrink-0 text-[#155EEF]"></i>
                                        <span><strong class="text-slate-700">{{ $row['label'] ?? '' }}</strong><br>{!! nl2br(e($row['value'] ?? '')) !!}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="mt-3 text-slate-600">Apeldoorn · info@slimme-pc.nl</p>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </main>

    @include('landing.partials.footer')
    @include('landing.partials.floating')
    @include('landing.partials.ai-chat')
@endsection
