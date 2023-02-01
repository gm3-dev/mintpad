<x-admin-layout>
    <div class="overflow-hidden">
        <div class="text-center mb-10">
            <h1>{{ __('Dashboard') }}</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4">
            <x-box>
                <x-slot name="title">Collections</x-slot>
                <div>
                    @foreach ($collection_list as $chain_id => $count)
                        <x-box-row class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                            <div class="basis-2/3">{{ config('blockchains.'.$chain_id.'.full') }}</div>
                            <div class="basis-1/3">{{ $count }}</div>
                        </x-box-row>
                    @endforeach
                    <x-box-row class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                        <div class="basis-2/3 pr-4 text-right">Total</div>
                        <div class="basis-1/3">{{ $collections }}</div>
                    </x-box-row>
                </div>
            </x-box>

            <x-box>
                <x-slot name="title">Recent imports</x-slot>
                <div>
                    <x-box-row class="flex flex-wrap text-sm dark:text-mintpad-300 font-jpegdevmd">
                        <div class="basis-2/4">Blockchain</div>
                        <div class="basis-1/4">Total USD</div>
                        <div class="basis-1/4 text-right">Period</div>
                    </x-box-row>
                    @foreach ($imports as $import)
                        <x-box-row class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                            <div class="basis-2/4">{{ config('blockchains.'.$import->chain_id.'.full') }}</div>
                            <div class="basis-1/4">${{ $import->total }}</div>
                            <div class="basis-1/4 text-right">{{ date('m-Y', strtotime($import->import_at)) }}</div>
                        </x-box-row>
                    @endforeach
                </div>
            </x-box>

            <x-box>
                <x-slot name="title">Users</x-slot>
                <div>
                    <x-box-row class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                        <div class="basis-2/3">Total</div>
                        <div class="basis-1/3">{{ $users }}</div>
                    </x-box-row>
                </div>
            </x-box>
        </div>
    </div>
</x-admin-layout>
