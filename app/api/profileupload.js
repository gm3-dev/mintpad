const express = require('express');
const bodyParser = require('body-parser');
const mysql = require('mysql2');
const cors = require('cors');
const axios = require('axios');
const app = express();
const port = 6000;
//test
app.use(bodyParser.json());
const API_KEY = 'APUVAUXICC2927IVW8RDN4W6Q6FWTFNHV8'; 
const BLOCKSCOUT_API_URL = 'https://blockscoutapi.hekla.taiko.xyz/api';
app.use(cors({
    origin: 'http://127.0.0.1:8000',
    methods: ['POST','GET'],
    allowedHeaders: ['Content-Type'],
}));

const connection = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: 'Koireng@1',
    database: 'mydatabase'
});

connection.connect((err) => {
    if (err) {
        console.error('Error connecting to MySQL: ' + err.stack);
        return;
    }
    console.log('Connected to MySQL as id ' + connection.threadId);
});


app.post('/api/uploadImage', (req, res) => {
    const { name, base64Image } = req.body;

    if (!name || !base64Image) {
        return res.status(400).json({ error: 'Name and base64Image are required' });
    }

    const query = `UPDATE users SET profile_picture = ? WHERE name = ?`;

    connection.query(query, [base64Image, name], (err, results) => {
        if (err) {
            console.error('Error updating profile picture: ' + err.stack);
            return res.status(500).json({ error: 'Internal server error' });
        }
        console.log(`Profile picture updated for user: ${name}`);
        res.status(200).json({ message: 'Profile picture updated successfully' });
    });
});

// Get total number of mint from contracts

async function getTransactions(contractAddress, walletAddress) {
  try {
    const response = await axios.get(`${BLOCKSCOUT_API_URL}?module=account&action=txlist&address=${contractAddress}`);
    
    if (response.data && response.data.result) {
      const transactions = response.data.result;
      const filteredTransactions = transactions.filter(tx => 
        tx.from.toLowerCase() === walletAddress.toLowerCase() || 
        tx.to.toLowerCase() === walletAddress.toLowerCase()
      );
      const transactionCount = filteredTransactions.length;
      
      return transactionCount;
    } else {
      console.error('No transactions found');
      return 0;
    }
  } catch (error) {
    console.error('Error fetching transactions:', error);
    return 0;
  }
}

app.get('/getxn/:walletAddress/:contractAddress', async (req, res) => {
  const { contractAddress, walletAddress } = req.params;

  try {
    const count = await getTransactions(contractAddress, walletAddress);
    res.json({ count });
  } catch (error) {
    res.status(500).json({ error: 'Error fetching transactions' });
  }
});
// Endpoint to save player details
app.post('/api/playerdetails', (req, res) => {
  const { address, house, housetype, housename, latestactivity } = req.body;

  // Validate input
  if (!address || !house || !housetype || !housename || !latestactivity) {
    return res.status(400).json({ error: 'address, house, housetype, housename, and latestactivity are required' });
  }

  // Query to check if the address already exists
  const checkQuery = 'SELECT totalmint FROM taikocampaign WHERE address = ?';

  connection.query(checkQuery, [address], (err, results) => {
    if (err) {
      console.error('Error checking player details:', err.stack);
      return res.status(500).json({ error: 'Internal server error' });
    }

    if (results.length > 0) {
      // Player exists, increment totalmint
      const currentTotalMint = parseInt(results[0].totalmint, 10) || 0;
      const newTotalMint = currentTotalMint + 1;

      const updateQuery = 'UPDATE taikocampaign SET house = ?, housetype = ?, housename = ?, totalmint = ?, latestactivity = ? WHERE address = ?';
      connection.query(updateQuery, [house, housetype, housename, newTotalMint, latestactivity, address], (err) => {
        if (err) {
          console.error('Error updating player details:', err.stack);
          return res.status(500).json({ error: 'Internal server error' });
        }
        console.log(`Player details updated: ${address}, ${house}, ${housetype}, ${housename}, totalmint: ${newTotalMint}`);
        res.status(200).json({ message: 'Player details updated successfully' });
      });
    } else {
      // Player does not exist, insert new row with totalmint set to 1
      const insertQuery = 'INSERT INTO taikocampaign (address, house, housetype, housename, totalmint, latestactivity) VALUES (?, ?, ?, ?, 1, ?)';
      connection.query(insertQuery, [address, house, housetype, housename, latestactivity], (err) => {
        if (err) {
          console.error('Error saving player details:', err.stack);
          return res.status(500).json({ error: 'Internal server error' });
        }
        console.log(`Player details saved: ${address}, ${house}, ${housetype}, ${housename}, totalmint: 1`);
        res.status(200).json({ message: 'Player details saved successfully' });
      });
    }
  });
});




