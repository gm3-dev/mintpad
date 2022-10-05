
<div v-if="errorMessage" class="flex fixed w-1/3 bottom-2 inset-x-0 mx-auto p-3 mb-4 bg-red-100 border-t-4 border-red-500 dark:bg-red-200" role="alert">
    <div class="w-full">
        <i class="fas fa-info-circle text-red-700"></i>
        <div class="inline ml-3 text-sm font-medium text-red-700" v-html="errorMessage"></div>
        <a v-if="showRefreshButton" class="text-sm float-right mt-1 text-red-700 underline" href="/{{ request()->path() }}">refresh</a>
    </div>
</div>
<div v-if="successMessage" class="flex fixed w-1/3 bottom-2 inset-x-0 mx-auto p-3 mb-4 bg-green-100 border-t-4 border-green-500 dark:bg-green-200" role="alert">
    <i class="fas fa-info-circle text-green-700"></i>
    <div class="ml-3 text-sm font-medium text-green-700" v-html="successMessage"></div>
</div>
@if ($message = Session::get('success'))
    <div class="flex fixed w-1/3 bottom-2 inset-x-0 mx-auto p-3 mb-4 bg-green-100 border-t-4 border-green-500 dark:bg-green-200" role="alert">
        <i class="fas fa-info-circle text-green-700"></i>
        <div class="ml-3 text-sm font-medium text-green-700">{{ $message }}</div>
    </div>
@endif