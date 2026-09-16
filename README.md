# Store Order & Inventory Mini-System

A Laravel 13 REST API for managing products, customers and orders.

The project also handles stock updates safely when multiple orders are placed at the same time.

## Requirements

- PHP 8.3+
- Composer
- MySQL or SQLite
- Node.js and NPM
- Laravel Herd / Xampp

## Setup

### 1. Clone the project

```bash
git clone https://github.com/saravanapriyasubburaj-dev/Inventory_mini_system.git
cd Inventory_mini_system
````

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Setup environment

Copy the example environment file:

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Update the database settings in `.env`.

For example:

```env
DB_DATABASE=inventory_db
DB_USERNAME=root
DB_PASSWORD=
```

For the queue:

```env
QUEUE_CONNECTION=database
```

### 4. Run migrations and seed data

```bash
php artisan migrate --seed
```

The seeder creates:

* 10 manually added retail products
* 15 additional products using the ProductFactory
* 10 sample customers

### 5. Install frontend dependencies Laravel 13 uses Vite for frontend assets

Install the required NPM packages:

```bash
npm install
```

Build the assets:

```bash
npm run build
```

### 6. Start the application

#### Using Laravel Herd

This project was developed and tested using Laravel Herd with Nginx.

If you are using Laravel Herd, place the project inside your Herd directory and start Herd. else use xampp continue artisan command

For example:
```text
C:\Users\<username>\Herd\Inventory_mini_system

http://inventory_mini_system.test 
 
```bash
php artisan serve
```

The API will be available at:

```text
http://127.0.0.1:8000
```

### 7. Start the queue worker

Open another terminal and run:

```bash
php artisan queue:work
```

The queue is used for the order confirmation job.

## API Endpoints

| Method | Endpoint                      | Description                            |
| ------ | ----------------------------- | -------------------------------------- |
| GET    | `/api/products`               | Get all products                       |
| GET    | `/api/products/low-stock`     | Get products below the stock threshold |
| POST   | `/api/orders`                 | Create a new order                     |
| GET    | `/api/orders/history/{email}` | Get order history for a customer       |

## Create Order

### Endpoint

```text
POST /api/orders
```

### Example Request

```json
{
    "customer": {
        "name": "Saravanapriya",
        "email": "saravanapriya@gmail.com"
    },
    "items": [
        {
            "product_id": 1,
            "quantity": 2
        },
        {
            "product_id": 5,
            "quantity": 1
        }
    ]
}
```

The API will:

1. Validate the request.
2. Check the requested products.
3. Check available stock.
4. Calculate item taxes.
5. Calculate subtotal, tax and grand total.
6. Deduct the stock.
7. Create the order and order items.
8. Dispatch the order confirmation job.

## Main Implementation

### Order Service

The main order logic is handled in:

```text
app/Services/OrderService.php
```

The controller only handles the request and response.

The service handles:

* Order creation
* Stock checking
* Stock deduction
* Tax calculation
* Order totals

### Transaction and Stock Locking

The order creation process uses:

```php
DB::transaction()
```

Products are fetched using:

```php
lockForUpdate()
```

This locks the selected product rows while the order is being processed.

This helps prevent two concurrent requests from purchasing more stock than is available.

If there is not enough stock, an exception is thrown and the transaction is rolled back.

## Queue Job

Order confirmation is handled by:

```text
app/Jobs/SendOrderConfirmationJob.php
```

The job implements:

```php
ShouldQueue
```

No real email configuration is used for this task.

Instead, a successful order confirmation is written to:

```text
storage/logs/laravel.log
```

using:

```php
Log::info()
```

## Validation

The order request is validated using:

```text
app/Http/Requests/StoreOrderRequest.php
```

The request validates:

* Customer name
* Customer email
* Product ID
* Quantity
* Order items

## Testing

Feature tests are included for the main order API scenarios.

Run the tests using:

```bash
php artisan test
```

The tests cover:

### Successful Order

* Order creation
* Tax calculation
* Stock deduction
* Queue job dispatch

### Insufficient Stock

* API returns `422`
* Order is not created
* Stock is not changed
* Database transaction is rolled back

## AI Prompt Documentation

The AI prompts used during development are available in:

```text
/prompts
```

```
```
