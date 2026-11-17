<?php
// setup.php - CREATE database, tables & INSERT data guna kod PHP + SQL
// Cara guna: buka http://localhost/stockroom-php/setup.php
// RESTRICTION: kalau database dah ada user, hanya ADMIN yang boleh run page ni.
session_start();
mysqli_report(MYSQLI_REPORT_OFF);

$conn = mysqli_connect("localhost", "root", "");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// semak sama ada sistem dah di-setup (ada user dalam database)
$check = mysqli_query($conn, "SELECT COUNT(*) AS n FROM stockroom_db.users");
if ($check) {
    $row = mysqli_fetch_assoc($check);
    if ($row['n'] > 0) {
        if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
            die("<h2>Access denied</h2><p>Only Admin can access setup.php. Please <a href='login.php'>login as admin</a> first.</p>");
        }
    }
}

function run($conn, $sql, $label) {
    if (mysqli_query($conn, $sql)) {
        echo "<p>&#10004; $label</p>";
    } else {
        echo "<p>&#10008; $label - " . mysqli_error($conn) . "</p>";
    }
}

echo "<h2>Stockroom Inventory System - Database Setup</h2>";

// 1) CREATE DATABASE
run($conn, "CREATE DATABASE IF NOT EXISTS stockroom_db", "Database stockroom_db created");
mysqli_select_db($conn, "stockroom_db");

// 2) CREATE TABLES
run($conn, "CREATE TABLE IF NOT EXISTS users (
    user_id   INT AUTO_INCREMENT PRIMARY KEY,
    username  VARCHAR(30) NOT NULL UNIQUE,
    password  VARCHAR(255) NOT NULL,
    full_name VARCHAR(80) NOT NULL,
    role      ENUM('admin','staff') NOT NULL DEFAULT 'staff'
) ENGINE=InnoDB", "Table users created");

run($conn, "CREATE TABLE IF NOT EXISTS categories (
    category_id   INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB", "Table categories created");

run($conn, "CREATE TABLE IF NOT EXISTS suppliers (
    supplier_id   INT AUTO_INCREMENT PRIMARY KEY,
    supplier_name VARCHAR(80) NOT NULL,
    email         VARCHAR(80),
    phone         VARCHAR(20)
) ENGINE=InnoDB", "Table suppliers created");

run($conn, "CREATE TABLE IF NOT EXISTS items (
    item_id     INT AUTO_INCREMENT PRIMARY KEY,
    sku         VARCHAR(15) NOT NULL UNIQUE,
    item_name   VARCHAR(80) NOT NULL,
    description TEXT,
    category_id INT NOT NULL,
    supplier_id INT NOT NULL,
    quantity    INT NOT NULL DEFAULT 0,
    unit_price  DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status      ENUM('In Stock','Low Stock','Out of Stock') NOT NULL DEFAULT 'In Stock',
    fragile     ENUM('Yes','No') NOT NULL DEFAULT 'No',
    date_added  DATE NOT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(category_id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id)
) ENGINE=InnoDB", "Table items created");

run($conn, "CREATE TABLE IF NOT EXISTS activity_log (
    log_id      INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NULL,
    username    VARCHAR(30) NOT NULL,
    action      VARCHAR(30) NOT NULL,
    description VARCHAR(255) NOT NULL,
    log_time    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB", "Table activity_log created");

// 3) INSERT DATA (hanya kalau table masih kosong) - minimum 4 rows
function is_empty($conn, $table) {
    $r = mysqli_query($conn, "SELECT COUNT(*) AS n FROM $table");
    $row = mysqli_fetch_assoc($r);
    return $row['n'] == 0;
}

if (is_empty($conn, "users")) {
    $users = [
        ["admin",  "admin123",  "Administrator",         "admin"],
        ["hazim",  "hazim123",  "Mohamad Hazim Aizat",   "staff"],
        ["syamil", "syamil123", "Syamil Hairis",         "staff"],
        ["danish", "danish123", "Muhammad Danish Hazim", "staff"],
    ];
    foreach ($users as $u) {
        $hash = password_hash($u[1], PASSWORD_DEFAULT);
        run($conn, "INSERT INTO users (username,password,full_name,role)
                    VALUES ('$u[0]','$hash','$u[2]','$u[3]')", "Insert user $u[0] ($u[3])");
    }
}

if (is_empty($conn, "categories")) {
    run($conn, "INSERT INTO categories (category_name) VALUES
        ('Electronics'),('Packaging'),('Safety Gear'),('Equipment'),('Office Supplies')",
        "Insert 5 categories");
}

if (is_empty($conn, "suppliers")) {
    run($conn, "INSERT INTO suppliers (supplier_name,email,phone) VALUES
        ('TechParts Sdn Bhd','sales@techparts.com.my','03-5521 1100'),
        ('PackPro Enterprise','order@packpro.com.my','03-7845 2200'),
        ('SafeWorks Supplies','info@safeworks.com.my','03-6150 3300'),
        ('OfficeMart Trading','hello@officemart.com.my','03-8940 4400')",
        "Insert 4 suppliers");
}

if (is_empty($conn, "items")) {
    run($conn, "INSERT INTO items
        (sku,item_name,description,category_id,supplier_id,quantity,unit_price,status,fragile,date_added) VALUES
        ('STK-1001','Wireless Barcode Scanner','Bluetooth wireless barcode scanner used for stock check-in and check-out.',1,1,24,189.00,'In Stock','Yes','2026-01-12'),
        ('STK-1002','Corrugated Shipping Box (M)','Medium size box for packing and shipping.',2,2,3,1.20,'Low Stock','No','2026-01-15'),
        ('STK-1003','Forklift Safety Vest','High-visibility vest for warehouse staff.',3,3,0,24.50,'Out of Stock','No','2026-01-20'),
        ('STK-1004','Hand Pallet Truck','Manual pallet jack, 2500 kg capacity.',4,1,6,899.00,'In Stock','No','2026-02-03'),
        ('STK-1005','A4 Paper (Box of 5 Reams)','White 80gsm A4 copy paper.',5,4,40,78.90,'In Stock','No','2026-02-10')",
        "Insert 5 items");
}

if (is_empty($conn, "activity_log")) {
    run($conn, "INSERT INTO activity_log (user_id, username, action, description) VALUES
        ((SELECT user_id FROM users WHERE username='admin'), 'admin', 'Setup', 'Database stockroom_db created'),
        ((SELECT user_id FROM users WHERE username='admin'), 'admin', 'Setup', 'Tables created: users, categories, suppliers, items, activity_log'),
        ((SELECT user_id FROM users WHERE username='admin'), 'admin', 'Setup', 'Sample data inserted'),
        ((SELECT user_id FROM users WHERE username='admin'), 'admin', 'Add User', 'Created 4 sample accounts (1 admin, 3 staff)')",
        "Insert 4 activity log records");
}

echo "<hr><p><b>Setup siap!</b> <a href='login.php'>Pergi ke Login</a></p>";
mysqli_close($conn);
?>
