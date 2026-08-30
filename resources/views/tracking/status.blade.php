@extends('landing.layouts.app')

@section('content')
    @include('landing.partials.header')

    <main class="min-h-[calc(100vh-64px)] flex items-center justify-center p-6 bg-slate-50 dark:bg-slate-900">
        <div class="w-full max-w-2xl">
            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl shadow-slate-200/60 dark:shadow-none border border-slate-100 dark:border-slate-700 overflow-hidden">
                <div class="bg-gradient-to-br from-blue-600 to-blue-800 p-8 text-white text-center">
                    <div class="inline-flex h-20 w-20 items-center justify-center rounded-2xl bg-white p-3 mb-4 shadow-lg border border-white/10">
                        <img src="{{ asset($c['header']['logo_image'] ?? 'assets/img/landing/logo.png') }}" alt="Logo" class="h-full w-full object-contain">
                    </div>
                    <h1 class="text-2xl font-extrabold tracking-tight">Status van uw apparaat</h1>
                    <p class="mt-2 text-blue-100 text-sm">Ordernummer: <span class="font-bold text-white">{{ $receipt->receiptNumber() }}</span></p>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-10 p-5 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700">
                        <div class="p-2">
                            <p class="text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500 mb-1">Klant</p>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $receipt->customer_name }}</p>
                        </div>
                        <div class="p-2">
                            <p class="text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500 mb-1">Apparaat</p>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $receipt->device_type }}</p>
                        </div>
                        <div class="p-2">
                            <p class="text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500 mb-1">Serienummer</p>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $receipt->serial_number ?? 'Niet opgegeven' }}</p>
                        </div>
                        <div class="p-2">
                            <p class="text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500 mb-1">Ontvangen op</p>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $receipt->received_at ? $receipt->received_at->format('d-m-Y H:i') : '—' }}</p>
                        </div>
                    </div>

                    <div class="relative py-4">
                        {{-- Connection Line --}}
                        <div class="absolute top-[28px] left-0 w-full h-1 bg-slate-100 dark:bg-slate-700 z-0 rounded-full"></div>
                        
                        @php
                            $stages = [
                                'received' => [
                                    'label' => 'Ontvangen',
                                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
                                    'desc' => 'Uw apparaat is veilig bij ons binnengekomen.',
                                ],
                                'processing' => [
                                    'label' => 'In behandeling',
                                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l5.877 5.877M11.42 15.17 5.877 5.877M15.17 11.42l5.877 5.877m-5.877-5.877L11.42 15.17"/>',
                                    'desc' => 'Onze technici zijn bezig met de reparatie.',
                                ],
                                'completed' => [
                                    'label' => 'Voltooid',
                                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                                    'desc' => 'Uw apparaat is klaar voor ophaal!',
                                ],
                            ];
                            $currentStatus = $receipt->status;
                            $stageKeys = array_keys($stages);
                            $currentIndex = array_search($currentStatus, $stageKeys);
                            if ($currentIndex === false) {
                                $currentIndex = 0;
                            }
                        @endphp

                        <div class="relative z-10 flex justify-between">
                            @foreach ($stages as $key => $stage)
                                @php $isActive = $key === $currentStatus; @endphp
                                <div class="flex flex-col items-center text-center w-1/3 group">
                                    <div class="h-12 w-12 rounded-full flex items-center justify-center transition-all duration-500 {{ $isActive ? 'bg-blue-600 text-white ring-4 ring-blue-100 dark:ring-blue-900/30 scale-110 shadow-lg shadow-blue-600/30' : ($loop->index < $currentIndex ? 'bg-blue-600 text-white' : 'bg-white dark:bg-slate-800 text-slate-300 border-2 border-slate-100 dark:border-slate-700') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6">
                                            {!! $stage['icon'] !!}
                                        </svg>
                                    </div>
                                    <p class="mt-4 text-xs font-bold {{ $isActive ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400 dark:text-slate-500' }}">{{ $stage['label'] }}</p>
                                    @if ($isActive)
                                        <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400 max-w-[120px] leading-relaxed hidden sm:block">{{ $stage['desc'] }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    @include('landing.partials.footer')
@endsection