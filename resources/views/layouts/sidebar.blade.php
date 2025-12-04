@php
    use App\Models\Business;
    use App\Support\BusinessContext;

    $business = BusinessContext::get();

    if (! $business) {
        $routeBusiness = request()->route('business') ?? request()->route('business_slug');

        if ($routeBusiness instanceof Business) {
            $business = $routeBusiness;
        } elseif (is_string($routeBusiness)) {
            $business = Business::where('slug', $routeBusiness)->first();
        } else {
            $business = auth()->user()?->business;
        }
    }

    $businessSlug = $business?->slug ?? (is_string(request()->route('business')) ? request()->route('business') : null);

    $menu = $business ? [
        [
            'label' => 'Dashboard',
            'route' => route('business.dashboard', ['business' => $businessSlug]),
            'active' => request()->routeIs('business.dashboard'),
        ],
        [
            'label' => 'Estates',
            'route' => route('app.estates', ['business' => $businessSlug]),
            'active' => request()->routeIs('app.estates*'),
        ],
        [
            'label' => 'Houses',
            'route' => route('app.houses', ['business' => $businessSlug]),
            'active' => request()->routeIs('app.houses*'),
        ],
        [
            'label' => 'Residents',
            'route' => route('app.residents', ['business' => $businessSlug]),
            'active' => request()->routeIs('app.residents*'),
        ],
        [
            'label' => 'Service Charges',
            'route' => route('app.service-charges', ['business' => $businessSlug]),
            'active' => request()->routeIs('app.service-charges*'),
        ],
        [
            'label' => 'Invoices',
            'route' => route('app.invoices', ['business' => $businessSlug]),
            'active' => request()->routeIs('app.invoices*'),
        ],
        [
            'label' => 'Payments',
            'route' => route('app.payments', ['business' => $businessSlug]),
            'active' => request()->routeIs('app.payments*'),
        ],
        [
            'label' => 'Reports',
            'route' => route('app.reports', ['business' => $businessSlug]),
            'active' => request()->routeIs('app.reports*'),
        ],
    ] : [];
@endphp

<div class="bg-white border border-gray-200 shadow-sm rounded-xl p-4 lg:sticky lg:top-6">
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Workspace</p>
            <p class="text-sm font-semibold text-gray-900">{{ $business?->name ?? 'My account' }}</p>
            @if ($business)
                <p class="text-xs text-gray-500">Slug: {{ $business->slug }}</p>
            @else
                <p class="text-xs text-gray-500">Add or select a business to unlock modules.</p>
            @endif
        </div>
        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-1 text-[11px] font-medium text-emerald-700">
            Signed in
        </span>
    </div>

    @if ($business && count($menu))
        <div class="mt-4 space-y-1">
            @foreach ($menu as $item)
                <a
                    href="{{ $item['route'] }}"
                    class="flex items-center justify-between rounded-md border px-3 py-2 text-sm font-medium transition {{ $item['active'] ? 'border-indigo-200 bg-indigo-50 text-indigo-700' : 'border-transparent text-gray-700 hover:border-gray-200 hover:bg-gray-50 hover:text-gray-900' }}"
                >
                    <span>{{ $item['label'] }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                        <path fill-rule="evenodd" d="M7.22 4.22a.75.75 0 011.06 0l4.5 4.5a.75.75 0 010 1.06l-4.5 4.5a.75.75 0 11-1.06-1.06L10.94 10 7.22 6.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                    </svg>
                </a>
            @endforeach
        </div>
    @endif

    <div class="mt-6 border-t border-gray-100 pt-4 space-y-1">
        @if (auth()->user()?->isSuperAdmin())
            <a
                href="{{ route('admin.businesses.index') }}"
                class="flex items-center justify-between rounded-md border border-transparent px-3 py-2 text-sm font-medium text-gray-700 hover:border-gray-200 hover:bg-gray-50 hover:text-gray-900"
            >
                <span>Admin · Businesses</span>
            </a>
        @endif

        <a
            href="{{ route('docs') }}"
            class="flex items-center justify-between rounded-md border border-transparent px-3 py-2 text-sm font-medium text-gray-700 hover:border-gray-200 hover:bg-gray-50 hover:text-gray-900"
        >
            <span>Docs</span>
        </a>

        <a
            href="{{ route('profile.edit') }}"
            class="flex items-center justify-between rounded-md border border-transparent px-3 py-2 text-sm font-medium text-gray-700 hover:border-gray-200 hover:bg-gray-50 hover:text-gray-900"
        >
            <span>Profile settings</span>
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                class="w-full text-left flex items-center justify-between rounded-md border border-transparent px-3 py-2 text-sm font-medium text-gray-700 hover:border-gray-200 hover:bg-gray-50 hover:text-gray-900"
            >
                <span>Log out</span>
            </button>
        </form>
    </div>
</div>