// Endpoint to create campaign collection
app.post('/createcampaign', (req, res) => {
    const { symbol, name, feeRecipient } = req.body;

    if (!symbol || !name || !feeRecipient) {
        return res.status(400).json({ error: 'symbol, name, and feeRecipient are required' });
    }

    // Check if the campaign already exists
    const checkQuery = 'SELECT * FROM taikocampaigncollection WHERE symbol = ?';

    connection.query(checkQuery, [symbol], (err, results) => {
        if (err) {
            console.error('Error checking campaign existence: ' + err.stack);
            return res.status(500).json({ error: 'Internal server error' });
        }

        if (results.length > 0) {
            // Campaign already exists
            return res.status(409).json({ error: 'Campaign already exists' });
        } else {
            // Insert new campaign into taikocampaigncollection
            const insertQuery = 'INSERT INTO taikocampaigncollection (symbol, name, feeRecipient) VALUES (?, ?, ?)';
            connection.query(insertQuery, [symbol, name, feeRecipient], (err) => {
                if (err) {
                    console.error('Error creating campaign collection: ' + err.stack);
                    return res.status(500).json({ error: 'Internal server error' });
                }
                console.log(`Campaign collection created: Symbol: ${symbol}, Name: ${name}, Fee Recipient: ${feeRecipient}`);
                res.status(200).json({ message: 'Campaign collection created successfully' });
            });
        }
    });
});

app.get('/collectiondetails', (req, res) => {
  //update
  // Query to get all collection details
  //more
  const query = 'SELECT name, symbol, type, address FROM collections';

  connection.query(query, (err, results) => {
      if (err) {
          console.error('Error fetching collection details: ' + err.stack);
          return res.status(500).json({ error: 'Internal server error' });
      }

      res.status(200).json(results);
  });
});


app.get('/getdetailstopone', (req, res) => {
    // Query to fetch all data from 'taikocampaign' table
    connection.query('SELECT * FROM taikocampaign', (error, results, fields) => {
      if (error) {
        console.error('Error fetching data from taikocampaign table:', error);
        res.status(500).json({ error: 'Internal server error' });
        return;
      }
  
      // Find the record with the highest totalmint
      let highestMintRecord = null;
      results.forEach((row) => {
        if (!highestMintRecord || parseInt(row.totalmint) > parseInt(highestMintRecord.totalmint)) {
          highestMintRecord = row;
        }
      });
  
      // Prepare response
      let response = {};
      if (highestMintRecord) {
        response = {
          data: highestMintRecord
        };
      } else {
        response = {
          message: 'No records found in the taikocampaign table.'
        };
      }
  
      // Send response
      res.json(response);
    });
  });
  
  app.get('/getdetails', async (req, res) => {
    // Default limit if not specified in query params
    const limit = req.query.limit ? parseInt(req.query.limit) : 20;

    try {
        const [results] = await connection.query(`
            SELECT * FROM taikocampaign
            ORDER BY totalmint DESC
            LIMIT ${limit}
        `);

        const response = results.length > 0
            ? results
                .sort((a, b) => b.totalmint - a.totalmint)
                .map((row, index) => ({
                    rank: index + 1,
                    wallet: row.address,
                    username: row.username,
                    rankScore: index + 1,
                    nfts: row.totalmint,
                    labels: row.categories,
                    avatar: `https://res.cloudinary.com/twdin/image/upload/v1719839745/avatar-example_mc0r1g.png`,
                    opensea: row.opensea,
                    twitter: row.twitter,
                    blockscan: row.Blockscan,
                    profile: row.profilepic,
                    activity: row.latestactivity,
                }))
            : { message: 'No records found in the taikocampaign table.' };

        res.json(response);
    } catch (error) {
        console.error('Error fetching data from taikocampaign table:', error);
        res.status(500).json({ error: 'Internal server error' });
    }
});

