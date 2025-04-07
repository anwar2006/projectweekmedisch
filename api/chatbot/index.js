require('dotenv').config();
const express = require('express');
const axios = require('axios');
const cors = require('cors');
const path = require('path');

const app = express();
const port = process.env.PORT || 3000;

// Middleware
app.use(cors());
app.use(express.json());
app.use(express.static(path.join(__dirname, 'public')));

// Root route
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'public', 'index.html'));
});

// DeepSeek AI API endpoint
const DEEPSEEK_API_URL = 'https://api.deepseek.com/v1/chat/completions';

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

        console.log('Sending request to DeepSeek API...');
        console.log('Message:', message);

        const response = await axios.post(
            DEEPSEEK_API_URL,
            {
                messages: [
                    {
                        role: 'user',
                        content: message
                    }
                ],
                model: 'deepseek-chat',
                max_tokens: 1000,
                temperature: 0.7,
                stream: false
            },
            {
                headers: {
                    'Authorization': `Bearer ${process.env.DEEPSEEK_API_KEY}`,
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
    console.log(`API Key configured: ${process.env.DEEPSEEK_API_KEY ? 'Yes' : 'No'}`);
}); 