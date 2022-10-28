@props(['content'])

<span class="inline ml-2" content="{{ $content }}" v-tippy="{ arrow : true }">
    <i class="fas fa-question-circle dark:text-gray-200"></i>
</span>