// Get holder address along with contract address

async function fetchCollectionDetails() {
  try {
      const response = await axios.get(collectionDetailsUrl);
      const collections = response.data;

      const contracts = [];

      let counter = 1;

      for (const collection of collections) {
          const contractAddress = collection.address;

          const tokenHoldersUrl = `${tokenHoldersBaseUrl}?module=token&action=getTokenHolders&contractaddress=${contractAddress}&page=1&offset=100`;
          const tokenHoldersResponse = await axios.get(tokenHoldersUrl);

          const tokenHolders = tokenHoldersResponse.data.result;
          contracts.push({
              number: counter,
              address: contractAddress,
              holders: tokenHolders.map(holder => holder.address)
          });

          counter++;
      }
      return {
          contracts: contracts
      };
  } catch (error) {
      console.error('Error fetching data:', error);
      return { error: 'Failed to fetch data' };
  }
}

app.get('/getholderaddress', async (req, res) => {
  const data = await fetchCollectionDetails();
  res.json(data);
});

  //testcase hekla

  app.get('/checktxnhekla/:txnhash', async (req, res) => {
    const txnHash = req.params.txnhash;
    const apiUrl = `https://blockscoutapi.hekla.taiko.xyz/api?module=transaction&action=gettxreceiptstatus&txhash=${txnHash}`;

    try {
        const response = await axios.get(apiUrl);
        const status = response.data.result.status; // Assuming the API returns a JSON with result.status field

        if (status === "1") {
            res.json({ message: "success" });
        } else {
            res.json({ message: "failed" });
        }
    } catch (error) {
        console.error('Error fetching transaction status:', error);
        res.status(500).json({ error: 'Failed to fetch transaction status' });
    }
});



// Main-net ready api endpoint

app.get('/checktxn/:txnhash', async (req, res) => {
  const { txnhash } = req.params;

  try {
    const response = await axios.get('https://api.taikoscan.io/api', {
      params: {
        module: 'transaction',
        action: 'gettxreceiptstatus',
        txhash: txnhash,
        apikey: API_KEY,
      },
    });

    const resultStatus = response.data.result.status;

    if (resultStatus === '0') {
      res.status(200).json({ status: 'failed' });
    } else if (resultStatus === '1') {
      res.status(200).json({ status: 'success' });
    } else {
      res.status(200).json({ status: 'unknown' }); // Handle other statuses as needed
    }
  } catch (error) {
    console.error('Error fetching transaction status:', error);
    res.status(500).json({ error: 'Failed to fetch transaction status' });
  }
});


app.get('/totalTokens/:contractAddress', async (req, res) => {
  const { contractAddress } = req.params;
  try {
      const response = await axios.get(`${BASE_URL}/?module=token&action=getToken&contractaddress=${contractAddress}&apikey=${API_KEY}`);
      const totalTokens = response.data.result;
      res.json({ totalTokens });
  } catch (error) {
      console.error('Error fetching total tokens:', error);
      res.status(500).json({ error: 'Failed to fetch total tokens' });
  }
});

app.get('/getprofile/:address', (req, res) => {
  const address = req.params.address;

  // Query to fetch profilepic data
  const query = 'SELECT profilepic FROM taikocampaign WHERE address = ?';

  connection.query(query, [address], (error, results, fields) => {
      if (error) {
          console.error('Error querying database: ', error);
          res.status(500).json({ error: 'Error querying database' });
          return;
      }

      if (results.length > 0 && results[0].profilepic) {
          const profileData = {
              data: Array.from(results[0].profilepic)
          };
          res.json(profileData);
      } else {
          res.status(404).json({ error: 'No profile picture found' });
      }
  });
});

