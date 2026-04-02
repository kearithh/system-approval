# System Approval (E-Approval System)

A multi-company electronic approval and workflow management platform built with Laravel. Designed for organizations to create, submit, review, approve/reject, and track various types of business requests across multiple subsidiaries.

## Features

- **Multi-Company Support** — Serves multiple companies/subsidiaries (STSK, MFI, NGO, ORD, ST, MMI, MHT, TSP) with company-scoped data
- **38+ Request Types** — Special Expense, General Expense, Cash Advance, PO, PR, GRN, Memos, OT, Training, Missions, Loans, Disposal, Contracts, and more
- **Hierarchical Approval Workflow** — Position-based approval chain from officer to president level
- **Role-Based Access Control** — System admin, sub-admin, manager, and regular user roles
- **Notifications** — Telegram bot, OneSignal push notifications, and email alerts at each approval stage
- **PDF Generation** — Auto-generate PDF request forms
- **Excel Export/Import** — Summary reports and bulk data operations
- **Khmer Language Support** — Primary locale is Khmer (km) with English fallback
- **Real-time Updates** — WebSocket support via Laravel WebSockets

## Tech Stack

| Category | Technology |
|---|---|
| Language | PHP 7.2+ |
| Framework | Laravel 6.2 |
| Database | MySQL |
| Frontend | AdminLTE 3, Bootstrap |
| Build Tool | Laravel Mix (Webpack) |
| PDF | FPDF / FPDI |
| Excel | Maatwebsite Excel 3.1 |
| Notifications | Telegram Bot SDK, OneSignal |
| Realtime | Laravel WebSockets |

## Requirements

- PHP >= 7.2
- Composer
- Node.js & npm
- MySQL
- WAMP/XAMPP (or equivalent local server)

## Installation

```bash
# Clone the repository
git clone <repository-url>
cd system-approval

# Install PHP dependencies
composer install

# Install JS/CSS dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

## Configuration

Edit `.env` and set the following:

```env
APP_URL=http://localhost/system-approval

DB_CONNECTION=mysql
DB_DATABASE=approval_ord
DB_USERNAME=root
DB_PASSWORD=

TELEGRAM_BOT_TOKEN=<your-bot-token>
```

## Database Setup

```bash
# Create the MySQL database
mysql -u root -e "CREATE DATABASE approval_ord;"

# Run migrations
php artisan migrate

# Seed initial data (positions, users, companies)
php artisan db:seed
```

## Compile Assets

```bash
# Development
npm run dev

# Production
npm run prod
```

## Running the Application

1. Start WAMP (Apache + MySQL)
2. Navigate to `http://localhost/system-approval`
3. Log in with seeded credentials (default password: `123456`)

## Project Structure

```
app/
├── Console/            # Artisan commands
├── Entities/           # Domain entity models
├── Exports/            # Excel export classes
├── Http/
│   ├── Controllers/    # 75 controllers (CRUD + approval workflows)
│   ├── Helpers.php     # Global helper functions
│   └── Middleware/     # HTTP middleware
├── Imports/            # Excel import classes
├── Mail/               # Mailable classes
├── Model/              # Additional Eloquent models
├── Notifications/      # Notification classes
├── User.php            # User model
├── Approve.php         # Approval workflow model
└── Request*.php        # Request type models (80+)
config/
├── adminlte.php        # Sidebar menu and UI config
├── app.php             # Request type constants, roles, status codes
├── database.php        # MySQL connection
└── telegram.php        # Telegram bot config
database/
├── migrations/         # 45 migration files
└── seeds/              # 15 seeder files
resources/
└── views/              # Blade templates (74 view directories)
routes/
├── web.php             # Web routes (~900 lines)
└── api.php             # API endpoints
```

## Approval Workflow

1. User creates a request (Memo, Expense, PR, etc.)
2. Request enters **Pending** status
3. Designated reviewers approve/reject/comment
4. Request moves through approval chain based on position hierarchy
5. President/CEO gives final approval
6. Notifications sent at each stage via Telegram and email

## Position Hierarchy

| Level | Role |
|---|---|
| 1 | President |
| 10 | CEO |
| 20 | VP / Director |
| 30 | Manager |
| 50 | Assistant Manager |
| 70 | Supervisor |
| 90 | Officer |
| 500 | Other |

## API Endpoints

| Method | Endpoint | Description |
|---|---|---|
| POST | `/api/approval-api/get-mission` | Get mission data |
| POST | `/api/approval-api/get-all-user` | Get all users |
| POST | `/api/approval-api/get-user-by-id` | Get user by ID |

## License

Proprietary. All rights reserved.
