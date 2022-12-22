<div v-if="modal.show" class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <x-bg-overlay class="!bg-opacity-75"></x-bg-overlay>
    <div class="fixed z-50 inset-0 overflow-y-auto">
        <div class="flex items-end sm:items-center justify-center min-h-full p-4 sm:p-0">
            <div class="relative bg-white dark:bg-mintpad-800 dark:border dark:border-mintpad-700 rounded-md text-left overflow-hidden transform transition-all sm:my-8 sm:max-w-3xl sm:w-full">
                <div v-if="modal.title" class="border-b border-mintpad-200 dark:border-mintpad-700 font-jpegdev px-10 py-4">
                    <h2 v-html="modal.title" class="!mb-0"></h2>
                </div>
                <div class="bg-white dark:bg-mintpad-800 px-10 py-6">
                    <a href="#" class="absolute right-4 top-3 text-xs font-medium text-mintpad-300 p-2 hover:text-mintpad-400" @click.prevent="modalToggle(false)">close</a>
                    <div class="overflow-y-auto">
                        <div v-html="modal.content"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>