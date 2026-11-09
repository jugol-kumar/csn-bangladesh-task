# Haat

Shared-catalog shop manager. Laravel 13 API, Vue 3 and Tailwind CSS 4.

This is the complete project. The only things not included are the folders that `composer install` and `npm install` create (`vendor/` and `node_modules/`).

## Requirements

PHP 8.3+, Composer, Node 20+, MySQL (or any database Laravel supports).

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Create an empty MySQL database called `haat`, then check the `DB_*` values in `.env`.

```bash
php artisan migrate --seed
php artisan storage:link
npm install
```

Run it in two terminals:

```bash
php artisan serve
npm run dev
```

Open http://localhost:8000. For production use `npm run build` instead of `npm run dev`.

Run the backend tests with `php artisan test`. They use an in-memory SQLite database, so the `pdo_sqlite` PHP extension must be enabled.

## Logins (password for all: `password`)

| Who | URL | Email |
| --- | --- | --- |
| Admin | `/admin/login` | admin@haat.test |
| Shop owner | `/login` | rafi@haat.test, mim@haat.test |
| Customer | `/login` | customer@haat.test |

Shop owners and customers share `/login`. The admin has a separate page and a separate API endpoint, so an admin cannot sign in from the public page and the other roles cannot sign in as admin.

## How it works

- `products` is the shared catalog. Admins and shop owners both create rows there (`source` records who).
- `shop_products` is a shop's own shelf: one row per shop and product, with the shop's price, optional title, description and thumbnail. Empty overrides fall back to the catalog product.
- Customers only read `shop_products` rows that are active and whose shop is active. A catalog product with no shelf row is never returned by the storefront API.
- Every create, update and delete goes through a class in `app/Services`. Controllers only validate (Form Requests), call a service and return a Resource.
- Auth uses Sanctum bearer tokens. The `role` middleware (`app/Http/Middleware/EnsureRole.php`) guards the admin and owner route groups.

## Frontend layout

```
resources/js
  modules/<name>/components   components of one module (shops, products, owners, catalog, listings, storefront, auth, dashboard)
  modules/<name>/services     axios calls of that module
  modules/shared              reusable components and composables
  views/pages                 pages; they only compose module components
  layouts                     store, admin, owner and bare (login) layouts
```

The admin area has no sidebar: `/admin` shows counters, then one box per module.
