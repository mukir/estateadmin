<x-app-layout>
    <div class="py-10">
        <div class="max-w-md mx-auto bg-white shadow-md border border-slate-100 rounded-2xl p-6 space-y-4">
            <div class="space-y-2">
                <h1 class="text-xl font-semibold text-slate-900">MFA verification</h1>
                <p class="text-sm text-slate-600">Enter the 6-digit code from your authenticator app to continue.</p>
            </div>
            <form method="POST" action="{{ route('mfa.verify') }}" class="space-y-3">
                @csrf
                <label class="block text-sm font-medium text-slate-700">
                    6-digit code
                    <input name="code" class="mt-2 w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200" required>
                </label>
                @error('code')
                    <p class="text-sm text-rose-600">{{ $message }}</p>
                @enderror
                <div class="flex justify-end gap-2">
                    <x-primary-button class="px-5">Verify</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
