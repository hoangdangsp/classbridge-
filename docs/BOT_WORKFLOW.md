# Bot workflow — the standing rota

An autonomous dev bot works the backlog like a junior dev with a rota.
It operates under these rules, no exceptions:

## Daily

- **Morning CI triage.** Overnight failures are classified: flake, real
  regression, or migration drift. Flakes get a quarantine PR; regressions
  get a fix PR with the failing test linked. A summary is ready by 09:00 —
  the human just makes the calls.

## Intake

- Work arrives as GitHub issues labelled `bot-ready`, each naming an
  acceptance test. No acceptance test, no work.
- Max **3 parallel sessions**: (1) PR queue burn-down, oldest first —
  rebasing and fixing CI on other people's PRs; (2) v2 endpoint ports
  behind the contract tests; (3) mobile parity screens.

## Guardrails

- Everything lands as a **draft PR**. Nothing merges without green CI
  plus a human click.
- The bot never touches production, billing data, or migrations that
  drop columns.

## Weekly

- **Friday burn-down report:** PRs closed, tests fixed, queue age.
  If the queue grows two weeks straight, the scope is wrong — not the bot.
