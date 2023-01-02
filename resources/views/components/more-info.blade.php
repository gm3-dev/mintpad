@props(['content'])

<span class="inline ml-2" content="{{ $content }}" v-tippy="{ arrow : true }">
    <i class="fas fa-question-circle text-sm text-mintpad-700 dark:text-gray-200"></i>
</span>