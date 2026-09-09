<section>
    <div class="flex items-start gap-4">
        <div class="flex w-11 h-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
            <i data-lucide="key-round" class="w-5 h-5"></i>
        </div>
        <div>
            <h2 class="text-[15px] font-extrabold text-[#0b1734]">
                Wachtwoord wijzigen
            </h2>
            <p class="mt-1 text-[12px] leading-5 text-slate-500">
                Gebruik een lang, willekeurig wachtwoord om je account veilig te houden.
            </p>
        </div>
    </div>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-[12px] font-bold text-slate-700">Huidig wachtwoord</label>
            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"
                class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-[13px] text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            @if($errors->updatePassword->get('current_password'))
                <p class="mt-1.5 text-[12px] font-semibold text-rose-600">{{ $errors->updatePassword->first('current_password') }}</p>
            @endif
        </div>

        <div>
            <label for="update_password_password" class="block text-[12px] font-bold text-slate-700">Nieuw wachtwoord</label>
            <input id="update_password_password" name="password" type="password" autocomplete="new-password"
                class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-[13px] text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            @if($errors->updatePassword->get('password'))
                <p class="mt-1.5 text-[12px] font-semibold text-rose-600">{{ $errors->updatePassword->first('password') }}</p>
            @endif
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-[12px] font-bold text-slate-700">Bevestig wachtwoord</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-[13px] text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            @if($errors->updatePassword->get('password_confirmation'))
                <p class="mt-1.5 text-[12px] font-semibold text-rose-600">{{ $errors->updatePassword->first('password_confirmation') }}</p>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-[12px] font-bold text-white shadow-sm transition hover:bg-blue-700">
                <i data-lucide="save" class="w-4 h-4"></i>
                Opslaan
            </button>

            @if (session('status') === 'password-updated')
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
