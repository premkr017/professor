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

## Phase 4: Extras
- [ ] Profile & Settings pages (edit profile, change password)
- [ ] Notifications system
- [ ] Test the app end-to-end

