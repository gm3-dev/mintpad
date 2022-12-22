<x-app-layout>
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="relative mb-12 px-2">
            <div class="text-center mb-10">
                <h1>{{ __('Invoices') }}</h1>
                <p>{{ __('Download your invoices here.') }}</p>
            </div>

            <x-box class="w-full">
                <x-slot name="title">Your invoices</x-slot>
                <x-slot name="tutorial">https://www.youtube.com/embed/4HVmvaBeYIs</x-slot>
                <x-slot name="content">
                    <p>{{ __('Are you a company? Add your details to your account ') }} <x-link href="{{ route('users.profile') }}">here</x-link>.</p>
                </x-slot>
            </x-box>

            <form method="POST" action="{{ route('users.update') }}">
                @csrf
                @method('PUT')

                <x-box class="w-full">
                    <x-slot name="title">Your invoices</x-slot>
                        @if ($invoices->count() == 0)
                            <x-slot name="content">
                                <p>{{ __('You don\'t have any invoices yet.') }}</p>
                            </x-slot>
                        @else
                            <x-box-row class="flex flex-wrap text-sm font-jpegdevmd">
                                <div class="basis-1/5">{{ __('Month') }}</div>
                                <div class="basis-3/5">{{ __('Collection name') }}</div>
                                <div class="basis-1/5"></div>
                            </x-box-row>
                            @foreach ($invoices as $invoice)
                                <x-box-row class="flex flex-wrap text-sm items-center text-mintpad-700 font-medium">
                                    <div class="basis-1/5">{{ date('F', strtotime($invoice['created_at'])) }}</div>
                                    <div class="basis-3/5">{{ $invoice['reference'] }}</div>
                                    <div class="basis-1/5 text-right">
                                        <x-link-button href="{{ route('users.download', $invoice['id']) }}" target="_blank">Download</x-link-button>
                                    </div>
                                </x-box-row>
                            @endforeach
                        @endif
                </x-box>
            </form>
        </div>
    </div>
</x-app-layout>
