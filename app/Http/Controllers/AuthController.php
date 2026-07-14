<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function showLogin(): Response
    {
        return response()->view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email:rfc'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Veuillez saisir votre adresse email.',
            'email.email' => 'L\'adresse email saisie n\'est pas valide.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        $remember = $request->boolean('remember');

        // login : tentative de connexion avec email et mot de passe.
        if (! Auth::attempt($credentials, $remember)) {
            return back()
                ->withErrors(['email' => 'Identifiants invalides.'])
                ->onlyInput('email');
        }

        // session utilisateur : regeneration de session apres connexion.
        $request->session()->regenerate();
        UserPreference::query()->firstOrCreate(
            ['user_id' => $request->user()->id],
            $this->defaultPreferences()
        );

        $defaultRoute = $request->user()->isAdmin()
            ? route('admin.dashboard', [], false)
            : route('reader.index', [], false);

        return redirect()->intended($defaultRoute);
    }

    public function showRegister(): Response
    {
        return response()->view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Veuillez indiquer votre nom.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'email.required' => 'Une adresse email est requise.',
            'email.email' => 'L\'adresse email saisie n\'est pas valide.',
            'email.unique' => 'Cette adresse email est déjà associée à un compte.',
            'password.required' => 'Un mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        $user = User::query()->create([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'role' => 'reader',
            'email_verified_at' => now(),
        ]);

        $user->preferences()->create($this->defaultPreferences());
        Auth::login($user);

        // session utilisateur : regeneration de session apres inscription.
        $request->session()->regenerate();

        $defaultRoute = $user->isAdmin()
            ? route('admin.dashboard', [], false)
            : route('reader.index', [], false);

        return redirect()->to($defaultRoute);
    }

    public function logout(Request $request): RedirectResponse
    {
        // session utilisateur : fermeture propre de la session.
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to(route('login', [], false));
    }

    protected function defaultPreferences(): array
    {
        return [
            'theme' => 'dark',
            'font_size' => 'medium',
            'line_spacing' => 'comfortable',
            'page_flip_enabled' => true,
            'immersive_mode_default' => false,
        ];
    }
}
