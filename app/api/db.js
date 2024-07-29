// lib/db.js

const mysql = require('mysql2/promise');

let pool;

const getDbConnection = async () => {
    if (!pool) {
        pool = mysql.createPool({
            host: 'localhost',
            user: 'forge', // Replace with your MySQL username
            password: '67yBCxjyCaC3TcOf01JJ',
            database: 'forge',
        });
    }
    return pool;
};

module.exports = { getDbConnection };
