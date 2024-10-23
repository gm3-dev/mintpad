import { connectMetaMask, disconnectMetaMask } from '@/Wallets/MetaMask'

export async function disconnectWallet() {
    const walletName = localStorage.getItem('walletName')
    
    if (walletName == 'metamask') {
        return await disconnectMetaMask()
    }
}

export async function reconnectWallet() {
    const walletName = localStorage.getItem('walletName')
    
    if (walletName == 'metamask') {
        return await connectMetaMask(false)
    }

    return getDefaultWalletData()
}

export async function connectWallet(wallet) {
    if (wallet == 'metamask') {
        return await connectMetaMask(true)
    }
}

export function getDefaultWalletData() {
    return {
        name: 'none',
        signer: false,
        account: false,
        chainId: false,
        balance: false
    }
}