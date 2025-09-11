# Inventory Management System - Database Schema Documentation

## Overview
This document describes the complete database schema for the Inventory Management System, including all tables, relationships, and key constraints.

## Core Tables

### 1. Users Table
**Table Name:** `users`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique user identifier |
| employee_no | VARCHAR(50) | UNIQUE, NOT NULL | Employee number |
| first_name | VARCHAR(100) | NOT NULL | Employee first name |
| last_name | VARCHAR(100) | NOT NULL | Employee last name |
| department_id | BIGINT | FOREIGN KEY → departments.id | Department assignment |
| position | VARCHAR(100) | NULLABLE | Job position |
| email | VARCHAR(150) | UNIQUE, NOT NULL | Email address |
| password | VARCHAR(255) | NOT NULL | Encrypted password |
| role_id | BIGINT | FOREIGN KEY → roles.id | User role |
| status | VARCHAR(50) | DEFAULT 'Active' | User status (Active, Inactive, Resigned) |
| created_at | TIMESTAMP | NOT NULL | Record creation time |
| updated_at | TIMESTAMP | NOT NULL | Last update time |

### 2. Departments Table
**Table Name:** `departments`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique department identifier |
| parent_id | BIGINT | FOREIGN KEY → departments.id, NULLABLE | Parent department (for hierarchy) |
| name | VARCHAR(100) | NOT NULL | Department name |
| description | TEXT | NULLABLE | Department description |
| manager_id | BIGINT | FOREIGN KEY → users.id, NULLABLE | Department manager |
| created_at | TIMESTAMP | NOT NULL | Record creation time |
| updated_at | TIMESTAMP | NOT NULL | Last update time |

### 3. Roles Table
**Table Name:** `roles`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique role identifier |
| name | VARCHAR(50) | NOT NULL | Role name (Admin, IT Staff, Employee) |
| description | TEXT | NULLABLE | Role description |
| guard_name | VARCHAR(255) | NOT NULL | Guard name for Laravel permissions |
| created_at | TIMESTAMP | NOT NULL | Record creation time |
| updated_at | TIMESTAMP | NOT NULL | Last update time |

### 4. Permissions Table
**Table Name:** `permissions`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique permission identifier |
| name | VARCHAR(100) | NOT NULL | Permission name |
| description | TEXT | NULLABLE | Permission description |
| guard_name | VARCHAR(255) | NOT NULL | Guard name for Laravel permissions |
| created_at | TIMESTAMP | NOT NULL | Record creation time |
| updated_at | TIMESTAMP | NOT NULL | Last update time |

## Asset Management Tables

### 5. Assets Table (Central Table)
**Table Name:** `assets`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique asset identifier |
| asset_tag | VARCHAR(50) | UNIQUE, NOT NULL | Asset tag/barcode |
| category_id | BIGINT | FOREIGN KEY → asset_categories.id | Asset category |
| vendor_id | BIGINT | FOREIGN KEY → vendors.id | Vendor/supplier |
| name | VARCHAR(100) | NOT NULL | Asset name |
| description | TEXT | NULLABLE | Asset description |
| serial_number | VARCHAR(100) | NULLABLE | Serial number |
| purchase_date | DATE | NULLABLE | Purchase date |
| warranty_end | DATE | NULLABLE | Warranty expiration |
| cost | DECIMAL(12,2) | NULLABLE | Purchase cost |
| status | VARCHAR(50) | DEFAULT 'Available' | Asset status |
| movement | VARCHAR(50) | DEFAULT 'New Arrival' | Asset movement status |
| assigned_to | BIGINT | FOREIGN KEY → users.id, NULLABLE | Assigned user |
| department_id | BIGINT | FOREIGN KEY → departments.id, NULLABLE | Assigned department |
| assigned_date | DATE | NULLABLE | Assignment date |
| created_at | TIMESTAMP | NOT NULL | Record creation time |
| updated_at | TIMESTAMP | NOT NULL | Last update time |

**Status Values:** Active, Inactive, Under Maintenance, Issue Reported, Pending Confirmation, Disposed
**Movement Values:** New Arrival, Deployed, Returned, Transferred, Disposed

### 6. Asset Categories Table
**Table Name:** `asset_categories`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique category identifier |
| name | VARCHAR(100) | NOT NULL | Category name |
| description | TEXT | NULLABLE | Category description |
| created_at | TIMESTAMP | NOT NULL | Record creation time |
| updated_at | TIMESTAMP | NOT NULL | Last update time |

### 7. Vendors Table
**Table Name:** `vendors`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique vendor identifier |
| name | VARCHAR(150) | NOT NULL | Vendor name |
| contact_person | VARCHAR(100) | NULLABLE | Contact person |
| phone | VARCHAR(50) | NULLABLE | Phone number |
| email | VARCHAR(150) | NULLABLE | Email address |
| address | TEXT | NULLABLE | Physical address |
| created_at | TIMESTAMP | NOT NULL | Record creation time |
| updated_at | TIMESTAMP | NOT NULL | Last update time |

