<div class="w-full mx-auto mb-3 pb-3">
    <div class="flex items-center text-sm font-bold">
        <div class="basis-1/3 p-2">
            <p class="text-primary-600 mb-1">Step 1: Deploy smart contract</p>
            <div class="border-4 border-primary-600 w-full"></div>
        </div>
        <div class="basis-1/3 p-2">
            <p class="mb-1 {{ $step > 1 ? 'text-primary-600' : '' }}">Step 2: Claim phases</p>
            <div class="border-4 w-full {{ $step > 1 ? 'border-primary-600' : 'border-gray-100' }}"></div>
        </div>
        <div class="basis-1/3 p-2">
            <p class="mb-1 {{ $step > 2 ? 'text-primary-600' : '' }}">Step 3: Upload collection</p>
            <div class="border-4 w-full {{ $step > 2 ? 'border-primary-600' : 'border-gray-100' }}"></div>
        </div>
    </div>
</div>