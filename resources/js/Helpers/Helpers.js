import { getMetaMaskError } from '@/Wallets/MetaMask'
import { ethers } from 'ethers'
import { toRaw, unref } from 'vue'
import { reportError } from './Sentry'
import BigNumber from "bignumber.js";

export function ipfsToUrl(url) {
    url = url.replace('ipfs://', '')
    // url = 'https://ipfs-public.thirdwebcdn.com/ipfs/'+url
    url = 'https://ipfs.io/ipfs/'+url
    return url
}

export function shortenWalletAddress(address) {
    return address ? address.substring(0, 6)+'...'+address.substr(address.length - 4) : ''
}

export function copyToClipboard(e) {
    var text = e.target.innerHTML
    e.target.style.width = e.target.offsetWidth
    e.target.textContent = 'Copied'
    setTimeout(function() {
        e.target.style.width = ''
        e.target.innerHTML = text
    }, 1000)
    navigator.clipboard.writeText(e.target.getAttribute('text'))
}

export function getSelectInputBlockchainObject(blockchains) {
    let output = {'Mainnets': {}, 'Testnets': {}}

    for (const key in blockchains.value) {
        const blockchain = blockchains.value[key]
        output[(blockchain.testnet == false ? 'Mainnets' : 'Testnets')][blockchain.chainId] = blockchain.name
    }

    return output
}

export function formateDatetimeLocal(datetime) {
    var d = new Date(datetime)
    var year = d.getFullYear()
    var month = getDoubleDigitNumber(d.getMonth() + 1)
    var day = getDoubleDigitNumber(d.getDate())
    var hours = getDoubleDigitNumber(d.getHours())
    var minutes = getDoubleDigitNumber(d.getMinutes())
    // var seconds = this.getDoubleDigitNumber(d.getSeconds())
    var date = year + '-' + month + '-' + day
    var time = hours + ':' + minutes

    return date + 'T' + time
}

export function getDoubleDigitNumber(number) {
    return number < 10 ? '0'+number : number
}

export function parseClaimConditions(claimConditions) {
    var output = []

    for (var i = 0; i < claimConditions.length; i++) {
        var cc = claimConditions[i]
        var nextIndex = i + 1
        var nextCc = nextIndex > claimConditions.length ? false : claimConditions[nextIndex]
        var hasNoWhitelist = (cc.snapshot == undefined || cc.snapshot.length == 0)
        output.push({
            id: nextIndex,
            name: cc.metadata != undefined && typeof cc.metadata.name !== 'undefined' ? cc.metadata.name : 'Phase '+nextIndex,
            startTime: formateDatetimeLocal(cc.startTime),
            endTime: nextCc ? formateDatetimeLocal(nextCc.startTime) : false,
            price: hexToValue(cc.price),
            maxClaimableSupply: cc.maxClaimableSupply == 'unlimited' ? 0 : parseInt(cc.maxClaimableSupply),
            maxClaimablePerWallet: hasNoWhitelist ? (cc.maxClaimablePerWallet == 'unlimited' ? 0 : parseInt(cc.maxClaimablePerWallet)) : (cc.snapshot[0].maxClaimable == 'unlimited' ? 0 : parseInt(cc.snapshot[0].maxClaimable)),
            // waitInSeconds: parseInt(cc.waitInSeconds) == 5 ? 1 : 0, // Contract v2, Contract v3
            whitelist: hasNoWhitelist ? 0 : 1,
            snapshot: cc.snapshot ?? [],
            modal: false,
            countdown: ''
        })
    }

    return output
}

export function hexToNumber(hex) {
    return parseInt(hex, 16)
}

export function WeiToValue(wei) {
    return ethers.utils.formatEther(wei.toString())
    // return wei / 1000000000000000000
}

export function hexToValue(hex) {
    // return WeiToValue(parseInt(hex, 16))
    return WeiToValue(hex.toString())
}

export function objectToRgba(object, opacity) {
    object = toRaw(object)
    if (object.rgba) {
        object = object.rgba
    }
    return 'rgba('+parseInt(object['r'])+', '+parseInt(object['g'])+', '+parseInt(object['b'])+', '+opacity+')';
}