app.get('/checkmost', async (req, res) => {
  try {
    // Fetch all addresses, name, type, symbol, and totalSupply from collections table
    connection.query('SELECT address, name, type, symbol FROM collections', async (error, results, fields) => {
      if (error) {
        console.error('Error fetching data from collections table:', error);
        res.status(500).json({ error: 'Internal server error' });
        return;
      }

      // Array to store results
      const tokenInfos = [];

      // Iterate over each row in results
      for (let i = 0; i < results.length; i++) {
        const { address, name, type, symbol } = results[i];
        const apiUrl = `https://blockscoutapi.hekla.taiko.xyz/api?module=token&action=getToken&contractaddress=${address}`;

        try {
          const response = await axios.get(apiUrl);
          const tokenInfo = response.data.result; // Adjust according to API response structure

          if (tokenInfo) {
            tokenInfos.push({
              address,
              name,
              type,
              symbol,
              tokenInfo // Include token information
            });
          } else {
            tokenInfos.push({
              address,
              name,
              type,
              symbol,
              message: "Token not found"
            });
          }
        } catch (error) {
          console.error(`Error fetching token information for address ${address}:`, error);
          tokenInfos.push({
            address,
            name,
            type,
            symbol,
            error: 'Failed to fetch token information'
          });
        }
      }


      tokenInfos.sort((a, b) => {
    
        const totalSupplyA = Number(a.tokenInfo?.totalSupply) || 0;
        const totalSupplyB = Number(b.tokenInfo?.totalSupply) || 0;
        return totalSupplyB - totalSupplyA;
      });


      const top5Tokens = tokenInfos.slice(0, 5);

      res.json({ tokenInfos: top5Tokens });
    });
  } catch (error) {
    console.error('Error in /checkmost endpoint:', error);
    res.status(500).json({ error: 'Internal server error' });
  }
});


async function fetchTransactions(contractAddress) {
  try {
    const response = await axios.get(`${BLOCKSCOUT_API_URL}?module=account&action=txlist&address=${contractAddress}`);
    // console.log('API Response:', response.data);
// Too much of log

    if (!response.data || !response.data.result || !Array.isArray(response.data.result)) {
      throw new Error('Invalid API response structure');
    }

    const transactions = response.data.result
      .filter(tx => tx.input.startsWith('0x84bb1e42')) // Filter transactions that have input/method of '0x84bb1e42'
      .map(tx => ({
        to: tx.to,
        from: tx.from,
        // hash: tx.hash, // Add hash just if needed to verify
        input: tx.input.substring(0, 10) // Display first 10 characters
      }));

    return transactions;
  } catch (error) {
    console.error('Error fetching transactions:', error.message);
    throw error;
  }
}

const collectionDetailsUrl = 'http://127.0.0.1:6000/collectiondetails';
const tokenHoldersBaseUrl = 'https://blockscoutapi.hekla.taiko.xyz/api';

// Fetch collection details and token holders
async function fetchCollectionDetails() {
  try {
    const response = await axios.get(collectionDetailsUrl);
    const collections = response.data;

    const contracts = [];

    let counter = 1;

    for (const collection of collections) {
      const contractAddress = collection.address;

      const tokenHoldersUrl = `${tokenHoldersBaseUrl}?module=token&action=getTokenHolders&contractaddress=${contractAddress}&page=1&offset=1000`;
      const tokenHoldersResponse = await axios.get(tokenHoldersUrl);

      const tokenHolders = tokenHoldersResponse.data.result;
      contracts.push({
        number: counter,
        address: contractAddress,
        holders: tokenHolders.map(holder => holder.address)
      });

      counter++;
    }
    return {
      contracts: contracts
    };
  } catch (error) {
    console.error('Error fetching data:', error);
    return { error: 'Failed to fetch data' };
  }
}

app.get('/getholderaddress', async (req, res) => {
  const data = await fetchCollectionDetails();
  res.json(data);
});

