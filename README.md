# StockFlow - Inventory Management System

<div align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 11.x">
  <img src="https://img.shields.io/badge/Livewire-3.x-4E56A6?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire 3.x">
  <img src="https://img.shields.io/badge/TailwindCSS-3.x-06B6D4?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
</div>

## 📋 Overview

**StockFlow** is a powerful yet intuitive inventory management system designed to help small to medium businesses track stock levels, manage suppliers, process purchase orders, and gain real-time insights into inventory performance. Built with Laravel 11 and Livewire 3, StockFlow provides a modern, reactive interface for efficient warehouse and inventory operations.

### ✨ Key Features

- 📦 **Real-time Stock Tracking** - Monitor inventory levels with automatic low-stock alerts
- 🏷️ **Barcode Management** - Auto-generate and print barcode labels (EAN-13, UPC-A, Code 128)
- 🔔 **Smart Notifications** - Get alerted for low stock, out-of-stock items, and PO updates
- 👥 **Role-Based Access Control** - Admin, Manager, and Staff roles with granular permissions
- 📊 **Visual Analytics** - Charts and reports for inventory performance insights
- 🛒 **Purchase Order Management** - Complete workflow from creation to receiving
- 📱 **Responsive Design** - Works seamlessly on desktop, tablet, and mobile devices
- 🌙 **Dark Mode** - User-preference theme with persistent storage
- 📈 **Comprehensive Reports** - Stock valuation, movement history, and supplier performance

---

## 🚀 Quick Start

### Prerequisites

- PHP 8.2 or higher
- Composer
- MySQL 8.0+
- Node.js & npm
- Visual Studio Code (optional, for local development)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/TomisinII/stockflow.git
   cd stockflow
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database**
   
   Edit `.env` with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=stockflow
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run migrations and seeders**
   ```bash
   php artisan migrate --seed
   ```

6. **Build assets**
   ```bash
   npm run dev
   ```

7. **Start the application**
   ```bash
   php artisan serve
   ```

8. **Access the application**
   
   Open your browser and navigate to `http://localhost:8000`

### Demo Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@stockflow.test | Admin123! |
| Manager | manager@stockflow.test | Manager123! |
| Staff | staff@stockflow.test | Staff123! |

---

## 📐 System Architecture

### Tech Stack

**Backend:**
- Laravel 11.x - PHP framework
- MySQL 8.0+ - Database
- Spatie Laravel Permission - Role-based access control
- Picqer PHP Barcode Generator - Barcode generation
- DomPDF - PDF report generation

**Frontend:**
- Livewire 3.x - Reactive components
- Tailwind CSS 3.x - Utility-first styling
- Alpine.js - JavaScript interactions
- Chart.js - Data visualization
- Heroicons - Icon system

### Database Schema

```
users
├── roles (Admin, Manager, Staff)
└── permissions

categories
└── products
    ├── suppliers
    ├── stock_adjustments
    └── purchase_order_items

purchase_orders
├── supplier
├── purchase_order_items
└── user (created_by)

notifications
└── user
```

[View Full Database Schema](docs/database-schema.md)

---

## 🎯 Core Features

### 1. Product Management
- Create, edit, and archive products
- Auto-generate SKU and barcode
- Image upload support
- Category organization
- Real-time stock level indicators:
  - 🟢 **Green** - Healthy stock (above minimum)
  - 🟡 **Amber** - Low stock warning (at/near minimum)
  - 🔴 **Red** - Critical/Out of stock (below minimum)
- Bulk CSV import
- Stock adjustment history

### 2. Supplier Management
- Maintain supplier database
- Track contact information and payment terms
- View supplier performance metrics
- Link products to suppliers
- Purchase order history per supplier

### 3. Purchase Order Workflow
- Create draft purchase orders
- Add multiple line items
- Send to suppliers
- Receive goods with automatic stock updates
- Partial receiving support
- Print/download PO as PDF
- Track PO status (Draft → Sent → Received)

### 4. Stock Adjustments
- Record stock movements:
  - Stock In (purchase, returns)
  - Stock Out (sales, damage, theft)
  - Corrections (manual counts)
- Complete audit trail
- Reference tracking (PO numbers, invoices)
- User attribution for all adjustments

### 5. Notifications & Alerts
- 🚨 Critical alerts for out-of-stock items
- ⚠️ Low stock warnings
- ✅ Purchase order status updates
- ℹ️ System notifications
- Unread count badge
- Mark as read/unread

### 6. Reports & Analytics
- **Inventory Reports:**
  - Current stock levels
  - Low stock items
  - Stock valuation
  - Dead stock analysis
  - Category-wise breakdown

- **Purchase Reports:**
  - PO summary by status
  - Supplier performance
  - Purchase history

