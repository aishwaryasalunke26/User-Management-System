
📌 User Management System (PHP + MySQL)

A complete backend-based User Management System built using PHP and MySQL with authentication, CRUD operations, profile management, and messaging functionality.

🎯 Project Objective

To implement dynamic backend features including:

Authentication System

CRUD Operations

Profile Management

Secure Database Integration

Messaging System

🛠 Technologies Used

PHP

MySQL

HTML

CSS

XAMPP (Apache Server)

Git & GitHub

✨ Features
🔐 Authentication

User Registration

Password Hashing (password_hash())

Login & Logout using Sessions

👤 Profile Management

View Profile

Edit Profile

Upload Profile Picture

Image validation (size & type)

📊 CRUD Operations

Add User

View Users

Update User

Delete User

💬 Messaging System

Send Message

View Inbox

Reply to Messages

🗄 Database Structure
1️⃣ users Table
Field	Type
id	INT (Primary Key)
name	VARCHAR
email	VARCHAR
password	VARCHAR
profile_image	VARCHAR
created_at	DATETIME
2️⃣ messages Table
Field	Type
id	INT (Primary Key)
sender_id	INT (Foreign Key → users.id)
receiver_id	INT (Foreign Key → users.id)
message_text	TEXT
sent_at	DATETIME
🔗 ER Diagram

Users (1) --------< Messages >-------- (1) Users

One user can send many messages

One user can receive many messages

🧠 Database Normalization
✔ 1NF

No repeating columns

Atomic values

Unique primary key

✔ 2NF

All non-key attributes depend on full primary key

✔ 3NF

No transitive dependency

All attributes depend only on primary key

Database is fully normalized up to 3NF.

## 🔗 ER Diagram

![ER Diagram](er-diagram.png)

## 🧠 Database Normalization

### 1NF (First Normal Form)
- Each column contains atomic (single) values.
- No repeating groups.
- Each table has a primary key.

In this project:
- Users table stores one value per field.
- Messages table stores one message per row.

---

### 2NF (Second Normal Form)
- Table must be in 1NF.
- All non-key attributes fully depend on the primary key.

In this project:
- In users table, name, email, password depend on id.
- In messages table, message_text and sent_at depend on id.

---

### 3NF (Third Normal Form)
- Table must be in 2NF.
- No transitive dependency.

In this project:
- No column depends on another non-key column.
- All fields depend only on the primary key.

Therefore, the database is normalized up to 3NF.

