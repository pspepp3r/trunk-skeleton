<p align="center">
  <img src="../trunk/docs/public/logo.svg" alt="Trunk" width="100">
</p>

<h1 align="center">Trunk Skeleton</h1>

<p align="center">A starter API application built on the <a href="../trunk">Trunk</a> async PHP framework.</p>

---

## Requirements

- PHP 8.1+
- Composer
- MySQL or PostgreSQL

## Setup

```bash
composer create-project trunk/skeleton my-app
cd my-app
```

This pulls the skeleton straight from Packagist, installs dependencies, and copies `.env.example` to `.env` with a fresh, randomly-generated `JWT_SECRET` already in place - no manual `key:generate` step needed.

Edit `.env` to set your database credentials, then:

```bash
php trunk db:create
php trunk orm:schema-diff
php trunk migrate
php trunk start --watch
```

<details>
<summary>Setting up from a clone instead</summary>

```bash
git clone https://github.com/pspepp3r/trunk-skeleton my-app
cd my-app
composer install
cp .env.example .env
php trunk key:generate
```

</details>

The API is now listening on `http://127.0.0.1:8080` (configurable via `APP_PORT`).

## What's included

This skeleton isn't a blank slate - it's a working, end-to-end register/login example wired against a real `users` table:

| Route | Description |
|---|---|
| `GET /health` | Built-in health check (always on, not user-defined). |
| `POST /register` | Creates a user (validated via `RegisterRequest`, password hashed with `password_hash()`) and returns a JWT. |
| `POST /login` | Verifies email/password against the `users` table and returns a JWT. |
| `GET /me` | Auth-protected - returns the authenticated user, resolved from the JWT via `AuthMiddleware`. |

Try it:

```bash
curl -X POST http://127.0.0.1:8080/register \
  -H 'Content-Type: application/json' \
  -d '{"name":"Ada","email":"ada@example.com","password":"secret123"}'

TOKEN=$(curl -s -X POST http://127.0.0.1:8080/login \
  -H 'Content-Type: application/json' \
  -d '{"email":"ada@example.com","password":"secret123"}' | jq -r .token)

curl http://127.0.0.1:8080/me -H "Authorization: Bearer $TOKEN"
```

For a full walkthrough of adding your own resources (migrations, entities, FormRequests, controllers, relationships) on top of this, see the framework's Tutorial docs.

## Project layout

```
bootstrap/app.php     # builds the App, loads config + routes
config/                # app.php, database.php, auth.php, events.php, routes.php, ...
public/index.php       # front controller
src/
  Controllers/  Entities/  Requests/  Middleware/
database/migrations/    # created by `php trunk make:migration`
trunk                   # CLI entrypoint (`php trunk <command>`)
```

## Testing

```bash
composer test
```

See [`../trunk/docs/guide/testing.md`](../trunk/docs/guide/testing.md) for the patterns used here (pure validation tests, a real-dependency controller test, and a fully-mocked async controller test).

## Learn more

Full framework documentation: [`../trunk/docs`](../trunk/docs).
