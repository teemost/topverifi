---
name: Laravel app structure
description: Project layout and environment specifics for the TopVerifi Laravel app.
---

## Rule
The Laravel app lives in the `blues-laravel/` subdirectory. All artisan commands must be run from `blues-laravel/` (cd blues-laravel && php artisan ...).

**Why:** Replit project root has start.sh and other config; the actual Laravel code is inside blues-laravel/.

## Environment
- Replit: PostgreSQL (DATABASE_URL env var)
- cPanel/production: MySQL — needs its own .env
- Port: 5000 (configured in start.sh / .env)
- Admin login: /adminlogin — credentials: admin@blues.com / admin123

## How to apply
- Always `cd blues-laravel` before running artisan, composer, or npm commands.
- `start.sh` at workspace root handles the Replit workflow startup.
- Settings (API keys, site name, etc.) are stored in the `settings` DB table, managed via Admin → Settings panel.
