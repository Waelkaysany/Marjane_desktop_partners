# Admin Dashboard - Marjan Partners

A comprehensive administration panel for managing all aspects of the first_page project, including partners, orders, plan requests, and equipment requests.

## 🚀 Features

### **Core Management**
- **Partners Management** - View, add, edit, and manage partner accounts
- **Orders Management** - Track cart orders with status updates
- **Plan Requests** - Manage plan subscription requests
- **Equipment Requests** - Handle equipment rental requests

### **Administrative Features**
- **Dashboard Overview** - Real-time statistics and quick actions
- **User Authentication** - Secure admin login system
- **Status Management** - Update statuses across all modules
- **Database Integration** - Connects to all project databases
- **Responsive Design** - Works on desktop and mobile devices

## 📁 File Structure

```
admin_dashboard/
├── config.php              # Database configuration and helper functions
├── index.php               # Login page
├── dashboard.php           # Main dashboard overview
├── partners.php            # Partners management
├── orders.php              # Orders management
├── plan_requests.php       # Plan requests management
├── equipment_requests.php  # Equipment requests management
├── logout.php              # Logout functionality
├── setup_admin.php         # Initial setup script
├── assets/
│   └── css/
│       └── admin.css       # Admin dashboard styles
└── README.md               # This file
```

## 🛠️ Setup Instructions

### 1. **Database Configuration**
Ensure you have the following databases running:
- `marjanpartner` - Main partners database
- `cartpartner` - Cart and orders database
- `planpartner` - Plan requests database
- `equipementpartner` - Equipment requests database

### 2. **Run Setup Script**
1. Navigate to your admin dashboard folder
2. Open `setup_admin.php` in your browser
3. This will:
   - Test all database connections
   - Create the admin_users table
   - Create a default admin user

### 3. **Default Login Credentials**
- **Username:** `admin`
- **Password:** `admin123`
- ⚠️ **IMPORTANT:** Change this password after first login!

### 4. **Access the Dashboard**
- Navigate to `index.php` to login
- Use the credentials from step 3
- You'll be redirected to the main dashboard

## 🔧 Database Connections

The admin dashboard automatically connects to all your project databases:

```php
// Main database (partners)
getMainDbConnection()

// Cart database (orders)
getCartDbConnection()

// Plan database (plan requests)
getPlanDbConnection()

// Equipment database (equipment requests)
getEquipmentDbConnection()
```

## 📊 Dashboard Overview

### **Statistics Cards**
- Active Partners count
- Total Orders
- Plan Requests count
- Equipment Requests count
- Pending items counts
- Total Revenue

### **Quick Actions**
- Add new partner
- View orders
- Manage plan requests
- Handle equipment requests

### **Recent Activity**
- New orders
- Pending plans
- Equipment requests
- Active partners

## 🔐 Security Features

- **Session Management** - Secure PHP sessions
- **Password Hashing** - Bcrypt password encryption
- **CSRF Protection** - Cross-site request forgery prevention
- **Input Sanitization** - XSS protection
- **Role-based Access** - Admin and moderator roles

## 🎨 Status Management

### **Order Statuses**
- `pending` - Yellow badge
- `processing` - Blue badge
- `paid` - Primary blue badge
- `completed` - Green badge
- `cancelled` - Red badge

### **Request Statuses**
- `pending` - Yellow badge
- `approved` - Green badge
- `rejected` - Red badge
- `completed` - Green badge

## 📱 Responsive Design

The dashboard is built with Bootstrap 5 and includes:
- Mobile-friendly navigation
- Responsive tables
- Touch-friendly buttons
- Adaptive layouts

## 🚨 Troubleshooting

### **Common Issues**

1. **Database Connection Failed**
   - Check database credentials in `config.php`
   - Ensure all databases exist and are running
   - Verify MySQL service is active

2. **Login Not Working**
   - Run `setup_admin.php` first
   - Check if admin_users table exists
   - Verify password hash in database

3. **Status Updates Not Working**
   - Check database permissions
   - Verify table structure
   - Check error logs

### **Error Logs**
Check your PHP error logs for detailed error messages:
- XAMPP: `C:\xampp\php\logs\php_error_log`
- Linux: `/var/log/apache2/error.log`

## 🔄 Adding New Modules

To add new management modules:

1. Create new PHP file (e.g., `new_module.php`)
2. Include `config.php`
3. Add authentication check
4. Implement CRUD operations
5. Add navigation link in sidebar

## 📈 Future Enhancements

- **Export Functionality** - CSV/PDF exports
- **Advanced Analytics** - Charts and graphs
- **Email Notifications** - Status change alerts
- **Audit Logs** - Track all admin actions
- **API Integration** - REST API endpoints
- **Multi-language Support** - Internationalization

## 🤝 Support

For technical support or questions:
1. Check this README first
2. Review error logs
3. Verify database connections
4. Test with setup script

## 📝 License

This admin dashboard is part of the Marjan Partners project and is intended for internal use only.

---

**Last Updated:** <?php echo date('F j, Y'); ?>
**Version:** 1.0.0
