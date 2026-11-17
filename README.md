# College Management Software - Public Portfolio Edition

A Laravel 13 College & Higher-Education Management System demonstrating recruitment-ready full-stack engineering skills. This public portfolio edition showcases core architectural patterns, working implementations, and technical capabilities built for the complete WebotApp Indian College ERP system.

## 📦 Public Edition vs Private Edition

**This repository contains a carefully selected, functional subset of the complete WebotApp Indian College ERP system.** The private full edition (`college-management-software-Full`) contains the complete source code with all 30 modules, while this public edition focuses on core, recruiter-friendly features that demonstrate engineering capabilities without exposing proprietary business logic or sensitive institutional data.

### ✅ Available in Public Edition
- Authentication & Role-Based Access Control (RBAC) system
- Dashboard with analytics and Chart.js data visualization
- Online Admission Management (public portal)
- Student Portal (self-service dashboard)
- Examination Management (marks entry, marksheet generation)
- Library Management (book issue/return system)
- Fee & Payment Tracking (receipt generation)
- Certificate Management (QR verification)
- Role-based portal interfaces (Student, Faculty, Parent)
- Core Eloquent models and database migrations
- Blade templates with Tailwind CSS styling
- Route architecture and controller patterns
- PHPUnit test structure
- Full dependency configuration (Composer & npm)

### 🔒 Excluded from Public Edition
- HR & Payroll salary details and employee records
- Full Finance & Accounting with GST/TDS calculations
- Hostel Management with room allocations and mess management
- Transport Management with GPS and fuel records
- Procurement & Inventory with vendor data
- Grievance & Discipline confidential records
- Scholarship approval workflows with sensitive data
- Multi-campus configuration with institutional specifics
- Audit logging with system administration details
- Placement selection pipelines and offer tracking
- Full parent portal with multi-child linking details
- Any production-specific configuration or credentials

## 🚀 Key Features (Public Edition)

### Authentication & RBAC
- Email/password authentication with status verification
- Role-based middleware (`role:super_admin`, `role:student`, `role:faculty`, `role:parent`)
- Automatic home route redirection by user role
- Audit logging for login/logout actions
- Password Bcrypt with 12 rounds

### Dashboard & Analytics
- Real-time student/faculty/staff metrics
- Admission conversion rate calculation
- Today's attendance tracking with percentage
- Upcoming examinations preview
- Fee collection analytics and outstanding balance
- Library overview (books issued/overdue)
- Placement statistics (drives, selected students)
- Income/expense tracking from financial vouchers
- Course and department distribution charts
- Grade distribution analysis
- Recent announcements display

### Admission Management
- Public application portal (`/apply`)
- Application status tracking (`/track-application`)
- Merit list generation with cut-off management
- Application receipt generation
- Enquiry tracking and follow-up scheduling

### Student Portal
- Timetable view
- Marks sheet download
- Assignment submission
- Fee receipt generation
- Leave application
- Grievance submission
- Notice and announcement viewing

### Examination Management
- Exam schedule display
- Mark entry functionality
- SGPA/CGPA computation
- Admit card generation
- Marks sheet generation
- Grade calculation and publication

### Library Management
- Book catalogue browsing
- Book issue and return
- Book renewal
- Overdue fine calculation (₹5/day)
- Lost/damaged book tracking

### Fee & Payment
- Fee structure display (course/category-wise)
- Online fee payment tracking
- UPI/Cards/NetBanking payment records
- Receipt generation with unique numbers
- Pending fee tracking and balance amounts
- Ledger tracking for student accounts

### Portal Interfaces (Blade + Tailwind)
- Student portal dashboard
- Faculty portal dashboard
- Parent portal dashboard
- Responsive design for mobile/desktop
- Chart.js integration for analytics visualization

## 🛠️ Technology Stack