## Asset Type Specific Tables

### 8. Computers Table
**Table Name:** `computers`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique computer identifier |
| asset_id | BIGINT | FOREIGN KEY → assets.id, CASCADE DELETE | Related asset |
| processor | VARCHAR(100) | NULLABLE | CPU specifications |
| ram | VARCHAR(50) | NULLABLE | RAM specifications |
| storage | VARCHAR(100) | NULLABLE | Storage specifications |
| os | VARCHAR(50) | NULLABLE | Operating system |
| created_at | TIMESTAMP | NOT NULL | Record creation time |
| updated_at | TIMESTAMP | NOT NULL | Last update time |

### 9. Monitors Table
**Table Name:** `monitors`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique monitor identifier |
| asset_id | BIGINT | FOREIGN KEY → assets.id, CASCADE DELETE | Related asset |
| size | VARCHAR(50) | NULLABLE | Screen size |
| resolution | VARCHAR(50) | NULLABLE | Screen resolution |
| panel_type | VARCHAR(50) | NULLABLE | Panel type (LCD, LED, OLED) |
| created_at | TIMESTAMP | NOT NULL | Record creation time |
| updated_at | TIMESTAMP | NOT NULL | Last update time |

### 10. Printers Table
**Table Name:** `printers`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique printer identifier |
| asset_id | BIGINT | FOREIGN KEY → assets.id, CASCADE DELETE | Related asset |
| type | VARCHAR(50) | NULLABLE | Printer type (Inkjet, Laser, Thermal) |
| color_support | BOOLEAN | DEFAULT FALSE | Color printing capability |
| duplex | BOOLEAN | DEFAULT FALSE | Duplex printing capability |
| created_at | TIMESTAMP | NOT NULL | Record creation time |
| updated_at | TIMESTAMP | NOT NULL | Last update time |

### 11. Peripherals Table
**Table Name:** `peripherals`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique peripheral identifier |
| asset_id | BIGINT | FOREIGN KEY → assets.id, CASCADE DELETE | Related asset |
| type | VARCHAR(50) | NULLABLE | Peripheral type (Mouse, Keyboard, RAM) |
| interface | VARCHAR(50) | NULLABLE | Interface type (USB, Wireless, Bluetooth) |
| created_at | TIMESTAMP | NOT NULL | Record creation time |
| updated_at | TIMESTAMP | NOT NULL | Last update time |

## Activity and Tracking Tables

### 12. Asset Timeline Table
**Table Name:** `asset_timeline`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique timeline entry identifier |
| asset_id | BIGINT | FOREIGN KEY → assets.id, CASCADE DELETE | Related asset |
| action | VARCHAR(255) | NOT NULL | Action type (assigned, unassigned, transferred, created, updated) |
| from_user_id | BIGINT | FOREIGN KEY → users.id, SET NULL | Previous user |
| to_user_id | BIGINT | FOREIGN KEY → users.id, SET NULL | New user |
| from_department_id | BIGINT | FOREIGN KEY → departments.id, SET NULL | Previous department |
| to_department_id | BIGINT | FOREIGN KEY → departments.id, SET NULL | New department |
| notes | TEXT | NULLABLE | Additional notes |
| old_values | JSON | NULLABLE | Previous asset state |
| new_values | JSON | NULLABLE | New asset state |
| performed_by | BIGINT | FOREIGN KEY → users.id, CASCADE DELETE | User who performed action |
| performed_at | TIMESTAMP | NOT NULL | When action was performed |
| created_at | TIMESTAMP | NOT NULL | Record creation time |
| updated_at | TIMESTAMP | NOT NULL | Last update time |

### 13. Transfers Table
**Table Name:** `transfers`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique transfer identifier |
| asset_id | BIGINT | FOREIGN KEY → assets.id | Related asset |
| from_user_id | BIGINT | FOREIGN KEY → users.id, NULLABLE | Previous user |
| to_user_id | BIGINT | FOREIGN KEY → users.id | New user |
| from_department | VARCHAR(100) | NULLABLE | Previous department |
| to_department | VARCHAR(100) | NOT NULL | New department |
| transfer_date | DATETIME | NOT NULL | Transfer date |
| remarks | TEXT | NULLABLE | Transfer remarks |
| created_at | TIMESTAMP | NOT NULL | Record creation time |

### 14. Maintenance Table
**Table Name:** `maintenance`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique maintenance identifier |
| asset_id | BIGINT | FOREIGN KEY → assets.id | Related asset |
| vendor_id | BIGINT | FOREIGN KEY → vendors.id | Service vendor |
| issue_reported | TEXT | NULLABLE | Reported issue |
| repair_action | TEXT | NULLABLE | Repair actions taken |
| cost | DECIMAL(12,2) | NULLABLE | Maintenance cost |
| start_date | DATETIME | NOT NULL | Maintenance start date |
| end_date | DATETIME | NULLABLE | Maintenance end date |
| status | VARCHAR(50) | NOT NULL | Maintenance status |
| remarks | TEXT | NULLABLE | Additional remarks |
| created_at | TIMESTAMP | NOT NULL | Record creation time |
| updated_at | TIMESTAMP | NOT NULL | Last update time |

