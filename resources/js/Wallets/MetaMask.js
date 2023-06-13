import { getBlockchains } from "@/Helpers/Blockchain"
import { resportError } from "@/Helpers/Sentry"
import { ethers } from "ethers";
import { ThirdwebSDK } from '@thirdweb-dev/sdk'
import { MetaMaskWallet } from "@thirdweb-dev/wallets"

export async function disconnectMetaMask() {
    const wallet = createMetaMaskInstance()

    localStorage.removeItem('walletName')
    await wallet.disconnect()

    window.location.reload()
}

export async function connectMetaMask(connect) {
    localStorage.setItem('walletName', 'metamask');

    const wallet = createMetaMaskInstance()

    if (connect == true) {
        try {  
            await wallet.connect()
            window.location.reload()
        } catch(error) {
            console.log(error)
        }
    } else {
        try {    
            wallet.on('accountsChanged', (accounts) => {
                // Time to reload your interface with accounts[0]!
                console.log('accountsChanged', accounts)
                window.location.reload()
            })
    
            wallet.on('chainChanged', () => {
                // Time to reload your interface with accounts[0]!
                console.log('chainChanged')
                window.location.reload()
            })
    
            wallet.on('message', function (message) {
                console.log('message', message)
            })
    
            wallet.on('connect', function (info) {
                console.log('Connected to network', info)
            })
    
            wallet.on('disconnect', function (error) {
                console.log('Disconnected from network', error)
                window.location.reload()
            })
        
            const signer = await wallet.getSigner()
            const address = await wallet.getAddress()
            const chainId = await wallet.getChainId()
    
            const blockchains = getBlockchains()
            const blockchain = blockchains[chainId]
    
            const sdk = ThirdwebSDK.fromSigner(signer, blockchain, {})
            const balance = await sdk.wallet.balance()
        
            return {
                name: 'metamask',
                signer: signer,
                account: address,
                chainId: chainId,
                balance: balance
            } 
    
        } catch(error) {
            console.log(error)
        }
    
        return {
            name: 'metamask',
            signer: false,
            account: false,
            chainId: false,
            balance: false
        } 
    }
}

export async function switchChainTo(chainId) {
    try {
        const wallet = createMetaMaskInstance()
        await wallet.switchChain(chainId)

    } catch(error) {
        let metamaskError = getMetaMaskError(error)
        if (metamaskError) {
            return metamaskError
        } else {
            resportError(error)
            return 'Failed to switch to the correct blockchain, try to do it manually.'
        }
    }

    return true
}

export function getMetaMaskError(error) {
    // console.log('error', error)
    // console.log('error.code', error.code)
    // console.log('error.reason', error.reason)
    switch (error.code) {
        case -32002: 
            return 'Request already pending: open MetaMask to see the request.'
        case 4001:
            return 'Request canceled: you rejected the request.'
        case 4902:
            return 'Unrecognized chain ID: try adding the chain to MetaMask.'
    }

    switch(error.reason) {
        case 'Not enough tokens owned': 
            return 'You don\'t have enough NFTs to burn.'
        case '!Qty': 
            return 'You reached the maximum number of claimable NFTs per wallet.'
        case '!MaxSupply': 
            return 'There are no more NFTs left to claim in this mint phase.'
        case 'user rejected transaction': 
            return 'Request canceled: you rejected the request.'
        case 'Internal JSON-RPC error.':
            if (error.message.search('execution reverted: !MaxSupply') !== -1) {
                return 'There are no more NFTs left to claim in this mint phase.'
            } else if (error.message.search('execution reverted: !Qty') !== -1) {
                return 'You reached the maximum number of claimable NFTs per wallet.'
            }
    }
    return false
}

function createMetaMaskInstance() {
    return new MetaMaskWallet({
        dappMetadata: {
            name: "Mintpad",
            url: "https://mintpad.co",
            description: "Mintpad",
            logoUrl: "https://app.mintpad.co/favicon.png"
        }
    })
}