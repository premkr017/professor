# Money Management App - Build TODO

## Phase 1: Data Layer (Migrations & Models)
- [x] Create migrations: accounts, categories, transactions, budgets, savings_goals, loans, recurring_transactions, notifications
- [x] Create Eloquent models for each with User relationships
- [x] Add relationships to User model
- [x] Run migrations

## Phase 2: Controllers & Routes
- [x] Create DashboardController
- [x] Create controllers: AccountController, CategoryController, TransactionController, BudgetController, SavingsGoalController, LoanController, RecurringController, ReportController, NotificationController, ProfileController, SettingsController
- [x] Add RESTful resource routes under auth middleware

## Phase 3: Views
- [x] Build shared admin layout with sidebar navigation

## Accounts / Wallets Module (enhancement)
- [x] Migration: add bank_name, account_number, opening_balance columns + expand type enum
- [x] Model: add new fields to $fillable
- [x] Controller: add ACCOUNT_TYPES constant, update validation, set initial balance
- [x] Create view: admin/accounts/create.blade.php
- [x] Create view: admin/accounts/edit.blade.php
- [x] Update view: admin/accounts/index.blade.php (richer cards, negative balance handling)
- [x] Update dashboard account listing for consistency
- [x] Run migration

## Transactions Module (core)
- [x] Migration: add payment_method, attachment, to_account_id columns
- [x] Model: add new fields to $fillable + toAccount relationship
- [x] Controller: add PAYMENT_METHODS constant, transfer balance logic, attachment upload
- [x] Create view: admin/transactions/create.blade.php
- [x] Update view: admin/transactions/index.blade.php (summary cards, filters, table)
- [x] Create view: admin/transactions/edit.blade.php
- [x] Run migrate and storage:link
- [x] Verify routes and PHP syntax
- [x] Commit & push to `blackboxai/transactions-module` branch

## Income Management Module (💰)
- [x] Define default income sources constant (Salary, Freelancing, Business, Commission, Interest, Rent, Investment, Bonus, Gift, Other)
- [x] Seed default income categories for users on registration (AuthController)
- [x] Create IncomeController (index + store)
- [x] Add routes for income page
- [x] Create view: admin/income/index.blade.php (month breakdown, quick add, recent income)
- [x] Add "Income" nav link in layout sidebar
- [x] Update TODO.md
- [x] Verify PHP syntax & routes

## Expense Management Module (🛒)
- [x] Define default expense categories constant (Food, Rent, Electricity, Mobile, Transport, Shopping, Education, Medical, Entertainment, EMI, Bills, Other)
- [x] Seed default expense categories for users on registration (AuthController)
- [x] Create ExpenseController (index + store)
- [x] Add routes for expense page
- [x] Create view: admin/expense/index.blade.php (month breakdown, quick add, recent expenses)
- [x] Add "Expenses" nav link in layout sidebar
- [x] Update TODO.md
- [x] Verify PHP syntax & routes
- [ ] Commit & push to `blackboxai/expense-module` branch

## Categories Module (📊)
- [x] Migration: add parent_id + status columns to categories table
- [x] Model: add parent/children relationships + path helper + status scope
- [x] Controller: handle parent_id, status, hierarchical index, prevent self-parent
- [x] Update seeders (Income/Expense) to build hierarchical Food/Transport trees
- [x] Create view: admin/categories/index.blade.php (hierarchical tree)
- [x] Create view: admin/categories/create.blade.php
- [x] Create view: admin/categories/edit.blade.php
- [x] Run migration
- [x] Verify routes and PHP syntax
- [ ] Commit & push to `blackboxai/categories-module` branch

## Phase 4: Extras
- [x] Budget management screens and routes
- [x] Recurring transaction screens and routes
- [x] Savings goals screens and routes
- [x] Loans & debts screens and routes
- [x] Reports & analytics screen
- [x] Notifications screen
- [ ] Profile & Settings pages (edit profile, change password)
- [ ] Test the app end-to-end
