<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class MfaController extends Controller
{
    public function showSetup(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        if ($user->mfa_enabled) {
            return redirect()->route('dashboard');
        }

        $google2fa = new Google2FA();
        $secret = session('mfa_secret') ?: $google2fa->generateSecretKey();
        session(['mfa_secret' => $secret]);

        $qrUrl = $google2fa->getQRCodeUrl(
            config('app.name', 'App'),
            $user->email,
            $secret
        );

        return view('auth.mfa-setup', [
            'secret' => $secret,
            'qr_url' => $qrUrl,
        ]);
    }

    public function enable(Request $request): RedirectResponse
    {
        $request->validate(['code' => ['required', 'string']]);

        $secret = session('mfa_secret');
        if (! $secret) {
            return redirect()->route('mfa.setup')->withErrors(['code' => 'Secret missing, restart setup.']);
        }

        $google2fa = new Google2FA();
        if (! $google2fa->verifyKey($secret, $request->code)) {
            return back()->withErrors(['code' => 'Invalid code. Try again.']);
        }

        $user = $request->user();
        $user->mfa_secret = Crypt::encryptString($secret);
        $user->mfa_enabled = true;
        $user->save();

        session()->forget('mfa_secret');
        session(['mfa_passed' => true]);

        return redirect()->route('dashboard')->with('status', 'MFA enabled.');
    }

    public function challenge(): View
    {
        return view('auth.mfa-challenge');
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate(['code' => ['required', 'string']]);
        $user = $request->user();

        if (! $user->mfa_enabled || ! $user->mfa_secret) {
            return redirect()->route('dashboard');
        }

        $google2fa = new Google2FA();
        $secret = Crypt::decryptString($user->mfa_secret);

        if (! $google2fa->verifyKey($secret, $request->code)) {
            return back()->withErrors(['code' => 'Invalid code.']);
        }

        session(['mfa_passed' => true]);

        return redirect()->intended('/dashboard');
    }

    public function disable(Request $request): RedirectResponse
    {
        $user = $request->user();
        $user->mfa_enabled = false;
        $user->mfa_secret = null;
        $user->save();

        session()->forget('mfa_passed');

        return redirect()->route('dashboard')->with('status', 'MFA disabled.');
    }
}
