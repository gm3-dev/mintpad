<x-app-layout>
    <div class="max-w-7xl mx-auto px-6">
        <div class="relative mb-12 px-2">
            <div class="text-center mb-10">
                <h1>{{ __('Two-factor authentication') }}</h1>
                <p>{{ __('Manage two-factor authentication') }}</p>
            </div>

            <x-box class="w-full">
                <x-slot name="title">Two-factor authentication</x-slot>
                <x-slot name="content">
                    <p class="mb-4">Two-factor authentication is an extra layer of security for your Mintpad account in addition to the password. This extra layer prevents unwanted people from gaining access to your account when they have retrieved the password.</p>
                    <h3>This is how it works</h3>
                    <p class="mb-4">Every time you log in, you will be asked for an extra verification code in addition to your password. Before you activate two-step verification, you will receive 8 codes from us as a backup. Keep it in a safe place that only you can access. You can use these codes when you do not have access to the control code.</p>
                    @if (session('status') == 'two-factor-authentication-confirmed')
                        <p class="mb-4">Two factor authentication confirmed and enabled successfully.</p>
                    @endif
                    
                    @if (session('status') == 'two-factor-authentication-enabled')
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4">
                            <div class="col-span-1 sm:col-span-2">
                                <h2>Step 1</h2>
                            </div>
                            <div class="mb-4">
                                <p class="mb-4">You have now enabled 2fa, please scan the following QR code into your phones authenticator application.</p>
                                <p class="text-center">{!! $user->twoFactorQrCodeSvg() !!}</p>
                            </div>
                            <div class="mb-4">
                                <p class="mb-4">Please store these recovery codes in a secure location.</p>
                                @foreach ($user->recoveryCodes() as $code)
                                    <p class="text-center">{{ $code }}</p>
                                @endforeach
                            </div>
                            <div class="col-span-1 sm:col-span-2">
                                <h2>Step 2</h2>
                                <form method="POST" action="{{ route('two-factor.confirm') }}">
                                    @csrf
                                    <x-label for="code" :value="__('Code')" />
                                    <x-input id="code" class="w-full sm:w-1/5" type="text" name="code" :value="old('code')" required />
                                    <x-button class="w-full sm:w-1/5">{{ __('Comfirm') }}</x-button>
                                </form>
                            </div>
                        </div>
                    @else
                        @if (! $user->two_factor_secret)
                            <form method="POST" action="{{ route('two-factor.enable') }}">
                        @else
                            <form method="POST" action="{{ route('two-factor.disable') }}">
                            @method('DELETE')
                        @endif
                            @csrf
                            @if (! $user->two_factor_secret)
                                <x-button>{{ __('Enable') }}</x-button>
                            @else
                                <x-button>{{ __('Disable') }}</x-button>
                            @endif
                        </form>
                    @endif
                </x-slot>
            </x-box>
        </div>
    </div>
</x-app-layout>
