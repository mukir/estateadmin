@if (session('status'))
    <div
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 3500)"
        x-show="show"
        x-transition.opacity
        class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-2"
    >
        {{ session('status') }}
    </div>
@endif

@if ($errors->any())
    <div
        x-data="{ show: true }"
        x-show="show"
        x-transition.opacity
        class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-2"
    >
        <div class="flex items-start justify-between gap-3">
            <ul class="list-disc list-inside text-sm flex-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button
                type="button"
                class="text-xs text-red-600 underline hover:text-red-700"
                @click="show = false"
            >
                Dismiss
            </button>
        </div>
    </div>
@endif
