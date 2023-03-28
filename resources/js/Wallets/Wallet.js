import { connectMetaMask } from '@/Wallets/MetaMask'

export function disconnectWallet() {
    localStorage.removeItem('walletName')
    window.location.reload()
}

export async function connectWallet(wallet, forceRequest) {
    if (wallet == 'metamask') {
        return await connectMetaMask(forceRequest)
    }
    // if (wallet == 'phantom') {
    //     await this.connectPhantom()
    // }



    // this.setWalletUI()
    // this.setPage()
    // this.setPageData()
}