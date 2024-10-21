# USSD and SMS App for Agricultural Extension Workers

## Overview

This project is a USSD and SMS application designed to assist agricultural extension workers in disseminating information to farmers. It allows users to register, access agricultural knowledge on crop and animal production, and receive step-by-step guidance on farming practices through their mobile phones.

The application uses Africa's Talking USSD gateway for interaction and MySQL for storing user information. The code handles user registration, navigation through different agricultural topics, and the dissemination of expert advice based on user selections.

## Features

### SMS Application
- Bulk SMS sending to multiple phone numbers.
- Integration with **Africa's Talking** API for reliable SMS delivery.
- Customizable message content and sender name.
- Database-driven approach for managing recipient phone numbers.

### USSD Application
- Interactive USSD menus for extension workers.
- Real-time data retrieval from the MySQL database.
- Easy to expand with additional USSD menu options.
- Integration with **Africa's Talking** USSD API.

## Technologies Used
- **PHP**: Backend logic for handling USSD requests and SMS responses.
- **Africa's Talking API**: For USSD and SMS services.
- **MySQL**: Database for user management.
- **HTML**: The USSD response is provided as plain text for Africa's Talking.

## Prerequisites

To run this application, ensure you have the following installed:

- [Node.js](https://nodejs.org/) (v12.x or higher)
- [MySQL](https://www.mysql.com/) (for database management)
- An **Africa's Talking** account for accessing the API (sandbox for testing or live for production)

