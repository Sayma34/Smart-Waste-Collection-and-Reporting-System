# Smart-Waste-Collection-and-Reporting-System
# ♻️ Smart Waste Collection & Reporting System

A role-based web application for managing municipal waste collection activities, citizen reports, waste collectors, vehicles, service fees, fines, and collection performance.

The system is developed using **PHP, MySQL, HTML5, CSS3, and PHP Sessions**.

---

## 📌 About the Project

The **Smart Waste Collection & Reporting System** provides a central platform for three types of users:

* 👤 Citizens / Users
* 👷 Waste Collectors
* 🛠️ Administrators

Each user type receives a dedicated dashboard with different permissions and functionality.

The main objective of the project is to improve waste collection monitoring, reporting, vehicle management, and communication between citizens, collectors, and administrators.

---

# ✨ Main Features

## 👤 Citizen / User

Registered citizens can:

* Create an account
* Login securely
* Access a personal dashboard
* Submit waste-related complaints or reports
* Specify the area where an issue occurred
* View previously submitted reports
* Track report status
* View the monthly waste collection fee
* View assigned fines
* Update payment status
* Logout securely

---

## 👷 Waste Collector

Collectors receive a dedicated operational dashboard.

Collectors can:

* Login securely
* Access their collector dashboard
* View assigned collection goals
* Update the number of bins collected
* Change their work status
* View performance badges
* View their assigned vehicle
* View fleet information
* Submit vehicle-condition reports
* Report vehicle problems
* Update vehicle operating status
* Logout

### Collector Work Status

Collectors can use statuses such as:

```text
On Work
On Leave
```

Bin collection updates are linked to the collector's current work status.

---

## 🚛 Vehicle Reporting

Collectors can report problems relating to their assigned vehicle.

Vehicle statuses supported by the system include:

```text
Operational
Under Maintenance
Out of Service
```

The administrator can later review vehicle information and recent reported issues.

---

## 🏅 Collector Performance

The system contains a collector performance and badge mechanism.

Collector information includes:

```text
Daily Goal
Bins Collected
Current Status
Badge
Assigned Vehicle
```

Badge levels used by the interface include:

```text
Silver
Blue
Gold
```

This provides a simple incentive mechanism for monitoring collection performance.

---

# 🛠️ Administrator Features

Administrators have access to the main management dashboard.

The administrator can manage several parts of the waste-management operation.

## 🗺️ Area Management

Administrators can:

* View area names
* View postal codes
* View area descriptions
* View total waste bins
* Update the number of bins in each area

---

## 🚚 Vehicle Management

Administrators can:

* View vehicle numbers
* View vehicle types
* View vehicle status
* See which collector has been assigned a vehicle
* View recently reported vehicle issues
* Assign vehicles to collectors

The application also checks vehicle assignments to help prevent the same vehicle from being allocated to multiple collectors.

---

## 👷 Collector Management

Administrators can:

* View registered collectors
* Assign collection areas
* Define daily collection goals
* Update collector information
* Assign vehicles
* Reassign vehicles when required

---

## 📝 Citizen Report Management

Administrators can:

* View reports submitted by citizens
* View the citizen who submitted a report
* View the reported area
* View report descriptions
* View creation dates
* Change report status

Current report states include:

```text
Pending
Accepted
```

---

## 💰 Fee & Fine Management

Administrators can:

* View citizen payment information
* View fine status
* View outstanding fines
* Assign a fine
* Remove a fine

The current implementation includes a predefined fine amount of:

```text
200 Tk
```

---

# 🔐 Authentication & Authorisation

The application uses **PHP sessions** for authentication.

Users are redirected according to their role.

```text
Admin
   ↓
admin_dashboard.php

Collector
   ↓
collector.php

User
   ↓
user.php
```

Protected pages check the current session before allowing access.

For example:

```php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
```

Passwords are stored using PHP password hashing.

```php
password_hash($password, PASSWORD_DEFAULT);
```

Login authentication uses:

```php
password_verify($password, $hashed_password);
```

---

# 🧰 Technologies Used

| Technology          | Purpose                              |
| ------------------- | ------------------------------------ |
| PHP                 | Server-side application logic        |
| MySQL               | Relational database                  |
| MySQLi              | PHP database communication           |
| HTML5               | Page structure                       |
| CSS3                | Interface styling                    |
| PHP Sessions        | Authentication                       |
| Prepared Statements | Safer SQL operations                 |
| Git                 | Version control                      |
| GitHub              | Repository hosting and collaboration |

---

# 📂 Project Files

The project currently contains the following main source files:

```text
smart-waste-collection/
│
├── index.php
│
├── login.php
├── signup.php
├── logout.php
├── dashboard.php
│
├── user.php
├── collector.php
├── admin_dashboard.php
│
├── db_connect.php
│
├── assign_vehicle.php
├── collector_status.php
├── vehicle_report.php
├── vehicle_status_collector.php
│
├── update_area_bins.php
├── update_collector.php
├── update_goal.php
├── update_report_status.php
├── update_user_fine.php
│
└── style.css
```

