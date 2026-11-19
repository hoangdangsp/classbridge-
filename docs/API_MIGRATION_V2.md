# API v1 -> v2 migration

Strangler pattern. v2 grows endpoint by endpoint behind a version header;
nothing is switched off until it is provably unused.

## Guardrails

1. **Contract tests.** The same request suite runs against v1 and v2.
   Any response-shape drift fails CI before a human sees the PR.
2. **OpenAPI diffing in CI.** A breaking change to a published endpoint is a
   hard red. Bot PRs cannot merge on red, and the bot cannot approve its
   own work — every merge needs a human click.
3. **Version pinning.** The mobile apps pin their API version per release.
   v1 stays alive until old-version traffic is zero for 30 consecutive days,
   so a v2 mistake cannot reach a parent's phone mid-semester.

## Progress

34 of 118 endpoints ported. Remaining: billing (in progress), reports,
notification preferences.
