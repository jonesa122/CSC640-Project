-- Create animals table
CREATE TABLE animals (
  id INT NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) DEFAULT NULL,
  species VARCHAR(50) DEFAULT NULL,
  breed VARCHAR(100) DEFAULT NULL,
  age INT DEFAULT NULL,
  gender ENUM('Male','Female') NOT NULL,
  arrival_date DATE DEFAULT NULL,
  status ENUM('Available','Adopted','Fostered','Transferred') DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Seed animals
INSERT INTO animals (name, species, breed, age, gender, arrival_date, status) VALUES
('Bella', 'Dog', 'Labrador Retriever', 3, 'Female', '2025-10-01', 'Available'),
('Max', 'Cat', 'Siamese', 2, 'Male', '2025-09-15', 'Fostered'),
('Charlie', 'Rabbit', 'Dutch', 1, 'Male', '2025-10-20', 'Available');

-- Create adoptions table
CREATE TABLE adoptions (
  id INT NOT NULL AUTO_INCREMENT,
  animal_id INT DEFAULT NULL,
  adoption_date DATE DEFAULT NULL,
  adopter_name VARCHAR(100) DEFAULT NULL,
  adopter_phone VARCHAR(20) DEFAULT NULL,
  adopter_email VARCHAR(100) DEFAULT NULL,
  adopter_address TEXT,
  status VARCHAR(50) DEFAULT 'pending',
  PRIMARY KEY (id),
  KEY animal_id (animal_id),
  CONSTRAINT adoptions_ibfk_1 FOREIGN KEY (animal_id) REFERENCES animals (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Seed adoptions
INSERT INTO adoptions (animal_id, adoption_date, adopter_name, adopter_phone, adopter_email, adopter_address, status) VALUES
(1, '2025-11-01', 'Abby Johnson', '555-1234', 'abby.j@example.com', '123 Maple St, Fort Mitchell, KY', 'pending'),
(2, '2025-11-03', 'Liam Smith', '555-5678', 'liam.smith@example.com', '456 Oak Ave, Fort Mitchell, KY', 'approved'),
(3, '2025-11-05', 'Emma Davis', '555-9012', 'emma.d@example.com', '789 Pine Rd, Fort Mitchell, KY', 'pending');

-- Create users table
CREATE TABLE users (
  id INT NOT NULL AUTO_INCREMENT,
  username VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY username (username),
  UNIQUE KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Seed users
INSERT INTO users (username, email, password_hash) VALUES
('admin', 'admin@example.com', 'admin123'),
('volunteer1', 'vol1@example.com', 'volpass1'),
('staff', 'staff@example.com', 'staffpass');
