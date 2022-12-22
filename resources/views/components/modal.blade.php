@props(['title' => false])

<div class="relative z-40" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <x-bg-overlay class="!bg-opacity-75"></x-bg-overlay>
    <div class="fixed z-50 inset-0 overflow-y-auto">
        <div class="flex items-end sm:items-center justify-center min-h-full p-4 sm:p-0">
            <div class="relative bg-white dark:bg-slate-900 rounded-md text-left overflow-hidden transform transition-all sm:my-8 sm:max-w-3xl sm:w-full">
                @if('title')
                <div class="border-b border-mintpad-200 font-jpegdev px-10 py-4">
                    <h2 class="!mb-0"> {{ $title }}</h2>
                </div>
                @endif
                <div class="bg-white dark:bg-slate-900 px-10 py-6">
                    @if(isset($close))
                        {{ $close }}
                    @else
                        <a href="#" class="absolute right-4 top-3 text-xs font-medium text-mintpad-300 p-2 hover:text-mintpad-400" @click.prevent="modalClose">close</a>
                    @endif
                    <div class="overflow-y-auto">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>