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

export async function getSmartContract(chainId, address) {
    const sdk = setSDK(chainId)
    return await sdk.getContract(address, 'nft-drop')
}

export async function getSmartContractFromSigner(signer, chainId, address) {
    const sdk = getSDKFromSigner(toRaw(signer), chainId)
    return await sdk.getContract(address, 'nft-drop')
}

export async function getCollectionData(contract, withAllowList, withNfts) {
    try {
        // metadata
        const metadata = await contract.metadata.get()
    
        // royalties
        const royalties = await contract.royalties.getDefaultRoyaltyInfo()
    
        // Fees
        const platformFees = await contract.platformFees.get()
    
        // Sales
        const primarySalesRecipient = await contract.sales.getRecipient()
    
        // Claim phases
        const claimConditions = await contract.claimConditions.getAll({withAllowList: withAllowList})
        // const activeClaimCondition = await contract.claimConditions.getActive()
    
        // Collection
        const totalSupply = await contract.totalSupply()
        const totalClaimedSupply = await contract.totalClaimedSupply()
        const totalRatio = Math.round((totalClaimedSupply/totalSupply)*100)
        const nfts = withNfts ? await contract.getAll({count: 8}) : []
    
        return {
            metadata: {
                name: metadata.name,
                description: metadata.description
            },
            royalties: {
                feeRecipient: royalties.fee_recipient,
                royalties: royalties.seller_fee_basis_points / 100
            },
            platformFees: {
                platformFee: platformFees.platform_fee_basis_points / 100,
                platformFeeRecipient: platformFees.platformee_recipient
            },
            sales: {
                primarySalesRecipient: primarySalesRecipient
            },
            claimConditions: claimConditions,
            nfts: nfts,
            totalSupply: totalSupply,
            totalClaimedSupply: totalClaimedSupply,
            totalRatioSupply: totalRatio
        }
    } catch(error) {
        console.log(error)
        return false
    }
}