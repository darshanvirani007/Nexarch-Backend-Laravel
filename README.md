# Nexarch Backend — Beginner Setup Guide

This repository is the Laravel backend for **Nexarch**.

It connects the Nexarch frontend to:

- **Supabase Auth** for user accounts;
- **Supabase PostgreSQL** for saved data;
- **Render** for hosting the Laravel API.

Live API: [https://nexarch-api.onrender.com/api/v1](https://nexarch-api.onrender.com/api/v1)

Live frontend: [https://nexarch-command-center.darshanvirani2468.chatgpt.site](https://nexarch-command-center.darshanvirani2468.chatgpt.site)

Commerce features and Google/Apple login are not implemented by this backend.

## How everything connects

```text
User opens the Nexarch frontend
              |
              | Supabase login token
              v
Laravel API hosted on Render
              |
              | Read/write the signed-in user's data
              v
Supabase PostgreSQL database
```

In plain English:

1. Supabase signs the user in.
2. The frontend sends the user's access token to Laravel.
3. Laravel verifies the token and learns the user's UUID.
4. Laravel reads or writes only records belonging to that UUID.

## Important safety rules

- Never commit your `.env` file.
- Never place the Supabase database password in frontend code.
- Never place a Supabase `service_role` key in frontend code.
- The Supabase anonymous/publishable key is **not** the service-role secret key.
- Never share bearer tokens shown in Chrome Developer Tools.
- This project uses the existing Supabase tables. Do not recreate them.
- Do not run destructive migrations against the production database.

The included `.gitignore` already excludes `.env`.

## What this backend provides

- Email/password authentication through Supabase
- User profile settings
- Personal links
- Businesses
- Business links and social profiles
- Business notes and website checks
- Encrypted development-key metadata
- Learning items
- Goals
- Daily and general tasks
- Job applications
- Dashboard summaries and search

All API routes start with `/api/v1`.

## Part 1: Put the project on GitHub

Open Terminal and enter the backend folder:

```bash
cd "/Users/darshanvirani/Desktop/Nexarch Backend"
```

Check which files will be committed:

```bash
git status
```

Make sure `.env` is **not** listed. Then push your changes:

```bash
git add .
git commit -m "Update Nexarch backend"
git push origin main
```

If GitHub asks for a password, do not enter your normal GitHub account password. Use one of these:

1. Sign in through GitHub Desktop; or
2. Use a GitHub Personal Access Token as the password; or
3. Run `gh auth login` if GitHub CLI is installed.

Repository:

[https://github.com/darshanvirani007/Nexarch-Backend-Laravel](https://github.com/darshanvirani007/Nexarch-Backend-Laravel)

## Part 2: Get the Supabase values

Open your Supabase project.

### Database connection values

1. Click **Connect** near the top of the Supabase project.
2. Choose **Transaction pooler**.
3. Copy the host, database name, username, and port shown there.
4. Use port **6543**, not 5432.

The values normally look like this:

```dotenv
DB_HOST=aws-0-your-region.pooler.supabase.com
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=postgres.your-project-reference
DB_PASSWORD=your-database-password
DB_SSLMODE=require
DB_PGSQL_DISABLE_PREPARES=true
```

`DB_PASSWORD` is the database password chosen when the Supabase project was created. It is not your GitHub password and not your Supabase website login password. If you forgot it, reset it from the Supabase database settings and update Render afterward.

### Supabase API values

In Supabase, open **Project Settings → API** (or the current **API Keys** page) and copy:

```dotenv
SUPABASE_URL=https://your-project-reference.supabase.co
SUPABASE_ANON_KEY=your-anonymous-or-publishable-key
```

Use the **anon/publishable** key for `SUPABASE_ANON_KEY`. Do not use the `service_role` secret key.

## Part 3: Deploy on Render Free

### First deployment

1. Sign in to [Render](https://dashboard.render.com).
2. Click **New**.
3. Choose **Blueprint**.
4. Connect GitHub if requested.
5. Select `Nexarch-Backend-Laravel`.
6. Render will read `render.yaml` automatically.
7. Enter the requested secret environment values.
8. Start the deployment.

Use these Render environment variables:

| Variable | Value |
| --- | --- |
| `FRONTEND_URL` | `https://nexarch-command-center.darshanvirani2468.chatgpt.site` |
| `DB_HOST` | Transaction-pooler host from Supabase |
| `DB_PORT` | `6543` |
| `DB_DATABASE` | `postgres` |
| `DB_USERNAME` | Transaction-pooler username from Supabase |
| `DB_PASSWORD` | Supabase database password |
| `DB_SSLMODE` | `require` |
| `DB_PGSQL_DISABLE_PREPARES` | `true` |
| `SUPABASE_URL` | Supabase project URL |
| `SUPABASE_ANON_KEY` | Supabase anon/publishable key |

`render.yaml` supplies the other production settings and generates `APP_KEY`. Do not copy your local `.env` file into GitHub.

### Deploy a later GitHub update

Normally Render automatically deploys new commits pushed to `main`.

If it does not:

1. Open the `nexarch-api` service in Render.
2. Click **Manual Deploy**.
3. Click **Deploy latest commit**.
4. Wait until the deployment says **Live**.

Render's free service sleeps when unused. The first request after inactivity can take about a minute; this is normal.

## Part 4: Check that the backend works

Open this URL:

[https://nexarch-api.onrender.com/api/v1/health](https://nexarch-api.onrender.com/api/v1/health)

A healthy result looks similar to:

```json
{
  "status": "ok",
  "service": "nexarch-api",
  "database": "available",
  "application_tables": "available",
  "application_models": "available"
}
```

If the page returns HTTP 200 and says `database: available`, Laravel can reach Supabase.

## Part 5: Connect the frontend

In the frontend environment configuration, set:

```dotenv
NEXT_PUBLIC_API_URL=https://nexarch-api.onrender.com/api/v1
NEXT_PUBLIC_SUPABASE_URL=https://your-project-reference.supabase.co
NEXT_PUBLIC_SUPABASE_ANON_KEY=your-anonymous-or-publishable-key
```

Do not add a database password, service-role key, or Laravel `APP_KEY` to the frontend.

The frontend sends browser requests through `/api/nexarch`. Its proxy forwards them to `NEXT_PUBLIC_API_URL` and preserves the Supabase authorization token.

After changing frontend environment variables, redeploy the frontend.

## Run the backend locally (optional)

You need:

- PHP 8.2 or newer;
- Composer;
- the PHP PostgreSQL extension;
- the Supabase values described above.

Then run:

```bash
cd "/Users/darshanvirani/Desktop/Nexarch Backend"
cp .env.example .env
composer install
php artisan key:generate
php artisan serve
```

Open `.env` and add your real local values before starting the server. The local API will normally be available at `http://127.0.0.1:8000/api/v1`.

Do not commit the completed `.env` file.

## Main API endpoints

Except for health and the public authentication actions, endpoints require this header:

```http
Authorization: Bearer <Supabase access token>
```

| Feature | Endpoints |
| --- | --- |
| Health | `GET /health` |
| Authentication | `/auth/login`, `/auth/register`, `/auth/refresh`, `/auth/forgot-password`, `/auth/me` |
| Dashboard | `GET /dashboard` |
| Search | `GET /search?q=...` |
| Profile | `GET /profile`, `PUT /profile` |
| Personal links | `/links` |
| Businesses | `/businesses` |
| Business links | `/businesses/{businessId}/links` |
| Social links | `/businesses/{businessId}/social-links` |
| Business note | `PUT /businesses/{businessId}/note` |
| Website check | `POST /businesses/{businessId}/website-checks` |
| Learning | `/learning` |
| Goals | `/goals` |
| Daily tasks | `/daily-tasks` |
| General tasks | `/tasks` |
| Job applications | `/job-applications` |

## Important business-creation detail

Creating a business and creating its links are separate operations:

```text
1. POST /businesses
2. Read the new business ID from the response
3. POST /businesses/{newBusinessId}/links for each business link
4. POST /businesses/{newBusinessId}/social-links for each social profile
5. GET /businesses/{newBusinessId} to reload the complete business
```

The frontend must perform these follow-up requests. The backend cannot save link values that the frontend never sends.

## Troubleshooting

### The health endpoint is slow

The Render free service probably went to sleep. Wait up to a minute and retry.

### The frontend says “Your data could not be loaded”

1. Open the health URL.
2. Confirm Render shows the latest Git commit as **Live**.
3. Open **Render → nexarch-api → Logs**.
4. Search for `production.ERROR`.
5. In Chrome Developer Tools, open **Network**, select the failed request, and read its **Response** tab.

Never copy an Authorization token when sharing screenshots or logs.

### HTTP 401

The Supabase login token is missing, expired, or belongs to a different Supabase project. Sign out and sign in again. Also confirm the frontend and Laravel use the same Supabase project URL.

### HTTP 422

Laravel rejected invalid or missing form data. Open the failed request's Response tab to see which field must be corrected.

### HTTP 500

Open Render Logs and find the newest `production.ERROR` entry at the same time as the request. Copy only the exception class, SQLSTATE, safe PostgreSQL summary, route, and request ID. Do not share credentials.

### PostgreSQL error `23514`

This means a database CHECK constraint rejected a value. The personal-link type fix normalizes display labels such as `GitHub` and `YouTube` to lowercase database identifiers.

### A business exists but its `links` array is empty

Check Chrome's Network tab. After `POST /businesses`, the frontend must also send one or more requests to:

```text
POST /businesses/{businessId}/links
POST /businesses/{businessId}/social-links
```

If those requests are missing, fix the frontend creation flow. If they return an error, inspect their Response tabs.

### Port 5432 or 6543?

Use **6543** for the Supabase transaction pooler configured by this project. Port 5432 is the normal direct PostgreSQL port and is not the connection described in this deployment guide.

## Test before pushing

If PHP and Composer are installed:

```bash
php artisan test
```

Then verify that only intended files changed:

```bash
git status
git diff --check
```

## Expected Git workflow

```bash
cd "/Users/darshanvirani/Desktop/Nexarch Backend"
git status
git add README.md
git commit -m "Improve backend setup guide"
git push origin main
```

Again: `.env` must never be added or pushed.
