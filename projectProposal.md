IDEALIZE 2026 – Team Proposal: AgriSync


Team Name: EMBERWOLVES
Category (Web/App): Web
Category (School / Open): Open Category
Name of the Web/App: AgriSync
Field of the Web/App: Agriculture Technology / B2B Supply Chain
 
Table of Contents
Problem & Solution	3
The Problem Statement	3
Application Blueprint	3
Innovation & Technical Design	4
Agentic AI Integration	4
Technical Architecture & Feasibility	4
Market Potential & Sustainability	5
Market Potential & Sustainability	5
Sustainability & Long-Term Impact	6
Team Details	7
Team Leader	7
Team Member 1	7
Team Member 2	8
Team Member 3	8
Team Member 4	9

 
Problem & Solution
The Problem Statement
Sri Lanka’s agricultural supply chain is highly fragmented and inefficient. Farmers transport their produce to central hubs (like the Dambulla Economic Center) without knowing the exact daily demand, often leading to oversupply and massive food wastage. A long chain of middlemen heavily inflates prices for the end consumer (restaurants/supermarkets) while giving the farmers a remarkably low profit margin. Additionally, coordination of logistics is manual and chaotic.
Application Blueprint
AgriSync is a decentralized, B2B marketplace application that connects Sri Lankan farmers directly with restaurants, supermarkets, and grocers.

Main Features:
1. Farmer Mobile App: A simple vernacular app for farmers to list expected harvest yields.
2. Business Web Dashboard: A portal for supermarkets/restaurants to place bulk pre-orders.
3. Automated Matching Engine: Directly matches buyer orders with the closest farmers based on required yield.
4. Logistics Coordination: Connects confirmed matches automatically with freelance truck drivers for pickup and delivery, eliminating manual logistics planning
Innovation & Technical Design
Agentic AI Integration
AgriSync utilizes a robust Multi-Agent System to automate the entire supply chain workflow:

1. Demand Prediction Agent (RAG): Uses Retrieval-Augmented Generation to analyze historical market prices, weather data from the Met Department, and upcoming holidays to predict demand spikes, autonomously advising farmers on what to harvest.
2. The "Broker" Agent: When a business requests produce, this agent queries the database for farmers in the closest proximity. It uses Tool Calling to autonomously send SMS offers to farmers and negotiates based on a fair-trade guardrail.
3. The Logistics Agent: Once a farmer accepts an order, this agent triggers a Tool Call to ping registered truck drivers, negotiating delivery fees and setting up optimized routing via the Google Maps API without human intervention.

Technical Architecture & Feasibility

Technologies & System Design:
•	Frontend: React Native (or Expo) for the mobile app and React.js/Next.js for the Business web dashboard.
•	Backend & DB: Node.js with Express for REST APIs, using PostgreSQL or Firebase/Firestore for the database.
•	Agentic AI: We will use Firebase Genkit (Google's free AI framework for Node.js) or the native Gemini API directly within our backend. This completely removes the need for a complex Python microservice. We can build the Multi-Agent workflows and Tool Calling directly in JavaScript/TypeScript using the free tier of Google AI Studio.
•	External APIs: Twilio for free trial SMS notifications and Google Maps API for transport routing.

Development Plan:
•	Week 1-3: UI/UX prototyping and core database architecture.
•	Week 4-6: Backend API development and web/mobile frontend integration.
•	Week 7-8: Building the Agentic AI workflows using Firebase Genkit (RAG & Tool Calling).
•	Week 9-10: Integration of the AI agents into the core platform, end-to-end testing, and deployment.
Market Potential & Sustainability
Market Potential & Sustainability

Target Users: 
 Supply Side: Farmers in major agricultural districts (e.g., Nuwara Eliya, Dambulla) and freelance truck drivers.
 Demand Side: Supermarket chains (Keells, Cargills, Arpico), local restaurant networks, and large-scale grocers in the Western Province.
Value Proposition & Growth:
By bypassing the traditional physical economic centers, AgriSync offers a 10-15% cost reduction in supply chain overhead for businesses while significantly increasing the profit margin for farmers. The platform scales by initially onboarding large corporate buyers (guaranteed demand), which in turn naturally incentivizes local farmer co-ops to join the platform for better, guaranteed prices.

Sustainability & Long-Term Impact
AgriSync directly targets the UN Sustainable Development Goals: Goal 2 (Zero Hunger) by massively reducing post-harvest food wastage due to oversupply, and Goal 8 (Decent Work and Economic Growth) by providing a transparent, fair-trade pricing structure for rural farmers. 
Furthermore, by using the Logistics AI Agent to optimize transport routes, the platform reduces the carbon footprint associated with multiple redundant trips to central economic centers, creating a resilient, eco-friendly, and sustainable food network for Sri Lanka that remains relevant for decades.
.
