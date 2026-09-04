# Task Manager Web Application

A lightweight, secure, MVC-architected Task Management web application built with PHP and PDO. Features user authentication, session-based access control, CSRF protection, input validation, and task CRUD (Create, Read, Update, Delete) functionality with priority management.

---

## Key Features

- **User Authentication**: Secure user registration and login utilizing password hashing (`password_hash` with `PASSWORD_DEFAULT`).
- **Task Management**: Create, edit, update, list, and delete tasks.
- **Priority Tracking**: Assign priority levels (`low`, `medium`, `high`) to tasks.
- **Security Protections**:
  - CSRF token generation and validation on post operations.
  - Prepared statements with PDO to eliminate SQL injection vulnerabilities.
  - HTML escaping with `htmlspecialchars` to prevent Cross-Site Scripting (XSS).
  - Session-based authorization ensuring tasks remain private per user account.
- **Flexible Database Support**: Configurable PDO driver supporting MySQL and SQLite.

---

## Tech Stack

- **Language**: PHP 8.x
- **Architecture**: MVC (Model-View-Controller) pattern with OOP design
- **Database**: MySQL / MariaDB (production-ready) or SQLite (lightweight / testing)
- **Security**: PDO Prepared Statements, Password Hashing, CSRF Tokens, Input Sanitization

---

## Project Structure

```text
├── app/
│   ├── Controllers/
│   │   ├── AuthController.php      # User authentication & registration handling
│   │   └── TaskController.php      # Task management workflow logic
│   ├── Helpers/
│   │   ├── auth.php                # Session authorization check helper
│   │   └── csrf.php                # CSRF token generation & validation helper
│   ├── Models/
│   │   ├── Task.php                # Task database data access layer
│   │   └── User.php                # User account database data access layer
│   └── Views/
│       ├── auth/
│       │   ├── login.php           # Login page template
│       │   └── register.php        # Registration page template
│       ├── layout/
│       │   ├── header.php          # Header layout template
│       │   └── footer.php          # Footer layout template
│       └── tasks/
│           ├── index.php           # Task listing & creation form
│           └── edit.php            # Task edit page
├── config/
│   ├── database.php                # PDO database connection setup
│   └── validation.php              # Server-side task validation rules
├── .github/
│   └── workflows/
│       └── ci.yml                  # Automated GitHub Actions CI workflow
├── .env.example                    # Environment configuration template
├── autoload.php                    # PSR-4 compatible class autoloader
├── index.php                       # Application entry point & front controller
├── schema.sql                      # Database table schema definition
└── verify_app.php                  # Automated application verification script
```

---

## Database Setup

The application supports both MySQL/MariaDB and SQLite.

### MySQL Setup

Import the SQL schema provided in `schema.sql`:

```bash
mysql -u root -p task_manager < schema.sql
```

The database schema defines two relational tables (`users` and `tasks`):

- **`users`**: `id`, `name`, `email` (unique), `password` (hashed), `created_at`
- **`tasks`**: `id`, `title`, `priority`, `user_id` (foreign key to `users`), `created_at`

---

## Configuration

Environment configuration is managed using standard environment variables. You can reference `.env.example`:

| Variable | Description | Default |
| :--- | :--- | :--- |
| `DB_DRIVER` | Database driver (`mysql` or `sqlite`) | `mysql` |
| `DB_HOST` | Database host name | `localhost` |
| `DB_PORT` | Database port | `3306` |
| `DB_NAME` | MySQL database name | `task_manager` |
| `DB_USER` | MySQL database username | `root` |
| `DB_PASS` | MySQL database password | `your_secure_password` |
| `DB_PATH` | SQLite database file path (when `DB_DRIVER=sqlite`) | `database.sqlite` |

---

## Installation & Local Execution

### Prerequisites

- PHP 8.0+
- MySQL / MariaDB server or SQLite extension enabled (`pdo_sqlite`)

### Setup Instructions

1. **Clone the repository**:
   ```bash
   git clone <repository-url>
   cd <repository-folder>
   ```

2. **Configure Environment Variables**:
   Optionally set environment variables or copy `.env.example`:
   ```bash
   export DB_DRIVER=mysql
   export DB_NAME=task_manager
   export DB_USER=root
   export DB_PASS=your_password
   ```

3. **Start Development Server**:
   Start the built-in PHP development server:
   ```bash
   php -S localhost:8000
   ```

4. **Access Application**:
   Navigate to `http://localhost:8000` in your browser.

---

## Testing & Verification

An automated verification script is included in `verify_app.php` to validate database connections, autoloading, routing, authentication, and task operations:

```bash
DB_DRIVER=sqlite DB_PATH=/tmp/test.sqlite php -d error_reporting=0 verify_app.php
```

To run PHP syntax checks across all codebase files:

```bash
find . -type f -name "*.php" -exec php -l {} \;
```

---

## Continuous Integration (CI)

A GitHub Actions pipeline is configured in `.github/workflows/ci.yml`. On every push and pull request to `main` or `master`, the workflow:

1. Sets up PHP 8.2 environment with required extensions (`pdo`, `pdo_sqlite`, `pdo_mysql`).
2. Performs PHP syntax linting on all codebase files.
3. Runs the automated application verification test suite (`verify_app.php`).

---

## Security Audit Highlights

- **SQL Injection Prevention**: All queries use PDO prepared statements with bound parameter arrays.
- **XSS Protection**: Output escaping applied across all HTML views.
- **CSRF Token Validation**: Post requests (task creation, editing, deletion) verify unique per-session CSRF tokens.
- **Authentication Safeguards**: Password hashing via BCRYPT (`password_hash`), session regeneration upon login (`session_regenerate_id`).
