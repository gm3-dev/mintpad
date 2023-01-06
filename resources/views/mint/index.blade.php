<x-mint-layout>
    @section('title', $seo['title'])
    @section('description', $seo['description'])
    @section('sharing-image', $seo['image'] ?? false)

    @include('partials.mint-page')
</x-app-layout>
