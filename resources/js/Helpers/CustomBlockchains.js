export let LineaMainnet = {
  "name": "Linea Mainnet",
  "title": "Linea Goerli Testnet",
  "chain": "ETH",
  "rpc": [
    "https://rpc.linea.build/"
  ],
  "faucets": [],
  "nativeCurrency": {
    "name": "Linea Ether",
    "symbol": "ETH",
    "decimals": 18
  },
  "infoURL": "https://linea.build",
  "shortName": "linea-mainnet",
  "chainId": 59144,
  "networkId": 59144,
  "icon": {
    "url": "ipfs://QmURjritnHL7a8TwZgsFwp3f272DJmG5paaPtWDZ98QZwH",
    "width": 97,
    "height": 102,
    "format": "svg"
  },
  "parent": {
    "type": "L2",
    "chain": "eip155-5",
    "bridges": [
      {
        "url": "https://goerli.hop.exchange/#/send?token=ETH&sourceNetwork=ethereum&destNetwork=linea"
      }
    ]
  },
  "explorers": [
    {
      "name": "blockscout",
      "url": "https://explorer.goerli.linea.build",
      "standard": "EIP3091",
      "icon": {
        "url": "ipfs://QmURjritnHL7a8TwZgsFwp3f272DJmG5paaPtWDZ98QZwH",
        "width": 97,
        "height": 102,
        "format": "svg"
      }
    }
  ],
  "status": "active",
  "testnet": false,
  "slug": "linea-mainnet"
}