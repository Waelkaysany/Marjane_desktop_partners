# MARJANE Partner Application - PHP Setup

## Overview
This is a complete PHP-based partner management application with secure authentication, equipment catalog, and shopping cart functionality.

## File Structure
```
first_page/
├── index.php              # Entry point (redirects to login)
├── login.php              # Secure login page
├── home.php               # Main dashboard (requires auth)
├── cart.php               # Shopping cart (requires auth)
├── logout.php             # Secure logout handler
├── equipment/
│   └── equipment.php      # Equipment catalog (requires auth)
├── includes/
│   ├── db.php            # Database connection
│   └── session.php       # Session management
├── assets/
│   ├── css/
│   │   └── main.css      # Parameter dropdown styles
│   └── js/
│       └── profile.js    # Parameter dropdown functionality
├── style.css              # Main application styles
├── cart.css               # Cart page styles
├── cart.js                # Cart functionality
├── script.js              # Main application scripts
└── recommendation-product.js # Product recommendations
```

## Setup Instructions

### 1. Database Setup
Create the database and tables:

```sql
CREATE DATABASE marjanpartner;
USE marjanpartner;

CREATE TABLE partners (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  phone VARCHAR(20),
  company VARCHAR(100),
  role ENUM('partner', 'admin') DEFAULT 'partner',
  avatar_url VARCHAR(255),
  address TEXT,
  status ENUM('active', 'inactive', 'pending') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  last_login TIMESTAMP NULL,
  notes TEXT
);
```

### 2. Create Test User
```sql
INSERT INTO partners (username, password_hash, full_name, email, phone, company, role) 
VALUES ('wail', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Wail El Kaysani', 'wail@example.com', '+1234567890', 'Marjane Partner', 'partner');
```
**Login Credentials:**
- Username: `wail`
- Password: `wailelkaysany`

### 3. Database Configuration
Update `includes/db.php` with your database credentials:
```php
$DB_HOST = 'localhost';
$DB_NAME = 'marjanpartner';
$DB_USER = 'root';
$DB_PASS = '';
```

## Usage Flow

### 1. Access Application
- Navigate to: `http://localhost/Vtuber_project2/first_page/`
- You'll be redirected to `login.php`

### 2. Authentication
- Login with the credentials above
- On success, you'll be redirected to `home.php`

### 3. Navigation
- **Home**: Main dashboard with product recommendations
- **Equipment**: Equipment catalog with GSAP animations
- **Cart**: Shopping cart functionality
- **Parameters**: Click the gear icon (⚙️) to view profile and logout

### 4. Features
- **Secure Authentication**: Session-based with password hashing
- **Equipment Catalog**: 4 product pages with smooth scrolling
- **Shopping Cart**: Add/remove items, view totals
- **Profile Management**: View partner information
- **Responsive Design**: Works on desktop and mobile

## Security Features
- ✅ Password hashing (bcrypt)
- ✅ Session management
- ✅ CSRF protection
- ✅ SQL injection prevention (PDO prepared statements)
- ✅ Session regeneration on login
- ✅ Secure logout

## Technical Details
- **Backend**: PHP 7.4+ with PDO
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript
- **Animations**: GSAP ScrollTrigger for equipment pages
- **Styling**: Custom CSS with responsive design

## Troubleshooting
- Ensure XAMPP Apache and MySQL are running
- Check database credentials in `includes/db.php`
- Verify all PHP files have proper permissions
- Check browser console for JavaScript errors

## File Extensions
All main application files now use `.php` extension:
- `home.html` → `home.php`
- `equipment.html` → `equipment.php`
- `cart.html` → `cart.php`

This ensures proper session handling and authentication throughout the application.
