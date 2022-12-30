const ThirdwebSDK = require('@thirdweb-dev/sdk')
const { readFileSync, existsSync, writeFileSync } = require("fs");

const getClaimConditions = async (collection) => {
    const sdk = new ThirdwebSDK.ThirdwebSDK(collection.chain)
    const contract = await sdk.getContract(collection.address, 'nft-drop')
    const claimConditions = await contract.claimConditions.getAll({withAllowList: false})

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