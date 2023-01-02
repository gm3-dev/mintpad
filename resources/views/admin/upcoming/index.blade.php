<x-admin-layout>
    <div class="overflow-hidden">
        <div class="text-center mb-10">
            <h1>{{ __('Upcoming') }}</h1>
        </div>

        <x-box>
            <x-slot name="title">Mint phases</x-slot>
            <x-slot name="action"><span class="absolute right-10 top-5 text-xs font-medium text-mintpad-300">Last update: {{ $last_phase_update }}</span></x-slot>
            <div>
                <x-box-row class="flex flex-wrap text-sm dark:text-mintpad-300 font-jpegdevmd">
                    <div class="basis-3/5">Collection name</div>
                    <div class="basis-1/5">Mint info</div>
                    <div class="basis-1/5 text-right">When</div>
                </x-box-row>
                @foreach ($collections as $collection)
                    <x-box-row class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                        <div class="basis-3/5">{{ $collection->name }}</div>
                        <div class="basis-1/5">{{ $collection->mint_price }} {{ $collection->token }}</div>
                        <div class="basis-1/5 text-right">{{ $collection->mint_at }}</div>
                    </x-box-row>
                @endforeach
            </div>
        </x-box>
    </div>
</x-admin-layout>
