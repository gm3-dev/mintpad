<div {{ $attributes->merge(['class' => 'mb-4 border border-mintpad-200 dark:border-mintpad-700 bg-white dark:bg-mintpad-800 rounded-md']) }}>
    @if(isset($title))
        <div class="relative border-b border-mintpad-200 dark:border-mintpad-700 font-jpegdev px-10 py-4">
            <h2 class="!mb-0">{{ $title }}
                @if(isset($tutorial))
                    <x-blue-button href="#" class="align-middle ml-4 !rounded-full !px-5 !py-1 !text-xs" @click.prevent="openYouTubeModal('{{ $tutorial }}')"><i class="fas fa-play mr-1 text-md align-middle"></i> <span class="align-middle text-xs">{{ __('Watch tutorial') }}</span></x-blue-button>
                @endif
            </h2>
            @if(isset($action))
                {{ $action }}
            @endif
        </div>
    @endif
    @if(isset($content))
        <div class="px-10 py-6">
            {{ $content }}
        </div>
    @endif

    {{ $slot }}
</div>