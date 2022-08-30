<x-app-layout>
    <div class="bg-white overflow-hidden">
        <div class="bg-white">
            <div class="mb-12 px-2">
                <h2 class="text-center text-3xl mb-6 font-semibold">{{ __('Invoices') }}</h2>

                <div class="px-6 mb-1 text-mintpad-300 flex flex-row text-sm">
                    <div class="p-1 basis-2/5">{{ __('Invoice ID') }}</div>
                    <div class="p-1 basis-1/5">{{ __('Amount') }}</div>
                    <div class="p-1 basis-1/5">{{ __('Status') }}</div>
                    <div class="p-1 basis-1/5"></div>
                </div>
                @foreach ($invoices as $invoice)
                    <div class="px-6 py-1 mb-4 rounded-2xl border-2 border-mintpad-200 bg-primary-100 flex flex-row text-left items-center text-sm">
                        <div class="p-1 basis-2/5 font-semibold">{{ $invoice['invoice_id'] }}</div>
                        <div class="p-1 basis-1/5 font-semibold">{{ $invoice['amount'] }},-</div>
                        <div class="p-1 basis-1/5 font-semibold">{{ $invoice['status'] }}</div>
                        <div class="p-1 basis-1/5 text-right">
                            <x-link-button href="#" class="!py-1">Download</x-link-button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
