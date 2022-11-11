<x-admin-layout>
    <div class="overflow-hidden">
        <div class="text-center mb-12">
            <h2 class="text-3xl text-center mb-1 font-semibold">{{ __('Dashboard') }}</h2>
        </div>

        <div class="mb-10 grid grid-cols-2 gap-4">
            <div class="p-6 mb-4 rounded-2xl border-2 border-mintpad-200 bg-primary-100">
                <h3 class="text-xl text-center mb-4 font-semibold">Collections</h3>
                <div class="flex flex-wrap">
                    @foreach ($collection_list as $chain_id => $count)
                        <div class="basis-1/2 p-1">{{ config('blockchains.'.$chain_id.'.full') }}</div><div class="basis-1/2 p-1">{{ $count }}</div>
                    @endforeach
                    <div class="basis-1/2 border-t-2 border-mintpad-200 p-1"><p>Total</p></div>
                    <div class="basis-1/2 border-t-2 border-mintpad-200 p-1"><p>{{ $collections }}</p></div>
                </div>
            </div>

            <div class="p-6 mb-4 rounded-2xl border-2 border-mintpad-200 bg-primary-100">
                <h3 class="text-xl text-center mb-4 font-semibold">Recent imports</h3>
                <div class="flex flex-wrap">
                    <div class="basis-2/4 text-mintpad-300 text-sm px-1">Blockchain</div>
                    <div class="basis-1/4 text-mintpad-300 text-sm px-1">Total USD</div>
                    <div class="basis-1/4 text-mintpad-300 text-sm px-1 text-right">Period</div>
                    @foreach ($imports as $import)
                        <div class="basis-2/4 p-1">{{ config('blockchains.'.$import->chain_id.'.full') }}</div>
                        <div class="basis-1/4 p-1">${{ $import->total }}</div>
                        <div class="basis-1/4 p-1 text-right">{{ date('m-Y', strtotime($import->import_at)) }}</div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
