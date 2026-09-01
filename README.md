# 🍽️ ShareFood - Surplus Food Sharing Platform

**ShareFood** is a web application designed to connect surplus food donors (restaurants, bakeries, households, event organizers) with receivers (NGOs, community centers, charities, individuals) to reduce food waste and combat hunger.

---

## ✨ Features

- **User Authentication & Role Management**:
  - **Donors**: Post surplus food listings with description, quantity, expiry timestamp, and pickup address.
  - **Receivers**: Browse available food, filter/sort by urgency, and claim items in real-time.
- **Interactive Dashboard**: Track active listings, total claimed items, and status updates (Available / Claimed / Picked Up).
- **Urgency Indicator**: Dynamic calculation highlighting food items nearing expiration.
- **Responsive UI**: Clean CSS styling with glassmorphism header design and mobile-responsive layout.

---

## 🛠️ Tech Stack

- **Backend**: PHP 8.x
- **Database**: MySQL 8.x / MariaDB (PDO)
- **Frontend**: HTML5, CSS3 (Modern Flexbox & Grid)

---

## 🚀 Quick Start Guide

### 1. Prerequisites
- PHP 8.0 or higher
- MySQL / MariaDB Server

### 2. Database Setup
1. Start your MySQL server.
2. Import `database.sql` into MySQL:
   ```bash
   mysql -u root -p < database.sql
   ```
   *(This creates the `food_sharing_platform` database, required tables, and populates initial demo data.)*

3. Verify or adjust database credentials in [`config.php`](file:///c:/Users/dasha/Documents/Web_Project/config.php):
   ```php
   $host = 'localhost';
   $dbname = 'food_sharing_platform';
   $username = 'root';
   $password = 'dash'; // Adjust to your MySQL root password
   ```

### 3. Run the Application
Start PHP's built-in web server from the project root:
```bash
php -S localhost:8000
```
Open your browser and navigate to: **[http://localhost:8000](http://localhost:8000)**

---

## 🔑 Demo Credentials

| Role | Email | Password |
| :--- | :--- | :--- |
| **Donor (Restaurant)** | `restaurant@abc.com` | `password123` |
| **Receiver (NGO)** | `ngo@foodbank.com` | `password123` |

---

## 📁 Project Structure

```
Web_Project/
├── config.php          # Database PDO connection & session helpers
├── index.php           # Landing page with platform impact stats
├── login.php           # User login page
├── register.php        # User registration (Donor / Receiver)
├── dashboard.php       # Role-based dashboard for donors and receivers
├── post_food.php       # Form for donors to list surplus food
├── browse_food.php     # Public & receiver list of available food items
├── claim_food.php      # Claim processing backend endpoint
├── logout.php         # Session termination
├── database.sql        # MySQL database schema & sample seed data
├── .gitignore          # Git exclusion rules
└── README.md           # Project documentation
```

---

## 📤 How to Push to Your GitHub Profile

1. Open your terminal in the project directory.
2. Initialize Git (if not already initialized):
   ```bash
   git init
   ```
3. Add and commit all project files:
   ```bash
   git add .
   git commit -m "Initial commit: ShareFood Web Application"
   ```
4. Create a new public repository on [GitHub](https://github.com/new) (e.g. named `ShareFood`).
5. Link your local repository to GitHub and push:
   ```bash
   git remote add origin https://github.com/YOUR_USERNAME/ShareFood.git
   git branch -M main
   git push -u origin main
   ```
