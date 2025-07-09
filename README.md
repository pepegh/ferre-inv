# Ferre Inventory Management System

A comprehensive inventory control application built with Laravel 11 and Filament 3, designed to manage products, suppliers, purchase orders, and sales for hardware store operations.

## Features

### Core Functionality
- **Product Management**: Track products with detailed information including brands, categories, pricing, and stock levels
- **Brand Management**: Organize products by brands with descriptions
- **Category Management**: Flexible product categorization with many-to-many relationships
- **Supplier Management**: Maintain supplier information with contact details
- **Purchase Order Management**: Create and track purchase orders with items and status
- **Sales Management**: Record sales transactions with automatic stock updates

### Admin Panel
- Built with Filament 3 for a modern, responsive admin interface
- User authentication system
- Dashboard with overview widgets
- CRUD operations for all entities
- Search and filtering capabilities
- Bulk operations support

## Technology Stack

- **Framework**: Laravel 11
- **Admin Panel**: Filament 3.2
- **Database**: SQLite (configurable)
- **Frontend**: Tailwind CSS with Alpine.js
- **Build Tool**: Vite
- **PHP Version**: 8.2+

## Database Schema

### Products
- Name, description, price, cost price, stock quantity
- Belongs to a brand
- Can have multiple categories (many-to-many)

### Brands
- Name and description
- Has many products

### Categories
- Name
- Belongs to many products

### Suppliers
- Name, website, contact information
- Can have multiple purchase orders

### Purchase Orders
- Order number, date, status, total amount
- Belongs to a supplier
- Has many purchase order items

### Sales
- Unique code, client name, date, total amount
- Has many sale items
- Automatically decrements product stock on sale item creation

## Installation

1. Clone the repository
2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install Node.js dependencies:
   ```bash
   npm install
   ```

4. Set up environment file:
   ```bash
   cp .env.example .env
   ```

5. Generate application key:
   ```bash
   php artisan key:generate
   ```

6. Run database migrations:
   ```bash
   php artisan migrate
   ```

7. Build frontend assets:
   ```bash
   npm run build
   ```

8. Start the development server:
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```

## Development

### Running in Development Mode
```bash
# Start Laravel development server
php artisan serve --host=0.0.0.0 --port=8000

# Start Vite development server (in another terminal)
npm run dev
```

### Docker Support
The project includes Docker configuration:
- `docker-compose.yml` for containerized development
- `dockerfile` for custom container builds

## File Structure

```
app/
├── Filament/
│   └── Resources/          # Admin panel resources
│       ├── BrandResource.php
│       ├── CategoryResource.php
│       ├── ProductResource.php
│       ├── PurchaseOrderResource.php
│       ├── SaleResource.php
│       └── SupplierResource.php
├── Models/                 # Eloquent models
│   ├── Brand.php
│   ├── Category.php
│   ├── Product.php
│   ├── PurchaseOrder.php
│   ├── PurchaseOrderItem.php
│   ├── Sale.php
│   ├── SaleItem.php
│   └── Supplier.php
└── Providers/
    └── Filament/
        └── AdminPanelProvider.php
```

## Key Features

### Inventory Tracking
- Real-time stock level monitoring
- Automatic stock updates on sales
- Cost price and selling price tracking

### Purchase Order Management
- Create orders with multiple items
- Track order status and totals
- Link to suppliers for easy management

### Sales Processing
- Record sales with multiple items
- Automatic stock deduction
- Client information tracking

### User Interface
- Clean, intuitive Filament admin panel
- Responsive design for mobile access
- Search and filter capabilities
- Bulk operations support

## Currency
The system uses Guatemalan Quetzal (Q) as the default currency, configurable in the Filament resources.

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run tests (if available)
5. Submit a pull request

## License

This project is licensed under the MIT License.

## Support

For issues and questions, please use the GitHub issue tracker.