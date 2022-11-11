<x-admin-layout>
    <div class="overflow-hidden">
        <div class="text-center mb-12">
            <h2 class="text-3xl text-center mb-1 font-semibold">{{ __('Import') }}</h2>
        </div>

        <div class="mb-10 grid grid-cols-2 gap-4">
            <div class="p-6 rounded-2xl border-2 border-mintpad-200 bg-primary-100 text-center">
                <h3 class="text-xl text-center mb-4 font-semibold">{{ __('New EVM-token import') }}</h3>

                <form method="POST" action="{{ route('admin.import.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="file" class="inline-block mb-4 p-2 w-full border-2 border-mintpad-200 rounded-lg file:mr-2 file:px-4 file:py-3 file:bg-mintpad-200 file:text-mintpad-300 hover:text-mintpad-400 file:rounded-lg file:text-sm file:text-center file:border-0" />
                    
                    <div class="grid grid-cols-2 gap-2 mb-4">
                        <div>
                            <x-label for="import-month" :value="__('Month')" class="text-left ml-1 w-full" />
                            <x-select name="month" for="import-month" class="!w-full" :options="config('global.months')" :selected="date('n')"></x-select>
                        </div>
                        <div>
                            <x-label for="import-year" :value="__('Year')" class="text-left ml-1 w-full" />
                            <x-select name="year" for="import-year" class="!w-full" :options="config('global.years')" :selected="date('Y')"></x-select>
                        </div>
                        <div class="col-span-2">
                            <x-label for="import-chain_id" :value="__('Blockchain')" class="text-left ml-1 w-full" />
                            <x-select name="chain_id" for="import-chain_id" class="!w-full" :options="$blockchains"></x-select>
                        </div>
                    </div>

                    <x-button>Import</x-button>
                </form>
            </div>

            <div class="p-6 rounded-2xl border-2 border-mintpad-200 bg-primary-100 text-center">
                <h3 class="text-xl text-center mb-4 font-semibold">{{ __('Generate invoices manually') }}</h3>
                <p class="mb-4 text-sm">Normally you don't need to do this manually. A Cronjob will take care of this.</p>

                <form method="POST" action="{{ route('admin.invoices.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="grid grid-cols-2 gap-2 mb-4">
                        <div>
                            <x-label for="import-month" :value="__('Month')" class="text-left ml-1 w-full" />
                            <x-select name="month" for="import-month" class="!w-full" :options="config('global.months')" :selected="date('n')"></x-select>
                        </div>
                        <div>
                            <x-label for="import-year" :value="__('Year')" class="text-left ml-1 w-full" />
                            <x-select name="year" for="import-year" class="!w-full" :options="config('global.years')" :selected="date('Y')"></x-select>
                        </div>
                    </div>

                    <x-button>Add to queue</x-button>
                </form>
            </div>

            <div class="p-6 rounded-2xl border-2 border-mintpad-200 bg-primary-100 text-left">
                <h3 class="text-xl text-center mb-4 font-semibold">{{ __('Import history') }}</h3>

                <div class="flex flex-wrap">
                    <div class="basis-2/4 text-mintpad-300 text-sm px-1">Blockchain</div>
                    <div class="basis-1/4 text-mintpad-300 text-sm">No. txn</div>
                    <div class="basis-1/4 text-mintpad-300 text-sm px-1 text-right">Period</div>
                    @foreach ($imports as $import)
                        <div class="basis-2/4 p-1">{{ config('blockchains.'.$import->chain_id.'.full') }} ({{ $import->chain_id }})</div>
                        <div class="basis-1/4 p-1">{{ $import->transactions->count() }}</div>
                        <div class="basis-1/4 p-1 text-right">{{ date('m-Y', strtotime($import->import_at)) }}</div>
                    @endforeach
                </div>
            </div>
    </div>
</x-admin-layout>
