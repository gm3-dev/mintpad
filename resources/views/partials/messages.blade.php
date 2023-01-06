<div v-if="messageBag" class="fixed w-11/12 sm:w-1/3 bottom-2 inset-x-0 mx-auto z-50">
    <div v-for="message in messageBag" class="px-6 py-2 mb-2 rounded-md bg-white dark:bg-mintpad-800 border dark:border-primary-600 border-primary-200 z-50" role="alert">
        <div class="w-full">
            <i v-if="message.type == 'info'" class="fa-solid fa-circle-exclamation text-xl text-mintpad-700 align-middle"></i>
            <i v-if="message.type == 'error'" class="fa-solid fa-circle-exclamation text-xl text-red-400 align-middle"></i>
            <i v-if="message.type == 'success'" class="fa-solid fa-circle-check text-xl text-primary-600 align-middle"></i>
            <div class="inline ml-3 text-sm text-mintpad-700 dark:text-white" v-html="message.message"></div>
            <a v-if="message.refresh" class="text-sm float-right mt-0.5 underline text-mintpad-700 dark:text-white" href="/{{ request()->path() }}">refresh</a>
        </div>
    </div>
</div>
@if ($message = Session::get('success'))
    <div class="flex fixed w-11/12 sm:w-1/3 bottom-2 inset-x-0 mx-auto px-6 py-2 mb-2 rounded-md bg-white dark:bg-mintpad-800 border dark:border-primary-600 border-primary-200" role="alert">
        <i class="fa-solid fa-circle-check text-xl text-primary-600 align-middle"></i>
        <div class="ml-3 text-sm text-mintpad-700 dark:text-white">{{ $message }}</div>
    </div>
@endif
@if ($message = Session::get('error'))
    <div class="flex fixed w-11/12 sm:w-1/3 bottom-2 inset-x-0 mx-auto px-6 py-2 mb-2 rounded-md bg-white dark:bg-mintpad-800 border dark:border-primary-600 border-primary-200" role="alert">
        <i class="fa-solid fa-circle-exclamation text-xl text-red-400 align-middle"></i>
        <div class="ml-3 text-sm text-mintpad-700 dark:text-white">{{ $message }}</div>
    </div>
@endif