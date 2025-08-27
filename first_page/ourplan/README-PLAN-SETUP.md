# Plan Partner System Setup

This document explains how to set up the plan partner system that allows users to request different partnership plans.

## Files Created/Modified

1. **`ourplan.php`** - Main plans page with database integration
2. **`admin_plan_requests.php`** - Admin panel to view plan requests
3. **`setup_planpartner_db.sql`** - Database setup script
4. **`ourplan.html`** - Deleted (replaced by ourplan.php)

## Database Setup

### 1. Create Database and Table

Run the SQL script to create the database and table:

```sql
-- Execute this in your MySQL client (phpMyAdmin, MySQL Workbench, etc.)
SOURCE setup_planpartner_db.sql;
```

Or manually execute:

```sql
CREATE DATABASE planpartner;
USE planpartner;

CREATE TABLE plan_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    partnerid INT NOT NULL,
    planid INT NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 2. Database Configuration

Update the database credentials in both PHP files:

```php
// In ourplan.php and admin_plan_requests.php
$dbHost = 'localhost';  // Your database host
$dbUser = 'root';       // Your database username
$dbPass = '';           // Your database password
$dbName = 'planpartner'; // Database name
```

## Plan Mapping

The system uses the following plan IDs:

- **Plan ID 1** = Basic Partner (MAD 299/month)
- **Plan ID 2** = Business Partner (MAD 799/month) 
- **Plan ID 3** = Elite Partner (Custom Pricing)

## Features Implemented

### ✅ Security Features
- CSRF token protection
- Prepared statements (SQL injection prevention)
- Input validation and sanitization
- Session-based authentication

### ✅ User Experience
- Form submission with POST method
- Success/error message display
- Post/Redirect/Get pattern (prevents duplicate submissions)
- Auto-hiding messages after 5 seconds

### ✅ Database Integration
- MySQL connection with error handling
- Prepared statements for safe queries
- Automatic timestamp recording
- Status tracking (pending/approved/rejected)

### ✅ Admin Panel
- Simple password-protected admin interface
- View all plan requests
- Statistics dashboard
- Sort by request time (newest first)

## Usage

### For Users
1. Navigate to `ourplan.php`
2. Choose a plan (Basic, Business, or Elite)
3. Click the "CHOOSE [PLAN]" button
4. System will create a record in the database
5. Success message will be displayed

### For Admins
1. Navigate to `admin_plan_requests.php`
2. Login with password: `admin123` (change this!)
3. View all plan requests and statistics
4. Use the table to track partner requests

## Security Notes

⚠️ **Important Security Updates Needed:**

1. **Change Admin Password**: Update `$adminPassword` in `admin_plan_requests.php`
2. **Database Credentials**: Update database connection details
3. **Session Security**: Consider implementing proper session management
4. **Rate Limiting**: Consider adding rate limiting to prevent spam

## Error Handling

The system includes comprehensive error handling:

- Database connection errors
- Invalid form submissions
- Missing CSRF tokens
- Invalid plan selections
- User authentication issues

All errors are logged to the server error log and display user-friendly messages.

## File Structure

```
ourplan/
├── ourplan.php                 # Main plans page
├── admin_plan_requests.php     # Admin panel
├── setup_planpartner_db.sql    # Database setup
├── ourplan.css                 # Styling (existing)
├── ourplan.js                  # JavaScript (existing)
└── README-PLAN-SETUP.md        # This file
```

## Testing

1. **Database Connection**: Ensure MySQL is running and credentials are correct
2. **User Flow**: Test plan selection as a logged-in user
3. **Admin Access**: Test admin panel with correct password
4. **Error Scenarios**: Test with invalid inputs and database issues

## Troubleshooting

### Common Issues

1. **Database Connection Failed**
   - Check MySQL service is running
   - Verify database credentials
   - Ensure database `planpartner` exists

2. **CSRF Token Errors**
   - Check session is working properly
   - Verify `session_start()` is called

3. **Form Not Submitting**
   - Check PHP is enabled on server
   - Verify file permissions
   - Check server error logs

### Error Logs

Check your server's error log for detailed error messages:
- Apache: `/var/log/apache2/error.log`
- XAMPP: `xampp/apache/logs/error.log`
- PHP errors: Check `php.ini` error_log setting
