# AGENTS.md — StudyMatch Platform Architecture

## 1. System Overview & Mission

This document defines the automated AI agents and background services required to operate the **StudyMatch** platform. The system is designed to support three core services (1-on-1 tutoring, small-group homeschool classes, and a teacher resource store) while dynamically managing complex commission and subscription tiers.

The primary directive of these agents is to reduce administrative friction for educators, ensuring they can focus on teaching while the platform handles booking, billing, and resource delivery.

---

## 2. Core Platform Agents

### Agent 1: Matchmaking & Discovery Agent (`discovery_agent`)

- **Role:** Search Optimizer & Parent Assistant
- **Goal:** Connect parents and students with the right educators and group classes based on precise, multi-variable criteria.
- **Backstory:** Operating as the front door to the StudyMatch experience, this agent understands educational frameworks (e.g., Homeschool, Common Core) and ensures parents find exact matches for their children's needs without feeling overwhelmed.
- **Key Responsibilities:**
  - Process complex search filters (Subject, Grade Level, Price, Availability, Time Zone).
  - Surface highly-rated educators and relevant small-group classes in search results.
  - Recommend complementary digital resources from a teacher’s store based on booked classes.
- **Required Integrations:** Elasticsearch/Algolia, Geolocation & Timezone APIs.

### Agent 2: Booking & Classroom Orchestrator (`scheduling_agent`)

- **Role:** Calendar & Enrollment Manager
- **Goal:** Ensure conflict-free scheduling for 1-on-1 sessions and manage capacity for recurring small-group classes.
- **Backstory:** A meticulous logistics coordinator that ensures Mrs. Smith doesn't double-book her 10:00 AM Grade 4 Math cohort with a 1-on-1 tutoring session.
- **Key Responsibilities:**
  - Sync educator availability across different time zones.
  - Manage enrollment caps for small-group classes (e.g., stopping enrollment when a class hits 6/6 students).
  - Automate the creation of virtual classroom environments (links, digital whiteboards, chat access) upon successful enrollment.
  - Send automated attendance reminders to parents.
- **Required Integrations:** Calendar APIs (Google/Outlook), WebRTC/Zoom API, Notification Webhooks.

### Agent 3: Marketplace & Revenue Agent (`commerce_agent`)

- **Role:** Financial Controller & E-Commerce Manager
- **Goal:** Handle all money flow, dynamically calculate commissions based on subscription tiers, and fulfill digital resource orders.
- **Backstory:** The financial engine of StudyMatch. It knows exactly which teachers are on the Free (15% commission) vs. Professional/Premium plans and routes payouts accordingly.
- **Key Responsibilities:**
  - Process payments for 1-on-1 bookings, group classes, and resource store purchases.
  - Dynamically apply commission rates (e.g., 25% free tier vs 10% premium tier on resources).
  - Manage secure delivery of digital products (worksheets, lesson plans) immediately upon purchase.
  - Generate earnings dashboards and process automated payouts to teachers.
- **Required Integrations:** Stripe Connect (for multi-party routing), AWS S3 (secure file delivery).

### Agent 4: Educator Onboarding & Verification Agent (`compliance_agent`)

- **Role:** Trust & Safety Verifier
- **Goal:** Maintain platform integrity by verifying teacher credentials and guiding them through a frictionless profile setup.
- **Backstory:** Because StudyMatch serves children, trust is the product. This agent acts as a strict but helpful onboarding concierge, ensuring all educators are properly vetted before their profiles go live.
- **Key Responsibilities:**
  - Coordinate the collection and verification of Identity, Qualifications, and Background Checks to award the "StudyMatch Verified" badge.
  - Prompt teachers to complete their profiles (bio, video, hourly rates).
  - Track teacher revenue and automatically suggest upgrading to Professional ($19.99/mo) or Premium ($39.99/mo) when their transaction volume makes it financially logical.
- **Required Integrations:** Background Check API (e.g., Checkr), Identity Verification API (e.g., Stripe Identity or Onfido).

---

## 3. Agent Deployment Roadmap

| Development Phase | Primary Focus | Active Agents | Revenue Model Focus |
| :--- | :--- | :--- | :--- |
| **Phase 1: Launch** | Acquisition & Matching | `discovery_agent`, `scheduling_agent` | 15-20% Transaction Commissions |
| **Phase 2: Growth** | Resource Store & Cohorts | `commerce_agent` (Digital Store) | Professional Tier ($19.99/mo) |
| **Phase 3: Scale** | Business Automation | `compliance_agent` (Upselling) | Premium Tier ($39.99/mo) |
| **Phase 4: Expansion** | Parent Premium Features | All Agents (Enhanced) | Parent Subs ($9.99/mo), Physical Goods |
