<section>
    <div class="flex items-start gap-4">
        <div class="flex w-11 h-11 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-600">
            <i data-lucide="triangle-alert" class="w-5 h-5"></i>
        </div>
        <div>
            <h2 class="text-[15px] font-extrabold text-[#0b1734]">
                Account verwijderen
            </h2>
            <p class="mt-1 text-[12px] leading-5 text-slate-500">
                Zodra je account is verwijderd, worden alle gegevens permanent gewist. Download vooraf alles wat je wilt bewaren.
            </p>
        </div>
    </div>

    <div class="mt-6">
        <x-danger-button
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        >Account verwijderen</x-danger-button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-[16px] font-extrabold text-[#0b1734]">
                Weet je zeker dat je je account wilt verwijderen?
            </h2>

            <p class="mt-2 text-[13px] leading-6 text-slate-500">
                Zodra je account is verwijderd, worden alle gegevens permanent gewist. Vul je wachtwoord in om te bevestigen.
            </p>

            <div class="mt-6">
                <label for="password" class="sr-only">Wachtwoord</label>

                <input
                    id="password"
                    name="password"
                    type="password"
                    placeholder="Wachtwoord"
                    class="mt-1 block w-3/4 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-[13px] text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                >

                @if($errors->userDeletion->get('password'))
                    <p class="mt-1.5 text-[12px] font-semibold text-rose-600">{{ $errors->userDeletion->first('password') }}</p>
                @endif
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Annuleren
                </x-secondary-button>

                <x-danger-button>
                    Account verwijderen
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
