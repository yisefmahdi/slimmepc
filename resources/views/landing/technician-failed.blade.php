@extends('landing.layouts.app')

@section('content')
    <main class="min-h-screen bg-[#FAFCFF] text-navy">
        <div class="max-w-[680px] mx-auto px-4 sm:px-6 py-14">
            <div class="rounded-3xl border border-slate-100 bg-white p-6 sm:p-10 text-center shadow-floating">
                <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-red-100 text-red-600">
                    <i data-lucide="x-circle" class="h-9 w-9"></i>
                </div>

                <h1 class="text-2xl sm:text-3xl font-black text-slate-900">Betaling niet gelukt</h1>
                <p class="mx-auto mt-2 max-w-md text-slate-600">
                    @if(session('error'))
                        {{ session('error') }}
                    @else
                        De betaling ({{ $form->user->klantnummer ?? '' }}) is niet voltooid. Er is niets afgeschreven.
                    @endif
                </p>

                <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="{{ route('technician.payment', ['klantnummer' => $form->user->klantnummer ?? '']) }}"
                       class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-2xl bg-[#155EEF] px-6 py-3 text-sm font-black text-white shadow-blue transition hover:bg-[#0C4CD9]">
                        Opnieuw proberen
                    </a>
                    <a href="{{ route('technician.login') }}"
                       class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                        Terug naar login
                    </a>
                </div>
            </div>
        </div>
    </main>
@endsection
@endsection
