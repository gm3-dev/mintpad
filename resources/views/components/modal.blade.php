@props(['title' => false])

<div class="relative z-40" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
    <div class="fixed z-50 inset-0 overflow-y-auto">
        <div class="flex items-end sm:items-center justify-center min-h-full p-4 sm:p-0">
            <div class="relative bg-white dark:bg-slate-900 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-3xl sm:w-full">
                <div class="bg-white dark:bg-slate-900 p-14">
                    <a href="#" class="absolute right-4 top-3 text-3xl text-mintpad-300 p-2 hover:text-mintpad-400" @click.prevent="modalClose"><i class="fas fa-times"></i></a>
                    <div>
                        @if('title')
                            <h3 class="text-2xl mb-4">{{ $title }}</h3>
                        @endif
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>