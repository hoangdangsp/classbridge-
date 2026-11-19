# Architecture

ClassBridge is a monorepo. One Laravel API serves three clients (web admin,
parent app, teacher app) so business rules — billing, scheduling, attendance —
exist exactly once.

## Stack

- **API:** Laravel 11, PHP 8.3, PostgreSQL 16, Redis (queues + cache),
  Laravel Horizon for billing jobs and notification fan-out.
- **Web:** React + TypeScript, Vite.
- **Mobile:** React Native (parent + teacher apps), shared component library
  in `packages/ui`.
- **CI:** GitHub Actions — PHPUnit, Jest, Detox, plus a contract-test suite
  that replays the same requests against API v1 and v2.

## Core domain

`sessions` is the heart of the system: every class meeting is one session row
with a teacher, a room, and a time range. Attendance, invoicing and progress
reports all hang off sessions. Every operational table is campus-scoped
(`campus_id`) so a multi-campus chain never leaks data between sites.

## Hard problems

1. **Timetable solver.** Classes share teachers and rooms across campuses, so
   scheduling is a constraint problem (teacher availability, room capacity,
   level grouping). Greedy pass + local repair; unresolved conflicts land in
   a manual review queue.
2. **Offline attendance.** Teachers mark attendance with no signal and sync
   later. Sync is append-only: server wins on grades, client wins on
   attendance notes.
