# Online Learning Platform API

A Laravel 12 API-only distance-learning platform built for Week 5 assignment.

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
php artisan serve
```

## Authentication

All endpoints require a Sanctum bearer token obtained from `POST /api/login`.

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"carol@example.com","password":"password"}'
```

Seeded credentials (password: `password`):

| Email | Role |
|---|---|
| alice@example.com | teacher |
| bob@example.com | teacher |
| carol@example.com | student |
| dan@example.com | student |
| eve@example.com | student |

## API Endpoints

### Auth
| Method | URI | Description |
|---|---|---|
| POST | `/api/login` | Obtain a Sanctum token |
| GET | `/api/me` | Authenticated user info |
| POST | `/api/me/image` | Upload profile image |

### Courses
| Method | URI | Description |
|---|---|---|
| GET | `/api/courses` | List courses (published only for students; own courses for teachers) |
| GET | `/api/courses/{course}` | Show a course |
| POST | `/api/courses` | Create a course (teacher only) |
| PATCH | `/api/courses/{course}` | Update a course (owner teacher only) |
| DELETE | `/api/courses/{course}` | Delete a course (owner teacher only) |
| POST | `/api/courses/{course}/image` | Upload course image (owner teacher only) |

### Lessons
| Method | URI | Description |
|---|---|---|
| GET | `/api/courses/{course}/lessons` | List lessons |
| GET | `/api/courses/{course}/lessons/{lesson}` | Show a lesson |
| POST | `/api/courses/{course}/lessons` | Create a lesson (owner teacher only) |
| PATCH | `/api/courses/{course}/lessons/{lesson}` | Update a lesson (owner teacher only) |
| DELETE | `/api/courses/{course}/lessons/{lesson}` | Delete a lesson (owner teacher only) |
| POST | `/api/courses/{course}/lessons/{lesson}/image` | Upload lesson image (owner teacher only) |

### Assignments
| Method | URI | Description |
|---|---|---|
| POST | `/api/assignments/{assignment}/image` | Upload assignment image (owner teacher only) |

### Comments
| Method | URI | Description |
|---|---|---|
| GET | `/api/comments/latest` | Latest 20 comments across all content (visibility filtered by role) |
| GET | `/api/courses/{course}/comments` | Comments on a course |
| POST | `/api/courses/{course}/comments` | Add comment (enrolled student only) |
| GET | `/api/courses/{course}/lessons/{lesson}/comments` | Comments on a lesson |
| POST | `/api/courses/{course}/lessons/{lesson}/comments` | Add comment on lesson (enrolled student only) |
| GET | `/api/courses/{course}/assignments/{assignment}/comments` | Comments on an assignment |
| POST | `/api/courses/{course}/assignments/{assignment}/comments` | Add comment on assignment (enrolled student only) |

### Categories
| Method | URI | Description |
|---|---|---|
| GET | `/api/categories` | List all categories |
| GET | `/api/categories/{category}` | Show category with attached courses, lessons, and assignments |

## Query Count: GET /api/comments/latest

**Endpoint:** `GET /api/comments/latest`  
**Request:**
```bash
curl http://localhost:8000/api/comments/latest \
  -H "Authorization: Bearer <token>"
```

**Eager loading strategy:**

The endpoint uses `Comment::with(['user:id,name', 'commentable' => morphWith([...])])` which groups polymorphic records by type and issues one query per type present, rather than one query per comment.

**Query breakdown** (derived from code analysis against the seeded database, which has comments on `course`, `lesson`, and `assignment` types):

| # | Query |
|---|---|
| 1 | `SELECT * FROM comments ORDER BY created_at DESC LIMIT 20` |
| 2 | `SELECT id, name FROM users WHERE id IN (...)` — all comment authors in one query |
| 3 | `SELECT * FROM courses WHERE id IN (...)` — all course-type commentables |
| 4 | `SELECT * FROM lessons WHERE id IN (...)` — all lesson-type commentables |
| 5 | `SELECT * FROM courses WHERE id IN (...)` — parent courses for the lessons above |
| 6 | `SELECT * FROM assignments WHERE id IN (...)` — all assignment-type commentables |
| 7 | `SELECT * FROM courses WHERE id IN (...)` — parent courses for the assignments above |

**Total: 7 queries** when all three commentable types are present in the result set.

If only one commentable type is present (e.g. only course comments), the count reduces to **3 queries** (comments + users + courses).

**How this was determined:** Static analysis of the eager-loading call chain combined with Laravel's `morphWith` behaviour, which groups morph records by type and issues one IN-query per type. The seeded database contains comments on all three types (verified during development). A live `DB::getQueryLog()` measurement could not be taken during this session because MySQL was not available from the command line at verification time. The count above is the deterministic result of the code structure and can be confirmed by running `DB::enableQueryLog()` before the query and `DB::getQueryLog()` after it in any environment with a running database.

## N+1 Prevention Summary

| Endpoint | Relationships eager-loaded |
|---|---|
| `GET /api/comments/latest` | `user` (batch), `commentable` (morphWith by type), `commentable.course` (batch for lessons and assignments) |
| `GET /api/courses/{course}/comments` | `user` (batch) |
| `GET /api/courses/{course}/lessons/{lesson}/comments` | `user` (batch) |
| `GET /api/courses/{course}/assignments/{assignment}/comments` | `user` (batch) |
| `GET /api/categories/{category}` | `lessons.course` (batch), `assignments.course` (batch) |
