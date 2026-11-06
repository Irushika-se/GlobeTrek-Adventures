

 
CREATE DATABASE IF NOT EXISTS globetrek;
USE globetrek;
 
-- Users table (admin, staff, customer)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('admin', 'staff', 'customer') NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
 
-- Travel packages table
CREATE TABLE packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    destination VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    duration_days INT NOT NULL,
    max_persons INT DEFAULT 10,
    image_name VARCHAR(200),
    inclusions TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
 
-- Bookings table
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    package_id INT NOT NULL,
    travel_date DATE NOT NULL,
    num_persons INT NOT NULL DEFAULT 1,
    total_price DECIMAL(10,2) NOT NULL,
    special_requests TEXT,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    booked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id),
    FOREIGN KEY (package_id) REFERENCES packages(id)
);
 
-- Payments table
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('credit_card', 'debit_card', 'bank_transfer') NOT NULL,
    payment_status ENUM('paid', 'pending', 'failed') DEFAULT 'pending',
    paid_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
);
 
-- Inquiries / Contact messages
CREATE TABLE inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    reply TEXT,
    status ENUM('open', 'replied') DEFAULT 'open',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
 


INSERT INTO users (full_name, email, password, phone, role) VALUES
('Admin User', 'admin@globetrek.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0771234567', 'admin'),
('Sarah Perera', 'staff@globetrek.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0772345678', 'staff'),
('John Silva', 'john@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0773456789', 'customer');
 

INSERT INTO packages (title, destination, description, price, duration_days, max_persons, image_name, inclusions, status) VALUES
('Sigiriya Heritage Tour',
'Sigiriya, Sri Lanka',
'Discover Sri Lanka''s cultural heritage with a visit to the iconic Sigiriya Rock Fortress. Explore ancient ruins, traditional village life, and breathtaking landscapes while learning about the island''s rich history and traditions.',
25000.00, 2, 15, 'sigiriya.jpg',
'Hotel accommodation (2 nights), Daily breakfast, Sigiriya Rock Fortress entrance fee, Village tour experience, Transportation by air-conditioned vehicle, Professional guide service, Mineral water during tours',
'active'),

('Ella Adventure Tour',
'Ella, Sri Lanka',
'Experience the beauty of Sri Lanka''s hill country with scenic train journeys, mountain views, and famous attractions including Ella Rock, Nine Arches Bridge, and Ravana Falls.',
35000.00, 3, 20, 'ella.jpg',
'Hotel accommodation (2 nights), Daily breakfast, Ella Rock hiking experience, Nine Arches Bridge visit, Ravana Falls sightseeing, Transportation during excursions, Local guide assistance',
'active'),

('Mirissa Beach Escape',
'Mirissa, Sri Lanka',
'Relax on the golden beaches of Mirissa and enjoy a peaceful coastal getaway. Experience whale watching, stunning sunsets, and the vibrant atmosphere of Sri Lanka''s southern coast.',
28000.00, 2, 15, 'mirissa.jpg',
'Beach hotel accommodation (2 nights), Daily breakfast, Whale watching tour, Transportation from Colombo, Welcome drink on arrival, Local guide assistance, Beach leisure activities',
'active'),

('Kandy Cultural Experience',
'Kandy, Sri Lanka',
'Discover the cultural heart of Sri Lanka. Visit the iconic Temple of the Tooth Relic, Royal Botanical Gardens in Peradeniya, and enjoy a traditional Kandyan dance performance.',
22000.00, 2, 20, 'kandy.jpg',
'Hotel accommodation (2 nights), Daily breakfast, Return transport from Colombo, Temple of the Tooth entrance, Peradeniya Botanical Gardens, Traditional Kandyan dance show, Gem museum visit',
'active'),

('Yala Wildlife Safari',
'Yala, Sri Lanka',
'Thrilling jeep safari through Yala National Park — one of the best places in the world to spot leopards. Also includes Bundala Bird Sanctuary and a scenic coastal drive.',
32000.00, 2, 12, 'yala.jpg',
'Safari lodge accommodation (2 nights), All meals, Jeep safari (2 rounds), Bundala Bird Sanctuary visit, Coastal scenic drive, Expert wildlife guide',
'active'),

('Nuwara Eliya Hill Country Tour',
'Nuwara Eliya, Sri Lanka',
'Experience the cool misty hills of "Little England". Visit sprawling tea estates, Victoria Park, Gregory Lake and enjoy a scenic train journey through the mountains.',
30000.00, 3, 15, 'nuwaraeliya.jpg',
'Hotel accommodation (3 nights), Breakfast & Dinner, Scenic train journey (Kandy to Nuwara Eliya), Tea estate factory tour and tea tasting, Victoria Park, Gregory Lake boating, Strawberry farm visit',
'active'),