| Layer | Technology |
|---|---|
| **Backend** | Laravel 13, PHP 8.4+ |
| **Database** | MySQL / MariaDB (configuration for college database) |
| **ORM** | Laravel Eloquent |
| **Frontend** | Blade templates, Tailwind CSS v4 |
| **Visualization** | Chart.js |
| **Build Tool** | Vite |
| **Package Managers** | Composer (PHP), npm (JS) |
| **Testing** | PHPUnit / Pest Feature Test Suite |

## ⚙️ Installation & Setup (Public Edition)

### Prerequisites
- PHP 8.4 or higher
- Composer 2.6 or higher
- Node.js 20 or higher (for Vite build)
- MySQL or MariaDB (or SQLite for testing)
- npm or yarn

### Installation Steps

1. **Clone the repository:**
   ```bash
   git clone https://github.com/nadeemmx/college-management-software.git
   cd college-management-software
   ```

2. **Install Composer dependencies:**
   ```bash
   composer install
   ```

3. **Configure Environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   
   Update `.env` with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=college_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Run Migrations:**
   ```bash
   php artisan migrate:fresh --seed
   ```
   _(Note: Seed data in public edition is limited to core demonstration records)_

5. **Install Node.js dependencies:**
   ```bash
   npm install
   ```

6. **Build assets:**
   ```bash
   npm run build
   ```

7. **Start the application:**
   ```bash
   php artisan serve
   ```
   
   Access at `http://localhost:8000`.

## 📁 Project Structure (Public Edition)

```
college-management-software/
├── app/                    # Core application code
│   ├── Http/Controllers/   # Key controllers (Auth, Dashboard, Student, Exam, etc.)
│   ├── Models/             # Eloquent models (User, Student, Course, Exam, etc.)
│   └── Http/Middleware/   # RBAC EnsureRole middleware
├── routes/                 # Route definitions (authenticated + role groups)
├── database/
│   ├── migrations/         # Core database schema migrations
│   └── seeders/          # Core demonstration seed data
├── resources/views/        # Blade templates (auth, dashboard, portals, etc.)
├── public/               # Entry point and assets
├── config/               # Laravel configuration files
├── composer.json          # PHP dependencies
├── package.json           # JavaScript dependencies
└── phpunit.xml            # Testing configuration
```

## 👥 Authentication (Public Edition Demo Credentials)

The public edition includes demonstration accounts for exploring role-based access:

- **Student**: `student@webotapp.edu.in` / `password`
- **Faculty**: `faculty@webotapp.edu.in` / `password`
- **Parent**: `parent@webotapp.edu.in` / `password`

> **Note**: These are demo credentials for the public portfolio edition. The private full edition may have additional role accounts.

## 📚 API Endpoints (Selected)

| Method | Endpoint | Description |
|---|---|---|
| GET | `/` | Homepage (login redirect) |
| GET | `/login` | Login form |
| POST | `/login` | Process login |
| POST | `/logout` | User logout |
| GET | `/portal/student` | Student portal |
| GET | `/portal/faculty` | Faculty portal |
| GET | `/portal/parent` | Parent portal |
| GET | `/students/{student}/id-card` | Student ID card |
| GET | `/students/{student}/marksheet` | Exam marksheet |
| GET | `/exams/{exam}/admit-card/{student}` | Exam admit card |
| GET | `/fees/payments/{payment}/receipt` | Fee receipt |
| GET | `/certificates/{certificate}/print` | Certificate print |
| GET | `/verify-certificate/{token}` | Certificate QR verification |

## 🧪 Testing

```bash
composer test
# or
php artisan test
```

The test suite includes core feature tests for the public edition functionality. Run with `php artisan test` or `vendor/bin/phpunit`.

## 📄 License

MIT License. See the LICENSE file for full text.

> This is the **Public Portfolio Edition**. For the complete private edition with all 30 modules, database configurations, and full development suite, visit the private repository: `college-management-software-Full`

## 👤 Maintainer

**Project Owner / Maintainer:** Nadeem Qureshi  
**GitHub:** [nadeemmx](https://github.com/nadeemmx)