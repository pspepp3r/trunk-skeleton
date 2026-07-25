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
php trunk migrate
php trunk start
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

This skeleton isn't a blank slate - it's a working, end-to-end example wired against a real `users` table:

| Route | Description |
|---|---|
| `GET /health` | Built-in health check (always on, not user-defined). |
| `POST /login` | Issues a JWT for demo credentials (`demo@trunk.dev` / `password`). |
| `GET /users` | List users, session-tracked visit count. |
| `POST /users` | Create a user - validated (`CreateUserRequest`), auth-protected, dispatches a `UserRegistered` event. |
| `GET /users/{id}` | Plain path param example. |
| `GET /users/{id}/async` | Manual async ORM lookup with a 404 on miss. |
| `GET /users/{user}/bound` | Same lookup via automatic route model binding - no manual `find()`/null-check. |
| `POST /graphql` | GraphQL endpoint over the same ORM. |

Try it:

```bash
TOKEN=$(curl -s -X POST http://127.0.0.1:8080/login \
  -H 'Content-Type: application/json' \
  -d '{"email":"demo@trunk.dev","password":"password"}' | jq -r .token)

curl -X POST http://127.0.0.1:8080/users \
  -H 'Content-Type: application/json' -H "Authorization: Bearer $TOKEN" \
  -d '{"name":"Ada","email":"ada@example.com"}'
```

## Project layout

```
bootstrap/app.php     # builds the App, loads config + routes
config/                # app.php, database.php, auth.php, events.php, routes.php, ...
public/index.php       # front controller
src/
  Controllers/  Entities/  Requests/  Middleware/  Events/  Listeners/  GraphQL/  Providers/
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
