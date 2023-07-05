import { reportError } from "@/Helpers/Sentry"
import { ethers } from "ethers";

export async function connectMetaMask(forceRequest) {
    localStorage.setItem('walletName', 'metamask');

    const provider = getMetaMaskProvider()
    if (provider) {
        setMetaMaskEvents()
        return await loadMetaMaskAccount()
    } else {
        return false
    }

    function getMetaMaskProvider() {
        var provider = false
        try {
            provider = new ethers.providers.Web3Provider(window.ethereum, "any")
            // this.provider = await detectEthereumProvider() // not used
    
        } catch(error) {
            //
        }
    
        if (provider) {
            provider.on("pending", function(e) {
                //
            })
        } else {
            // this.setErrorMessage('MetaMask is not installed - <a href="https://metamask.io/download/" target="_blank" class="underline">download here</a>', true)
        }
    
        return provider
    }
    
    function setMetaMaskEvents() {
        if (window.ethereum) {
            ethereum.on('accountsChanged', (accounts) => {
                // Time to reload your interface with accounts[0]!
                console.log('accountsChanged', accounts)
                window.location.reload()
            })
    
            ethereum.on('chainChanged', () => {
                // Time to reload your interface with accounts[0]!
                console.log('chainChanged')
                window.location.reload()
            })
    
            ethereum.on('message', function (message) {
                console.log('message', message)
            })
    
            ethereum.on('connect', function (info) {
                console.log('Connected to network', info)
            })
    
            ethereum.on('disconnect', function (error) {
                console.log('Disconnected from network', error)
                window.location.reload()
            })
        }
    }
    
    async function loadMetaMaskAccount() {
        var requestAccount = false
        var signer = false
        var account = false
        var chainId = false
        var accounts = []
        var network = false
        var balance = false
    
        try {
            signer = provider.getSigner()
            accounts = await ethereum.request({method: 'eth_accounts'})
            if (accounts.length > 0) {
                account = accounts[0]
            } else {
                throw new Error('Not connected')
            }
            network = await provider.getNetwork()
            balance = await provider.getBalance(account)
        } catch (error) {
            if (error.message != 'Not connected') {
                // return 'Metamask issue. Click <a href="https://mintpad.co/troubleshooting/" target="_blank" class="underline">here</a> to find out more.', true)
                reportError(error) 
            }
            requestAccount = true
        }
    
        if (window.ethereum) {
            if (requestAccount && forceRequest) {
                try {
                    accounts = await ethereum.request({method: 'eth_requestAccounts'})
                } catch(error) {
                    // return 'Metamask issue. Click <a href="https://mintpad.co/troubleshooting/" target="_blank" class="underline">here</a> to find out more.', true)
                    reportError(error) 
                }
                if (accounts.length > 0) {
                    account = accounts[0]
                }
            }
            
            chainId = parseInt(window.ethereum.networkVersion)
        }

        if (forceRequest == true) {
            window.location.reload()
        }

        return {
            name: 'metamask',
            signer: signer,
            account: account,
            chainId: chainId,
            balance: balance
        }
    }
}

export async function switchBlockchainTo(chainId) {
    try {
        await window.ethereum.request({
            method: 'wallet_switchEthereumChain',
            params: [{ chainId: ethers.utils.hexValue(parseInt(chainId)) }],
        })
    } catch(error) {
        let metamaskError = getMetaMaskError(error)
        if (metamaskError) {
            return metamaskError
        } else {
            reportError(error)
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