app.get('/getdetails', async (req, res) => {
  const { contractAddress, walletAddress } = req.query;

  if (!contractAddress || !walletAddress) {
    return res.status(400).json({ error: 'Missing contractAddress or walletAddress parameter' });
  }

  try {
    const transactions = await fetchTransactions(contractAddress);

    if (transactions.length === 0) {
      return res.status(404).json({ message: 'No transactions found for the given contract and wallet address' });
    }
    const filteredTransactions = transactions.filter(tx =>
      tx.to.toLowerCase() === walletAddress.toLowerCase() ||
      tx.from.toLowerCase() === walletAddress.toLowerCase()
    );

    res.json({
      totalCount: filteredTransactions.length,
      transactions: filteredTransactions
    });
  } catch (error) {
    res.status(500).json({ error: 'Error fetching transactions' });
  }
});

// Enndpoint to fetch mint txn per address for * collection
app.get('/fetchminttxn', async (req, res) => {
  try {
    const contracts = await fetchCollectionDetails();

    const result = [];

    for (const contract of contracts.contracts) {
      const { address: contractAddress, holders } = contract;

      for (const holder of holders) {
        const details = await fetchTransactions(contractAddress);

        // Filter transactions for the current holder
        const holderTransactions = details.filter(tx =>
          tx.to.toLowerCase() === holder.toLowerCase() ||
          tx.from.toLowerCase() === holder.toLowerCase()
        );

        if (holderTransactions.length > 0) {
          result.push({
            contractAddress,
            holder,
            totalCount: holderTransactions.length,
            transactions: holderTransactions
          });
        }
      }
    }

    res.json(result);
  } catch (error) {
    res.status(500).json({ error: 'Error fetching mint transactions' });
  }
});



// this will just fetch the total count by an address

app.get('/fetchviacontract', async (req, res) => {
  try {
    const contracts = await fetchCollectionDetails();

    const holderTransactionMap = new Map();

    for (const contract of contracts.contracts) {
      const { address: contractAddress, holders } = contract;

      for (const holder of holders) {
        const details = await fetchTransactions(contractAddress);

        // Filter transactions for the current holder
        const holderTransactions = details.filter(tx =>
          tx.to.toLowerCase() === holder.toLowerCase() ||
          tx.from.toLowerCase() === holder.toLowerCase()
        );

        if (holderTransactions.length > 0) {
          if (!holderTransactionMap.has(holder)) {
            holderTransactionMap.set(holder, { totalCount: 0, transactions: [] });
          }

          const holderData = holderTransactionMap.get(holder);
          holderData.totalCount += holderTransactions.length;
          holderData.transactions.push(...holderTransactions);
        }
      }
    }

    const result = Array.from(holderTransactionMap.entries()).map(([holder, data]) => ({
      holder,
      totalCount: data.totalCount,

    }));

    res.json(result);
  } catch (error) {
    res.status(500).json({ error: 'Error fetching mint transactions' });
  }
});


async function fetchTopCollectorList() {
  try {
    const response = await axios.get('http://localhost:6000/fetchminttxn');
    const data = response.data;
    const collectorPoints = {};

    data.forEach(contract => {
      contract.transactions.forEach(txn => {
        const holder = txn.from.toLowerCase();
        if (!collectorPoints[holder]) {
          collectorPoints[holder] = { points: 0, collections: {} };
        }
        if (!collectorPoints[holder].collections[contract.contractAddress]) {
          collectorPoints[holder].collections[contract.contractAddress] = 0;
        }
        collectorPoints[holder].points += 1;
        collectorPoints[holder].collections[contract.contractAddress] += 1;
      });
    });

    const sortedCollectors = Object.entries(collectorPoints).sort((a, b) => b[1].points - a[1].points);

    const result = sortedCollectors.map(([holder, info], index) => ({
      rank: index + 1,
      holder,
      points: info.points,
      collections: Object.entries(info.collections).map(([contractAddress, count]) => ({
        contractAddress,
        mints: count
      }))
    }));

    return result;
  } catch (error) {
    console.error('Error fetching mint transactions:', error);
    throw error;
  }
}

app.get('/topcollectorlist', async (req, res) => {
  try {
    const collectors = await fetchTopCollectorList();
    res.json(collectors);
  } catch (error) {
    res.status(500).send('Error fetching mint transactions');
  }
});


app.listen(port, () => {
    console.log(`Server running at http://localhost:${port}`);
});
