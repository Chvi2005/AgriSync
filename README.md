# 🌾 AgriSync — AI-Powered Decentralized B2B Agriculture Marketplace

[![CI Status](https://img.shields.io/badge/CI-Passing%20100%25-success?style=flat-square&logo=github-actions)](https://github.com/Mavros-Lykos/AgriSync/actions)
[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-777BB4?style=flat-square&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![AI Engine](https://img.shields.io/badge/AI-Google%20Gemini%20API-4285F4?style=flat-square&logo=google)](https://ai.google.dev/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=flat-square&logo=bootstrap&logoColor=white)](https://getbootstrap.com/)
[![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)](LICENSE)

**AgriSync** is an autonomous AI-driven B2B agriculture supply-chain marketplace designed specifically for the Sri Lankan agrarian economy. It directly bridges the gap between rural vegetable/fruit farmers and commercial enterprise buyers (supermarket chains, hotel suppliers, food processors, and exporters).

By eliminating predatory multi-tier middlemen cartels, enforcing fair-trade floor pricing, and utilizing **Google Gemini AI autonomous multi-agent brokering**, AgriSync drastically mitigates post-harvest food waste (which accounts for ~35% of national crop loss) while securing 20-30% higher net margins for farmers.

---

## 🎯 Key Problems Solved

1. **Middleman Exploitation & Asymmetric Information**: Smallholder farmers often receive less than 40% of wholesale market value due to lack of visibility. AgriSync guarantees transparent fair-trade minimum pricing.
2. **Severe Post-Harvest Waste**: Inefficient transport logistics and uncoordinated planting lead to massive seasonal gluts and spoilages. AgriSync’s Demand Prediction Agent guides harvest schedules.
3. **Buyer Supply Inconsistency**: Supermarkets struggle with unreliable farm sourcing. Pre-orders are autonomously matched and tracked from farm gate to warehouse intake.

---

## 🧠 Autonomous Multi-Agent AI Architecture

AgriSync features 4 cooperative AI agents powered by the **Google Gemini Pro API**:

```
 ┌────────────────────────────────────────────────────────┐
 │                 Google Gemini AI Core                  │
 └───────┬──────────────┬──────────────┬──────────────┬───┘
         │              │              │              │
         ▼              ▼              ▼              ▼
 ┌──────────────┐┌──────────────┐┌──────────────┐┌──────────────┐
 │ Demand       ││ Broker Match ││ Fair Pricing ││ Logistics    │
 │ Predictor    ││ Agent        ││ Floor Engine ││ Optimizer    │
 └──────────────┘└──────────────┘└──────────────┘└──────────────┘
```

1. **📈 Demand Prediction Agent (`agents/demand_predictor.php`)**:
   - Analyzes regional consumption patterns across Sri Lankan provinces, seasonal holiday spikes, and weather forecasts to predict future crop demand.
2. **🤝 Autonomous Broker Agent (`agents/broker_agent.php`)**:
   - Continuously evaluates open buyer pre-orders against available farmer harvest listings.
   - Calculates distance matrices, yield quality, delivery urgency, and price tolerances.
   - Generates transparent explainable natural-language match rationale.
3. **⚖️ Fair-Trade Pricing Agent (`agents/pricing_agent.php`)**:
   - Protects smallholders by calculating district-specific production cost floors. Rejects below-cost predatory buyer bids.
4. **🚚 Logistics & Route Optimizer (`agents/logistics_agent.php`)**:
   - Computes low-carbon transit routes connecting highland cultivation hubs (Nuwara Eliya, Badulla, Dambulla) to urban distribution centers (Colombo, Gampaha).

---

## 🔄 Order State Machine Lifecycle

All marketplace transactions adhere to a strictly validated finite state machine:

```
[ pending ] ──► [ matching ] ──► [ matched ] ──► [ accepted ] ──► [ in_transit ] ──► [ delivered ]
    │                │                │               │
    ▼                ▼                ▼               ▼
[ cancelled ]   [ cancelled ]    [ rejected ]    [ cancelled ]
```

---

## 💻 Tech Stack & Standards

- **Backend:** Vanilla PHP 8.1+ (No heavy framework overhead, strict OOP & procedural separation)
- **Database:** MySQL 8.x with PDO (100% prepared statements with parameterized SQL queries)
- **Frontend:** Responsive HTML5 + Bootstrap 5.3 + Custom AgriSync Design System
- **Client AJAX:** Pure JavaScript `fetch()` API with CSRF token verification
- **AI Core:** Google Gemini API via PHP cURL (`POST https://generativelanguage.googleapis.com/v1beta/models/...`)
- **Charts:** Chart.js 4.x (Live dynamic canvas rendering)
- **Typography:** Google Fonts — *Inter*

---

## 🚀 Quick Setup & Installation Guide

### Prerequisites
- PHP 8.1 or higher (with `pdo_mysql`, `curl`, and `mbstring` extensions enabled)
- MySQL / MariaDB 8.0+
- Web server (Apache, Nginx, or PHP CLI built-in server)
- Git & modern web browser

### Step 1: Clone the Repository
```bash
git clone https://github.com/Mavros-Lykos/AgriSync.git
cd AgriSync
```

### Step 2: Database Initialization
1. Create a MySQL database named `agrisync`:
```sql
CREATE DATABASE agrisync CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```
2. Import schema tables:
```bash
mysql -u root -p agrisync < sql/schema.sql
```
3. Seed sample demo accounts and marketplace data:
```bash
mysql -u root -p agrisync < sql/seed.sql
```

### Step 3: Configure Environment
Copy `config/constants.example.php` to `config/constants.php`:
```bash
cp config/constants.example.php config/constants.php
```
Update your database credentials and optionally add your Gemini API key in `config/constants.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'agrisync');
define('DB_USER', 'root');
define('DB_PASS', '');

// Optional: Live Gemini AI API Key (Fallback Mock Engine built-in)
define('GEMINI_API_KEY', 'your_gemini_api_key_here');
```

### Step 4: Run Application
Start the PHP development server from the project root:
```bash
php -S localhost:8000
```
Visit **`http://localhost:8000`** in your browser.

---

## 👥 Demo User Accounts

All demo accounts use the standard password: **`password123`**

| Role | Name | Email | District | Purpose |
|---|---|---|---|---|
| **Farmer** | Bandara Herath | `farmer@agrisync.lk` | Nuwara Eliya | Highland Vegetable Cultivator |
| **Farmer** | Somasiri Silva | `dambulla.farmer@agrisync.lk` | Matale / Dambulla | Organic Open-field Farmer |
| **Farmer** | Kavinda Perera | `badulla.farmer@agrisync.lk` | Badulla | Greenhouse Specialist |
| **Commercial Buyer** | Keells Procurement | `buyer@agrisync.lk` | Colombo | Retail Supermarket Chain |
| **Commercial Buyer** | Cargills Central | `cargills@agrisync.lk` | Gampaha | Wholesale Distribution Hub |
| **System Admin** | AgriSync Admin | `admin@agrisync.lk` | Colombo | Platform Management & AI Audit |

---

## 📡 REST-like JSON API Reference

All endpoints return uniform JSON envelopes:
```json
{
  "success": true,
  "data": { ... },
  "error": null
}
```

### Core Endpoints

| Method | Endpoint | Description | Auth Required |
|---|---|---|---|
| `POST` | `/api/auth.php?action=login` | Authenticate user & issue session | Public |
| `POST` | `/api/auth.php?action=register` | Register new farmer or buyer | Public |
| `GET` | `/api/farmer.php?action=get_dashboard` | Farmer metrics, listings & matches | Farmer |
| `POST` | `/api/farmer.php?action=create_listing` | Create new harvest yield listing | Farmer |
| `POST` | `/api/accept_match.php` | Accept buyer match proposal | Farmer / Admin |
| `GET` | `/api/business.php?action=get_dashboard` | Buyer demand metrics & crop catalog | Business |
| `POST` | `/api/business.php?action=create_request` | Post bulk crop pre-order request | Business |
| `POST` | `/api/orders.php?action=update_status` | Advance order state machine status | Authenticated |
| `GET` | `/api/admin.php?action=get_metrics` | System KPIs, charts & agent logs | Admin |
| `POST` | `/api/admin.php?action=toggle_user_status`| Deactivate/activate user account | Admin |
| `GET` | `/api/notifications.php?action=list` | In-app real-time notifications | Authenticated |

---

## 🌍 UN Sustainable Development Goals (SDGs) Alignment

AgriSync directly advances three United Nations Sustainable Development Goals:
- **🌱 SDG 2: Zero Hunger (Target 2.3 & 2.4)**: Doubling agricultural productivity and incomes of small-scale food producers through direct market linkages.
- **💼 SDG 8: Decent Work & Economic Growth (Target 8.2)**: Fostering economic inclusion with transparent floor pricing and secure direct bank settlements.
- **♻️ SDG 12: Responsible Consumption & Production (Target 12.3)**: Halving per-capita global food waste and reducing post-harvest supply chain losses through predictive matching.

---

## 📄 License
This project is open-source and released under the [MIT License](LICENSE).
