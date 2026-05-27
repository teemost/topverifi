---
name: TopVerifi rebrand
description: Full rebrand from Blues Marketplace to TopVerifi with orange color scheme.
---

## Rule
All brand color references use `#f97316` (orange-500) as primary, `#ea580c` (orange-600) as dark, `#fb923c` (orange-400) as light. The Tailwind config extends colors with `brand: { DEFAULT: '#f97316', dark: '#ea580c' }`.

**Why:** Rebrand from Blues Marketplace (sky blue #0ea5e9) to TopVerifi (orange). All layouts, auth views, admin views updated.

## How to apply
- Replace any `sky-*`, `#0ea5e9`, `#38bdf8` with `brand` / `orange-*` / `#f97316` equivalents.
- All three layouts (app.blade.php, dashboard.blade.php, admin.blade.php) and auth/admin-login.blade.php already updated.
- Site name default changed from "Blues Marketplace" to "TopVerifi" in SettingsController.
