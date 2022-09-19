@props(['content'])

<span class="inline" content="{{ $content }}" v-tippy="{ arrow : true }">
    <i class="fas fa-question-circle"></i>
</span>