You may also have local image resources referenced by the interface.

For a cleaner GitHub repository, avoid keeping backup files such as:

```text
Copy of update_collector.php
```

inside the production source tree.

Use Git history instead of duplicate source files.

---

# 🗄️ Database

The application is configured to use a MySQL database called:

```text
waste_management
```

The PHP source references the following major tables:

```text
user
admin
citizen
collector
area
vehicle
reports
vehicle_reports
```

---

# 🧩 Simplified Database Relationships

```text
                       ┌──────────────┐
                       │     USER     │
                       └──────┬───────┘
                              │
             ┌────────────────┼────────────────┐
             │                │                │
             ▼                ▼                ▼
       ┌───────────┐   ┌─────────────┐   ┌───────────┐
       │   ADMIN   │   │  COLLECTOR  │   │  CITIZEN  │
       └───────────┘   └──────┬──────┘   └─────┬─────┘
                              │                │
                              │                ▼
                              │          ┌────────────┐
                              │          │  REPORTS   │
                              │          └────────────┘
                              │
                              ▼
                       ┌─────────────┐
                       │   VEHICLE   │
                       └──────┬──────┘
                              │
                              ▼
                    ┌──────────────────┐
                    │ VEHICLE_REPORTS  │
                    └──────────────────┘


                       ┌─────────────┐
                       │    AREA     │
                       └─────────────┘
```

---

# 🚀 How to Run the Project

The easiest way to run the application locally on Windows is with **XAMPP**.

## Requirements

Install:

* XAMPP
* PHP
* Apache
* MySQL / MariaDB
* phpMyAdmin
* Git

---

## 1. Install XAMPP

Download and install XAMPP.

After installation, open:

```text
XAMPP Control Panel
```

Start:

```text
Apache
MySQL
```

Both services should be running.

---

# 2. Put the Project Inside `htdocs`

Navigate to:

```text
C:\xampp\htdocs
```

Clone the GitHub repository:

```bash
git clone https://github.com/YOUR-USERNAME/YOUR-REPOSITORY.git
```

Then:

```bash
cd YOUR-REPOSITORY
```

Alternatively, download the repository as a ZIP file and extract it inside:

```text
C:\xampp\htdocs\
```

---

# 3. Create the MySQL Database

Open your browser and visit:

```text
http://localhost/phpmyadmin
```

Create a new database called:

```text
waste_management
```

You can also use:

```sql
CREATE DATABASE waste_management;
```

---

# 4. Import the Database

For the repository to be fully reproducible, export your current MySQL database from phpMyAdmin.

In phpMyAdmin:

```text
Select waste_management
        ↓
Export
        ↓
Quick
        ↓
SQL
        ↓
Export
```

Save the file as:

```text
waste_management.sql
```

Then create:

```text
database/
```

and put the SQL file there:

```text
database/waste_management.sql
```

Your repository would then look like:

```text
smart-waste-collection/
│
├── database/
│   └── waste_management.sql
│
├── index.php
├── login.php
├── signup.php
├── ...
└── README.md
```

Other contributors can then import the file through phpMyAdmin.

---

# 5. Configure the Database Connection

The connection settings are located in:

```text
db_connect_
```
For a normal XAMPP installation, the configuration should look similar to:

<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "waste_management";

$conn = new mysqli(
    $servername,
    $username,
    $password,
    $dbname
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

Do not use real production passwords directly inside a public GitHub repository.

6. Open the Application

If your folder is called:

smart-waste-collection

open:

http://localhost/smart-waste-collection/

The application will load:

index.php

From there users can login or register.

▶️ Alternative: PHP Development Server

If PHP and MySQL are installed without XAMPP, navigate to the project directory and run:

php -S localhost:8000

Then visit:

http://localhost:8000

Your MySQL database must still be running.

🌐 Can GitHub Run This Project?

No.

GitHub Pages cannot execute this project because GitHub Pages only hosts static files such as:

HTML
CSS
JavaScript

This project requires:

PHP
MySQL
Server-side sessions

GitHub should therefore be used for:

Source-code hosting
Version control
Team collaboration
Issue tracking
Pull Requests
Release management

To make the actual website publicly accessible, deploy it to a server that supports PHP and MySQL.

🌍 Deployment Options

The project can be deployed using:

Shared PHP hosting
cPanel hosting
VPS hosting
Apache server
Nginx + PHP-FPM
Docker-based deployment
PHP/MySQL-compatible cloud hosting

Production hosting must support:

PHP
MySQL / MariaDB
MySQLi
Sessions


📚 Learning Outcomes

This project demonstrates concepts including:

PHP Server-Side Development
MySQL Database Design
CRUD Operations
Authentication
Role-Based Authorisation
PHP Sessions
Prepared SQL Statements
Password Hashing
Relational Databases
HTML Forms
CSS Interface Design
Git Version Control
GitHub Collaboration
