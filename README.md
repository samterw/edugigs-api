<p align="center">
  <h1 align="center">EduGigs API (Backend)</h1>
</p>

<p align="center">
  The robust Laravel backend API powering <strong>EduGigs</strong>, a campus freelance marketplace.
</p>

---

## 🚀 About The Project

EduGigs API serves as the centralized backend service for the EduGigs platform. It handles secure user authentication via Laravel Sanctum, gig management, slot scheduling, order workflows, real-time messaging, gamification badges, and automated database seeders.

## 🛠️ Tech Stack

* **Framework:** Laravel (PHP 8.2+)
* **Authentication:** Laravel Sanctum
* **Database:** MySQL
* **Key Packages:** Carbon (Date Handling), Intervention/Image (Media handling)

## ⚙️ Getting Started & Installation

To get a local copy up and running, follow these simple steps:

1. **Clone the repository:**
   ```bash
   git clone https://github.com/samterw/edugigs-api.git
   cd edugigs-api
   ```

2. **Install PHP dependencies:**
    ```bash
    composer install
    ```

3. **Configure Environment File:**
    Copy the example environment template and generate your application key:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Set up your database:**
    Update your database credentials (DB_DATABASE, DB_USERNAME, DB_PASSWORD) inside your .env file, then run the database migration and seeder command:
    ```bash
    php artisan migrate:fresh --seed
    ```

5. **Run the local development server:**
    ```bash
    php artisan serve
    ```

The API will be accessible at http://localhost:8000.

## 📂 Core Architecture

* **Controllers:** Handles business logic for Gigs, Orders, Authentication, Messages, Reviews, and Admin tasks.
* **Seeders:** Includes EduGigsSeeder to automatically populate realistic student profiles, faculty data, service categories, and chat logs for testing and evaluation.
* **Console Commands:** Custom scheduled background tasks for managing expired booking slots.

## 📄 License
This project is developed as part of an academic final year project.