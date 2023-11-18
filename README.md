# task15-11-2023

## Objective

This exercise aims to design a set of APIs that manage transactions of a company.
Managing transactions include: creating transactions, viewing transactions, recording
payments and generating reports.

## Requirements Overview
- This application will serve two ends:
    - Web
    - Mobile
- There are two types of users who can access the app:
    - Admin
    - Customer
- Admins will have the ability to:
    - Create transactions
    - View transactions
    - Record payments
    - Generate reports
- Users will be able to:
    - View their transactions
- Authentication is required

### API Endpoints:

#### Authentication:
1. **POST /api/login**
    - Authenticate users (both Admins and Customers).
    - Receive a token for subsequent requests.

2. **POST /api/logout**
    - Log out and invalidate the token.

#### Admin Actions:
3. **POST /api/transactions**
    - Create a new transaction.
    - Requires authentication as an admin.

4. **GET /api/transactions**
    - View all transactions.
    - Requires authentication as an admin.

5. **POST /api/payments**
    - Record payments for a specific transaction.
    - Requires authentication as an admin.

6. **GET /api/reports**
    - Generate reports.
    - Requires authentication as an admin.

#### User Actions:
7. **GET /api/transactions**
    - View transactions for the authenticated user.
    - Requires authentication as a user (both Admins and Customers).

### Example Workflow:
1. Admin logs in using `/api/login`.
2. Admin creates a transaction using `/api/transactions`.
3. Admin views transactions using `/api/transactions`.
4. Admin records payments for a transaction using `/api/payments`.
5. Admin generates reports using `/api/reports`.
6. Customer logs in using `/api/login`.
7. Customer views their transactions using `/api/transactions`.

- [Postman | Task 15/11/2023 | Coding Exercise - Backend](https://documenter.getpostman.com/view/2573933/2s9YXpWKMi)

## Requirements

- PHP 7.4 or higher
- Composer
- Node.js and npm
- MySQL or another database of your choice

## Getting Started

1. **Clone the repository:**

   ```bash
   git clone git@github.com:TariqAyman/task15-11-2023.git
   cd task15-11-2023
   ```

2. **Install PHP Dependencies:**

   ```bash
   composer install
   ```

3. **Copy Environment File:**

   ```bash
   cp .env.example .env
   ```

4. **Generate Application Key:**

   ```bash
   php artisan key:generate
   ```

5. **Configure Database:**

   Update the `.env` file with your database credentials:

   ```dotenv
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password
   ```

6. **Run Migrations:**

   ```bash
   php artisan migrate
   ```

7. **Install Frontend Dependencies:**

   ```bash
   npm install
   ```

8. **Compile Assets:**

   ```bash
   npm run dev
   ```

9. **Serve the Application:**

   ```bash
   php artisan serve
   ```

   Your application should now be running at [http://127.0.0.1:8000](http://127.0.0.1:8000).
