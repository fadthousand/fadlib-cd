# Fad Library

**Fad Library** is a web-based library information system and digital archive built with **Laravel** for managing official `.epub` light novel collections.

Academically, this project fulfills the Final Project (UAS) requirements for the **Web Framework Programming** (*Pemrograman Web Framework*) course in the Information Systems Study Program, Universitas Singaperbangsa Karawang.
Practically, it serves as a private, structured digital archive—drawing inspiration from curated community archives like U2, but designed with a friendlier access model that avoids rigid IP bans in favor of clear role-based access control and straightforward borrowing circulation.

- **Live Application:** [https://fadlib-cd.wasmer.app](https://fadlib-cd.wasmer.app)
- **GitHub Repository:** [https://github.com/fadthousand/fadlib-cd](https://github.com/fadthousand/fadlib-cd)

---

## Developer

This project was developed individually in accordance with the course final project guidelines.

| Name | Student ID (NPM) | Study Program |
| :--- | :--- | :--- |
| Abdurrahman Fadly | `2410631250001` | Information Systems — Universitas Singaperbangsa Karawang |

---

## Demo Access

To protect catalog integrity and private EPUB storage on the live server, production credentials for **Admin**, **Staff (Librarian)**, and **Member** accounts are omitted from this public repository and are provided privately to the course evaluator in the final report.

To test all roles locally, run `php artisan migrate --seed` after setting up your local environment, then sign in with the local seeder accounts configured in `database/seeders/DatabaseSeeder.php`.

---

## Role-Based Access Control

The application enforces distinct permissions across three user roles through session authentication and route middleware:

| Module / Feature | Admin (`admin`) | Staff / Librarian (`staff`) | Member (`member`) |
| :--- | :---: | :---: | :---: |
| **Login & Logout** | Yes | Yes | Yes |
| **Dashboard Overview** | Yes | Yes | No |
| **User Management (`Users`)** | Full CRUD | No | No |
| **Categories / Series (`Series`)** | Full CRUD | Read-Only | Read-Only |
| **Book Collection (`Collection`)** | Full CRUD + EPUB Download | Full CRUD + EPUB Download | Browse + Self-Checkout |
| **Member Management (`Members`)** | Full CRUD | Full CRUD | No |
| **Circulation (`Circulation`)** | Record Loans & Returns | Record Loans & Returns | View Active Loans (`My Shelf`) |

---

## Core Modules & Features

1. **Authentication & Authorization**
   - Session-based authentication powered by Laravel Breeze.
   - Server-side route protection using authentication and role middleware (`admin` and `staff`) to prevent unauthorized requests.

2. **Dashboard**
   - Displays real-time library statistics: total cataloged titles, available stock copies, active borrowing count, registered members, and recent borrowing transactions.

3. **User Management (Admin Only)**
   - List system users, create new accounts, update user details, delete accounts, and assign roles (`admin`, `staff`, or `member`).

4. **Category Management (`Series`)**
   - Organizes books by series title, author, epigraph, and description.
   - Admins have full create, update, and delete permissions, while Staff members have read-only access to browse series and their volumes.

5. **Book Management (`Collection`)**
   - Full CRUD operations for book volumes, including series assignment, volume code (`volume_code`), title, author, publication year, stock copies, synopsis, notes, and `.epub` file uploads.
   - Supports filtering by series and searching by volume code or author name.

6. **Member Management (`Members`)**
   - Manages library member profiles (`member_code`, full name, email, phone, address, and `active` / `suspended` status), optional linking to `users` login accounts, and individual borrowing histories.

7. **Borrowing & Circulation (`Circulation` & `My Shelf`)**
   - **Staff-Assisted Borrowing:** Supports multi-volume loans in a single transaction, logs the processing staff member, sets an automatic 7-day due date, and decrements book stock atomically.
   - **Member Self-Service Borrowing:** Authenticated members can borrow available volumes directly from a book's detail page and download the `.epub` file via `My Shelf` while the loan is active.
   - **Returns:** Records the return date, updates transaction status to `returned`, and restores available book stock.

---

## Architecture & Database Schema

### Tech Stack
- **Backend:** PHP 8.3, Laravel Framework
- **Frontend:** Blade Templates, Tailwind CSS, Alpine.js, Vite
- **Database:** TiDB Cloud (MySQL-compatible over TLS)
- **Object Storage:** Cloudflare R2 (private S3-compatible storage for `.epub` files)
- **Hosting:** Wasmer Edge

### Main Database Tables
- `users` — Stores login credentials and user roles (`admin`, `staff`, `member`).
- `members` — Stores library member profiles with an optional one-to-one link to `users` (`user_id`).
- `categories` — Stores book series/categories.
- `books` — Stores volume metadata, stock counts, and private EPUB storage paths (`category_id` foreign key).
- `borrowings` — Stores transaction headers, transaction codes, `member_id`, `processed_by` (`users` foreign key), borrow date, due date (+7 days), return date, and status (`borrowed` / `returned`).
- `borrowing_details` — Stores line items (`book_id` and `quantity`) belonging to each transaction in `borrowings` (`borrowing_id`).

---

## Local Setup

1. **Clone the repository**
   ```bash
   git clone [https://github.com/fadthousand/fadlib-cd.git](https://github.com/fadthousand/fadlib-cd.git)
   cd fadlib-cd
   ```

2. **Install PHP and frontend dependencies**
   ```bash
   composer install
   npm install
   npm run build
   ```

3. **Configure environment variables**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Set up database credentials in `.env`, then run migrations and seeders**
   ```bash
   php artisan migrate --seed
   ```

5. **Start the local development server**
   ```bash
   php artisan serve
   ```
   Open `http://127.0.0.1:8000` in your browser.
