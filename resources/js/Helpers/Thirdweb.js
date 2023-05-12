import { ThirdwebSDK } from '@thirdweb-dev/sdk'
import { toRaw } from 'vue'
import { getBlockchains } from './Blockchain'

export function setSDK(chainId) {
    const blockchains = getBlockchains()
    const blockchain = blockchains[chainId]

    return new ThirdwebSDK(blockchain)
}

export function getSDKFromSigner(signer, chainId) {
    const blockchains = getBlockchains()
    const blockchain = blockchains[chainId]

    return ThirdwebSDK.fromSigner(toRaw(signer), blockchain, {})
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
        case 'ERC1155Evolve': return 'edition-drop'
        default: return 'nft-drop'
    }
}

export async function getCollectionData(contract, contractType, withAllowList, withNfts) {
    try {
        // metadata
        const metadata = await contract.metadata.get()
    
        // royalties
        const royalties = await contract.royalties.getDefaultRoyaltyInfo()
    
        // Burn
        let nftsToBurn = 0;
        if (contractType.startsWith('ERC1155')) {
            nftsToBurn = await contract.call('numberToBurn')
        }
    
        // Sales
        const primarySalesRecipient = await contract.sales.getRecipient()
    
        // Claim phases
        let claimConditions = []
        if (contractType == 'ERC721') {
            claimConditions = await contract.claimConditions.getAll({withAllowList: withAllowList})
        } else if (contractType.startsWith('ERC1155')) {
            claimConditions = await contract.claimConditions.getAll(0, {withAllowList: withAllowList})
        }
        // const activeClaimCondition = await contract.claimConditions.getActive()
    
        // Collection
        if (contractType == 'ERC721') {
            var totalSupply = await contract.totalSupply()
            var totalClaimedSupply = await contract.totalClaimedSupply()
            var totalRatio = Math.round((totalClaimedSupply/totalSupply)*100)
        } else if (contractType.startsWith('ERC1155')) {
            var totalSupply = await contract.call('maxTotalSupply', 0)
            var totalClaimedSupply = await contract.totalSupply(0)
            var totalRatio = Math.round((totalClaimedSupply/totalSupply)*100)
        }
        const nfts = withNfts ? await contract.getAll({count: 8}) : []
    
        return {
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
            totalSupply: totalSupply,
            totalClaimedSupply: totalClaimedSupply,
            totalRatioSupply: totalRatio
        }
    } catch(error) {
        return false
    }
}