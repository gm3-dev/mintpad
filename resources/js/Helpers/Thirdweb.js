import { ThirdwebSDK } from '@thirdweb-dev/sdk'
import { toRaw } from 'vue'
import { getBlockchains } from './Blockchain'
import { reportError } from './Sentry'

export function setSDK(chainId) {
    const blockchains = getBlockchains()
    const blockchain = blockchains[chainId]

    return new ThirdwebSDK(blockchain, {
        clientId: import.meta.env.VITE_TW_CLIENT_ID,
    })
}

export function getSDKFromSigner(signer, chainId) {
    const blockchains = getBlockchains()
    const blockchain = blockchains[chainId]

    return ThirdwebSDK.fromSigner(toRaw(signer), blockchain, {
        clientId: import.meta.env.VITE_TW_CLIENT_ID,
    })
}

export async function getSmartContract(chainId, address, contractType) {
    const sdk = setSDK(chainId)
    return await sdk.getContract(address, getContractTypeName(contractType))
}

export async function getSmartContractFromSigner(signer, chainId, address, contractType) {
    const sdk = getSDKFromSigner(toRaw(signer), chainId)
    return await sdk.getContract(address, getContractTypeName(contractType))
}

function getContractTypeName(contractType) {
    switch (contractType) {
        case 'ERC721': return 'nft-drop'
        case 'ERC1155': return 'edition-drop'
        case 'ERC1155Burn': return 'edition-drop'
        default: return 'nft-drop'
    }
}

export async function verifyContract(signer) {
    const sdk = getSDKFromSigner(toRaw(signer), 256256)

    // const response = await sdk.verifier.verifyContract(
    //     '0x9370da428Ba84677512D6F5ab49b6315e51e20f0',
    //     'https://cmpscan.io/api',
    //     '7sprwe3gTZ1nzjZrZ8PZkWeLgc91',
    // );
}

export async function getCollectionData(contract, contractType, withAllowList, withNfts, tokenID) {
    tokenID = tokenID == undefined ? 0 : tokenID

    let output = {
        metadata: {
            name: ''
        },
        royalties: {
            feeRecipient: '',
            royalties: 0
        },
        sales: {
            primarySalesRecipient: ''
        },
        nftsToBurn: false,
        claimConditions: [],
        nfts: [],
        totalSupply: false,
        totalClaimedSupply: false,
        totalRatioSupply: false
    }
    try {
        // metadata
        const metadata = await contract.metadata.get()
    
        // royalties
        const royalties = await contract.royalties.getDefaultRoyaltyInfo()
    
        // Burn
        let nftsToBurn = 0;
        if (contractType == 'ERC1155Burn') {
            nftsToBurn = await contract.call('numberToBurn')
        }
    
        // Sales
        const primarySalesRecipient = await contract.sales.getRecipient()
    
        // Claim phases
        let claimConditions = await getClaimPhases(contract, contractType, withAllowList, tokenID)
        // const activeClaimCondition = await contract.claimConditions.getActive()
    
        // NFTs
        const nfts = withNfts !== false ? await contract.getAll({count: withNfts}) : []

        // Collection
        const totals = await getTotalsData(contract, contractType, tokenID)
    
        output = {
            metadata: {
                name: metadata.name
            },
            royalties: {
                feeRecipient: royalties.fee_recipient,
                royalties: royalties.seller_fee_basis_points / 100
            },
            sales: {
                primarySalesRecipient: primarySalesRecipient
            },
            nftsToBurn: nftsToBurn,
            claimConditions: claimConditions,
            nfts: nfts,
            totalSupply: totals.totalSupply,
            totalClaimedSupply: totals.totalClaimedSupply,
            totalRatioSupply: totals.totalRatio
        }
    } catch(error) {
        reportError(error)
    }
    return output
}

export async function getClaimPhases(contract, contractType, withAllowList, tokenID) {
    let claimConditions = []
    if (contractType == 'ERC721') {
        claimConditions = await contract.claimConditions.getAll({withAllowList: withAllowList})
    } else if (contractType.startsWith('ERC1155')) {
        claimConditions = await contract.claimConditions.getAll(tokenID, {withAllowList: withAllowList})
    }

    return claimConditions
}

export async function getTotalsData(contract, contractType, tokenID) {
    if (contractType == 'ERC721') {
        var totalSupply = await contract.totalSupply()
        var totalClaimedSupply = await contract.totalClaimedSupply()
        var totalRatio = Math.round((totalClaimedSupply/totalSupply)*100)
    } else if (contractType.startsWith('ERC1155')) {
        var totalSupply = await contract.call('maxTotalSupply', [tokenID], {})
        var totalClaimedSupply = await contract.totalSupply(tokenID)
        var totalRatio = Math.round((totalClaimedSupply/totalSupply)*100)
    }

    return {
        totalSupply: totalSupply,
        totalClaimedSupply: totalClaimedSupply,
        totalRatio: totalRatio
    }
}