('Galle Fort & Southern Coast',
'Galle, Sri Lanka',
'Explore the historic Dutch Fort in Galle, relax on pristine southern beaches, visit sea turtle hatcheries, and enjoy fresh seafood by the ocean.',
26000.00, 2, 15, 'galle.jpg',
'Boutique hotel (2 nights), Breakfast, Return transport, Galle Fort guided walk, Sea turtle hatchery visit, Unawatuna and Jungle Beach free time, Seafood dinner',
'active'),

('Anuradhapura Ancient City Tour',
'Anuradhapura, Sri Lanka',
'Step back 2500 years at the ancient capital of Sri Lanka. Explore sacred ruins, massive dagobas, ancient ponds and the world-famous Sri Maha Bodhi sacred fig tree.',
20000.00, 2, 20, 'anuradhapura.jpg',
'Hotel accommodation (2 nights), Breakfast, Return transport, Anuradhapura heritage site entrance, Guided tour of major ruins, Sri Maha Bodhi visit, Mihintale half-day trip',
'active'),

('Dubai City Experience',
'Dubai, UAE',
'Explore the modern wonders of Dubai with visits to world-famous landmarks, luxury shopping destinations, and exciting desert adventures. Perfect for travelers seeking both culture and entertainment.',
750.00, 4, 20, 'dubai.jpg',
'Hotel accommodation (4 nights), Daily breakfast, Dubai city tour, Desert safari with BBQ dinner, Airport transfers, Transportation by air-conditioned vehicle, Professional tour guide',
'active'),

('Maldives Island Getaway',
'Maldives',
'Escape to the tropical paradise of the Maldives and enjoy crystal-clear waters, white sandy beaches, and unforgettable island experiences. Ideal for relaxation and adventure lovers alike.',
1200.00, 5, 10, 'maldives.jpg',
'Beach resort accommodation (3 nights), Daily breakfast and dinner, Speedboat airport transfers, Snorkeling experience, Island exploration tour, Free Wi-Fi, Welcome drink',
'active'),

('Singapore Discovery Tour',
'Singapore',
'Discover Singapore''s unique blend of modern attractions, cultural landmarks, and world-class entertainment. Visit iconic locations while enjoying the city''s vibrant atmosphere.',
900.00, 4, 20, 'singapore.jpg',
'Hotel accommodation (3 nights), Daily breakfast, Singapore city tour, Gardens by the Bay visit, Marina Bay Sands photo stop, Airport transfers, Professional tour guide',
'active'),

('Bangkok Leisure Tour',
'Bangkok, Thailand',
'Explore the vibrant capital of Thailand. Visit glittering temples, cruise the Chao Phraya river, enjoy world-famous street food, and shop at bustling markets.',
85000.00, 5, 20, 'bangkok.jpg',
'Hotel (5 nights), Return flights (Colombo-Bangkok), Grand Palace & Wat Pho entrance, Chao Phraya dinner cruise, Chatuchak weekend market, Floating market day trip, Airport transfers',
'active'),

('Bali Bliss Retreat',
'Bali, Indonesia',
'A perfect blend of culture, nature and relaxation in the Island of the Gods. Visit ancient temples, terraced rice fields, experience Balinese spa treatments and stunning sunsets.',
110000.00, 6, 15, 'bali.jpg',
'Villa accommodation (6 nights), Daily breakfast, Return flights (Colombo-Bali), Tanah Lot Temple, Ubud rice terraces and monkey forest, Traditional Balinese spa session, Kecak fire dance show, Uluwatu Temple sunset tour',
'active'),

('Kuala Lumpur City Break',
'Kuala Lumpur, Malaysia',
'Experience the dynamic modern city of KL. Marvel at the iconic Petronas Twin Towers, explore Batu Caves, enjoy diverse Asian cuisine and shop at world-class malls.',
75000.00, 4, 20, 'kualalumpur.jpg',
'Hotel (4 nights), Return flights (Colombo-KL), Petronas Twin Towers sky bridge ticket, Batu Caves, KL Tower observation deck, Bukit Bintang shopping district, Airport transfers, City tour',
'active');
 
-- Sample bookings
INSERT INTO bookings (customer_id, package_id, travel_date, num_persons, total_price, status) VALUES
(3, 1, '2025-08-15', 2, 170000.00, 'confirmed'),
(3, 9, '2025-09-20', 1, 70000.00, 'pending');
 
-- Sample inquiries
INSERT INTO inquiries (name, email, subject, message) VALUES
('Kasun Fernando', 'kasun@gmail.com', 'Question about Bali package', 'Is the Bali package available in December? Do you offer discounts for groups?'),
('Nimali Jayawardena', 'nimali@gmail.com', 'Maldives booking help', 'I want to book the Maldives package for our honeymoon. What extras are available?');

