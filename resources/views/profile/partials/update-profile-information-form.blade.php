<section>
    <div class="flex items-start gap-4">
        <div class="flex w-11 h-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
            <i data-lucide="user-round" class="w-5 h-5"></i>
        </div>
        <div>
            <h2 class="text-[15px] font-extrabold text-[#0b1734]">
                Profielgegevens
            </h2>
            <p class="mt-1 text-[12px] leading-5 text-slate-500">
                Werk je naam en e-mailadres bij. Bij een nieuw e-mailadres moet je deze opnieuw verifiëren.
            </p>
        </div>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block text-[12px] font-bold text-slate-700">Naam</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autocomplete="name"
                class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-[13px] text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            @if($errors->get('name'))
                <p class="mt-1.5 text-[12px] font-semibold text-rose-600">{{ $errors->first('name') }}</p>
            @endif
        </div>

        <div>
            <label for="email" class="block text-[12px] font-bold text-slate-700">E-mailadres</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-[13px] text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            @if($errors->get('email'))
                <p class="mt-1.5 text-[12px] font-semibold text-rose-600">{{ $errors->first('email') }}</p>
            @endif

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3">
                    <p class="text-[12px] text-amber-800">
                        Je e-mailadres is nog niet geverifieerd.
                        <button form="send-verification" class="font-bold underline hover:text-amber-900">
                            Klik hier om de verificatiemail opnieuw te versturen.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-1.5 text-[12px] font-semibold text-emerald-600">
                            Er is een nieuwe verificatielink naar je e-mailadres verstuurd.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-[12px] font-bold text-white shadow-sm transition hover:bg-blue-700">
                <i data-lucide="save" class="w-4 h-4"></i>
                Opslaan
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-[12px] font-semibold text-emerald-600"
                >Opgeslagen.</p>
            @endif
        </div>
    </form>
</section>
