-- ============================================
-- HOTEL BOOKING SYSTEM DATABASE (COMPLETE SCHEMA)
-- ============================================

CREATE DATABASE IF NOT EXISTS hotel_booking
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE hotel_booking;


-- ============================================
-- USERS TABLE
-- ============================================

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================
-- ADMIN USERS TABLE
-- ============================================

CREATE TABLE IF NOT EXISTS admin_users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================
-- ROOMS TABLE
-- ============================================

CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_no VARCHAR(20) NOT NULL UNIQUE,
    room_type VARCHAR(100) NOT NULL,
    description TEXT,
    capacity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    status ENUM('Available', 'Unavailable', 'Booked', 'Maintenance') DEFAULT 'Available',
    image VARCHAR(255),
    destination VARCHAR(100) DEFAULT 'Ahmedabad',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================
-- BOOKINGS TABLE
-- ============================================

CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    room_id INT NOT NULL,
    customer_name VARCHAR(100) DEFAULT NULL,
    customer_email VARCHAR(150) DEFAULT NULL,
    customer_phone VARCHAR(20) DEFAULT NULL,
    destination VARCHAR(100) DEFAULT NULL,
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    guests INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('Pending', 'Confirmed', 'Cancelled', 'Completed') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE SET NULL,
    FOREIGN KEY (room_id)
        REFERENCES rooms(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================
-- DEFAULT ADMIN ACCOUNT (admin@example.com / Rushi123)
-- ============================================

INSERT IGNORE INTO admin_users (name, email, password)
VALUES (
    'Hotel Administrator',
    'admin@example.com',
    '$2y$10$XKSsbFnIwFcSgf3sgedn1umroxmK9IxrKhi57m55tnh3Mju.v7A5K'
);


-- ============================================
-- SAMPLE ROOMS
-- ============================================

INSERT IGNORE INTO rooms
(room_no, room_type, description, capacity, price, status, image, destination)
VALUES
('101', 'Single Room', 'Comfortable single room suitable for one guest.', 1, 1500.00, 'Available', 'single-room.jpg', 'Ahmedabad'),
('102', 'Double Room', 'Spacious double room with comfortable beds.', 2, 2500.00, 'Available', 'double-room.jpg', 'Mumbai'),
('103', 'Deluxe Room', 'Premium deluxe room with modern facilities.', 3, 3500.00, 'Available', 'deluxe-room.jpg', 'Goa'),
('104', 'Family Room', 'Large family room suitable for families.', 4, 4500.00, 'Available', 'family-room.jpg', 'Pune'),
('105', 'Executive Room', 'Luxury executive room for business and leisure guests.', 2, 5000.00, 'Available', 'executive-room.jpg', 'Delhi'),
('106', 'Suite Room', 'Luxury suite room with spacious living area.', 4, 7000.00, 'Available', 'suite-room.jpg', 'Jaipur');
