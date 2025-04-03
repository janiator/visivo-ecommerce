<x-filament-panels::page>
    <div class="bg-white shadow rounded-lg overflow-hidden">
        {{-- Product Main Image --}}
        @if($record->getFirstMediaUrl('main_images'))
            <img src="{{ $record->getFirstMediaUrl('main_images') }}" alt="{{ $record->name }}" class="w-full h-64 object-cover">
        @endif

        <div class="p-6">
            {{-- Product Title --}}
            <h1 class="text-3xl font-bold mb-4">
                {{ $record->name }}
            </h1>

            {{-- Short Description --}}
            @if($record->short_description)
                <p class="text-gray-700 mb-4">{{ $record->short_description }}</p>
            @endif

            {{-- Price --}}
            <div class="mb-4">
                <span class="text-2xl text-green-600 font-semibold">
                    ${{ number_format($record->price, 2) }}
                </span>
            </div>

            {{-- Detailed Description --}}
            @if($record->description)
                <div class="prose">
                    {!! $record->description !!}
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
