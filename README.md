# 🍽️ ShareFood – Surplus Food Sharing Platform

ShareFood is a full-stack web application that connects **surplus food donors** (restaurants, bakeries, households, event organizers) with **receivers** (NGOs, community centers, charities, individuals) to reduce food waste and help fight hunger.

---

## ✨ Features

- **Role-based authentication** – separate Donor and Receiver accounts with session management and hashed passwords.
- **Donors** post surplus food with type, quantity, description, expiry time and pickup location.
- **Receivers** browse available food, sort it by expiry time, date posted or food type, and claim items.
- **Urgency indicator** – listings close to expiry are highlighted automatically, and expired listings are hidden.
- **Dashboards** – donors track their listings and receivers track their claims, with Available / Claimed / Picked Up status badges.
- **Responsive UI** – clean HTML/CSS layout that works on desktop and mobile.

## 🔒 Security

- Passwords are hashed with `password_hash()` and verified with `password_verify()`; there are no built-in or default passwords.
- All database access uses **PDO prepared statements** (SQL injection protection); user-supplied output is escaped with `htmlspecialchars()` (XSS protection).
- **CSRF tokens** protect every state-changing form (login, register, post food, claim food).
- Session ID is regenerated on login, and session cookies are `HttpOnly` and `SameSite=Lax`.
- Claiming is **atomic** (a single transaction with a conditional `UPDATE`), so two receivers can never claim the same listing.
- Database credentials are read from environment variables or an untracked `config.local.php`, never from the repository.

---

## 🛠️ Tech Stack

| Layer | Technology |
| :--- | :--- |
| Backend | PHP 8.x |
| Database | MySQL 8.x / MariaDB (via PDO) |
| Frontend | HTML5, CSS3 (Flexbox & Grid), minimal JavaScript |

---

## 🚀 Getting Started

### 1. Prerequisites
- PHP 8.0 or higher (with the `pdo_mysql` extension)
- MySQL or MariaDB server

### 2. Set up the database
```bash
mysql -u root -p < database.sql
```
This creates the `food_sharing_platform` database, its tables and some demo data.
> ⚠️ `database.sql` drops and recreates the tables, so importing it again resets all data.

### 3. Configure credentials
Use **either** environment variables:
```bash
export DB_HOST=localhost
export DB_NAME=food_sharing_platform
export DB_USER=root
export DB_PASS=your_mysql_password
```
**or** a local config file (ignored by Git):
```bash
cp config.local.example.php config.local.php
# then edit config.local.php with your own values
```

### 4. Run the app
```bash
php -S localhost:8000
```
Open **http://localhost:8000** in your browser. On Windows you can also run `start_app.bat`.

---

## 🔑 Demo Accounts (local seed data only)

| Role | Email | Password |
| :--- | :--- | :--- |
| Donor (Restaurant) | `restaurant@abc.com` | `password123` |
| Receiver (NGO) | `ngo@foodbank.com` | `password123` |

These accounts exist only in the seed data for local testing. Change or delete them before any real deployment.

---

## 🗄️ Database Schema

- `users` – id, name, email (unique), hashed password, phone, address, `user_type` (donor / receiver)
- `food_listings` – donor, food type, quantity, description, expiry time, pickup location, `status` (available / claimed / picked_up)
- `food_claims` – links a listing to the receiver who claimed it, with a timestamp

Foreign keys with `ON DELETE CASCADE` keep the data consistent.

---

## 📁 Project Structure

```
ShareFood/
├── config.php                 # PDO connection, session setup, auth and CSRF helpers
├── config.local.example.php   # Template for local DB credentials (copy to config.local.php)
├── index.php                  # Landing page with platform impact stats
├── register.php               # Registration (Donor / Receiver)
├── login.php                  # Login
├── logout.php                 # Ends the session
├── dashboard.php              # Role-based dashboard
├── post_food.php              # Donors list surplus food
├── browse_food.php            # Available food listings
├── claim_food.php             # Atomic claim endpoint
├── database.sql               # Schema and seed data
├── start_app.bat              # One-click start script (Windows)
└── README.md
```

---

## 🔮 Possible Improvements

- Let donors mark claimed food as **picked up**
- Add search and location-based filtering
- Email or SMS notifications for new listings
- Automated tests and a Docker setup

## 👤 Author

**Ashutosh Dash** – [GitHub](https://github.com/ByteBrigadier) · [LinkedIn](https://www.linkedin.com/in/ashutosh-dash-691918297)
