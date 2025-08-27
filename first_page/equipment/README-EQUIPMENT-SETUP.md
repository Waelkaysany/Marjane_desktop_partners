# Equipment System Setup Guide

## Overview
This system allows partners to request equipment through the equipment.php page, with requests being stored in the `equipementpartner` database.

## Database Setup

### 1. Create the equipementpartner database
Run the SQL commands in `create_equipementpartner_db.sql` in your MySQL/phpMyAdmin:

```sql
-- Create database
CREATE DATABASE IF NOT EXISTS equipementpartner;
USE equipementpartner;

-- Create main table
CREATE TABLE IF NOT EXISTS requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    partner_id INT NOT NULL,
    equipement_id INT NOT NULL,
    time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending'
);
```

### 2. Database Connection
The system automatically connects to the `equipementpartner` database using the connection function in `includes/db.php`.

## How It Works

### 1. Equipment Request Flow
1. Partner visits `equipment.php`
2. Clicks on any equipment request button
3. System sends AJAX request to `process_equipment_request.php`
4. Request is stored in `requests` table with status 'pending'
5. Success message is displayed with unique reference number

### 2. Equipment IDs
- **ID 1**: FrostLine Triple Glass Door Cooler
- **ID 2**: Heavy-Duty Shelf  
- **ID 3**: Smart Checkout Pro

### 3. Database Fields
- `id`: Auto-incrementing primary key
- `partner_id`: Links to the authenticated partner
- `equipement_id`: References the specific equipment (1, 2, or 3)
- `time`: When the request was made
- `status`: Current status (pending/approved/rejected/completed)

## Files Modified/Created

### New Files:
- `process_equipment_request.php` - Handles equipment requests
- `create_equipementpartner_db.sql` - Database setup script
- `README-EQUIPMENT-SETUP.md` - This documentation

### Modified Files:
- `includes/db.php` - Added equipment database connection
- `equipment/equipment.php` - Updated to use database and AJAX

## Security Features

1. **Authentication Required**: Only logged-in partners can make requests
2. **CSRF Protection**: All requests include CSRF tokens
3. **Input Validation**: Equipment ID is required and validated
4. **SQL Injection Protection**: Uses prepared statements

## Testing

1. Log in as a partner
2. Navigate to `equipment.php`
3. Click any equipment request button
4. Check the `requests` table in the `equipementpartner` database for new entries
5. Verify the success message appears with reference number

## Troubleshooting

### Common Issues:
1. **Database Connection Error**: Check if `equipementpartner` database exists
2. **Permission Denied**: Ensure MySQL user has access to the database
3. **CSRF Token Error**: Check if session is working properly
4. **AJAX Error**: Check browser console for JavaScript errors

### Debug Mode:
Enable error reporting in PHP to see detailed error messages during development.

## Future Enhancements

1. **Admin Panel**: View and manage equipment requests
2. **Email Notifications**: Send confirmation emails to partners
3. **Request History**: Show partners their previous requests
4. **Status Updates**: Allow partners to track request progress
5. **Equipment Images**: Add actual product images to the catalog
