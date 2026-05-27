---
name: JAP SMM boosting integration
description: JustAnotherPanel (JAP) API integration for social media boosting services.
---

## Rule
JAP API key stored in `settings` table as `jap_api_key`. Markup percent stored as `boosting_markup_percent` (default 20%). Enable/disable via `boosting_enabled` setting.

**Why:** Full SMM boosting feature replacing the marketplace. JapService.php handles all API calls to https://justanotherpanel.com/api with POST + `key` + `action` params.

## Key files
- `app/Services/JapService.php` — API client (getBalance, getServices, placeOrder, getOrderStatus)
- `app/Models/BoostingOrder.php` — order model with status_badge / status_color accessors
- Migration: `2026_05_27_300001_create_boosting_orders_table.php` (already run)
- `app/Http/Controllers/User/BoostingController.php` — user-facing order placement
- `app/Http/Controllers/Admin/BoostingOrdersController.php` — admin order management + sync

## How to apply
- JAP services are fetched live from API and cached in memory per request.
- Price displayed = JAP USD rate × usd_to_ngn_rate × (1 + markup_percent/100) / 1000
- Orders deduct from wallet immediately; JAP order ID stored for status syncing.
