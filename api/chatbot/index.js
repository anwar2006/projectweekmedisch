require('dotenv').config();
const express = require('express');
const axios = require('axios');
const cors = require('cors');
const path = require('path');
const app = express();
const port = process.env.PORT || 7860;

// CORS configuration
const corsOptions = {
    origin: ['http://localhost', 'http://localhost:80', 'http://127.0.0.1', 'http://127.0.0.1:80'],
    methods: ['GET', 'POST'],
    allowedHeaders: ['Content-Type', 'Authorization'],
    credentials: true
};

// Middleware
app.use(cors(corsOptions));
app.use(express.json());
app.use(express.static(path.join(__dirname, 'public')));

// Root route
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'public', 'index.html'));
});

// Mistral AI API endpoint
const MISTRAL_API_URL = 'https://api.mistral.ai/v1/chat/completions';

// Chat endpoint
app.post('/api/chat', async (req, res) => {
    try {
        const { message } = req.body;

        if (!message) {
            return res.status(400).json({
                success: false,
                message: 'Message is required'
            });
        }

        console.log('Sending request to Mistral AI API...');
        console.log('Message:', message);

        const response = await axios.post(
            MISTRAL_API_URL,
            {
                messages: [
                    {
                        role: 'user',
                        content: message
                    }
                ],
                model: 'mistral-tiny',
                max_tokens: 1000,
                temperature: 0.7,
                stream: false
            },
            {
                headers: {
                    'Authorization': `Bearer ${process.env.MISTRAL_API_KEY}`,
                    'Content-Type': 'application/json'
                }
            }
        );

        console.log('Response received:', response.data);

        res.json({
            success: true,
            message: 'Success',
            response: response.data.choices[0].message.content
        });
    } catch (error) {
        console.error('Error details:', {
            message: error.message,
            response: error.response?.data,
            status: error.response?.status
        });
        
        res.status(500).json({
            success: false,
            message: 'An error occurred while processing your request',
            error: error.message
        });
    }
});

// Start server
app.listen(port, () => {
    console.log(`Server is running on port ${port}`);
    console.log(`API Key configured: ${process.env.MISTRAL_API_KEY ? 'Yes' : 'No'}`);
}); 