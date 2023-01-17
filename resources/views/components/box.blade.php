<div {{ $attributes->merge(['class' => 'mb-4 border border-mintpad-200 dark:border-mintpad-900 bg-white dark:bg-mintpad-800 rounded-md']) }}>
    @if(isset($title))
        <div {{ $title->attributes->merge(['class' => 'relative border-b border-mintpad-200 dark:border-mintpad-900 font-jpegdev px-8 py-3']) }}>
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
        <div class="px-8 py-5">
            {{ $content }}
        </div>
    @endif

    {{ $slot }}
</div>