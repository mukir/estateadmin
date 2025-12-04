<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl space-y-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-medium text-gray-900">Multi-factor authentication</h2>
                            <p class="text-sm text-gray-600">Add or remove app-based MFA for your account. Default is off.</p>
                        </div>
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ auth()->user()->mfa_enabled ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-gray-100 text-gray-700 border border-gray-200' }}">
                            {{ auth()->user()->mfa_enabled ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        @if (auth()->user()->mfa_enabled)
                            <form method="POST" action="{{ route('mfa.disable') }}">
                                @csrf
                                <x-primary-button type="submit" class="bg-rose-600 hover:bg-rose-700">Disable MFA</x-primary-button>
                            </form>
                        @else
                            <a href="{{ route('mfa.setup') }}" class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">
                                Enable MFA
                            </a>
                        @endif
                        <p class="text-sm text-gray-600">Use Google Authenticator or a similar app to scan the QR when enabling.</p>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
