# Apothecare Chatbot

A Node.js chatbot powered by DeepSeek AI for the Apothecare platform.

## Setup

1. Make sure you have Node.js installed on your system
2. Install dependencies:
   ```bash
   npm install
   ```
3. Create a `.env` file in the root directory with your DeepSeek API key:
   ```
   DEEPSEEK_API_KEY=your_api_key_here
   PORT=3000
   ```

## Running the Chatbot

1. Start the server:
   ```bash
   node index.js
   ```
2. Open your browser and navigate to `http://localhost:3000`
3. Start chatting with the AI assistant!

## Features

- Real-time chat interface
- Powered by DeepSeek AI
- Responsive design
- Error handling and user feedback

## API Endpoints

- `POST /api/chat`: Send messages to the chatbot
  - Request body: `{ "message": "your message here" }`
  - Response: `{ "success": true, "message": "Success", "response": "AI response" }`

## Security

- API keys are stored in environment variables
- CORS enabled for local development
- Input validation and error handling 