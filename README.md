# Project Management System

A comprehensive Project Management System built with **Laravel 12** and **Vue 3**. This application allows teams to manage projects, tasks, roles, and permissions effectively, with real-time updates and collaboration features.

## 🚀 Tech Stack

### Backend
- **Framework:** [Laravel 12](https://laravel.com)
- **Language:** PHP 8.2+
- **Database:** PostgreSQL (Default) / MySQL
- **Real-time:** Laravel Reverb (WebSockets)
- **Queue:** Laravel Horizon (Redis) / Database
- **Authentication:** Laravel Sanctum

### Frontend
- **Framework:** [Vue.js 3](https://vuejs.org/) (Composition API)
- **Build Tool:** [Vite](https://vitejs.dev/)
- **Styling:** [Tailwind CSS 4](https://tailwindcss.com/)
- **Routing:** Vue Router
- **HTTP Client:** Axios
- **UI Components:** Headless UI, Tabler Icons

## ✨ Features

- **Project Management:** Create, update, and manage projects.
- **Task Management:** Assign tasks to users, track status, and set due dates.
- **Role-Based Access Control (RBAC):**
  - Manage Roles (e.g., Admin, Manager, Developer).
  - Granular Permissions (View, Create, Edit, Delete).
- **Collaboration:**
  - Real-time comments on tasks.
  - Real-time notifications.
  - File attachments/uploads.
- **Dashboard:** Overview of projects and tasks.
- **User Management:** Handle user profiles and roles.

## 🛠 Prerequisites

Ensure you have the following installed on your machine:
- [PHP](https://www.php.net/downloads) (v8.2 or higher)
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) & NPM
- [PostgreSQL](https://www.postgresql.org/) (or MySQL)
- [Redis](https://redis.io/) (Optional, for Horizon/Queues)

## 📦 Installation

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd project_management_system
   ```

2. **Install Backend Dependencies:**
   ```bash
   composer install
   ```

3. **Install Frontend Dependencies:**
   ```bash
   npm install
   ```

4. **Environment Setup:**
   Copy the example environment file and configure your database credentials.
   ```bash
   cp .env.example .env
   ```
   Edit the `.env` file to match your database configuration:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=your_database_name
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Generate App Key:**
   ```bash
   php artisan key:generate
   ```

6. **Run Migrations & Seeders:**
   Create the database first, then run:
   ```bash
   php artisan migrate --seed
   ```
   *Note: The seeder typically creates default roles (Admin, etc.) and a default admin user.*

7. **Link Storage:**
   ```bash
   php artisan storage:link
   ```

## 🏃‍♂️ Running the Application

This project is configured with a convenience script to run the backend server, queue worker, and frontend dev server concurrently.

```bash
composer run dev
```

Or you can run them separately in different terminal tabs:

**Backend:**
```bash
php artisan serve
```

**Queue Worker:**
```bash
php artisan queue:listen
```

**Frontend:**
```bash
npm run dev
```

**Reverb (WebSockets):**
If you are using real-time features:
```bash
php artisan reverb:start
```

## 📂 Project Structure

- **`app/`**: Core PHP application logic (Models, Controllers, Events).
- **`resources/js/`**: Vue.js frontend application.
  - **`pages/`**: Vue page components.
  - **`components/`**: Reusable UI components.
  - **`composables/`**: Shared logic (Vue Composables).
- **`routes/`**: API and Web routes.
- **`database/`**: Migrations and Seeders.

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
