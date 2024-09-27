Installation

To set up the project, follow these steps:

    Clone the repository:
    git clone <repository-url>
    cd <project-directory>

    Install PHP dependencies:
    composer install

    Install Node.js dependencies:
    npm install

    Copy the environment configuration file:
    cp .env.example .env

    Generate the application key:
    php artisan key:generate

    Run the database migrations and seed the database:
    php artisan migrate:fresh --seed

    Create a symbolic link for storage:
    php artisan storage:link

Running the Application

To start the application, run the following commands in separate terminal windows:

    Start the Laravel development server:
    php artisan serve

    Compile the front-end assets:
    npm run dev

Prerequisites

Ensure you have the following installed:

    PHP >= 8.2
    Composer
    Node.js >= 12
    npm
    MySQL

Notes

    Update the .env file with your database credentials and any other environment-specific settings.
    For production environments, consider additional configurations for security, caching, and performance.
    Use Mail Smtp Credentials for sending emails.

