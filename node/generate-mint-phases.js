const ThirdwebSDK = require('@thirdweb-dev/sdk')
const { readFileSync, existsSync, writeFileSync } = require("fs");

const getContractTypeName = (contractType) => {
    switch (contractType) {
        case 'ERC721': return 'nft-drop'
        case 'ERC1155': return 'edition-drop'
        case 'ERC1155Burn': return 'edition-drop'
        default: return 'nft-drop'
    }
}

const getClaimConditions = async (collection) => {
    const sdk = new ThirdwebSDK.ThirdwebSDK(collection.chain)
    const contract = await sdk.getContract(collection.address, getContractTypeName(collection.type))
    var claimConditions = []
    if (contract.constructor.name == 'NFTDrop') {
        claimConditions = await contract.claimConditions.getAll({withAllowList: false})
    } else if (contract.constructor.name == 'EditionDrop') {
        claimConditions = await contract.claimConditions.getAll(0, {withAllowList: false})
    }
    output = []
    claimConditions.forEach((claimCondition) => {
        output.push({id: collection.id, address: collection.address, when: claimCondition.startTime, price: claimCondition.currencyMetadata.displayValue})
    })
    return output
}

const execute = async () => {
    const collectionsPath = './storage/app/data/collections.json'
    if (existsSync(collectionsPath)) {
        let collectionsObject = readFileSync(collectionsPath)
        const collections = JSON.parse(collectionsObject)

        let promises = []
        collections.forEach((collection) => {
            promises.push(getClaimConditions(collection))
        })

        let output = await Promise.all(promises)
        const json = JSON.stringify(output.flat());

        writeFileSync('./storage/app/data/mint-phases.json', json, 'utf8', function (err) {}); 
    }
}

execute()