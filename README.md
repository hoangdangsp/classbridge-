# ClassBridge

Open-source management platform for **multi-campus language schools** — scheduling,
attendance, tuition billing, and parent / teacher mobile apps, in one monorepo.

## Layout

| Path | What |
| --- | --- |
| `apps/api` | Laravel 11 API (PHP 8.3), serves API **v1** and **v2** side by side |
| `apps/web` | React + TypeScript admin / front-desk web app |
| `apps/parent-app` | React Native app for parents (schedules, invoices, progress) |
| `apps/teacher-app` | React Native app for teachers (offline attendance, gradebook) |
| `packages/ui` | Shared React Native / web component library |
| `docs` | Architecture, database schema, API migration plan, bot workflow |

## Current status (honest numbers)

- **1,900 automated tests** — 1,714 passing. The 186 failures break down as
  74 Detox flakes in the teacher app (attendance sync), 51 PHPUnit contract
  failures from the v2 migration, 38 Jest snapshot drift on web, and
  23 billing edge cases around mid-cycle proration.
- **214 open pull requests** — 96 Dependabot, 61 mobile-parity screens,
  34 v2 endpoint ports, 23 community contributions awaiting review.
- **API v1 -> v2 migration in flight.** Strangler pattern, contract tests run
  the same request suite against both versions. See `docs/API_MIGRATION_V2.md`.

## The bot rota

Backlog work is being handed to an autonomous dev bot under a strict rota:
CI triage every morning, draft PRs only, contract tests as the cage, human
keeps the merge button. The full operating rules live in `docs/BOT_WORKFLOW.md`.

## Quick start

```bash
# API
cd apps/api && composer install && php artisan serve
# Web
cd apps/web && npm install && npm run dev
```

## License

MIT
