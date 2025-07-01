# 📰 Headless CMS – Laravel Breeze + TALL Stack

A modular **Headless CMS** built with Laravel Breeze and the **TALL Stack** (Tailwind, Alpine.js, Livewire, Laravel).  
This project is designed with **multi-language support**, **role-based access**, and **media/file management** – perfect for use as a backend CMS for any frontend framework or site.

---

## 🚀 Tech Stack

- **Laravel 12**
- **Laravel Breeze (Livewire version)**
- **TALL Stack**: Tailwind CSS, Alpine.js, Livewire, Laravel
- **PostgreSQL** (as main database)
- **Spatie Permission** (for role & permission management)

---

## 📦 Features

### 🌐 CMS Modules
- ✅ **Posts Management**
- ✅ **Pages Management**
- ✅ **Categories Management**
- ✅ **Media Manager** (admin only)

### 🔒 Admin & User Control
- ✅ **User Management** (Superadmin only)
- ✅ **Role & Permission Management** (Superadmin only)

### 🌍 Localization (Multi-language)
- ✅ **English** (`en`)
- ✅ **Bahasa Indonesia** (`id`)
- ✅ **German** (`de`)
- Auto-detected locale using session + middleware
- JSON-based translation files (`/lang/{locale}.json`)

---

## 🛠️ Requirements

- PHP >= 8.2
- Composer
- Node.js & NPM
- PostgreSQL
- Laravel CLI

---

## Links:

🔗 API Documentation
https://documenter.getpostman.com/view/36920255/2sB34ZrQNr#51a33950-c147-4b17-bcde-f73db3de2635

🎥 Video Explanation

---

## ⚙️ Installation & Local Development

```bash
# Clone the repo
git clone https://github.com/yourusername/headless.git
cd headless

# Install PHP dependencies
composer install

# Install JS dependencies
npm install

# Copy env file
cp .env.example .env

# Generate app key
php artisan key:generate

# Set up database (edit .env with your PostgreSQL credentials)
php artisan migrate --seed

# Link storage for media
php artisan storage:link

# Start dev server
composer run dev
