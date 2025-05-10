-- ===== USERS TABLE =====
/*CREATE TABLE users (
    id INT IDENTITY(1,1) PRIMARY KEY,
    fname NVARCHAR(50),
    mname NVARCHAR(50),
    lname NVARCHAR(50),
    email NVARCHAR(50) NOT NULL,
    number NVARCHAR(11) NOT NULL,
    password NVARCHAR(255) NOT NULL,
    address NVARCHAR(500) NOT NULL
);*/

-- ===== USERS TABLE =====
CREATE TABLE users (
    id INT IDENTITY(1,1) PRIMARY KEY,
    fname NVARCHAR(50),
    mname NVARCHAR(50),
    lname NVARCHAR(50),
    email NVARCHAR(50) UNIQUE NOT NULL,
    number NVARCHAR(11) UNIQUE NOT NULL,
    password NVARCHAR(255) NOT NULL,
    address NVARCHAR(500) NOT NULL
);


-- ===== ADMIN TABLE =====
CREATE TABLE admin (
    id INT IDENTITY(1,1) PRIMARY KEY,
    name NVARCHAR(20) NOT NULL,
    password NVARCHAR(50) NOT NULL
);

-- Default admin accounts (plain text password: Diba@123)
INSERT INTO admin (name, password) VALUES
('HeadChef', 'Diba@123'),
('Management', 'Diba@123');

-- ===== CANCELLED ORDERS =====
CREATE TABLE cancelled_orders (
    id INT IDENTITY(1,1) PRIMARY KEY,
    user_id INT,
    order_id INT,
    cancelled_on DATETIME
);

-- ===== CARDS =====
CREATE TABLE cards (
    id INT IDENTITY(1,1) PRIMARY KEY,
    user_id INT NOT NULL,
    card_holder_name NVARCHAR(255) NOT NULL,
    card_number NVARCHAR(20) NOT NULL,
    expiry_date NVARCHAR(7) NOT NULL,
    cvv NVARCHAR(3) NOT NULL,
    created_at DATETIME DEFAULT GETDATE(),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ===== CART =====
CREATE TABLE cart (
    id INT IDENTITY(1,1) PRIMARY KEY,
    user_id INT NOT NULL,
    pid INT NOT NULL,
    name NVARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    image NVARCHAR(255) NOT NULL
);

-- ===== COMPLETED ORDERS =====
CREATE TABLE completed_orders (
    id INT IDENTITY(1,1) PRIMARY KEY,
    user_id INT,
    placed_on DATETIME,
    fname NVARCHAR(255),
    mname NVARCHAR(255),
    lname NVARCHAR(255),
    address NVARCHAR(MAX),
    total_products NVARCHAR(100),
    total_price DECIMAL(10,2),
    method NVARCHAR(50),
    order_status NVARCHAR(50),
    completed_timestamp DATETIME
);

-- ===== MESSAGES =====
CREATE TABLE messages (
    id INT IDENTITY(1,1) PRIMARY KEY,
    user_id INT NOT NULL,
    fname NVARCHAR(50),
    mname NVARCHAR(50),
    lname NVARCHAR(50),
    email NVARCHAR(100) NOT NULL,
    number NVARCHAR(12) NOT NULL,
    message NVARCHAR(500) NOT NULL,
    rating INT
);

-- ===== ORDERS =====
CREATE TABLE orders (
    id INT IDENTITY(1,1) PRIMARY KEY,
    user_id INT NOT NULL,
    fname NVARCHAR(255),
    mname NVARCHAR(255),
    lname NVARCHAR(255),
    number NVARCHAR(15) NOT NULL,
    email NVARCHAR(50) NOT NULL,
    method NVARCHAR(50) NOT NULL,
    address NVARCHAR(500) NOT NULL,
    total_products NVARCHAR(1000) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    placed_on DATETIME DEFAULT GETDATE(),
    order_status NVARCHAR(20) DEFAULT 'Preparing your Food',
    completed_timestamp DATETIME
);

-- ===== ORDER HISTORY =====
CREATE TABLE order_history (
    id INT IDENTITY(1,1) PRIMARY KEY,
    user_id INT,
    placed_on DATETIME DEFAULT GETDATE(),
    fname NVARCHAR(255),
    mname NVARCHAR(255),
    lname NVARCHAR(255),
    address NVARCHAR(MAX),
    total_products NVARCHAR(100),
    total_price DECIMAL(10,2),
    method NVARCHAR(50),
    order_status NVARCHAR(50)
);

-- ===== PRODUCTS =====
CREATE TABLE products (
    id INT IDENTITY(1,1) PRIMARY KEY,
    name NVARCHAR(100) NOT NULL,
    category NVARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image NVARCHAR(255) NOT NULL
);
