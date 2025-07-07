# Scheme Manager - Admin Panel

A comprehensive scheme management system with a premium admin panel for managing government schemes, customer enrollments, payments, and winners.

## 🚀 Features

- **Premium Admin Dashboard** with real-time statistics and charts
- **Customer Management** - Add, edit, and manage customer information
- **Scheme Management** - Create and manage different schemes
- **Payment Processing** - Track payments and generate receipts
- **Winner Management** - Manage scheme winners and prizes
- **Role-based Access Control** - SuperAdmin, Editor, and Verifier roles
- **Activity Logging** - Track all admin activities
- **Responsive Design** - Works on all devices
- **Modern UI/UX** - Clean, professional interface

## 📋 Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- XAMPP/WAMP/LAMP stack

## 🛠️ Installation

### 1. Database Setup

1. Create a new MySQL database named `scheme_manager`
2. Import the database schema from `config/requirememts/schema.sql`

```sql
-- Run this in your MySQL client
CREATE DATABASE scheme_manager;
USE scheme_manager;
-- Then import the schema.sql file
```

### 2. Configuration

1. Update database connection settings in `config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'scheme_manager');
define('DB_USER', 'root');  // Your MySQL username
define('DB_PASS', '');      // Your MySQL password
```

### 3. Initial Setup

1. Navigate to `http://localhost/scheme/admin/setup.php`
2. This will create the default admin user and sample data
3. Default login credentials:
   - **Email:** admin@gmail.com
   - **Password:** admin@123

### 4. Access Admin Panel

1. Go to `http://localhost/scheme/admin/login.php`
2. Login with the credentials above
3. You'll be redirected to the dashboard

## 📁 Project Structure

```
scheme/
├── admin/
│   ├── components/
│   │   ├── navbar.php      # Navigation component
│   │   └── footer.php      # Footer component
│   │   
│   ├── dashboard/
│   │   └── index.php       # Main dashboard
│   │   
│   ├── login.php           # Admin login
│   │   
│   ├── logout.php          # Logout script
│   │   
│   └── setup.php           # Initial setup
│   
├── config/
│   ├── database.php        # Database connection & utilities
│   │   
│   └── requirememts/
│       └── schema.sql      # Database schema
│   
└── README.md
```

## 🎨 UI Components

### Navbar Features
- Fixed top navigation with gradient background
- Admin profile dropdown with avatar
- Notification badge
- Responsive mobile menu
- Active page highlighting

### Dashboard Features
- Welcome section with animated background
- Statistics cards with hover effects
- Interactive revenue chart using Chart.js
- Recent activities feed
- Quick action buttons
- Responsive grid layout

### Login Page Features
- Modern gradient design
- Form validation
- Remember me functionality
- Password visibility toggle
- Loading states
- Demo credentials display

## 🔐 Security Features

- Password hashing using PHP's `password_hash()`
- Session management
- SQL injection prevention with prepared statements
- Input sanitization
- Role-based access control
- Activity logging

## 📊 Database Schema

The system includes the following main tables:

- **Admins** - Admin user accounts and roles
- **Customers** - Customer information and details
- **Schemes** - Available schemes and their criteria
- **Payments** - Payment records and transactions
- **Installments** - Installment payment tracking
- **Winners** - Scheme winners and prizes
- **Notifications** - System notifications
- **Activity Logs** - Admin activity tracking

## 🎯 Admin Roles

1. **SuperAdmin** - Full access to all features
2. **Editor** - Can edit schemes and manage customers
3. **Verifier** - Can verify payments and winners

## 🚀 Getting Started

1. **Clone/Download** the project to your web server directory
2. **Set up the database** using the schema file
3. **Configure** database connection in `config/database.php`
4. **Run setup** by visiting `admin/setup.php`
5. **Login** to the admin panel at `admin/login.php`

## 🎨 Customization

### Colors
The system uses a consistent color scheme:
- Primary: `#667eea` (Blue)
- Secondary: `#764ba2` (Purple)
- Success: `#10b981` (Green)
- Warning: `#f59e0b` (Orange)
- Danger: `#ef4444` (Red)

### Fonts
- Primary font: Inter (Google Fonts)
- Icons: Font Awesome 6.4.0

## 📱 Responsive Design

The admin panel is fully responsive and works on:
- Desktop computers
- Tablets
- Mobile phones
- All modern browsers

## 🔧 Development

### Adding New Features
1. Create new PHP files in appropriate directories
2. Include the navbar and footer components
3. Use the database connection from `config/database.php`
4. Follow the existing code structure and styling

### Styling Guidelines
- Use CSS Grid and Flexbox for layouts
- Implement smooth transitions and hover effects
- Maintain consistent spacing and typography
- Use the established color palette

## 🐛 Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check database credentials in `config/database.php`
   - Ensure MySQL service is running
   - Verify database name exists

2. **Login Issues**
   - Run `admin/setup.php` to create admin user
   - Check if session is enabled in PHP
   - Verify file permissions

3. **Page Not Found**
   - Ensure web server is configured correctly
   - Check file paths and permissions
   - Verify .htaccess configuration (if using Apache)

## 📞 Support

For support and questions:
- Check the troubleshooting section above
- Review the code comments for guidance
- Ensure all requirements are met

## 📄 License

This project is open source and available under the MIT License.

---

**Happy Coding! 🎉** 