<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\NewCustomerMail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View|RedirectResponse
    {
        // Eén pagina voor iedereen: /register?nieuwe-klant mag ook ingelogd geopend worden (monteur/admin).
        if ($request->user() && ! $request->has('nieuwe-klant')) {
            return redirect(route('home', absolute: false));
        }

        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Eén pagina: normale registratie is alleen voor gasten; nieuwe-klant mag ook ingelogd (monteur/admin).
        if ($request->user() && ! $request->has('nieuwe-klant')) {
            return redirect(route('home', absolute: false));
        }

        // Monteur-flow: /register?nieuwe-klant — extra velden verplicht + admin-mail met klantnummer.
        $isNieuweKlant = $request->has('nieuwe-klant');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => [$isNieuweKlant ? 'required' : 'nullable', 'string', 'max:30'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'house_number' => ['nullable', 'string', 'max:20'],
            'street' => [$isNieuweKlant ? 'required' : 'nullable', 'string', 'max:255'],
            'postcode' => [$isNieuweKlant ? 'required' : 'nullable', 'string', 'max:20'],
            'city' => [$isNieuweKlant ? 'required' : 'nullable', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'house_number' => $request->house_number,
            'street' => $request->street,
            'postcode' => $request->postcode,
            'city' => $request->city,
            'password' => Hash::make($request->password),
        ]);

        // Monteur-flow (zoals oud project): NIET inloggen (sessie van de monteur
        // mag niet wisselen), alleen admin-mail + terug naar monteur-login.
        if ($isNieuweKlant) {
            $notify = (string) (config('contact-inbox.notify_email') ?: '');
            if ($notify !== '') {
                dispatch(function () use ($user, $notify) {
                    Mail::to($notify)->send(new NewCustomerMail($user->fresh()));
                })->afterResponse();
            }

            return redirect(route('technician.login', absolute: false))
                ->with('status', 'Account aangemaakt! Klantnummer: ' . $user->klantnummer);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('home', absolute: false));
    }
}