- **Stock Movement:**
  - In/Out summary
  - Adjustment history
  - Fast/slow moving items

- **Visual Analytics:**
  - Stock value by category (pie chart)
  - Movement trends (line chart)
  - Monthly comparisons

### 7. Barcode System
- Auto-generate on product creation
- Support for EAN-13, UPC-A, Code 128, QR Code
- Print individual or bulk labels
- Customizable label templates
- PDF generation for printing

### 8. User Management (Admin Only)
- Create and manage users
- Assign roles and permissions
- View user activity
- Profile management with avatar upload

---

## 👥 User Roles & Permissions

### Admin
- Full system access
- User and role management
- System settings
- All inventory operations
- All reports

### Manager
- Inventory management
- Supplier management
- Purchase order creation and receiving
- Stock adjustments
- View all reports
- *Cannot manage users/roles*

### Staff
- View products and inventory
- Create stock adjustments
- View limited reports
- *Cannot modify suppliers or POs*

---

## 📸 Screenshots

### Dashboard
![Dashboard Overview](public/screenshots/dashboard.png)
*Real-time summary of inventory status with quick actions*

### Product Management
![Product List](public/screenshots/products.png)
*Searchable and filterable product catalog with stock indicators*

### Purchase Orders
![Purchase Order](public/screenshots/purchase-order.png)
*Complete PO workflow from creation to receiving*

### Reports
![Analytics](public/screenshots/reports.png)
*Visual analytics and comprehensive reporting*

---

## 🛠️ Development

### Project Structure

```
app/
├── Livewire/              # Livewire components
│   ├── Dashboard.php
│   ├── Products/
│   ├── Suppliers/
│   ├── PurchaseOrders/
│   └── Reports/
├── Services/              # Business logic
│   ├── StockService.php
│   ├── NotificationService.php
│   └── BarcodeService.php
├── Models/                # Eloquent models
└── Policies/              # Authorization policies

resources/
├── views/
│   ├── livewire/         # Livewire views
│   └── components/       # Blade components
└── js/                   # Frontend assets

database/
├── migrations/           # Database migrations
└── seeders/             # Database seeders
```

### Running Tests

```bash
php artisan test
```

### Code Style

```bash
# Run PHP CS Fixer
./vendor/bin/pint

# Run Laravel IDE Helper
php artisan ide-helper:generate
```

---

## 🚢 Deployment

### Production Checklist

- [ ] Set `APP_ENV=production` and `APP_DEBUG=false`
- [ ] Configure production database
- [ ] Set up file storage (S3 recommended)
- [ ] Configure email for notifications
- [ ] Install SSL certificate
- [ ] Run optimization commands:
  ```bash
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  php artisan optimize
  ```
- [ ] Set up automated backups
- [ ] Configure monitoring and logging

### Recommended Hosting

- Laravel Forge
- DigitalOcean
- AWS (with Laravel Sail)
- Any VPS with PHP 8.2+ support

---

## 📚 Documentation

- [Installation Guide](docs/installation.md)
- [User Guide](docs/user-guide.md)
- [API Documentation](docs/api.md)
- [Database Schema](docs/database-schema.md)
- [Contributing Guidelines](CONTRIBUTING.md)

---

## 🔮 Roadmap

### Phase 2 (Planned)
- [ ] Multi-warehouse support
- [ ] Sales order management
- [ ] Real-time barcode scanning via camera
- [ ] Email/SMS notifications
- [ ] Batch and lot tracking
- [ ] Expiry date tracking
- [ ] RESTful API

### Long-term Vision
- [ ] Mobile app (React Native/Flutter)
- [ ] Accounting software integration
- [ ] E-commerce platform integration
- [ ] AI-powered demand forecasting
- [ ] Automated reordering

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct and development process.

<!-- --- -->

<!-- ## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details. -->

---

## 👨‍💻 Author

**Your Name**
- GitHub: [@TomisinII](https://github.com/TomisinII)
- Email: jolutomisin@gmail.com
- LinkedIn: [Juwon Olutomisin](https://www.linkedin.com/in/olutomisinoluwajuwon/)

---

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - The PHP Framework
- [Livewire](https://livewire.laravel.com) - Full-stack framework for Laravel
- [Tailwind CSS](https://tailwindcss.com) - Utility-first CSS framework
- [Spatie](https://spatie.be) - Laravel Permission package
- All contributors and supporters

<!-- ---

## 📞 Support

For support, email support@stockflow.test or join our Slack channel. -->

---

<div align="center">
  Made with ❤️ by Olutomisin Oluwajuwon
  <br>
  <sub>Built as part of a portfolio project demonstrating full-stack Laravel development</sub>
</div>
