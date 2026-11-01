# 📉 Kakeibo

Keep track of household accounts with your family members.

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue.svg)](https://php.net)
[![CodeIgniter](https://img.shields.io/badge/CodeIgniter-3.x-red.svg)](https://codeigniter.com)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED.svg)](https://docker.com)

## ✨ Features

- **Income & Expense Tracking:** Effortlessly record and categorize transactions across different accounts.
- **Shared Family Budgets:** Manage household expenses collaboratively across multiple family members.
- **Monthly Summaries:** Stay on top of your finances with clear summaries of remaining balances.
- **Cash-Flow History:** Review spending trends and charts over time.
- **Minimalist Approach:** Designed to keep household finance management simple and clutter-free.

## 🛠️ Tech Stack

- **Backend:** PHP, CodeIgniter 3
- **Database:** MySQL / MariaDB
- **Migrations:** Phinx
- **Containerization:** Docker & Docker Compose

## 🚀 Getting Started

Follow these steps to spin up the application locally for development using Docker.

### Prerequisites

Ensure you have the following installed on your system:
- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)

### 1. Run the Containers

Build and start the application services:

```bash
docker compose build
docker compose up -d
```

### 2. Initialize the Application

Jump into the application container to install dependencies, set up your environment file, and run database migrations:

```bash
docker compose exec app /bin/bash

# Install PHP dependencies
composer install

# Set up environment variables
cp .env.example .env

# Run database migrations
vendor/bin/phinx migrate
```

> **Note:** If you plan to contribute or use the local git pre-commit linters, you can also run `yarn install` inside the container.

## 🌐 Access Points

Once running, you can access the services via your browser:

* **Application:** [http://localhost:8080](http://localhost:8080)
* **phpMyAdmin:** [http://localhost:4040](http://localhost:4040)

## 🔑 Default Development Accounts

> **Warning:** These credentials are strictly for local development and testing. Do not use them in a production environment.

| No. | Username | Password |
| --- | --- | --- |
| 1 | user1 | pass1234 |
| 2 | user2 | pass1234 |

## 📄 License

This project is licensed under the [MIT License](LICENSE).
