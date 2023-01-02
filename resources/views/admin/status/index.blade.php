<x-admin-layout>
    <div class="overflow-hidden">
        <div class="text-center mb-10">
            <h1>{{ __('Status') }}</h1>
        </div>

        <x-box class="mb-4">
            <x-slot name="title">Server status</x-slot>
            <div>
                <x-box-row class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                    <div class="basis-2/5">Platform live</div><div class="basis-3/5 text-green-700">Running with no known issues</div>
                </x-box-row>
                <x-box-row class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                    <div class="basis-2/5">Generator live</div><div class="basis-3/5 text-green-700">Running with no known issues</div>
                </x-box-row>
                <x-box-row class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                    <div class="basis-2/5">Platform development</div><div class="basis-3/5 text-green-700">Running with no known issues</div>
                </x-box-row>
                <x-box-row class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                    <div class="basis-2/5">Generator development</div><div class="basis-3/5 text-green-700">Running with no known issues</div>
                </x-box-row>
                <x-box-row class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                    <div class="basis-2/5">Demo website</div><div class="basis-3/5 text-green-700">Running with no known issues</div>
                </x-box-row>
            </div>
        </x-box>
        <x-box class="mb-4">
            <x-slot name="title">Functionality status</x-slot>
            <div>
                <x-box-row class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                    <div class="basis-2/5">Register</div><div class="basis-3/5 text-green-700">No issues reported</div>
                </x-box-row>
                <x-box-row class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                    <div class="basis-2/5">My profile</div><div class="basis-3/5 text-green-700">No issues reported</div>
                </x-box-row>
                <x-box-row class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                    <div class="basis-2/5">Create collection</div><div class="basis-3/5 text-green-700">No issues reported</div>
                </x-box-row>
                <x-box-row class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                    <div class="basis-2/5">1. Settings</div><div class="basis-3/5 text-green-700">No issues reported</div>
                </x-box-row>
                <x-box-row class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                    <div class="basis-2/5">2. Mint phases</div><div class="basis-3/5 text-green-700">No issues reported</div>
                </x-box-row>
                <x-box-row class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                    <div class="basis-2/5">3. Upload collection</div><div class="basis-3/5 text-green-700">No issues reported</div>
                </x-box-row>
                <x-box-row class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                    <div class="basis-2/5">4. Mint settings</div><div class="basis-3/5 text-green-700">No issues reported</div>
                </x-box-row>
                <x-box-row class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                    <div class="basis-2/5">Collection page</div><div class="basis-3/5 text-green-700">No issues reported</div>
                </x-box-row>
                <x-box-row class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                    <div class="basis-2/5">Collection page editor</div><div class="basis-3/5 text-green-700">No issues reported</div>
                </x-box-row>
                <x-box-row class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                    <div class="basis-2/5">Invoices</div><div class="basis-3/5 text-green-700">No issues reported</div>
                </x-box-row>
                <x-box-row class="flex flex-wrap text-sm items-center text-mintpad-700 dark:text-white font-medium">
                    <div class="basis-2/5">Collection generator</div><div class="basis-3/5 text-green-700">No issues reported</div>
                </x-box-row>
            </div>
        </x-box>
    </div>
</x-admin-layout>
