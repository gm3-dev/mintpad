require('./bootstrap')
window.$ = require('jquery')
import Vue from 'vue/dist/vue.js'
import Alpine from 'alpinejs'
import { ethers } from "ethers"
import { ContractFactory } from 'ethers'
import detectEthereumProvider from '@metamask/detect-provider'
const axios = require('axios')
axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
}
const contractAddress = require('./build/contracts/DazedDucks.json').networks[5777].address
const contractAbi = require('./build/contracts/DazedDucks.json').abi
const contractByteCode = require('./build/contracts/DazedDucks.json').bytecode

window.Alpine = Alpine
Alpine.start()

if (document.getElementById('app')) {
    new Vue({
        el: '#app',
        data: {
            collectionID: false,
            message: {
                error: false,
                success: false,
                info: false
            },
            provider: false,
            account: false,
            accounts: false,
            contract: false,
            agreeConditions: false,
            pinataApiKey: false,
            pinataSecretApiKey: false,
            form: {
                baseURI: 'ipfs://QmUvctUmemgZg1iPhCsfCoejp4ggeicxeSFPvwLuiNJR4e'
            }
        },
        async created() {
            // await this.addUser()
            // await this.getUsers()
            // await this.addWhitelistUser()
            // await this.getWhitelistUser()
            // await this.getString()
            // await this.mintNFT()
            // await this.mintNftObject()
        },

        async mounted() {
            console.log('dApp mounted')

            await this.setProvider()
            await this.loadWeb3()
            await this.loadAccount(false)
            if (this.account) {
                await this.loadContract()
            }

            this.collectionID = this.$el.getAttribute('data-id')
            this.pinataApiKey = 'cd220a3a08e90e4b069b';
            this.pinataSecretApiKey = 'b7c2860f8ee129311e684e525972f7f8c7d0e2eef1a8d70bf60b953013be620a';
        },
    
        methods: {
            uploadToPinata: async function(files) {
                const url = 'https://api.pinata.cloud/pinning/pinFileToIPFS'
            
                console.log(files);
                // let data = new FormData()
                // for (var i = 0; i < files.length; i++) {
                //     var file = files[i]
                //     data.append(`file`, file)
                // }
        
                // return axios.post(url, data, {
                //     maxBodyLength: 'Infinity', // This is needed to prevent axios from erroring out with large directories
                //     headers: {
                //         'Content-Type': `multipart/form-data; boundary=${data._boundary}`,
                //         pinata_api_key: this.pinataApiKey,
                //         pinata_secret_api_key: this.pinataSecretApiKey
                //     }
                // })
                // .then(function (response) {
                //     console.log('response', response)
                //     // Handle response here
                //     if (response.status == 200) {
                //         // Upload JSON

                //     }
                // })
                // .catch(function (error) {
                //     console.log('error', error)
                //     // Handle error here
                // })
            },

            validateFiles: function(files) {
                
            },

            generatePinataKeys: async function() {
                const url = `https://api.pinata.cloud/users/generateApiKey`;
                const body = {
                    keyName: 'Example Key',
                    permissions: {
                        endpoints: {
                            data: {
                                userPinnedDataTotal: true
                            },
                            pinning: {
                                pinJobs: true,
                                unpin: true,
                                userPinPolicy: true
                            }
                        }
                    }
                }
                return axios.post(url, body, {
                    headers: {
                        pinata_api_key: pinataApiKey,
                        pinata_secret_api_key: pinataSecretApiKey
                    }
                })
                .then(function (response) {
                    //handle response here
                })
                .catch(function (error) {
                    //handle error here
                })
            },

            switchBlockchain: function() {
                console.log('switchBlockchain');
                this.message.error = 'Something went wrong while deploying the smart contract, please contact us so we can investigate this issue.'
            },

            connectWallet: async function() {
                await this.loadAccount(true)
                await this.loadContract()
            },

            uploadCollection: function(event) {
                console.log('Uploaded collection')
                var files = event.target.files
                console.log('files', files);

                this.uploadToPinata(files)

                // axios.post('/collections/'+this.collectionID+'/upload', 
                //     this.prepareCollectionForUpload(files)
                // ).then(function(response) {
                //     console.log(response)
                // })
            },

            prepareCollectionForUpload: function(files) {
                var formData = new FormData() 
                for (var i = 0; i < files.length; i++) {
                    var file = files[i]
                    // formData.append('files[' + i + ']', file) // is the var i against the var j, because the i is incremental the j is ever 0
                    formData.append('paths[' + i + ']', file.webkitRelativePath) // is the var i against the var j, because the i is incremental the j is ever 0
                }
                return formData
            },

            deployContract: async function() {

                if (confirm("Deploying this smart contract is irreversible. Are your sure?") == true) {
                    console.log('deployContract')

                    const signer = this.provider.getSigner()
                    const factory = new ContractFactory(contractAbi, contractByteCode, signer)
    
                    try {
                        const contract = await factory.deploy('DazedDucks', 'DD', 3, 1653994136)
    
                        console.log('response 1 deployContract', contract.address)
                        console.log('response 2 deployContract', JSON.stringify(contract.deployTransaction))

                        axios.post('/collections/'+this.collectionID+'/deployed', {
                            address: contract.address
                        }).then(function(response) {
                            console.log(response)
                        })
                    } catch (e) {
                        this.message.error = 'Something went wrong while deploying the smart contract, please contact us so we can investigate this issue.'
                    }
                } else {
                    
                }
            },

            setBaseURI: async function() {
                console.log('setBaseURI')

                if (this.form.baseURI != '') {
                    var baseURI = await this.contract.setBaseURI(this.form.baseURI)
                    console.log('response setBaseURI', baseURI)
                }
            },

            getBaseURI: async function() {
                console.log('getBaseURI')

                var baseURI = await this.contract.baseTokenURI()
                console.log('response getBaseURI', baseURI)
                alert(baseURI)
            },

            loadContract: async function() {
                console.log('Loading contract')
                const provider = new ethers.providers.Web3Provider(window.ethereum)
                const signer = provider.getSigner()
                this.contract = new ethers.Contract(contractAddress, contractAbi, signer)
            },

            setProvider: async function() {
                this.provider = new ethers.providers.Web3Provider(window.ethereum, "any")
                // this.provider = await detectEthereumProvider()

                if (this.provider) {
                    // From now on, this should always be true:
                    // provider === window.ethereum
                } else {
                    console.log('Please install MetaMask!')
                }

                this.provider.on("pending", function(e) {
                    console.log(e)
                })
            },

            loadAccount: async function(triggerRequest) {
                var requestAccount = false
                try {
                    const signer = this.provider.getSigner()
                    this.account = await signer.getAddress()
                } catch (e) {
                    console.log('ERROR', e.message)
                    requestAccount = true
                }

                if (window.ethereum && requestAccount && triggerRequest) {
                    try {
                        this.accounts = await ethereum.request({method: 'eth_requestAccounts'})
                    } catch(e) {
                        if (e.code == -32002) {
                            this.message.error = 'A connect request is already pending, open your wallet and confirm the request.'
                        }
                    }
                    if (this.accounts.length > 0) {
                        this.account = this.accounts[0]
                    }
                }                
            },

            loadWeb3: async function() {
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
        }
    })
}