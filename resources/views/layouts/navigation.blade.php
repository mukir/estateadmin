@php
    use App\Models\Business;
    use App\Support\BusinessContext;

    $currentBusiness = BusinessContext::get();

    if (! $currentBusiness) {
        $routeBusiness = request()->route('business') ?? request()->route('business_slug');

        if ($routeBusiness instanceof Business) {
            $currentBusiness = $routeBusiness;
        } elseif (is_string($routeBusiness)) {
            $currentBusiness = Business::where('slug', $routeBusiness)->first();
        } else {
            $currentBusiness = auth()->user()?->business;
        }
    }

    $crumbs = [];
    if ($currentBusiness) {
        $crumbs[] = [
            'label' => 'Dashboard',
            'href' => route('business.dashboard', ['business' => $currentBusiness->slug]),
            'active' => request()->routeIs('business.dashboard'),
        ];

        $routes = [
            ['pattern' => 'app.estates*', 'label' => 'Estates', 'href' => route('app.estates', ['business' => $currentBusiness->slug])],
            ['pattern' => 'app.houses*', 'label' => 'Houses', 'href' => route('app.houses', ['business' => $currentBusiness->slug])],
            ['pattern' => 'app.residents*', 'label' => 'Residents', 'href' => route('app.residents', ['business' => $currentBusiness->slug])],
            ['pattern' => 'app.service-charges*', 'label' => 'Service Charges', 'href' => route('app.service-charges', ['business' => $currentBusiness->slug])],
            ['pattern' => 'app.invoices*', 'label' => 'Invoices', 'href' => route('app.invoices', ['business' => $currentBusiness->slug])],
            ['pattern' => 'app.payments*', 'label' => 'Payments', 'href' => route('app.payments', ['business' => $currentBusiness->slug])],
            ['pattern' => 'app.reports*', 'label' => 'Reports', 'href' => route('app.reports', ['business' => $currentBusiness->slug])],
            ['pattern' => 'profile.*', 'label' => 'Profile', 'href' => route('profile.edit')],
        ];

        foreach ($routes as $route) {
            if (request()->routeIs($route['pattern'])) {
                $crumbs[] = [
                    'label' => $route['label'],
                    'href' => $route['href'],
                    'active' => true,
                ];
                break;
            }
        }
    } else {
        $crumbs[] = ['label' => 'Dashboard', 'href' => route('dashboard'), 'active' => request()->routeIs('dashboard')];
    }
@endphp

@auth
    <nav class="border-b border-slate-200 bg-white/80 backdrop-blur">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between gap-3">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                @if (!empty($branding['logo_url']))
                    <img src="{{ $branding['logo_url'] }}" alt="{{ $branding['platform_name'] ?? config('app.name', 'Dashboard') }}" class="h-14 w-auto max-w-[200px] object-contain">
                @else
                    <x-application-logo class="block h-12 w-12 fill-current text-indigo-600" />
                @endif
            </a>
            <div class="flex items-center gap-2">
                <span class="hidden sm:inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 border border-emerald-100">
                    {{ Auth::user()->email }}
                </span>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-2 border border-slate-200 text-sm leading-4 font-semibold rounded-full text-slate-800 bg-white shadow-sm hover:border-indigo-200 hover:text-indigo-700 focus:outline-none transition">
                            <div>{{ Auth::user()->name }}</div>
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 text-sm text-gray-600 border-b border-gray-100">
                            {{ Auth::user()->email }}
                        </div>
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </nav>
@else
    <nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('dashboard') }}">
                            @if (!empty($branding['logo_url']))
                                <img src="{{ $branding['logo_url'] }}" alt="{{ $branding['platform_name'] ?? config('app.name', 'Dashboard') }}" class="h-9 w-auto rounded-md object-contain bg-white">
                            @else
                                <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                            @endif
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
@endauth
