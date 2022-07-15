const express = require('express');
const path = require('path');
const dotenv = require('dotenv').config()
const port = process.env.PORT || 8010;

const app = express();

app.use(express.json());
app.use(express.urlencoded({ extended: false }));

app.use('/api',require('./routes/authRoutes'));
app.use('/api/contract',require('./routes/contractRoutes'));
// app.use('/api/order',require('./routes/orderRoutes'));

app.listen(port, ()=> console.log(`Run on Port: ${port}`))