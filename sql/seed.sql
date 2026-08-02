-- AgriSync Database Seed Data Script
-- Seed data for instant local setup & demo testing
-- Default password for all demo accounts is: password123

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

-- --------------------------------------------------------
-- Seed Users (Password: password123)
-- Hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
-- --------------------------------------------------------
INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `phone`, `district`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Bandara Herath', 'farmer@agrisync.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'farmer', '0771234567', 'Nuwara Eliya', 1, NOW(), NOW()),
(2, 'Somasiri Silva', 'dambulla.farmer@agrisync.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'farmer', '0719876543', 'Dambulla', 1, NOW(), NOW()),
(3, 'Keells Supermarket Procurement', 'buyer@agrisync.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'business', '0112345678', 'Colombo', 1, NOW(), NOW()),
(4, 'Cargills Food City Central', 'cargills@agrisync.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'business', '0119876543', 'Gampaha', 1, NOW(), NOW()),
(5, 'AgriSync System Admin', 'admin@agrisync.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '0703534431', 'Colombo', 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE `updated_at` = NOW();

-- --------------------------------------------------------
-- Seed Harvest Listings
-- --------------------------------------------------------
INSERT INTO `harvest_listings` (`id`, `farmer_id`, `crop_type`, `quantity_kg`, `price_per_kg`, `harvest_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Carrot', 1500.00, 210.00, DATE_ADD(CURRENT_DATE, INTERVAL 3 DAY), 'available', NOW(), NOW()),
(2, 1, 'Potato', 2500.00, 180.00, DATE_ADD(CURRENT_DATE, INTERVAL 5 DAY), 'available', NOW(), NOW()),
(3, 2, 'Tomato', 800.00, 240.00, DATE_ADD(CURRENT_DATE, INTERVAL 2 DAY), 'matched', NOW(), NOW()),
(4, 2, 'Leek', 1200.00, 195.00, DATE_ADD(CURRENT_DATE, INTERVAL 7 DAY), 'available', NOW(), NOW()),
(5, 1, 'Cabbage', 900.00, 160.00, DATE_SUB(CURRENT_DATE, INTERVAL 2 DAY), 'sold', NOW(), NOW())
ON DUPLICATE KEY UPDATE `updated_at` = NOW();

-- --------------------------------------------------------
-- Seed Order Requests
-- --------------------------------------------------------
INSERT INTO `order_requests` (`id`, `business_id`, `crop_type`, `quantity_kg`, `max_price`, `delivery_date`, `urgency`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 3, 'Carrot', 1000.00, 230.00, DATE_ADD(CURRENT_DATE, INTERVAL 4 DAY), 'high', 'matching', 'Need fresh Nuwara Eliya carrots for retail store distribution.', NOW(), NOW()),
(2, 3, 'Tomato', 800.00, 250.00, DATE_ADD(CURRENT_DATE, INTERVAL 3 DAY), 'medium', 'matched', 'Grade A tomatoes required for salad section.', NOW(), NOW()),
(3, 4, 'Leek', 600.00, 200.00, DATE_ADD(CURRENT_DATE, INTERVAL 8 DAY), 'low', 'pending', 'Direct farm supply preferred.', NOW(), NOW()),
(4, 4, 'Cabbage', 900.00, 175.00, CURRENT_DATE, 'high', 'fulfilled', 'Delivered to Gampaha warehouse.', NOW(), NOW())
ON DUPLICATE KEY UPDATE `updated_at` = NOW();

-- --------------------------------------------------------
-- Seed Order Matches
-- --------------------------------------------------------
INSERT INTO `order_matches` (`id`, `order_id`, `listing_id`, `farmer_id`, `business_id`, `matched_price`, `agent_reasoning`, `confidence_score`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 3, 2, 3, 240.00, 'AI Broker matched Keells Tomato pre-order #2 with Somasiri Silva harvest listing #3. Price of Rs. 240/kg is within Rs. 250/kg max budget and provides a 22% margin above base cost.', 96, 'accepted', NOW(), NOW()),
(2, 4, 5, 1, 4, 160.00, 'AI Broker matched Cargills Cabbage order #4 with Bandara Herath listing #5. Order fulfilled and verified upon delivery.', 98, 'completed', NOW(), NOW())
ON DUPLICATE KEY UPDATE `updated_at` = NOW();

-- --------------------------------------------------------
-- Seed Agent Logs
-- --------------------------------------------------------
INSERT INTO `agent_logs` (`id`, `agent_type`, `order_id`, `action_step`, `log_data`, `created_at`, `updated_at`) VALUES
(1, 'demand_prediction', 1, 'Analyzed Western Province demand spike for carrots ahead of holiday weekend', '{"crop": "Carrot", "predicted_demand_kg": 4500, "confidence": 0.92}', NOW(), NOW()),
(2, 'broker', 2, 'Matched buyer order #2 with farmer #2 harvest yield #3', '{"match_score": 96, "fair_trade_validated": true}', NOW(), NOW()),
(3, 'logistics', 2, 'Negotiated transport route from Dambulla to Colombo distribution hub', '{"distance_km": 148, "est_delivery_hours": 3.5}', NOW(), NOW())
ON DUPLICATE KEY UPDATE `updated_at` = NOW();

-- --------------------------------------------------------
-- Seed Notifications
-- --------------------------------------------------------
INSERT INTO `notifications` (`id`, `user_id`, `message`, `link`, `is_read`, `created_at`, `updated_at`) VALUES
(1, 1, 'Welcome to AgriSync! List your harvest yield to start receiving buyer matches.', '/farmer/listings.php', 1, NOW(), NOW()),
(2, 3, 'Your pre-order for 800kg Tomatoes has been matched with a farmer in Dambulla!', '/business/matches.php', 0, NOW(), NOW()),
(3, 2, 'New buyer match proposal received for your Tomato harvest!', '/farmer/orders.php', 0, NOW(), NOW())
ON DUPLICATE KEY UPDATE `updated_at` = NOW();

COMMIT;
