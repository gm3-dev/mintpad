export let TaikoJolnir = {
  "name": "Taiko Jolnir L2",
  "chain": "ETH",
  "status": "active",
  "icon": {
    "url": "ipfs://QmcHdmVr5VRUJq13jnM6tgah5Ge7hn3Dm14eY6vwivJ5ui",
    "width": 288,
    "height": 258,
    "format": "png"
  },
  "rpc": [
    "https://rpc.jolnir.taiko.xyz"
  ],
  "faucets": [],
  "nativeCurrency": {
    "name": "Ether",
    "symbol": "ETH",
    "decimals": 18
  },
  "infoURL": "https://taiko.xyz",
  "shortName": "taiko-jolnir-l2",
  "chainId": 167007,
  "networkId": 167007,
  "explorers": [
    {
      "name": "blockscout",
      "url": "https://explorer.jolnir.taiko.xyz",
      "standard": "EIP3091"
    }
  ],
  "testnet": true,
  "slug": "taiko-jolnir-l2"
}

export let BlastL2 = {
  "name": "Blast L2",
  "chain": "BLAST",
  "status": "active",
  "icon": {
    "url": "ipfs://bafybeifc2h3x7jgy4x4nmg2m54ghbvmkfu6oweujambwefzqzew5vujhsi",
    "width": 400,
    "height": 400,
    "format": "png"
  },
  "rpc": [
    "https://blast.din.dev/rpc"
  ],
  "faucets": [],
  "nativeCurrency": {
    "name": "Ether",
    "symbol": "ETH",
    "decimals": 18
  },
  "infoURL": "https://blast.io",
  "shortName": "blast-l2",
  "chainId": 81457,
  "networkId": 81457,
  "explorers": [
    {
      "name": "Blastscan",
      "url": "https://blastscan.io",
      "standard": "EIP3091"
    }
  ],
  "testnet": false,
  "slug": "blast-l2"
}


// export let AbstractTestnet = {
//   "name": "Abstract Chain Testnet",
//   "chain": "ABSTRACT",
//   "status": "active",
//   "icon": {
//     "url": "",
//     "width": 100, 
//     "height": 100,
//     "format": "png"
//   },
//   "rpc": [
//     "https://api.testnet.abs.xyz"
//   ],
//   "faucets": [],
//   "nativeCurrency": {
//     "name": "Ether",
//     "symbol": "ETH",
//     "decimals": 18
//   },
//   "infoURL": "https://explorer.testnet.abs.xyz",
//   "shortName": "abstract-testnet",
//   "chainId": 11124,
//   "networkId": 11124,
//   "explorers": [
//     {
//       "name": "Abstract Explorer",
//       "url": "https://explorer.testnet.abs.xyz",
//       "standard": ""
//     }
//   ],
//   "testnet": true,
//   "slug": "abstract-testnet"
// }

export let WeaveEVMTestnet = {
  "name": "WeaveEVM Testnet",
  "chain": "WVM",
  "status": "active",
  "icon": {
    "url": "https://docs.wvm.dev/~gitbook/image?url=https%3A%2F%2F3863681628-files.gitbook.io%2F%7E%2Ffiles%2Fv0%2Fb%2Fgitbook-x-prod.appspot.com%2Fo%2Fspaces%252Fz2gd4Irh30FSnal6SJnL%252Ficon%252F72zLeEWT82SqUKwiPjwk%252FWVM_logo_lg.png%3Falt%3Dmedia%26token%3D92603341-4ca7-448a-98f2-406043283408&width=32&dpr=2&quality=100&sign=c7e7fb38&sv=1", // Replace with actual IPFS hash if available
    "width": 100, 
    "height": 100,
    "format": "png"
  },
  "rpc": [
    "https://testnet-rpc.wvm.dev"
  ],
  "rpcProxy": [
    "https://testnet.wvm.dev"
  ],
  "faucets": [],
  "nativeCurrency": {
    "name": "Test WVM",
    "symbol": "tWVM",
    "decimals": 18
  },
  "infoURL": "https://explorer.wvm.dev",
  "shortName": "wvm-testnet",
  "chainId": 9496,
  "networkId": 9496,
  "explorers": [
    {
      "name": "WVM Explorer",
      "url": "https://explorer.wvm.dev",
      "standard": "EIP3091"
    }
  ],
  "testnet": true,
  "slug": "wvm-testnet"
}

