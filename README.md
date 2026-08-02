# AgriSync — AI-Powered B2B Agriculture Marketplace

**AgriSync** is a decentralized B2B marketplace platform connecting Sri Lankan farmers directly with commercial buyers (supermarkets, restaurants, and grocers). By eliminating unnecessary middlemen and utilizing AI-driven demand prediction and automated order matching, AgriSync reduces post-harvest food waste and ensures fair-trade pricing for rural farming communities.

---

## Technical Stack & Architecture

- **Backend:** PHP 8.x (Vanilla, no heavy frameworks)
- **Database:** MySQL 8.x with PDO (Prepared statements ONLY)
- **Frontend:** HTML5 + Bootstrap 5.3 + Vanilla CSS + Vanilla JavaScript
- **AJAX:** Vanilla `fetch()` API (No jQuery dependencies)
- **Icons:** Bootstrap Icons 1.11 (CDN)
- **Charts:** Chart.js 4.x (CDN)
- **Typography:** Google Fonts — Inter

---

## Features Overview

### 1. Farmer Portal (`/farmer/`)
- **Harvest Yield Manager:** List expected crop yields with harvest dates, quantities (kg), and minimum pricing.
- **Order Match Manager:** View automated buyer pre-order matches, match confidence ratings, and accept or reject proposed orders.
- **Analytics Overview:** Visual dashboard summarizing active yields, total volume, and fulfilled earnings.

### 2. Business Buyer Portal (`/business/`)
- **Bulk Pre-Order Requests:** Place pre-orders specifying required crop, target yield (kg), budget limit, and target delivery date.
- **Live Market Availability:** Browse direct farm listings across Sri Lankan agricultural districts (Nuwara Eliya, Dambulla, Kandy, etc.).
- **Order Fulfillment Tracking:** Track accepted matches and confirm order delivery fulfillment.

### 3. Admin Management Portal (`/admin/`)
- **System Dashboard:** High-level platform analytics including user base growth, listed crop volume, and trade throughput.
- **User Directory:** Audit and manage registered farmers, business buyers, and admin accounts with one-click status toggling (`is_active`).
- **Audit Trails:** Comprehensive audit logs for all harvest listings, pre-orders, and automated matching agent steps (`agent_logs`).

---

## Local Setup & Installation Instructions

### Prerequisites
- PHP 8.1+
- MySQL / MariaDB Server 8.0+
- Web server (Apache with `mod_rewrite` enabled or Nginx)

### Step 1: Clone Repository
```bash
git clone https://github.com/Chvi2005/AgriSync.git
cd AgriSync
```

### Step 2: Import Database Schema & Seed Data
1. Create a MySQL database named `agrisync`:
```sql
CREATE DATABASE agrisync CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```
2. Import the schema definition:
```bash
mysql -u root -p agrisync < sql/schema.sql
```
3. Import sample seed data:
```bash
mysql -u root -p agrisync < sql/seed.sql
```

### Step 3: Configure Environment Constants
Copy `config/constants.example.php` to `config/constants.php` and update your database credentials:
```bash
cp config/constants.example.php config/constants.php
```
Verify the settings in `config/constants.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'agrisync');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### Step 4: Run Application
Serve the root directory via Apache or PHP built-in web server:
```bash
php -S localhost:8000
```
Open `http://localhost:8000` in your browser.

---

## Demo Account Credentials

All demo accounts come pre-seeded with the default password: **`password123`**

| Role | Name | Email | Password |
|---|---|---|---|
| **Farmer** | Bandara Herath | `farmer@agrisync.lk` | `password123` |
| **Farmer** | Somasiri Silva | `dambulla.farmer@agrisync.lk` | `password123` |
| **Business** | Keells Supermarket | `buyer@agrisync.lk` | `password123` |
| **Business** | Cargills Food City | `cargills@agrisync.lk` | `password123` |
| **Admin** | AgriSync System Admin | `admin@agrisync.lk` | `password123` |

---

## API Documentation

All API endpoints return JSON in the following standard envelope:
```json
{
  "success": true,
  "data": { ... },
  "error": null
}
```

### Core API Routes
- **`POST /api/auth.php?action=login`**: User login endpoint
- **`POST /api/auth.php?action=register`**: User registration endpoint
- **`GET /api/farmer.php?action=get_dashboard`**: Farmer dashboard stats & charts
- **`GET /api/farmer.php?action=get_listings`**: Farmer harvest yield listings
- **`POST /api/farmer.php?action=create_listing`**: Create new harvest yield listing
- **`POST /api/farmer.php?action=respond_match`**: Accept or reject order match
- **`GET /api/business.php?action=get_dashboard`**: Business dashboard metrics & live crop availability
- **`POST /api/business.php?action=create_request`**: Place bulk crop pre-order request
- **`POST /api/business.php?action=complete_order`**: Confirm & fulfill matched order
- **`GET /api/admin.php?action=get_metrics`**: High-level platform metrics & logs
- **`POST /api/admin.php?action=toggle_user_status`**: Toggle user account active status
- **`GET /api/notifications.php?action=list`**: Fetch unread notifications

---

## License
This project is open-source and licensed under the [MIT License](LICENSE).
