# School Management System (SMS) - Setup Instructions

## Project Overview
A comprehensive PHP-based School Management System built using pure OOP and MVC architecture. It handles students, teachers, classes, subjects, and academic performance tracking with a secure MySQL backend.

## Prerequisites
- **XAMPP** (or any LAMP/WAMP stack with PHP 7.4+ and MySQL)
- **Web Browser** (Chrome/Firefox/Edge)

## Installation Steps

### 1. Project Files
Extract the project folder and move it to your XAMPP web root:
`C:\xampp\htdocs\cse3101-g17-proj\`

### 2. Database Setup
1. Start **Apache** and **MySQL** in your XAMPP Control Panel.
2. Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
3. Create a new database named: **`School_Management`**
4. Click on the database name on the left.
5. Go to the **Import** tab.
6. Choose the file: `database.sql` from the project root.
7. Click **Go** at the bottom.

### 3. Accessing the System
Open your browser and navigate to:
`http://localhost/cse3101-g17-proj/`

## Test Credentials

### 🛡️ Office Admin (Full Access)
- **Username:** `admin`
- **Password:** `password123`

### 🍎 Teacher (Score Management)
- **Username:** `sarah_teacher`
- **Password:** `password123`

## Features
- **MVC Architecture:** Structured with Models, Views, and Controllers.
- **Security:** Uses PDO prepared statements for SQL injection prevention and CSRF protection.
- **Audit Trail:** Tracks which user modified student scores and when.
- **Reports:** Generates student report cards and grade average analytics.
- **RBAC:** Role-Based Access Control enforcing permissions for Teachers and Admins.

## Project Structure
- `/models/` - Database logic and data validation.
- `/controllers/` - Request handling and business logic.
- `/views/` - UI pages and templates.
- `/public/` - Static assets (CSS, Icons, JS).
- `/config/` - App and Database configurations.
