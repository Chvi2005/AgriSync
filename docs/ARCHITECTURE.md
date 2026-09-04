# 🏗️ AgriSync System Architecture & Design Specification

## Overview
AgriSync is engineered as a high-performance, secure, modular B2B web application. It enforces zero-trust database sanitization via PDO prepared statements, a finite state machine for order fulfillment, and asynchronous autonomous AI agent workers.

---

## 1. Directory Structure

```
agrisync/
├── admin/               # Administrative portal & system monitoring dashboards
├── agents/              # Autonomous Gemini AI agent workers & prompt engineering
│   ├── agent_logger.php # Agent audit and telemetry persistence
│   ├── broker_agent.php # Autonomous matchmaker engine
│   ├── demand_agent.php # Crop consumption demand predictor
│   └── gemini_client.php# cURL wrapper for Google Gemini API
├── api/                 # RESTful JSON endpoints (authenticated with CSRF protection)
├── assets/              # Static styling, Chart.js, Bootstrap 5.3, custom JS
├── auth/                # Session authentication, login, register, and RBAC guards
├── business/            # Commercial buyer portal & pre-order manager
├── config/              # Constants, PDO database pool, session manager
├── docs/                # Architectural diagrams, demo scripts, future plans
├── farmer/              # Farmer yield manager, incoming offers, profile editor
├── includes/            # Reusable UI components (header, navbar, dark sidebar, footer)
└── sql/                 # Database schema definitions and seed data
```

---

## 2. Order Lifecycle State Machine

```
[ pending ] ──► [ matching ] ──► [ matched ] ──► [ accepted ] ──► [ in_transit ] ──► [ delivered ]
    │                │                │               │
    ▼                ▼                ▼               ▼
[ cancelled ]   [ cancelled ]    [ rejected ]    [ cancelled ]
```

- **Pending:** Pre-order placed by commercial buyer.
- **Matching:** Autonomous AI Broker Agent actively scoring harvest listings.
- **Matched:** High-confidence match found; proposal sent to farmer.
- **Accepted:** Farmer accepted buyer's pricing and terms.
- **In Transit:** Logistics transport in progress.
- **Delivered:** Buyer warehouse intake completed & funds disbursed.

---

## 3. Security Architecture
- **SQL Injection Prevention:** 100% prepared statements with bound parameters (`$stmt->execute([...])`).
- **XSS Prevention:** Context-aware `htmlspecialchars($str, ENT_QUOTES, 'UTF-8')` on all dynamic outputs.
- **CSRF Defense:** Cryptographic session tokens generated per user and verified on all state-mutating requests via `X-CSRF-TOKEN` or form parameters.
- **Access Control:** Strict role-based middleware (`checkRole(['farmer'])`, `checkRole(['business'])`, `checkRole(['admin'])`).
