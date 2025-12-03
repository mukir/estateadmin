<x-app-layout>
    <div class="py-10">
        <div class="max-w-xl mx-auto bg-white shadow-md border border-slate-100 rounded-2xl p-6 space-y-4">
            <div class="space-y-2">
                <h1 class="text-xl font-semibold text-slate-900">Enable MFA</h1>
                <p class="text-sm text-slate-600">Scan the QR code with Google Authenticator (or similar) and enter the 6-digit code.</p>
            </div>

            <div class="flex items-center gap-4">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($qr_url) }}" alt="MFA QR" class="rounded-lg border border-slate-200">
                <div class="space-y-2">
                    <p class="text-xs uppercase text-slate-500 font-semibold">Secret key</p>
                    <p class="font-mono text-sm">{{ $secret }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('mfa.enable') }}" class="space-y-3">
                @csrf
                <label class="block text-sm font-medium text-slate-700">
                    6-digit code
                    <input name="code" class="mt-2 w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200" required>
                </label>
                @error('code')
                    <p class="text-sm text-rose-600">{{ $message }}</p>
                @enderror
                <div class="flex justify-end gap-2">
                    <a href="{{ route('dashboard') }}" class="text-sm text-slate-600 hover:text-slate-800">Cancel</a>
                    <x-primary-button class="px-5">Enable</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
