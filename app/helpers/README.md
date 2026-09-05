# ATMS Helpers

Reusable helper layer for authentication, sessions, CSRF protection, validation, responses, security, URLs, flash messages, input handling and audit logging.

## Files
- `session.php` — secure session lifecycle
- `auth.php` — authentication and role authorization
- `csrf.php` — CSRF tokens
- `validation.php` — request validation
- `response.php` — JSON/redirect/error responses
- `security.php` — escaping and security utilities
- `url.php` — URLs and named routes
- `flash.php` — one-time messages
- `input.php` — GET/POST/JSON input
- `audit.php` — audit-log bridge

Tax calculations belong in `app/services/`, while database access belongs in `app/models/`.
