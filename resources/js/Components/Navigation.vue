<script setup>
import Logo from '@/Components/Logo.vue';
import DarkMode from '@/Components/DarkMode.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownRow from '@/Components/DropdownRow.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import HamburgerMenu from '@/Components/HamburgerMenu.vue';
import HamburgerMenuLink from '@/Components/HamburgerMenuLink.vue';
import WalletManager from '@/Components/WalletManager.vue';
import NavLink from '@/Components/NavLink.vue';
import { inject, ref, watch } from 'vue'
import { disconnectWallet } from '@/Wallets/Wallet';
import { getBlockchain } from '@/Helpers/Blockchain'
import { WeiToValue } from '@/Helpers/Helpers'

let wallet = inject('wallet')
let balance = ref(false)
let blockchainName = ref(false)

watch(wallet, () => {
    const blockchain = getBlockchain(wallet.value.chainId)
    balance.value = WeiToValue(wallet.value.balance).toFixed(3) + ' ' + blockchain.nativeCurrency.symbol
    blockchainName.value = blockchain.name
})
</script>
<template>
    <nav class="bg-white dark:bg-mintpad-500 border-b border-mintpad-200 dark:border-mintpad-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex justify-between h-20">
                <div class="flex">
                    <div class="flex items-center">
                        <Logo />
                    </div>

                    <div v-if="route().current('admin.*')" class="hidden space-x-2 lg:space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        <NavLink :href="route('admin.dashboard.index')" :active="route().current('admin.dashboard.*')">Dashboard</NavLink>
                        <NavLink :href="route('admin.collections.index')" :active="route().current('admin.collections.*')">Collections</NavLink>
                        <NavLink :href="route('admin.users.index')" :active="route().current('admin.users.*')">Users</NavLink>
                        <NavLink :href="route('admin.import.index')" :active="route().current('admin.import.*')">Import</NavLink>
                        <NavLink :href="route('admin.upcoming.index')" :active="route().current('admin.upcoming.*')">Upcoming</NavLink>
                        <NavLink :href="route('admin.status.index')" :active="route().current('admin.status.*')">Status</NavLink>
                    </div>
                    <div v-else class="hidden space-x-2 lg:space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        <NavLink :href="route('collections.index')" :active="route().current('collections.*')">Collections</NavLink>
                        <NavLink :href="'https://mintpad.co/support'" element="a" target="_blank">Support</NavLink>
                        <NavLink :href="'https://generator.mintpad.co'" element="a" target="_blank" class="relative">NFT generator</NavLink>
                    </div>
                </div>

                <div class="hidden sm:flex sm:items-center sm:ml-6">
                    <WalletManager></WalletManager>
                    <DarkMode class="mr-4" />

                    <Dropdown v-if="wallet.account" id="dropdown-1" width="w-72">
                        <template v-slot:button>
                            <div><img :src="'/images/'+wallet.name+'.png'" width="26px" class="inline-block mr-4 w-6" /></div>
                        </template>
                        <template v-slot:links>
                            <DropdownRow v-if="wallet.balance" class="flex flex-row">
                                <div class="w-1/3">Blockchain</div>
                                <div class="w-2/3 text-primary-600">{{ blockchainName }}</div>
                            </DropdownRow>
                            <DropdownRow v-if="wallet.balance" class="flex flex-row">
                                <div class="w-1/3">Balance</div>
                                <div class="w-2/3 text-primary-600">{{ balance }}</div>
                            </DropdownRow>
                            <DropdownLink element="a" href="#" @click.prevent.native="disconnectWallet" class="border-t border-gray-200 dark:border-mintpad-900">Disconnect</DropdownLink>
                        </template>
                    </Dropdown>

                    <Dropdown id="dropdown-2">
                        <template v-slot:button>
                            <button class="flex items-center text-mintpad-700 dark:text-gray-200 text-sm font-medium hover:border-gray-300 hover:text-mintpad-300 focus:outline-none transition duration-150 ease-in-out">
                                <div><i class="fas fa-user-circle text-3xl"></i></div>

                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </template>
                        <template v-slot:links>
                            <div class="px-4 py-2 text-xs border-b border-gray-200 dark:border-mintpad-900">
                                <div class="text-sm text-mintpad-700 dark:text-white">{{ $page.props.auth.user.name }}</div>
                            </div>
                            <DropdownLink v-if="!route().current('admin.*') && $page.props.auth.user.role == 'admin'" :href="route('admin.dashboard.index')">Admin panel</DropdownLink>
                            <DropdownLink v-if="route().current('admin.*')" :href="route('collections.index')">User panel</DropdownLink>
                            <DropdownLink :href="route('users.profile')">My profile</DropdownLink>
                            <DropdownLink :href="route('users.2fa')">2fa settings</DropdownLink>
                            <DropdownLink :href="route('users.invoices')">Invoices</DropdownLink>
                            <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </div>

            <HamburgerMenu>
                <template v-slot:main>
                    <HamburgerMenuLink :href="route('collections.index')" :active="route().current('collections.*')">Collections</HamburgerMenuLink>
                    <HamburgerMenuLink :href="'https://mintpad.co/support'" target="'_blank'">Support</HamburgerMenuLink>
                </template>
                <template v-slot:settings>
                    <div class="px-4">
                        <div class="font-medium text-base text-gray-800 dark:text-white">{{ $page.props.auth.user.name }}</div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <HamburgerMenuLink v-if="$page.props.auth.user.role == 'admin'" :href="route('collections.index')">Admin panel</HamburgerMenuLink>
                        <HamburgerMenuLink :href="route('collections.index')" :active="route().current('collections.*')">My profile</HamburgerMenuLink>
                        <HamburgerMenuLink :href="route('collections.index')" :active="route().current('collections.*')">2fa settings</HamburgerMenuLink>
                        <HamburgerMenuLink :href="route('collections.index')" :active="route().current('collections.*')">Invoices</HamburgerMenuLink>
                        <form @submit.prevent="logout">
                            <HamburgerMenuLink :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Log Out</HamburgerMenuLink>
                        </form>
                    </div>
                </template>
            </HamburgerMenu>
        </div>
    </nav>
</template>