<x-admin-layout>
    <div class="overflow-hidden">
        <div class="text-center mb-10">
            <h1>{{ __('Users') }}</h1>
        </div>

        <x-box>
            <x-slot name="title">User list</x-slot>
            <div>
                <x-box-row class="flex flex-row text-sm dark:text-mintpad-300 font-jpegdevmd">
                    <div class="basis-1/6">Name</div>
                    <div class="basis-1/6">Role</div>
                    <div class="basis-1/6">Collections</div>
                    <div class="basis-1/6">status</div>
                    <div class="basis-2/6"></div>
                </x-box-row>
                @foreach ($users as $user)
                    <x-box-row class="flex flex-row text-sm items-center text-mintpad-700 dark:text-white font-medium">
                        <div class="basis-1/6">{{ $user->name }}</div>
                        <div class="basis-1/6">{{ $user->role }}</div>
                        <div class="basis-1/6">
                            {{ $user->collections->count() }} collection(s)
                            @if ($user->collections->count())
                                <span class="inline-block ml-2" content="{{ $user->collections->pluck('name')->implode(', ') }}" v-tippy="{ arrow : true }">
                                    <i class="fas fa-question-circle text-sm text-mintpad-700 dark:text-gray-200"></i>
                                </span>
                            @endif
                        </div>
                        <div class="basis-1/6">{{ $user->status }}</div>
                        <div class="basis-2/6 text-right">
                            <x-link-button href="{{ route('admin.users.edit', $user->id) }}" class="ml-2">{{ __('Edit') }}</x-link-button>
                        </div>
                    </x-box-row>
                @endforeach
            </div>
        </x-box>
    </div>
</x-admin-layout>
