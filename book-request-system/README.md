# Book Request System

A simple PHP & MySQL web application for managing book requests with user, admin, and super admin roles.

---

## Features

- User registration & login  
- Book request system  
- Admin request management  
- Status tracking (pending, in_progress, completed)  
- Super admin controls (users & admins)

---

## Requirements

- XAMPP / WAMP  
- PHP 7.4+  
- MySQL  
- Apache Server  

---

## Setup

1. Copy project to: htdocs/book-request-system

2. Start Apache & MySQL

3. Create database:book_request_system

4. Import `.sql` file in phpMyAdmin

5. Update DB config in:/config/db.php

---

## Run Project
http://localhost/book-request-system/

---

## Roles

- User → Request books  
- Admin → Manage requests  
- Super Admin → Manage system  

---

## Notes

- Use `password_hash()` for passwords  
- Ensure database is properly imported  
---


