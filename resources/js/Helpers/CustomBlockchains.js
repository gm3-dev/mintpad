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

export let AbstractTestnet = {
  "name": "Abstract Testnet",
  "chain": "ABS",
  "status": "active",
  "icon": {
    "url": "ipfs://QmWeBU36AH2DX9VA2GToMqtJzcdTtWprDK1urafxa8n61C", 
    "width": 100,
    "height": 100,
    "format": "png"
  },
  "rpc": [
    "https://api.testnet.abs.xyz"
  ],
  "ws": [
    "ws://api.testnet.abs.xyz/ws"
  ],
  "faucets": [],
  "nativeCurrency": {
    "name": "Ether",
    "symbol": "ETH",
    "decimals": 18
  },
  "infoURL": "https://explorer.testnet.abs.xyz",
  "shortName": "abs-testnet",
  "chainId": 11124,
  "networkId": 11124,
  "explorers": [
    {
      "name": "Abstract Explorer",
      "url": "https://explorer.testnet.abs.xyz",
      "standard": "EIP3091"
    }
  ],
  "verifyURL": "https://api-explorer-verify.testnet.abs.xyz/contract_verification",
  "testnet": true,
  "slug": "abstract-testnet"
}