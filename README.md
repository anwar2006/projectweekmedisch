# ApotheCare - Online Pharmacy Website

ApotheCare is a comprehensive online pharmacy system built with PHP and MySQL, featuring a responsive design with Tailwind CSS. This platform enables customers to purchase medications and health products online, with special handling for prescription medications.

## Features

- **Simple Access**: No login required - all features are available without authentication
- **Product Browsing**: Browse medications, health products, and other medical supplies
- **Prescription Management**: Upload and manage prescriptions
- **Shopping Cart System**: Add products to cart and checkout
- **Order Management**: Place and track orders
- **Dashboard**: Admin panel to manage orders, products, and prescriptions
- **Responsive Design**: Mobile-friendly interface using Tailwind CSS

## Tech Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML, CSS, JavaScript
- **CSS Framework**: Tailwind CSS
- **Icons**: Font Awesome

## Directory Structure

```
aphothecare/
├── actions/            # PHP scripts that handle form submissions and actions
├── assets/             # Static files (CSS, JS, images)
│   ├── images/         # Website images
│   │   └── products/   # Product images
├── config/             # Configuration files
├── dashboard/          # Admin dashboard files
├── database/           # Database schema and migrations
├── includes/           # PHP includes like header, footer, functions
├── pages/              # Individual page content files
├── uploads/            # Directory for uploaded files (prescriptions, etc.)
│   └── prescriptions/  # Uploaded prescription files
├── index.php           # Main entry point for the website
└── README.md           # Project documentation
```

## Installation

### Prerequisites

- XAMPP (includes PHP and MySQL)
- Web browser

### Steps

1. **Install XAMPP**
   - Download and install XAMPP from https://www.apachefriends.org/
   - Make sure to install Apache and MySQL components

2. **Set up the project**
   - Place the project files in your XAMPP's htdocs folder (typically `C:\xampp\htdocs\aphothecare`)
   - Start Apache and MySQL from the XAMPP Control Panel

3. **Set up the database**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database named `aphothecare_db`
   - Import the database schema from `database/aphothecare_db.sql`

4. **Configure the database connection**
   - Open `config/database.php`
   - Update the database credentials to match your XAMPP setup:
   ```php
   $host = 'localhost';
   $dbname = 'aphothecare_db';
   $username = 'root';  // Default XAMPP username
   $password = '';      // Default XAMPP password (empty)
   ```

5. **Access the website**
   - Open your web browser and navigate to http://localhost/aphothecare
   - The dashboard can be accessed directly at http://localhost/aphothecare/index.php?page=dashboard

## User Roles

The system has two types of users:

1. **Customers**: Can browse products, place orders, and upload prescriptions
2. **Admin**: Has access to the dashboard to manage orders, products, and prescriptions

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgements

- [Tailwind CSS](https://tailwindcss.com/)
- [Font Awesome](https://fontawesome.com/)
- [PHP](https://www.php.net/)
- [MySQL](https://www.mysql.com/) 


Email: admin@admin.com
Password: admin123

# Apothecare Chatbot

A Node.js chatbot powered by Mistral AI for the Apothecare platform.

## Setup

1. Make sure you have Node.js installed on your system
2. Install dependencies:
   ```bash
   npm install
   ```
3. Create a `.env` file in the root directory with your Mistral AI API key:
   ```
   MISTRAL_API_KEY=your_api_key_here
   PORT=3000
   ```

## Running the Chatbot
cd api/chatbot

1. Start the server:
   ```bash
   node index.js

   ```
2. Open your browser and navigate to `http://localhost:3000`
3. Start chatting with the AI assistant!

## Features

- Real-time chat interface
- Powered by Mistral AI
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