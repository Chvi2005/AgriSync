# 🎬 AgriSync — Official Video Demonstration Script (5 Minutes)

**Project:** AgriSync — AI-Powered Decentralized B2B Agriculture Marketplace  
**Target Duration:** 4 minutes 45 seconds (Buffer under 5:00 limit)  
**Tone:** Confident, professional, impact-driven, tech-forward  

---

## ⏱️ Minute-by-Minute Breakdown

### 📍 Scene 1: Problem Hook & Landing Page Overview (0:00 – 0:30)
- **Visual:** Screen opens on `http://localhost:8000/` (AgriSync Landing Page). Smooth scroll through the hero section showing stats: *35% Post-Harvest Food Loss* and *40% Middleman Margin Gap*.
- **Voiceover (Narrator):**
  > "Every single day in Sri Lanka, over 35% of harvested fresh produce is lost to inefficient supply chains, while smallholder farmers earn pennies on the rupee due to exploitative middleman cartels.
  > 
  > Welcome to **AgriSync** — an autonomous AI-driven B2B agriculture marketplace connecting rural farmers directly with commercial buyers like supermarket chains and hotel suppliers. Let's see how our multi-agent AI system revolutionizes fair trade."

---

### 📍 Scene 2: Architecture & Multi-Agent Overview (0:30 – 1:00)
- **Visual:** Display architecture graphic showing the 4 Gemini AI agents (Demand Predictor, Autonomous Broker, Fair Pricing Floor Engine, and Logistics Optimizer).
- **Voiceover:**
  > "Built on vanilla PHP 8 and MySQL with a strict PDO security layer, AgriSync is powered by Google Gemini Pro AI across four specialized agents: Demand Prediction, Autonomous Brokering, Fair-Trade Pricing Floor Enforcement, and Logistics Route Optimization.
  > 
  > All orders follow a finite state machine ensuring full end-to-end transparency."

---

### 📍 Scene 3: Farmer Experience — Yield Listing & Fair Trade Protection (1:00 – 1:30)
- **Visual:** Log in as Farmer Bandara Herath (`farmer@agrisync.lk`). Navigate to **Farmer Dashboard** ➔ Click **List Harvest**. Add 1,500 kg of Nuwara Eliya Carrots with projected harvest date.
- **Voiceover:**
  > "Logging in as Bandara, a highland farmer in Nuwara Eliya. On his clean dashboard, Bandara lists his upcoming harvest of 1,500 kilograms of carrots.
  > 
  > Notice the AI Fair Price indicator protecting Bandara with a guaranteed floor price of 210 rupees per kilogram, safeguarding his farming profit margin."

---

### 📍 Scene 4: Commercial Buyer Pre-Order & Autonomous Brokering (1:30 – 2:30) ⭐ *Star Feature*
- **Visual:** Switch window to Commercial Buyer (`buyer@agrisync.lk` - Keells Procurement). Navigate to **Place Pre-Order**. Request 1,000 kg of Carrots at max budget Rs. 230/kg. Submit. Show live AI Brokering status spinner transitioning to **MATCHED** in real-time.
- **Voiceover:**
  > "Now, switching to Keells Supermarket's procurement officer in Colombo. Keells places a bulk pre-order for 1,000 kg of carrots with a high-urgency delivery window.
  > 
  > Instantly, our Autonomous Broker Agent activates in the background. It calculates geographic proximity, crop freshness windows, and price tolerance. In seconds, Keells receives a confirmed match with Bandara's farm!"

---

### 📍 Scene 5: Transparent AI Reasoning & Agent Audit Log (2:30 – 3:15) ⭐ *Transparency*
- **Visual:** Click on the match card to reveal Gemini AI natural-language reasoning. Then switch to Admin Portal ➔ **Live AI Agent Logs** (`/admin/agent_logs.php`). Show JSON payload, step metrics, and confidence score (96%).
- **Voiceover:**
  > "Unlike black-box algorithms, AgriSync provides 100% explainable AI. Here is the Gemini broker's exact reasoning: it balanced the 4-day delivery requirement against cold-chain transit time and ensured a 22% net profit above the farmer's base cost.
  > 
  > In the Admin Agent Monitor, every inference, prompt token, and match score is logged with cryptographic auditability."

---

### 📍 Scene 6: Farmer Offer Acceptance & Fulfillment Flow (3:15 – 3:45)
- **Visual:** Switch back to Farmer window ➔ `/farmer/offers.php`. Bandara reviews Keells' incoming offer, sees the buyer verification badge, and clicks **Accept Deal**. The order state transitions to `accepted`.
- **Voiceover:**
  > "Back on Bandara's portal, he receives the incoming match proposal. With a single tap, Bandara accepts the verified deal.
  > 
  > The order state machine advances to accepted, and our Logistics Agent provides optimized route waypoints to the Colombo distribution center."

---

### 📍 Scene 7: Admin Intelligence, SDG Impact & Closing (3:45 – 4:45)
- **Visual:** Navigate to `/admin/dashboard.php` and `/admin/sdg_impact.php`. Showcase live Chart.js food waste reduction metrics, regional price distribution, and SDG progress indicators (SDG 2, 8, 12).
- **Voiceover:**
  > "On the Executive Admin Dashboard, administrators track platform trade throughput, active users, and real-time SDG metrics: over 40 tons of potential food waste averted and farmer revenue increased by 26%.
  > 
  > Looking ahead, we are expanding AgriSync with automated SMS/USSD for offline rural farmers, smart contract escrow settlements, and solar cold-storage fleet tracking.
  > 
  > AgriSync: Powering sustainable, equitable agriculture with artificial intelligence. Thank you!"

---

## 🎯 Recording Checklist
- [ ] Browser zoom set to 100%, 1080p 60fps recording resolution.
- [ ] Database reset with `sql/seed.sql` before starting recording.
- [ ] Both Farmer (`farmer@agrisync.lk`) and Buyer (`buyer@agrisync.lk`) credentials readily available.
- [ ] Clear narration audio with background noise suppression.
