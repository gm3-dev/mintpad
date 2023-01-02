<x-admin-layout>
    <div class="overflow-hidden">
        <div class="text-center mb-10">
            <h1>{{ __('Import') }}</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4">
            <x-box>
                <x-slot name="title">New EVM-token import</x-slot>
                <x-slot name="content">
                    <form method="POST" action="{{ route('admin.import.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="file" class="inline-block mb-2 file:mr-2 file:px-4 file:py-2.5 file:bg-mintpad-200 file:text-mintpad-300 hover:text-mintpad-400 file:rounded-md file:text-sm file:text-center file:border-0" />
                        
                        <div class="grid grid-cols-2 gap-x-4 mb-4">
                            <div>
                                <x-label for="import-month" :value="__('Month')" class="w-full" />
                                <x-select name="month" for="import-month" class="!w-full" :options="config('global.months')" :selected="date('n')"></x-select>
                            </div>
                            <div>
                                <x-label for="import-year" :value="__('Year')" class="w-full" />
                                <x-select name="year" for="import-year" class="!w-full" :options="config('global.years')" :selected="date('Y')"></x-select>
                            </div>
                            <div class="col-span-2">
                                <x-label for="import-chain_id" :value="__('Blockchain')" class="text-left ml-1 w-full" />
                                <x-select name="chain_id" for="import-chain_id" class="!w-full" :options="$blockchains"></x-select>
                            </div>
                        </div>

                        <x-button>Import</x-button>
                    </form>

                </x-slot>
            </x-box>

            <x-box>
                <x-slot name="title">Generate invoices manually</x-slot>
                <x-slot name="content">
                    <p class="mb-4 text-sm">Normally you don't need to do this manually. A Cronjob will take care of this.</p>
                    <form method="POST" action="{{ route('admin.invoices.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="grid grid-cols-2 gap-x-4 mb-4">
                            <div>
                                <x-label for="import-month" :value="__('Month')" class="w-full" />
                                <x-select name="month" for="import-month" class="!w-full" :options="config('global.months')" :selected="date('n')"></x-select>
                            </div>
                            <div>
                                <x-label for="import-year" :value="__('Year')" class="w-full" />
                                <x-select name="year" for="import-year" class="!w-full" :options="config('global.years')" :selected="date('Y')"></x-select>
                            </div>
                        </div>

                        <x-button>Add to queue</x-button>
                    </form>
                </x-slot>
            </x-box>

            <x-box>
                <x-slot name="title">Import history</x-slot>
                <div>
                    <x-box-row class="flex flex-wrap text-sm dark:text-mintpad-300 font-jpegdevmd">
                        <div class="basis-2/4 text-mintpad-300 text-sm px-1">Blockchain</div>
                        <div class="basis-1/4 text-mintpad-300 text-sm">No. txn</div>
                        <div class="basis-1/4 text-mintpad-300 text-sm px-1 text-right">Period</div>
                    </x-box-row>
                    @foreach ($imports as $import)
                        <x-box-row class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                            <div class="basis-2/4 p-1">{{ config('blockchains.'.$import->chain_id.'.full') }} ({{ $import->chain_id }})</div>
                            <div class="basis-1/4 p-1">{{ $import->transactions->count() }}</div>
                            <div class="basis-1/4 p-1 text-right">{{ date('m-Y', strtotime($import->import_at)) }}</div>
                        </x-box-row>
                    @endforeach
                </div>
            </x-box>
        </div>
    </div>
</x-admin-layout>
