# 📚 Library Management System

## 📖 Overview
The Library Management System is a full-stack web application designed to simplify and automate the process of managing a library’s resources.  
This system provides both frontend and backend functionalities, allowing librarians and administrators to manage books, members, and transactions efficiently.

---

## 🧩 Features
- 📘 Book Management – Add, update, delete, and search for books.  
- 👥 Member Management – Register and manage library members.  
- 🔄 Borrow/Return System – Track book borrowing and returning transactions.  
- 📊 Dashboard – Display statistics such as total books, borrowed books, and members.  
- 🔐 Authentication – Admin login system for secure access.  
- 🗃️ Database Integration – Data is securely stored and managed in SQL Server.  
- 💡 Responsive Interface – Fully responsive design built with HTML, CSS, and JavaScript.

---

## ⚙️ Technologies Used
| Layer | Technologies |
|-------|---------------|
| Frontend | HTML, CSS, JavaScript |
| Backend | PHP |
| Database | SQL Server |
| Server Type | Localhost (XAMPP or WAMP with SQLSRV driver) |

---

## 🧱 System Structure
- index.php – Homepage and login interface  
- dashboard.php – Admin dashboard and management area  
- add_book.php – Form to add new books  
- manage_members.php – Manage and view members  
- transactions.php – Handle borrow and return operations  
- connection.php – Database connection file (SQL Server)  
- assets/ – Contains CSS, JS, and image files  
- api.php – Handles backend CRUD operations via AJAX  

---

## 🗄️ Database Design
The system database consists of multiple tables such as:
- Library_Db – Stores main library branch info  
- Books – Contains details about each book  
- Members – Stores member records  
- Transactions – Records borrow and return details  
- Users – Admin login credentials  

All tables are linked with primary and foreign key constraints, ensuring data consistency and referential integrity.

---

## 🚀 How to Run
1. Clone this repository:  
   `bash
   git clone https://github.com/your-username/Library-Management-System.git
   #Create by #Jaweid #Moraadi 2025 -10-17
