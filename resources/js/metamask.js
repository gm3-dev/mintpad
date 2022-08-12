import { ethers } from 'ethers'

export async function initMetaMask(triggerRequest) {
    var output = {
        name: 'metamask'
    }

    if (typeof window.ethereum === 'undefined') {
        return false
    }

    function getProvider() {
        var provider = new ethers.providers.Web3Provider(window.ethereum, "any")
        // this.provider = await detectEthereumProvider()

        if (provider) {
            // From now on, this should always be true:
            // provider === window.ethereum
            output.provider = provider
        } else {
            console.log('Please install MetaMask!')
        }

        provider.on("pending", function(e) {
            console.log(e)
        })
    }
    const provider = getProvider()

    function loadWeb3() {
        if (window.ethereum) {        
            ethereum.on('accountsChanged', (accounts) => {
                // Time to reload your interface with accounts[0]!
                console.log('accountsChanged', accounts)
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
        var accounts = []

        try {
            signer = output.provider.getSigner()
            account = await signer.getAddress()
        } catch (e) {
            console.log('ERROR', e.message)
            requestAccount = true
        }

        if (window.ethereum && requestAccount && triggerRequest) {
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

        output.signer = signer
        output.account = account
    }
    await loadAccount(triggerRequest)

    return output;
}