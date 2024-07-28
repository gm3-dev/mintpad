const express = require('express');
const axios = require('axios');
const mysql = require('mysql2');
const cron = require('node-cron');
const app = express();
const PORT = 1000;

const connection = mysql.createPool({
    host: 'localhost',
    user: 'root',
    password: 'Koireng@1',
    database: 'mydatabase'
});

// established connectection
connection.getConnection((err, conn) => {
    if (err) {
        console.error('Error connecting to MySQL:', err);
        return;
    }
    console.log('Connected to MySQL');
    conn.release();
});

async function fetchContractDeployer(contractAddress) {
    const apiUrl = 'https://blockscoutapi.hekla.taiko.xyz/api';
    
    try {
        const response = await axios.get(apiUrl, {
            params: {
                module: 'contract',
                action: 'getcontractcreation',
                contractaddresses: contractAddress
            }
        });

        if (response.data && response.data.result && response.data.result.length > 0) {
            return response.data.result[0].creator;
        } else {
            console.log(`Deployer Address for ${contractAddress}: Not found`);
            return 'Not found';
        }
    } catch (error) {
        console.error(`Error fetching details for contract ${contractAddress}:`, error);
        return 'Error';
    }
}

// function to fetch the collector list
async function fetchtopcollectorlist() {
    try {
        const response = await axios.get('http://localhost:6000/fetchminttxn');
        const data = response.data;

        const collectionPoints = {};

        data.forEach(contract => {
            contract.transactions.forEach(txn => {
                const holder = txn.from.toLowerCase();
                const collection = contract.contractAddress.toLowerCase();

                if (!collectionPoints[collection]) {
                    collectionPoints[collection] = {};
                }

                if (!collectionPoints[collection][holder]) {
                    collectionPoints[collection][holder] = 0;
                }

                collectionPoints[collection][holder] += 1;
            });
        });

        const collectionTotals = {};
        Object.keys(collectionPoints).forEach(collection => {
            const holders = collectionPoints[collection];
            const totalPoints = Object.values(holders).reduce((sum, points) => sum + points, 0);
            collectionTotals[collection] = totalPoints;
        });

        const sortedCollections = Object.entries(collectionTotals)
            .sort(([, aPoints], [, bPoints]) => bPoints - aPoints);

        await new Promise((resolve, reject) => {
            connection.query('TRUNCATE TABLE top_creattor', (err) => {
                if (err) {
                    return reject(err);
                }
                resolve();
            });
        });

        // Insertt the data in the collection data/table
        for (const [collection, totalPoints] of sortedCollections) {
            const deployerAddress = await fetchContractDeployer(collection);
            const rank = sortedCollections.findIndex(([addr,]) => addr === collection) + 1;

            const query = `
                INSERT INTO top_creattor (\`rank\`, contractaddress, points) 
                VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE points = VALUES(points)
            `;
            const values = [rank, collection, totalPoints];
            
            connection.query(query, values, (err) => {
                if (err) {
                    console.error('Error inserting/updating data:', err);
                }
            });
            // maybe use this data to again, reverse to give back contract deployer address

            // Log the collection details
            console.log(`Collection Rank ${rank}: ${collection}`);
            console.log(`Total Points: ${totalPoints}`);
            console.log('---');
        }

        console.log('Top collector data updated successfully');

    } catch (error) {
        console.error('Error fetching mint transactions:', error);
    }
}

fetchtopcollectorlist().then(() => {
    cron.schedule('*/5 * * * *', () => {
        console.log('Running cron job to update top collectors');
        fetchtopcollectorlist();
    });
}).catch(err => {
    console.error('Initial update failed:', err);
});

app.listen(PORT, () => {
    console.log(`Server running on http://localhost:${PORT}`);
});
