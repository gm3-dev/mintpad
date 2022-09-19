@props(['content'])

<span class="inline" content="{{ $content }}" v-tippy="{ arrow : true }">
    <i class="fas fa-question-circle dark:text-gray-200"></i>
</span>