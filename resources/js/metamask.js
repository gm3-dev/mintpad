import { ethers } from 'ethers'

export async function initMetaMask(triggerRequest) {
    var output = {
        name: 'metamask'
    }

    if (typeof window.ethereum === 'undefined') {
        return false
    }

    async function getProvider() {
        const provider = new ethers.providers.Web3Provider(window.ethereum, "any")
        const network = await provider.getNetwork()
        // this.provider = await detectEthereumProvider() // not used

        if (provider) {
            // From now on, this should always be true:
            // provider === window.ethereum
            output.provider = provider
            output.network = network
        } else {
            console.log('Please install MetaMask!')
        }

        provider.on("pending", function(e) {
            console.log(e)
        })
    }
    await getProvider()

    function loadWeb3() {
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
            })
        }
    }
    loadWeb3()

    async function loadAccount(triggerRequest) {
        var requestAccount = false
        var signer = false
        var account = false
        var chainID = false
        var accounts = []

        try {
            signer = output.provider.getSigner()
            accounts = await ethereum.request({method: 'eth_accounts'})
            if (accounts.length > 0) {
                account = accounts[0]
            } else {
                throw Error('Not connected')
            }
            // account = await signer.getAddress()
        } catch (e) {
            console.log('ERROR', e.message)
            requestAccount = true
        }

        if (window.ethereum) {
            if (requestAccount && triggerRequest) {
                try {
                    accounts = await ethereum.request({method: 'eth_requestAccounts'})
                } catch(e) {
                    if (e.code == -32002) {
                        //
                    }
                }
                if (accounts.length > 0) {
                    account = accounts[0]
                }
            }
            
            chainID = window.ethereum.networkVersion
        }

        output.signer = signer
        output.account = account
        output.chainID = chainID
    }
    await loadAccount(triggerRequest)

    return output;
}