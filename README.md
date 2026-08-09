# Nexarch Laravel API

Laravel 12 REST API for the existing Nexarch Supabase database. It implements the non-commerce functionality visible in the Command Center frontend: Supabase email/password authentication, profile settings, personal links, businesses and workspaces, social links, encrypted development secrets, notes, website health checks, learning, goals, daily/general tasks, job applications, dashboard aggregation, and global search.

Commerce APIs and Google/Apple OAuth are intentionally excluded.

## Architecture

- Supabase Auth remains the identity provider. The frontend sends its access token as `Authorization: Bearer <token>`.
- Laravel validates each access token against Supabase Auth and obtains the authenticated UUID.
- Every query is additionally constrained by that UUID, even when the PostgreSQL connection uses an elevated database account.
- Secret values are stored in Supabase Vault. API responses expose only development-key metadata and never return secret values.
- Website checks reject private/reserved network targets to reduce SSRF risk.

## Local setup

Requirements: PHP 8.2+, Composer, PostgreSQL extensions, and access to the existing Supabase project.

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan serve
```

Fill these environment values:

```dotenv
FRONTEND_URL=http://localhost:3000
DB_HOST=<host shown by Supabase Connect>
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=<username shown by Supabase Connect>
DB_PASSWORD=<database-password>
DB_SSLMODE=require
SUPABASE_URL=https://<project-ref>.supabase.co
SUPABASE_ANON_KEY=<anon-key>
```

On an IPv4-only host, use the Supabase session pooler connection details from **Connect → Session pooler** rather than the direct IPv6 database host.

The supplied schema already exists, so this project does not migrate or replace those application tables. Supabase Vault must be enabled for development-key endpoints.

## API

All routes use the `/api/v1` prefix. Except for health and login flows, routes require a Supabase bearer token.

| Area | Endpoints |
|---|---|
| Health | `GET /health` |
| Auth | `POST /auth/login`, `/register`, `/refresh`, `/forgot-password`; authenticated `GET /auth/me`, `POST /auth/logout`, `PUT /auth/password` |
| Overview | `GET /dashboard`, `GET /search?q=...` |
| Settings | `GET /profile`, `PUT /profile` |
| CRUD resources | `/links`, `/businesses`, `/learning`, `/goals`, `/daily-tasks`, `/tasks`, `/job-applications` |
| Business workspace | `POST/PUT/DELETE /businesses/{id}/links`, `/social-links`, `/development-keys`; `PUT /businesses/{id}/note`; `POST /businesses/{id}/website-checks` |

Laravel returns standard `422` validation objects, `401` authentication errors, `404` for records not owned by the user, and `204` after deletion.

### Frontend connection

Set the frontend API base URL to the Render URL:

```dotenv
NEXT_PUBLIC_API_URL=https://nexarch-api.onrender.com/api/v1
```

After login, store the returned Supabase session and include `session.access_token` as the bearer token. When Supabase refreshes a session, replace the stored token before subsequent API calls.

## Render free deployment

1. Push this directory to GitHub.
2. In Render, choose **New → Blueprint** and select the repository. `render.yaml` creates the Docker web service.
3. Enter all environment variables marked `sync: false`. Set `FRONTEND_URL` to the exact deployed frontend origin; multiple origins can be comma-separated.
4. Deploy and verify `https://<service>.onrender.com/api/v1/health`.
5. Point the frontend API base URL at the deployed service and redeploy the frontend.

The container listens on Render's `$PORT`, caches production configuration/routes at startup, and does not run schema migrations against the existing Supabase database.

Render free services can sleep while idle, so the first API request after inactivity may be slower.
