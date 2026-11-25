# BIU Library Management System — Requirements & Planning

| | |
|---|---|
| **Document** | SDLC Phase 1 & 2 — Requirements Specification and Project Plan |
| **Version** | 1.0 |
| **Date** | 2026-11-23 |
| **Jira project** | [BIULMS on lihour.atlassian.net](https://lihour.atlassian.net/jira/software/projects/BIULMS/boards/2/backlog), 59 issues created 2026-11-23 (see §10) |
| **Backlog import file** | [jira-backlog.csv](jira-backlog.csv) (offline copy of the same backlog) |
| **Design artifacts** | [02-design.md](02-design.md) |

---

## 1. Purpose & Scope

The BIU Library Management System (LMS) replaces manual/spreadsheet circulation with a web application
that lets library staff manage the catalog, physical copies, members and membership plans, and record
borrow/return transactions by barcode. Members can sign in to see their loans, reserve titles, and
review books.

**In scope:** catalog (books, copies, authors, publishers, categories), members and plans, circulation
(borrow, return, overdue, fines), reservations, reviews, member notifications, audit log, dashboard and
reports, role-based access.

**Out of scope (v1):** online payments, inter-library loans, e-books/digital lending, self-service
kiosks, mobile native apps.

## 2. Stakeholders & Roles

| Role | System role | Responsibilities / needs |
|---|---|---|
| Library administrator | `admin` | Full access; manages staff accounts, plans, configuration, audit review |
| Librarian / circulation staff | `librarian` | Catalog upkeep, member registration, borrow/return at the desk |
| Library member (student/staff) | `member` | Sign in, view own loans and due dates, reserve, review |
| University IT | — | Hosting, backups, security compliance |
| Project sponsor (Head Librarian) | — | Approves scope, accepts releases |

## 3. Current State (as of v0.x, branch `feature`)

| Area | Status | Notes |
|---|---|---|
| Session login / logout, role middleware | ✅ Built | `LoginController`, `RoleMiddleware` |
| CRUD: books, copies, authors, publishers, categories | ✅ Built | DataTables index pages, cover upload |
| CRUD: members, member plans | ✅ Built | |
| Borrow by barcode (multi-copy), return | ⚠️ Partial | See gaps G1–G5 |
| Dark mode | ✅ Built | Last commit |
| Reservations, reviews, notifications, audit log | ❌ Schema only | Tables/models exist, no UI or logic |
| Dashboard | ⚠️ Placeholder | Theme demo content, no real metrics |
| Automated tests | ❌ None | Only `ExampleTest` |

**Known gaps found in code review (logged as Bugs in the backlog):**

- **G1** — `BorrowRepository::returnBook` sets `return_date` but never sets `status = 'returned'`.
- **G2** — `returnBook` is not wrapped in `DB::transaction` (borrow is), so a failure can leave copy and loan out of sync.
- **G3** — `staff_id` is set from `auth()->id()` (a **users** id) but the FK references **staff.id**.
- **G4** — `due_date` is entered manually; it should default from the member plan's `loan_duration_days`.
- **G5** — No check that the member is active / not expired or over their loan limit at borrow time.
- **G6** — `book_authors`, `reviews`, `audit_logs` use `unsignedInteger` without FK constraints, while parent PKs are `bigint`.

## 4. Functional Requirements

Priority uses MoSCoW: **M**ust, **S**hould, **C**ould, **W**on't (this release).

### 4.1 Authentication & Authorization (EPIC: AUTH)
| ID | Requirement | Pri |
|---|---|---|
| FR-AUTH-01 | Users sign in with email + password; failed attempts show a generic error. | M |
| FR-AUTH-02 | Access to each admin action is decided by the permissions of the user's role. Members use a separate front-end. | M |
| FR-AUTH-03 | After login, each role lands on its own home (staff dashboard vs. member portal). | M |
| FR-AUTH-04 | Users can reset a forgotten password by emailed token (`users.reset_token`). | S |
| FR-AUTH-05 | Admin can create, deactivate and change roles of staff user accounts. | S |
| FR-AUTH-06 | `last_login` is recorded on each successful sign-in. | C |
| FR-AUTH-07 | Admin can create, edit and delete roles, and sets the permissions on the role form in one step. | M |
| FR-AUTH-08 | Nobody can give, change or take over more access than they have; at least one administrator always remains. | M |
| FR-AUTH-09 | Visitors can register as members themselves; staff approve the membership before they can reserve or borrow. | M |
| FR-AUTH-10 | New admin actions get a permission automatically, given to no role until an administrator ticks it. | S |

### 4.2 Catalog Management (EPIC: CAT)
| ID | Requirement | Pri |
|---|---|---|
| FR-CAT-01 | Staff create/edit/soft-delete books with ISBN (unique), title, publisher, category, authors (many), year, pages, language, shelf location, cover image. | M |
| FR-CAT-02 | Staff manage authors, publishers and categories (slug unique). | M |
| FR-CAT-03 | Staff add physical copies per book with a unique barcode and status (`available`, `borrowed`, `damaged`, `lost`). | M |
| FR-CAT-04 | All list pages are searchable, sortable, paginated server-side. | M |
| FR-CAT-05 | Book detail shows copy counts by status and current borrowers. | S |
| FR-CAT-06 | Bulk import books from CSV. | C |
| FR-CAT-07 | Print barcode labels for copies. | C |

### 4.3 Membership (EPIC: MEM)
| ID | Requirement | Pri |
|---|---|---|
| FR-MEM-01 | Staff register members with code, name, contact, plan, join and expiry dates, status. | M |
| FR-MEM-02 | Admin manages plans: loan duration (days), fine per day, discount fee. | M |
| FR-MEM-03 | Members whose `expiry_date` has passed are automatically marked `expired` (daily job). | S |
| FR-MEM-04 | Member profile shows loan history, active loans, outstanding fines. | S |
| FR-MEM-05 | A member record can be linked to a `users` login so the member can sign in. | S |

### 4.4 Circulation (EPIC: CIRC)
| ID | Requirement | Pri |
|---|---|---|
| FR-CIRC-01 | Staff scan/enter a barcode; the system shows the copy and book if `available`. | M |
| FR-CIRC-02 | Staff borrow one or more copies for an active member in one atomic transaction. | M |
| FR-CIRC-03 | Due date defaults to borrow date + plan `loan_duration_days` (editable by staff). | M |
| FR-CIRC-04 | Borrowing is blocked for inactive/suspended/expired members or members over the loan limit. | M |
| FR-CIRC-05 | Return sets `return_date`, loan status `returned`, and copy status `available` atomically. | M |
| FR-CIRC-06 | A daily job flags loans past `due_date` as `overdue`. | M |
| FR-CIRC-07 | Fine = days overdue × plan `fine_per_day`, shown at return and on member profile. | S |
| FR-CIRC-08 | Staff can mark a loaned copy `lost` or `damaged` at return. | S |
| FR-CIRC-09 | Staff can renew a loan once if no reservation is pending on the title. | C |

### 4.5 Reservations (EPIC: RES)
| ID | Requirement | Pri |
|---|---|---|
| FR-RES-01 | Members reserve a title in the member front-end, whether or not a copy is free; status `pending`. Staff hold a copy and confirm the borrow at the desk. | S |
| FR-RES-02 | On return of a copy with pending reservations, the first member is notified and the hold expires after N days (`expires_at`). | S |
| FR-RES-03 | Members and staff can cancel a reservation; expired holds auto-transition to `expired`. | S |

### 4.6 Reviews (EPIC: REV)
| ID | Requirement | Pri |
|---|---|---|
| FR-REV-01 | Members who have borrowed a book can rate it 1–5 and write a review. | C |
| FR-REV-02 | Book pages show average rating and reviews; staff can remove abusive reviews. | C |

### 4.7 Notifications (EPIC: NOTI)
| ID | Requirement | Pri |
|---|---|---|
| FR-NOTI-01 | Members receive in-app + email reminders 2 days before due date and on overdue. | S |
| FR-NOTI-02 | Members receive a notification when a reserved title is ready for pickup. | S |
| FR-NOTI-03 | Navbar bell shows unread notifications; opening marks `read_at`. | S |

### 4.8 Audit Log (EPIC: AUD)
| ID | Requirement | Pri |
|---|---|---|
| FR-AUD-01 | Create/update/delete on core tables writes an audit row with user, table, record, old/new JSON, IP. | S |
| FR-AUD-02 | Admin can filter and view the audit log. | S |

### 4.9 Dashboard & Reports (EPIC: DASH)
| ID | Requirement | Pri |
|---|---|---|
| FR-DASH-01 | Staff dashboard shows KPI tiles: total titles, copies available, active loans, overdue loans, active members. | M |
| FR-DASH-02 | Dashboard shows loans-per-day chart (30 days) and top 5 borrowed titles. | S |
| FR-DASH-03 | Reports: overdue list, circulation by period, inventory by status — exportable to CSV/PDF. | S |
| FR-DASH-04 | Member portal home lists active loans with due dates, reservations, notifications. | M |

## 5. Non-Functional Requirements

| ID | Category | Requirement |
|---|---|---|
| NFR-01 | Performance | List pages return the first page in < 1 s for 50k books / 200k transactions (server-side DataTables, indexed FKs). |
| NFR-02 | Security | Passwords hashed (bcrypt); CSRF on all forms; role middleware on every non-public route; OWASP Top-10 reviewed before release. |
| NFR-03 | Integrity | Every multi-table write uses `DB::transaction`; FKs enforced; soft deletes on business tables. |
| NFR-04 | Usability | Bootstrap admin UI, responsive to 360 px, light and dark themes, WCAG 2.1 AA contrast. |
| NFR-05 | Availability | 99.5 % during library opening hours; nightly DB backup with 30-day retention. |
| NFR-06 | Maintainability | Layered architecture (Controller → FormRequest → Service → Repository interface → Model); Pint-clean; ≥ 70 % feature-test coverage of services. |
| NFR-07 | Portability | Runs on SQLite (dev) and MySQL 8 (prod); Docker/Sail compose provided. |
| NFR-08 | Auditability | All staff data changes traceable via audit log (FR-AUD-01). |

## 6. Business Rules

| ID | Rule |
|---|---|
| BR-01 | A copy can be in at most one active loan. Only `available` copies can be borrowed. |
| BR-02 | Default due date = borrow date + member plan `loan_duration_days`. |
| BR-03 | Fine per overdue day = plan `fine_per_day`; fines accrue until return. |
| BR-04 | Members with status ≠ `active` or `expiry_date` < today cannot borrow or reserve. |
| BR-05 | Maximum concurrent loans per member: 5 (configurable). |
| BR-06 | A reservation hold lasts 3 days after notification (`expires_at`). |
| BR-07 | Members can review only titles they have borrowed and returned. |

## 7. Project Plan

**Methodology:** Scrum, 2-week sprints, Jira Software board (To Do → In Progress → In Review → QA → Done).
**Team (suggested):** 1 PO (Head Librarian proxy), 1 Scrum Master, 2 Laravel devs, 1 UI/UX designer, 1 QA.
**Definition of Done:** code reviewed via PR, Pint passes, feature tests added and green, deployed to staging, PO accepted.

### 7.1 Release & Sprint Roadmap

| Sprint | Dates | Goal | Epics |
|---|---|---|---|
| Sprint 0 | 2026-03-09 → 03-20 | Setup, schema, auth | AUTH, QA (setup) — **Done** |
| Sprint 1 | 2026-03-23 → 04-03 | Catalog CRUD | CAT — **Done** |
| Sprint 2 | 2026-04-06 → 04-17 | Members & plans, borrow/return v1 | MEM, CIRC — **Done** |
| Sprint 3 | 2026-11-30 → 12-11 | Harden circulation, fix G1–G6, test suite | CIRC, QA |
| Sprint 4 | 2026-12-14 → 12-25 | Real dashboard, member portal, overdue job | DASH, MEM, CIRC |
| Sprint 5 | 2026-12-28 → 2027-01-08 | Reservations + notifications | RES, NOTI |
| Sprint 6 | 2027-01-11 → 01-22 | Audit log, reports, reviews | AUD, DASH, REV |
| Sprint 7 | 2027-01-25 → 02-05 | UAT, security review, prod deploy → **v1.0** | QA |

### 7.2 Milestones
| Milestone | Target | Exit criteria |
|---|---|---|
| M1 — MVP Circulation | 2026-12-11 | G1–G6 fixed, borrow/return covered by tests |
| M2 — Member Self-Service | 2027-01-08 | Portal, reservations, reminders live on staging |
| M3 — v1.0 Release | 2027-02-05 | UAT sign-off, security checklist, prod deployed |

### 7.3 Risks
| Risk | Impact | Likelihood | Mitigation |
|---|---|---|---|
| Barcode scanner hardware variance | Med | Med | Scanner acts as keyboard; test with 2 models in Sprint 3 |
| SQLite → MySQL behavioral differences | Med | Med | Run CI against MySQL container |
| Email deliverability for reminders | Med | Low | Queue + retry; in-app notifications as fallback |
| Data migration from legacy spreadsheets | High | Med | CSV import story (FR-CAT-06), dry-run on staging |
| Scope creep (payments, e-books) | Med | High | Explicit out-of-scope list; change requests via PO |

## 8. Requirements Traceability

Every FR above maps to one or more Jira Stories in [jira-backlog.csv](jira-backlog.csv); the FR ID is in
each story's **Labels** column (e.g. `FR-CIRC-05`) so a Jira filter `labels = FR-CIRC-05` returns all work
for that requirement. Design artifacts per requirement are listed in [02-design.md](02-design.md).

## 9. Importing the backlog into Jira

1. Create a Jira Software Scrum project (key `BIULMS`). Make sure the **Story Points** field is on the Story screen.
2. **Settings → System → External System Import → CSV** (or *Project → … → Import issues*).
3. Upload `jira-backlog.csv`, encoding UTF-8, delimiter `,`.
4. Map columns: `Issue Id` → *Issue Id*, `Parent Id` → *Parent Id* (links stories to epics), `Issue Type`,
   `Summary`, `Description`, `Priority`, `Labels` (split on space), `Story Points`, `Sprint`, `Status`.
5. Run the import; then create sprints 0–7 with the dates above if Jira did not auto-create them.

## 10. Jira Issue Map

Created in [BIULMS](https://lihour.atlassian.net/jira/software/projects/BIULMS/boards/2/backlog) with the Atlassian MCP.
Every child issue is parented to its epic, labelled with its requirement IDs and `epic-<KEY>`, and carries a
story-point estimate. Every child issue is also assigned to its Jira sprint (see table below).
Done = already implemented on branch `feature`.

| Epic | Key | Child issues |
|---|---|---|
| AUTH: Authentication & RBAC | [BIULMS-1](https://lihour.atlassian.net/browse/BIULMS-1) | BIULMS-11 ✅, 12 ✅, 13, 14, 15 |
| CAT: Catalog Management | [BIULMS-2](https://lihour.atlassian.net/browse/BIULMS-2) | BIULMS-16 ✅, 17 ✅, 18 ✅, 19, 20, 21, 22 (bug G6) |
| MEM: Membership | [BIULMS-3](https://lihour.atlassian.net/browse/BIULMS-3) | BIULMS-23 ✅, 24 ✅, 25, 26, 27 |
| CIRC: Circulation | [BIULMS-4](https://lihour.atlassian.net/browse/BIULMS-4) | BIULMS-28 ✅, 29 ✅, 30, 31, 32 (bug G1), 33 (bug G2), 34 (bug G3), 35, 36, 37, 38 |
| RES: Reservations | [BIULMS-5](https://lihour.atlassian.net/browse/BIULMS-5) | BIULMS-39, 40, 41 |
| REV: Reviews | [BIULMS-6](https://lihour.atlassian.net/browse/BIULMS-6) | BIULMS-42, 43 |
| NOTI: Notifications | [BIULMS-7](https://lihour.atlassian.net/browse/BIULMS-7) | BIULMS-44, 45, 46 |
| AUD: Audit Logging | [BIULMS-8](https://lihour.atlassian.net/browse/BIULMS-8) | BIULMS-47, 48 |
| DASH: Dashboard & Reports | [BIULMS-9](https://lihour.atlassian.net/browse/BIULMS-9) | BIULMS-49, 50, 51 |
| QA: Quality & DevOps | [BIULMS-10](https://lihour.atlassian.net/browse/BIULMS-10) | BIULMS-52 ✅, 53 ✅, 54, 55, 56, 57, 58, 59 |

Totals: 10 epics, 49 child issues (193 story points), 11 Done, 38 To Do. Useful filters:
`project = BIULMS AND labels = FR-CIRC-05` (traceability), `project = BIULMS AND issuetype = Bug` (the bugs found in code review).

### Sprints in Jira (board 2)

| Sprint | Dates | State | Issues | Points |
|---|---|---|---|---|
| BIULMS Sprint 0 | 2026-03-09 → 03-20 | Closed | BIULMS-11, 12, 52 | 9 |
| BIULMS Sprint 1 | 2026-03-23 → 04-03 | Closed | BIULMS-16, 17, 18 | 18 |
| BIULMS Sprint 2 | 2026-04-06 → 04-17 | Closed | BIULMS-23, 24, 28, 29, 53 | 18 |
| BIULMS Sprint 3 | 2026-11-30 → 12-11 | Future (next) | BIULMS-15, 22, 30, 31, 32, 33, 34, 54, 55 | 29 |
| BIULMS Sprint 4 | 2026-12-14 → 12-25 | Future | BIULMS-14, 19, 25, 26, 27, 35, 36, 49, 56 | 39 |
| BIULMS Sprint 5 | 2026-12-28 → 2027-01-08 | Future | BIULMS-13, 37, 39, 40, 41, 44, 45, 46 | 31 |
| BIULMS Sprint 6 | 2027-01-11 → 01-22 | Future | BIULMS-38, 42, 43, 47, 48, 50, 51 | 27 |
| BIULMS Sprint 7 | 2027-01-25 → 02-05 | Future | BIULMS-20, 21, 57, 58, 59 | 22 |

Sprints 0–2 were started and closed to record completed history; Sprint 3 is the next one to start.
