@extends('landing.layouts.app')

@section('content')
    @include('landing.partials.header')

    <main class="min-h-[calc(100vh-64px)] flex items-center justify-center p-6 bg-slate-50 dark:bg-slate-900">
        <div class="w-full max-w-md">
            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl shadow-slate-200/60 dark:shadow-none border border-slate-100 dark:border-slate-700 overflow-hidden">
                <div class="bg-gradient-to-br from-blue-600 to-blue-800 p-8 text-white text-center">
                    <div class="inline-flex h-20 w-20 items-center justify-center rounded-2xl bg-white p-3 mb-4 shadow-lg border border-white/10">
                        <img src="{{ asset($c['header']['logo_image'] ?? 'assets/img/landing/logo.png') }}" alt="Logo" class="h-full w-full object-contain">
                    </div>
                    <h1 class="text-2xl font-extrabold tracking-tight">Volg uw apparaat</h1>
                    <p class="mt-2 text-blue-100 text-sm">Vul uw gegevens in om de status van uw reparatie te bekijken.</p>
                </div>

                <div class="p-8">
                    @if ($errors->any())
                        <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 text-xs font-semibold border border-red-100 dark:border-red-900/30 flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 mt-0.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4a2 2 0 00-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z" />
                            </svg>
                            <div>{{ $errors->first('msg') }}</div>
                        </div>
                    @endif

                    <form action="{{ route('tracking.track') }}" method="POST" class="space-y-5">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 ml-1">T-nummer</label>
                            <div class="relative group">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10M7 12h6m-6 5h10M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </span>
                                <input type="text" name="t_number" value="{{ $tNumber ?? old('t_number') }}" 
                                       placeholder="bijv. DR-00001" required
                                       class="w-full pl-10 pr-4 py-3.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition text-sm text-slate-900 dark:text-white font-medium">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5 ml-1">E-mailadres</label>
                            <div class="relative group">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </span>
                                <input type="email" name="email" value="{{ old('email') }}" 
                                       placeholder="voorbeeld@mail.com" required
                                       class="w-full pl-10 pr-4 py-3.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition text-sm text-slate-900 dark:text-white font-medium">
                            </div>
                        </div>

                        <button type="submit" class="w-full py-3.5 rounded-xl bg-blue-600 text-white font-bold text-sm shadow-lg shadow-blue-600/20 hover:bg-blue-700 transition-all active:scale-[0.98] flex items-center justify-center gap-2 group">
                            <span>Volg uw apparaat</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-4 w-4 transition-transform group-hover:translate-x-1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>
    @include('landing.partials.footer')
@endsection