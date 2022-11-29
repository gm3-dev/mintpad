<x-app-layout>
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="relative mb-12 px-2">
            <div class="text-center mb-12">
                <h2 class="text-center text-3xl mb-1 font-semibold">{{ __('Invoices') }}</h2>
                <x-blue-button href="#" class="absolute right-0 top-0 align-middle !rounded-full !px-4 !py-1 !text-xs" @click.prevent="openYouTubeModal('https://www.youtube.com/embed/4HVmvaBeYIs')"><i class="fas fa-play mr-1 text-md align-middle"></i> <span class="align-middle">{{ __('Watch tutorial') }}</span></x-blue-button>
                <p class="text-center text-lg">{{ __('Download your invoices here.') }}</p>
            </div>
            <p class="mb-4 font-regular text-center text-sm">{{ __('Are you a company? Add your details to your account on the') }} <x-link href="{{ route('users.profile') }}" class="font-semibold">my profile</x-link> {{ __('page.') }}</p>
            
            @if ($invoices->count() == 0)
                <p class="text-sm text-center">{{ __('You don\'t have any invoices yet.') }}</p>
            @else
                <div class="px-6 mb-1 text-mintpad-300 flex flex-row text-sm">
                    <div class="p-1 basis-2/5">{{ __('Invoice ID') }}</div>
                    <div class="p-1 basis-1/5">{{ __('Amount') }}</div>
                    <div class="p-1 basis-1/5">{{ __('Status') }}</div>
                    <div class="p-1 basis-1/5">{{ __('Date') }}</div>
                    <div class="p-1 basis-1/5"></div>
                </div>
                @foreach ($invoices as $invoice)
                    <div class="px-6 py-1 mb-4 text-mintpad-500 dark:text-white rounded-2xl border-2 border-mintpad-200 dark:border-gray-600 bg-primary-100 dark:bg-mintpad-700 flex flex-row text-left items-center text-sm">
                        <div class="p-1 basis-2/5 font-semibold">{{ $invoice['invoice_id'] }}</div>
                        <div class="p-1 basis-1/5 font-semibold">$ {{ number_format($invoice['total_price_incl_tax'], 2) }}</div>
                        <div class="p-1 basis-1/5 font-semibold">{{ $invoice['state'] }}</div>
                        <div class="p-1 basis-1/5 font-semibold">{{ date('Y-m-d', strtotime($invoice['created_at'])) }}</div>
                        <div class="p-1 basis-1/5 text-right">
                            <x-link-button href="{{ route('users.download', $invoice['id']) }}" target="_blank" class="!py-1">Download</x-link-button>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</x-app-layout>
