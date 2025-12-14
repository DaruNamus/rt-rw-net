# RT-RW Net Management System v2

A comprehensive web application designed to streamline the management of community internet services (RT/RW Net). This system handles customer subscriptions, billing, payments, and automated notifications.

![Dashboard Preview](https://via.placeholder.com/1200x600.png?text=RT-RW+Net+Dashboard)

## 🚀 Key Features

### 👥 User Roles
*   **Administrator**: Full control over the system.
    *   Manage Internet Packages (Bandwidth, Price).
    *   Manage User/Customer Data.
    *   Generate and Monitor Monthly Bills (Tagihan).
    *   Verify Payment Proofs.
    *   Process Package Upgrade Requests.
*   **Customer (Pelanggan)**:
    *   Personal Dashboard.
    *   View Active Package & Status.
    *   Check Unpaid Bills & History.
    *   Upload Payment Proofs.
    *   Request Package Upgrades.

### 💰 Billing & Payments
*   **Flexible Billing**: Supports monthly bills and ad-hoc charges.
*   **Zero-Value Bills**: Handles free transactions (e.g., free upgrades) with proper receipt generation.
*   **Payment Verification**: Admin verification workflow for manual bank transfers/cash payments.
*   **Printable Receipts**: Optimized "Struk" layout for thermal printers (58mm/80mm) and standard A4/PDF.

### 📱 WhatsApp Integration
*   **Direct Notification**: Send payment receipts directly to the customer's WhatsApp with a single click.
*   **Smart Reminders**: "Ingatkan" button for unpaid bills that generates a pre-formatted WhatsApp message including the due date and amount.
*   **Auto-formatting**: Automatically converts local phone numbers (08xxx) to international format (628xxx).

## 🛠️ Technology Stack

*   **Framework**: [Laravel 11](https://laravel.com)
*   **Language**: PHP 8.2+
*   **Frontend**: [Tailwind CSS](https://tailwindcss.com), [Alpine.js](https://alpinejs.dev), Laravel Blade
*   **Database**: MySQL / MariaDB
*   **Authentication**: Laravel Breeze

## ⚙️ Installation

1.  **Clone the repository**
    ```bash
    git clone https://github.com/username/rt-rw-net-v2.git
    cd rt-rw-net-v2
    ```

2.  **Install PHP Dependencies**
    ```bash
    composer install
    ```

3.  **Install NPM Dependencies**
    ```bash
    npm install && npm run build
    ```

4.  **Environment Setup**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    *Configure your database (DB_DATABASE, DB_USERNAME, etc.) in the `.env` file.*

5.  **Database Migration & Seeding**
    ```bash
    php artisan migrate --seed
    ```

6.  **Run the Application**
    ```bash
    php artisan serve
    ```

## 📝 License
This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
