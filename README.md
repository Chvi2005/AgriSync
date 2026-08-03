# 🌾 AgriSync — AI-Powered Agricultural Supply Chain Platform

[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](https://opensource.org/licenses/MIT)
[![PHP: 8.x](https://img.shields.io/badge/PHP-8.x-777BB4.svg?logo=php&logoColor=white)](https://php.net)
[![MySQL: 8.x](https://img.shields.io/badge/MySQL-8.x-4479A1.svg?logo=mysql&logoColor=white)](https://mysql.com)
[![Google Gemini AI](https://img.shields.io/badge/AI-Google%20Gemini%202.5%20Flash-4285F4.svg?logo=google&logoColor=white)](https://ai.google.dev)
[![Bootstrap: 5.3](https://img.shields.io/badge/Frontend-Bootstrap%205.3-7952B3.svg?logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![UN SDGs](https://img.shields.io/badge/UN%20SDGs-Goal%202%20%7C%208%20%7C%2012-00A651.svg)](https://sdgs.un.org)

> **AgriSync** is an intelligent, fair-trade B2B agricultural marketplace and supply chain coordinator for Sri Lanka. By connecting smallholder farmers directly with verified commercial buyers (supermarkets, food processors, exporters) via **Google Gemini autonomous AI agents**, AgriSync eliminates exploitative middlemen cartels, minimizes post-harvest food waste (~35% national loss), and ensures guaranteed fair-trade floor pricing.

---

## 📌 Table of Contents

1. [Key Problems & Value Proposition](#-key-problems--value-proposition)
2. [How AI Was Utilized](#-how-ai-was-utilized)
   - [In-Application Multi-Agent Intelligence](#1-in-application-multi-agent-intelligence)
   - [AI in Engineering, UI Prototyping & GitHub Management](#2-ai-in-engineering-ui-prototyping--github-management)
3. [System Architecture & Multi-Agent Workflow](#-system-architecture--multi-agent-workflow)
4. [United Nations SDGs Alignment](#-united-nations-sdgs-alignment)
5. [Core User Portals & Features](#-core-user-portals--features)
6. [Technology Stack & Architectural Pivot](#-technology-stack--architectural-pivot)
   - [Original Proposal vs. Implemented Architecture](#original-proposal-vs-implemented-architecture)
   - [Strategic Justification for Pivoting to PHP & MySQL](#strategic-justification-for-pivoting-to-php--mysql)
7. [Installation & Quick Start Guide](#-installation--quick-start-guide)
8. [Demo Accounts & Walkthrough](#-demo-accounts--walkthrough)
9. [Automated Test Suite & Verification](#-automated-test-suite--verification)

---

## 🎯 Key Problems & Value Proposition

In Sri Lanka's agricultural supply chain, smallholder farmers face severe structural vulnerabilities:

1. **Middleman Exploitation & Asymmetric Pricing**: Farmers frequently receive less than 40% of retail wholesale prices due to multi-tier middleman cartels. AgriSync enforces an automated **Fair-Trade Minimum Multiplier (1.20x base margin)** on all matched orders.
2. **Catastrophic Post-Harvest Waste (35% - 40%)**: Uncoordinated planting and transit delays cause severe seasonal gluts and crop rot. AgriSync’s **Demand Prediction Agent** guides pre-harvest schedules before seeds are planted.
3. **Buyer Procurement Inconsistency**: Supermarket chains and exporters struggle with unpredictable supply volumes and fragmented farmer contacts. AgriSync provides automated contract matching from farm gate to warehouse delivery.

---

## 🤖 How AI Was Utilized

### 1. In-Application Multi-Agent Intelligence

AgriSync integrates **Google Gemini AI** through a resilient, multi-tiered autonomous agent architecture:

```
                  ┌──────────────────────────────────────────────┐
                  │          AgriSync Agent Gateway              │
                  └──────────────────────┬───────────────────────┘
                                         │
                 ┌───────────────────────┴───────────────────────┐
                 ▼                                               ▼
   ┌───────────────────────────┐                   ┌───────────────────────────┐
   │    Demand Predictor       │                   │     AI Broker Agent       │
   │  - Maha / Yala Seasons    │                   │  - Proximity Matching     │
   │  - Agro-Ecological Zones  │                   │  - Fair-Trade Floor Price │
   │  - Economic Center Trends │                   │  - Freshness Constraints  │
   └─────────────┬─────────────┘                   └─────────────┬─────────────┘
                 │                                               │
                 └───────────────────────┬───────────────────────┘
                                         │
                                         ▼
                 ┌───────────────────────────────────────────────┐
                 │       Multi-Model Fallback Chaining           │
                 │   1. Gemini 2.5 Flash (Primary)               │
                 │   2. Gemini Flash Latest (Fast Failover)      │
                 │   3. Gemini 2.0 Flash (Secondary)             │
                 │   4. Domain Heuristic Knowledge Base (Safe)   │
                 └───────────────────────────────────────────────┘
```

- **🌾 AI Demand Prediction Agent (`agents/demand_agent.php`)**:
  - Analyzes Sri Lankan agro-ecological zones (Up-country wet zones, Low-country dry zones), monsoon cycles (*Maha* from Sept–March, *Yala* from May–August), and historical economic center pricing (Dambulla, Meegoda, Keppetipola).
  - Delivers actionable advisory on staggered harvesting, contract timing, and crop rotation.
- **🤝 Autonomous AI Broker Agent (`agents/broker_agent.php`)**:
  - Multi-variable constraint solver that pairs buyer pre-orders with optimal farmer listings.
  - Scores listings based on geographic proximity (reducing food miles), harvest freshness window, and guaranteed fair-trade farmer margins.
  - Returns explainable AI reasoning logged transparently to the platform database.
- **🛡️ Multi-Model Fallback & High-Availability Engine (`agents/gemini_client.php`)**:
  - Eliminates single-point-of-failure risks by automatically chaining across Gemini models (`gemini-2.5-flash` → `gemini-flash-latest` → `gemini-2.0-flash`).
  - If external network outages occur, the system seamlessly transitions to verified domain-specific rule heuristics to guarantee uninterrupted uptime.

---

### 2. AI in Engineering, UI Prototyping & GitHub Management

Modern AI tooling was leveraged throughout the end-to-end development lifecycle:

- **⚡ Rapid UI/UX Design & Prototyping**:
  - Utilized Antigravity AI agentic workflows to rapidly construct responsive Bootstrap 5 dashboards, design tokens, and Chart.js analytics views.
- **📐 Database Schema & Clean Architecture**:
  - Designed normalized MySQL 8 schemas with strict foreign keys, cascade safety, and PDO prepared statements to guarantee zero SQL injection vulnerabilities.
- **🔄 Git & GitHub Issue Lifecycle Management**:
  - Tracked and executed development across 25+ granular GitHub issues organized by P0/P1/P2 milestones with atomic conventional commits (`feat`, `fix`, `test`).
- **🧪 Automated Test-Driven Verification (TDD)**:
  - Constructed an automated 20-point end-to-end verification suite (`tests/test_agents.php`) testing live agent responses, fallback mechanisms, and database transactions.

---

## 🏗️ System Architecture & Multi-Agent Workflow

```
agrisync/
├── admin/               # Admin diagnostics, live AI monitor, SDG impact dashboards
├── agents/              # Gemini AI autonomous agents & telemetry loggers
│   ├── agent_logger.php # Agent audit trail persistence
│   ├── broker_agent.php # Multi-constraint matching engine
│   ├── demand_agent.php # Agro-climatic forecasting agent
│   └── gemini_client.php# Resilient Gemini API client with fallback chaining
├── api/                 # RESTful JSON endpoints (authenticated & CSRF protected)
├── assets/              # Design system, CSS tokens, Chart.js 4.x scripts
├── auth/                # Session manager, bcrypt authentication, role guards
├── business/            # Commercial buyer procurement portal & order placement
├── config/              # Constants, PDO connection pool, session configurations
├── docs/                # Architecture specifications & system design documentation
├── farmer/              # Farmer yield manager, AI advisory, match approval
├── includes/            # Reusable components (header, sidebar, navbar, notifications)
├── sql/                 # Database schema definitions and seed data
└── tests/               # Automated end-to-end test suite
```

---

## 🌍 United Nations SDGs Alignment

| UN SDG | Target | AgriSync Direct Impact |
|---|---|---|
| **🌱 SDG 2: Zero Hunger** | **Target 2.3 & 2.4** | Doubles smallholder farmer revenue and halves post-harvest food waste by coordinating pre-orders before harvest. |
| **💼 SDG 8: Decent Work & Economic Growth** | **Target 8.2 & 8.5** | Guarantees minimum 20% fair-trade floor pricing and establishes formal digital market access for rural farmers. |
| **♻️ SDG 12: Responsible Consumption** | **Target 12.3** | Reduces food transit miles by prioritizing localized district-level matching to cut logistics emissions. |

---

## 💻 Core User Portals & Features

### 🚜 1. Farmer Portal (`/farmer/`)
- **Produce Yield Listing**: Add crops with expected harvest dates, minimum floor prices, and quantity in kilograms.
- **AI Demand Insights**: Query Gemini AI for real-time demand forecasts, price projections, and planting advice.
- **Match Proposals**: Review incoming algorithmic match proposals with full price breakdowns and one-click Accept/Reject controls.
- **Order History**: Track fulfillment stages from in-transit dispatch to buyer payout.

### 🏢 2. Commercial Buyer Portal (`/business/`)
- **Pre-Order Placement**: Specify required crop, target quantity, maximum budget per kg, and required delivery date.
- **Autonomous Matching**: Instant agent matchmaking against active farmer listings with explainable AI reasoning.
- **Procurement Analytics**: Visual breakdown of sourcing volume, fulfillment rate, and active contracts.
- **Organization Profile**: Manage delivery logistics hubs, company registration, and procurement contacts.

### 🛡️ 3. Executive Admin Portal (`/admin/`)
- **Live AI Agent Monitor**: Real-time audit telemetry of prompt payloads, response tokens, execution times, and reasoning.
- **SDG Impact Dashboard**: Quantitative metrics on food miles saved (km), spoilage prevented (kg), and revenue uplift (%).
- **One-Click CSV Export**: Download structured ESG audit reports for sustainability reporting.
- **System Diagnostics**: Server health, Gemini API connectivity, database status, and active user metrics.

---

## 🛠️ Technology Stack & Architectural Pivot

### Core Technology Stack
- **Backend Core**: PHP 8.x (Vanilla, Strict Object-Oriented Architecture, PDO Prepared Statements)
- **Relational Database**: MySQL 8.x with InnoDB Storage Engine, Foreign Keys, Referential Integrity, and Indexes
- **AI Subsystem**: Google Gemini 2.5 Flash / Flash Latest via Native cURL & JSON Structured Mode Chaining
- **Frontend Architecture**: HTML5, Bootstrap 5.3.3 Design System, Vanilla JavaScript (ES6+ `fetch` API), Custom CSS Tokens
- **Data Visualizations & Analytics**: Chart.js 4.4 CDN
- **Icons & Typography**: Bootstrap Icons, Google Fonts (Inter)
- **Security & Integrity**: CSRF Token Guarding across all mutating requests, Bcrypt Password Hashing (`PASSWORD_BCRYPT`), Full XSS Output Escaping (`htmlspecialchars`)

---

### 🔄 Architectural Pivot: Transition from MERN Stack to PHP & MySQL

In accordance with the **IDEALIZE 2026 Prototype Submission Guidelines** (*"Any pivots from the original proposal (tech stack, features, or agent behavior) must be clearly explained and justified"* — see [`prototypeSubmission.md`](prototypeSubmission.md)), this section documents our strategic transition from our initial proposal ([`projectProposal.md`](projectProposal.md)) to the final implemented prototype.

#### 1. Original Proposal vs. Implemented Architecture

| Architectural Layer | Initial Proposal (`projectProposal.md`) | Final Implemented Prototype |
|---|---|---|
| **Backend & Runtime** | Node.js with Express.js | **PHP 8.x (Vanilla Strict OOP)** |
| **Database System** | MongoDB / Firebase / PostgreSQL | **MySQL 8.x (InnoDB with ACID Transactions & Foreign Keys)** |
| **Frontend Framework** | React.js / Next.js & React Native | **Vanilla JavaScript (ES6+), Bootstrap 5.3.3 & Semantic HTML5** |
| **AI Integration Layer**| Firebase Genkit | **Google Gemini API (cURL + Multi-Model Fallback Engine)** |

#### 2. Strategic Justification for the Pivot

1. **Leveraging Core Familiarity vs. Steep Framework Overhead**:
   - In our original project proposal, the team enthusiastically proposed the **MERN Stack** (MongoDB, Express, React, Node.js) with the ambition of expanding our technical horizons and learning modern JavaScript frameworks.
   - However, during the early development sprint, the team encountered severe **academic scheduling pressures, semester coursework deadlines, and examination commitments**.
   - Developing with React and Node.js required substantial time dedicated to configuring bundlers, asynchronous state management, complex microservices, and debugging framework-specific build pipelines, which diverted critical hours away from the core domain problem.

2. **Rapid, Frictionless Delivery with Proven Technologies**:
   - The team possessed solid, practical, and battle-tested experience with **PHP and MySQL**. Choosing PHP enabled the team to achieve immediate velocity, writing clean, performant, and reliable code without getting bogged down by dependency friction or build configurations.
   - PHP 8.x provides modern object-oriented paradigms, exceptional execution speed, built-in session management, and robust native networking capabilities.

3. **100% Focus on High-Impact Innovation (Multi-Agent AI & Fair Trade)**:
   - By eliminating framework friction, the team was able to concentrate all engineering effort on what mattered most for the competition:
     - Engineering genuine, autonomous **AI Agent Workflows** ([`DemandAgent`](agents/demand_agent.php) and [`BrokerAgent`](agents/broker_agent.php)) powered by **Google Gemini 2.5 Flash**.
     - Constructing an ultra-resilient **Multi-Model Fallback Engine** ([`GeminiClient`](agents/gemini_client.php)) with automatic failover to local Sri Lankan agricultural heuristic knowledge bases.
     - Designing an algorithmic multi-constraint matching engine that calculates localized food miles, harvest freshness windows, and a guaranteed **1.20x Fair-Trade Minimum Price Multiplier**.
     - Developing three fully functional, dedicated portals for **Farmers**, **Commercial Buyers**, and **System Administrators**.

4. **Relational Data Integrity & Deterministic Transactions**:
   - Sri Lankan agricultural commerce involves strict transactional safety: crop listing quantities, buyer budgets, multi-stage order confirmations, and audit logging.
   - **MySQL 8.x with InnoDB** provided native ACID guarantees, strict foreign key cascade rules, and relational joins that offered greater stability and consistency than a NoSQL schema like MongoDB.

5. **Zero-Configuration Portability & Robust Evaluation**:
   - The final PHP 8 & MySQL architecture runs seamlessly using PHP's built-in web server (`php -S localhost:8000`) and standard MySQL with zero npm/node build dependencies, ensuring judges and reviewers can effortlessly clone, configure, run, and evaluate the entire application in under 2 minutes.

### 🔮 Future Plans & Next Phase Enhancements

As per our original proposal, we envisioned three autonomous agents. While we successfully implemented the **Demand Prediction Agent** and **Broker Agent**, we made the strategic decision to defer the **Logistics Agent** to the next phase of development. 

1. **The Logistics Agent (Phase 2)**: 
   - We will introduce our third AI agent which will automatically trigger when a farmer accepts a match. It will use the **Google Maps Route Optimization API** and **Twilio SMS API** to autonomously ping freelance truck drivers and negotiate delivery rates. This requires real-world driver data and API quotas which we plan to acquire post-prototype.
2. **Mobile Expansion (Phase 2)**:
   - While the current platform is fully responsive for mobile browsers (Bootstrap 5), we plan to launch a dedicated vernacular Android app (via React Native/Expo) specifically tailored for farmers who might have limited digital literacy, streamlining their harvest listing process even further.

---

## 🚀 Installation & Quick Start Guide

### Prerequisites
- **PHP**: 8.1 or higher (with `pdo_mysql` and `curl` extensions enabled)
- **MySQL**: 8.0 or higher
- **Composer / Git**: Standard Git client

### 1. Clone the Repository
```bash
git clone https://github.com/Mavros-Lykos/AgriSync.git
cd AgriSync
```

### 2. Configure Database & Environment
1. Create a MySQL database named `agrisync`:
   ```sql
   CREATE DATABASE agrisync CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
2. Import schema and seed records:
   ```bash
   mysql -u root -p agrisync < sql/schema.sql
   mysql -u root -p agrisync < sql/seed.sql
   ```
3. Create your configuration file:
   ```bash
   cp config/constants.example.php config/constants.php
   ```
4. Set your database credentials and **Google Gemini API Key** in `config/constants.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'agrisync');
   define('DB_USER', 'root');
   define('DB_PASS', 'your_password');
   define('GEMINI_API_KEY', 'your-google-gemini-api-key');
   define('GEMINI_MODEL', 'gemini-2.5-flash');
   ```

### 3. Launch Development Server
```bash
php -S localhost:8000
```
Open your browser and navigate to: **`http://localhost:8000`**

---

## 🔑 Demo Accounts & Walkthrough

| Role | Email | Password | Primary Dashboard |
|---|---|---|---|
| **Farmer (Nuwara Eliya)** | `kamal@agrisync.lk` | `password123` | `http://localhost:8000/farmer/dashboard.php` |
| **Farmer (Matale)** | `bandara@agrisync.lk` | `password123` | `http://localhost:8000/farmer/dashboard.php` |
| **Commercial Buyer (Cargills)** | `procurement@cargills.lk` | `password123` | `http://localhost:8000/business/dashboard.php` |
| **Commercial Buyer (Keells)** | `sourcing@keells.lk` | `password123` | `http://localhost:8000/business/dashboard.php` |
| **System Administrator** | `admin@agrisync.lk` | `password123` | `http://localhost:8000/admin/dashboard.php` |

---

## 🧪 Automated Test Suite & Verification

AgriSync includes an automated, self-contained end-to-end test suite verifying Gemini API connectivity, demand prediction accuracy, multi-step broker matchmaking, and transactional state safety.

Run the test suite via command line:
```bash
php tests/test_agents.php
```

### Expected Output:
```
=======================================================
       AgriSync AI Agents End-to-End Test Suite        
=======================================================
   - Connected to Live MySQL Database
1. Testing Gemini Client...
  [PASS] GeminiClient instantiates successfully
2. Testing Demand Prediction Agent (TASK-069)...
  [PASS] Prediction returned success=true for Tomato in Dambulla
  [PASS] Prediction returned success=true for Carrot in Nuwara Eliya
  [PASS] Prediction returned success=true for Big Onion in Matale
3. Testing AI Broker Agent Multi-Step Matching (TASK-055)...
  [PASS] Broker Agent matched order successfully
  [PASS] Match contains explainable AI reasoning
=======================================================
Tests Passed: 20 | Tests Failed: 0
=======================================================
ALL AI AGENT TESTS PASSED WITH ZERO ERRORS!
```

---

## 📄 License
This project is licensed under the MIT License — see the [LICENSE](LICENSE) file for details.
