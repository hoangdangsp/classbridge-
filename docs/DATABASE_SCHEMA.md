# Database schema (core tables)

| Table | Notes |
| --- | --- |
| `schools` | One row per school chain |
| `campuses` | Sites belonging to a school |
| `users` | Staff, teachers, parents (role via `roles`) |
| `roles` | admin, front_desk, teacher, parent |
| `courses` | A course offering (level, language) |
| `classes` | A group of students taking a course |
| `enrollments` | Student <-> class, with billing cycle |
| `sessions` | One class meeting: `teacher_id`, `room_id`, `starts_at`, `ends_at` |
| `attendance` | `session_id` + `enrollment_id`, status, note, sync origin |
| `invoices` | Generated from enrollment + billing cycle |
| `payments` | Reconciled against invoices |
| `notifications` | Fan-out log for app pushes / SMS |

Rules:

- Every operational table carries `campus_id`.
- `attendance` is append-only; corrections are new rows, never edits.
- Invoice generation is idempotent per (enrollment, cycle) — rerunning the
  billing job must never double-charge a parent.