export function setStyling(collectionData) {
    var css = ''
    
    // Set primary colors
    const primary = collectionData.theme.primary
    
    css += '.mint-text-primary { color: '+objectToRgba(primary, '1')+' !important; } '
    css += '.mint-bg-primary { background-color: '+objectToRgba(primary, '1')+' !important; } '
    css += '.mint-bg-primary:hover { background-color: '+objectToRgba(primary, '0.9')+' !important; } '
    css += '.mint-bg-primary-sm { background-color: '+objectToRgba(primary, '0.25')+' !important; } '
    css += '.mint-bg-primary-md { background-color: '+objectToRgba(primary, '0.5')+' !important; } '
    css += '.mint-bg-primary-lg { background-color: '+objectToRgba(primary, '0.75')+' !important; } '

    // Set background colors
    if (collectionData.theme.background) {
        const background = collectionData.theme.background
        css += '.mint-bg-box { background-color: '+objectToRgba(background, '1')+' !important; } '
    }

    // Set phase background colors
    if (collectionData.theme.phases) {
        const phases = collectionData.theme.phases
        css += '.mint-bg-phase { background-color: '+objectToRgba(phases, '1')+' !important; } '
    }

    let styleTag = document.createElement('style')
    styleTag.appendChild(document.createTextNode(css))
    document.head.appendChild(styleTag)
}

export function getDummyCollection() {
    const collection = {
        totalSupply: 1000,
        totalClaimedSupply: 256,
        totalRatioSupply: Math.round((100/256)*100),
        royalties: '7.5%',
        chainName: 'Ethereum'
    }
    const claimPhases = [
        {
            id: 1,
            name: 'Premium whitelist',
            price: 0.1,
            maxClaimableSupply: 100,
            maxClaimablePerWallet: 1,
            whitelist: 1,
            snapshot: Array.from(Array(100).keys()),
            countdown: '',
            active: false
        },
        {
            id: 2,
            name: 'Whitelist',
            price: 0.2,
            maxClaimableSupply: 300,
            maxClaimablePerWallet: 3,
            whitelist: 1,
            snapshot: Array.from(Array(300).keys()),
            countdown: '',
            active: true
        },
        {
            id: 3,
            name: 'Public',
            price: 0.2,
            maxClaimableSupply: 600,
            maxClaimablePerWallet: 0,
            whitelist: 0,
            snapshot: [],
            countdown: '',
            active: false
        }
    ]
    const timers = {
        0: Infinity,
        1: {state: 'Ends', days: '00', hours: '11', minutes: '22', seconds: '33'},
        2: {state: 'Starts', days: '00', hours: '11', minutes: '22', seconds: '33'},
    }
    return {collection, claimPhases, timers}
}

export function getAllowedNFTTypes() {
    return [
        'video/mp4', 
        'image/jpeg', 
        'image/png', 
        'image/jpg', 
        'image/gif'
    ]
}

export function fileIsImage(file) {
    const allowedTypes = [
        'image/jpeg', 
        'image/png', 
        'image/jpg', 
        'image/gif'
    ]
    const allowedExtensions = [
        'jpg',
        'jpeg',
        'png',
        'gif'
    ]
    if (typeof file === 'object') {
        if ('type' in file) {
            return allowedTypes.includes(file.type)
        } else if('src' in file) {
            const url = file.src.toLowerCase().split('?')[0]
            const extension = url.toLowerCase().split('.').pop()
            return allowedExtensions.includes(extension)
        }
    } else {
        const url = file.toLowerCase().split('?')[0]
        const extension = url.toLowerCase().split('.').pop()
        return allowedExtensions.includes(extension)
    }
}

export function fileIsVideo(file) {
    const allowedTypes = [
        'video/mp4'
    ]
    const allowedExtensions = [
        'mp4',
    ]

    if (typeof file === 'object') {
        if ('type' in file) {
            return allowedTypes.includes(file.type)
        } else if('src' in file) {
            const url = file.src.toLowerCase().split('?')[0]
            const extension = url.toLowerCase().split('.').pop()
            return allowedExtensions.includes(extension)
        }
    } else {
        const url = file.toLowerCase().split('?')[0]
        const extension = url.toLowerCase().split('.').pop()
        return allowedExtensions.includes(extension)
    }
}

export function formatTransactionFee(fee) {
    const feeData = parseFloat(fee).toString().split('.')
    if (feeData[1] && feeData[1].length > 18) {
        feeData[1] = feeData[1].slice(0, 18)
        fee = feeData.join('.')
    }
    
    return ethers.utils.parseUnits(fee.toString(), 18).toString()
}

export function calculateTransactionFee(fee, price) {
    // var feeResult = fee * 1000000000
    // var priceResult = price * 1000000000
    // var total = ((feeResult + priceResult) / 1000000000).toString()
    // const parsed = ethers.utils.parseUnits(total).toString()

    var feeResult = BigNumber(fee)
    var total = feeResult.plus(price)
    var parsed = ethers.utils.parseUnits(total.toString()).toString()

    return ethers.BigNumber.from(parsed)
}

export function handleError(error) {
    // console.log('error', error)
    let metamaskError = getMetaMaskError(error)
    if (metamaskError) {
        return metamaskError
    } else {
        reportError(error)
        return 'Something went wrong, please try again.'
    }
}