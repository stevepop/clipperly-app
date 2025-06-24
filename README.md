
---

# Appointment Booking with Laravel, Twilio, and SendGrid

This project is a Laravel-based appointment booking application that integrates with Twilio and SendGrid for seamless communication and notifications.

## Getting Started

These instructions will help you set up and run the project on your local machine for development and testing purposes.

### Prerequisites

- Docker
- Git

You do not need PHP or Node.js installed locally. All commands are run inside Docker containers managed by Laravel Sail.

### Installation

Follow these steps to set up and run the application:

#### 1. Clone the Repository

Use Git to clone the project repository to your local machine:

```bash
git clone git@github.com:stevepop/clipperly-app.git
cd clipperly-app
```

#### 2. Copy the Example Environment File

Create a new environment file by copying the example provided:

```bash
cp .env.example .env
```

#### 3. Update `.env` for SQLite

Edit the `.env` file to use SQLite as the database:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite
```

You can comment out or remove the lines for `DB_HOST`, `DB_PORT`, `DB_USERNAME`, and `DB_PASSWORD`.

#### 4. Start the Application with Sail

If this is your first time using Sail, you can run it via Docker without having PHP installed:

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    bash -c "composer install && cp .env.example .env"
```

Then, start Sail:

```bash
./vendor/bin/sail up -d
```

If you encounter a permissions error, try:

```bash
bash vendor/bin/sail up -d
```

#### 5. Install NPM Dependencies

Navigate inside the Docker container to install and build assets:

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

#### 6. Run Migrations and Seeders

Set up the database by running migrations and seeders:

```bash
./vendor/bin/sail artisan migrate --seed
```

#### 7. Access the Application

You can now access the application by visiting:

```
http://localhost
```
