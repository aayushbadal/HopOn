CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT NOT NULL,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS routes(
    id INT AUTO_INCREMENT PRIMARY KEY,
    startin VARCHAR(255) NOT NULL,
    destination VARCHAR(255) NOT NULL,
    duration VARCHAR(50),
    price DECIMAL (10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS vehicle_lists(
    id INT AUTO_INCREMENT PRIMARY KEY,
    route_id INT,
    starttime TEXT NOT NULL,
    endtime TEXT NOT NULL,
    vehicle_number TEXT,
    facilities TEXT,
    driver_name VARCHAR(255) NOT NULL,
    driver_phone_number TEXT NOT NULL,
    total_seats INT NOT NULL DEFAULT 33,
    available_seats INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (route_id) REFERENCES routes(id) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS route_date(
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT,
    routing_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES vehicle_lists(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS bookings(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id   INT NOT NULL,
    route_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    route_date_id INT NOT NULL,
    booking_reference VARCHAR(20) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    booking_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    payment_status VARCHAR(20) DEFAULT 'PENDING',
    payment_ref VARCHAR(100),
    payment_method VARCHAR(20),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (route_id) REFERENCES routes(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicle_id) REFERENCES vehicle_lists(id) ON DELETE CASCADE,
    FOREIGN KEY (route_date_id) REFERENCES route_date(id) ON DELETE CASCADE

);

CREATE TABLE IF   NOT EXISTS booking_seats
(
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    seat_number VARCHAR(10) NOT NULL,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS op_users (
    id INT AUTO_INCREMENT NOT NULL,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);




CREATE TABLE seat_locks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT NOT NULL,
    route_date_id INT NOT NULL,
    seat_number INT NOT NULL,
    session_id VARCHAR(128) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY unique_seat (vehicle_id, route_date_id, seat_number)
);
