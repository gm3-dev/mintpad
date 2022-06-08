<x-app-layout>
    <div class="bg-white overflow-hidden">
        <div class="bg-white">

            <div class="text-center mb-10">
                <h2 class="font-bold text-3xl text-center mb-1">{{ __('Collections') }}</h2>
                <p class="text-gray-500 text-center mb-5">{{ __('Create and manage your NFT collections.') }}</p>
                <x-link-button href="{{ route('collections.create') }}">Add Collection</x-link-button>
            </div>

            <div class="font-bold mb-10">
                <div class="px-6 mb-1 text-xs text-gray-500 flex flex-row">
                    <div class="p-2 basis-3/12 lg:basis-4/12">{{ __('Collection name') }}</div>
                    <div class="p-2 basis-3/12">{{ __('Contract Name') }}</div>
                    <div class="p-2 basis-1/12">{{ __('Symbol') }}</div>
                    <div class="p-2 basis-1/12">{{ __('Whitelist') }}</div>
                    <div class="p-2 basis-1/12">{{ __('Deployed') }}</div>
                    <div class="p-2 basis-3/12 lg:basis-2/12"></div>
                </div>
                @foreach ($collections as $collection)
                    <div class="px-6 py-3 mb-4 rounded-md border border-gray-200 flex flex-row text-left items-center">
                        <div class="p-2 basis-3/12 lg:basis-4/12">{{ $collection->name }}</div>
                        <div class="p-2 basis-3/12">{{ $collection->contract_name }}</div>
                        <div class="p-2 basis-1/12">{{ $collection->symbol }}</div>
                        <div class="p-2 basis-1/12">{!! $collection->whitelist ? 'Yes' : 'No' !!}</div>
                        <div class="p-2 basis-1/12">{!! $collection->deployed ? 'Yes' : 'No' !!}</div>
                        <div class="p-2 basis-3/12 lg:basis-2/12">
                            <x-link-button href="{{ route('collections.edit', $collection->id) }}">Manage</x-link-button>
                            <x-link-button href="{{ route('collections.deploy', $collection->id) }}">Deploy</x-link-button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-10">
                <h2 class="font-bold text-center mb-3">{{ __('Need help with your collection?') }}</h2>
                <x-link-button href="{{ route('collections.create') }}">Join the discord</x-link-button>
            </div>
        </div>
    </div>
</x-app-layout>