### 15. Disposals Table
**Table Name:** `disposals`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique disposal identifier |
| asset_id | BIGINT | FOREIGN KEY → assets.id | Related asset |
| disposal_date | DATETIME | NOT NULL | Disposal date |
| disposal_type | VARCHAR(50) | NOT NULL | Type of disposal |
| disposal_value | DECIMAL(12,2) | NULLABLE | Disposal value |
| approved_by | BIGINT | FOREIGN KEY → users.id | Approving user |
| remarks | TEXT | NULLABLE | Disposal remarks |
| created_at | TIMESTAMP | NOT NULL | Record creation time |
| updated_at | TIMESTAMP | NOT NULL | Last update time |

### 16. Logs Table
**Table Name:** `logs`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique log identifier |
| category | VARCHAR(50) | NOT NULL | Log category (System, Asset) |
| asset_id | BIGINT | FOREIGN KEY → assets.id, NULLABLE | Related asset |
| user_id | BIGINT | FOREIGN KEY → users.id | User who performed action |
| role_id | BIGINT | FOREIGN KEY → roles.id | User's role |
| permission_id | BIGINT | FOREIGN KEY → permissions.id, NULLABLE | Permission used |
| department_id | BIGINT | FOREIGN KEY → departments.id, NULLABLE | User's department |
| event_type | VARCHAR(100) | NOT NULL | Type of event |
| ip_address | VARCHAR(50) | NULLABLE | IP address |
| user_agent | TEXT | NULLABLE | User agent string |
| remarks | TEXT | NOT NULL | Event details |
| created_at | TIMESTAMP | NOT NULL | Record creation time |
| updated_at | TIMESTAMP | NOT NULL | Last update time |

## Junction Tables

### 17. Role Permissions Table
**Table Name:** `role_permissions`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| role_id | BIGINT | FOREIGN KEY → roles.id, CASCADE DELETE | Role |
| permission_id | BIGINT | FOREIGN KEY → permissions.id, CASCADE DELETE | Permission |
| created_at | TIMESTAMP | NOT NULL | Record creation time |
| updated_at | TIMESTAMP | NOT NULL | Last update time |

**Unique Constraint:** (role_id, permission_id)

### 18. Model Has Permissions Table
**Table Name:** `model_has_permissions`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| permission_id | BIGINT | FOREIGN KEY → permissions.id, CASCADE DELETE | Permission |
| model_type | VARCHAR(255) | NOT NULL | Model class name |
| model_id | BIGINT | NOT NULL | Model instance ID |

**Primary Key:** (permission_id, model_id, model_type)
**Index:** (model_id, model_type)

### 19. Model Has Roles Table
**Table Name:** `model_has_roles`

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| role_id | BIGINT | FOREIGN KEY → roles.id, CASCADE DELETE | Role |
| model_type | VARCHAR(255) | NOT NULL | Model class name |
| model_id | BIGINT | NOT NULL | Model instance ID |

**Primary Key:** (role_id, model_id, model_type)
**Index:** (model_id, model_type)

## Key Relationships

1. **Users ↔ Departments**: Many-to-One (users belong to departments)
2. **Users ↔ Roles**: Many-to-One (users have roles)
3. **Roles ↔ Permissions**: Many-to-Many (through role_permissions)
4. **Assets ↔ Categories**: Many-to-One (assets belong to categories)
5. **Assets ↔ Vendors**: Many-to-One (assets have vendors)
6. **Assets ↔ Users**: Many-to-One (assets assigned to users)
7. **Assets ↔ Asset Types**: One-to-One (computers, monitors, printers, peripherals)
8. **Assets ↔ Timeline**: One-to-Many (assets have multiple timeline entries)
9. **Assets ↔ Transfers**: One-to-Many (assets can have multiple transfers)
10. **Assets ↔ Maintenance**: One-to-Many (assets can have multiple maintenance records)
11. **Assets ↔ Disposals**: One-to-Many (assets can have disposal records)

## Indexes and Performance

### Recommended Indexes
- `users.employee_no` (UNIQUE)
- `users.email` (UNIQUE)
- `assets.asset_tag` (UNIQUE)
- `assets.serial_number` (UNIQUE)
- `assets.assigned_to`
- `assets.department_id`
- `asset_timeline.asset_id`
- `asset_timeline.performed_at`
- `logs.user_id`
- `logs.created_at`

## Data Integrity Rules

1. **Cascade Deletes**: Asset type tables (computers, monitors, etc.) cascade delete when parent asset is deleted
2. **Set Null**: User assignments set to null when user is deleted
3. **Unique Constraints**: Asset tags, serial numbers, employee numbers, and emails must be unique
4. **Foreign Key Constraints**: All relationships enforced at database level
5. **Timestamps**: All tables include created_at and updated_at timestamps

This schema supports a comprehensive inventory management system with full audit trails, user management, and detailed asset tracking capabilities.