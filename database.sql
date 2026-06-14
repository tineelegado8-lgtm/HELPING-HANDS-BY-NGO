CREATE DATABASE IF NOT EXISTS helping_hands_ngo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE helping_hands_ngo;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(80) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(160) NOT NULL,
    role VARCHAR(40) NOT NULL DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE navigation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(120) NOT NULL,
    url VARCHAR(255) NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    visible TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE content_blocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    block_key VARCHAR(120) NOT NULL UNIQUE,
    title VARCHAR(160) NOT NULL,
    body TEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(180) NOT NULL,
    description TEXT NOT NULL,
    goal_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    raised_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    beneficiaries INT NOT NULL DEFAULT 0,
    image_path VARCHAR(255) NULL,
    image_label VARCHAR(160) NOT NULL DEFAULT '[PROGRAM IMAGE HERE]',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(180) NOT NULL,
    email VARCHAR(180) NOT NULL,
    contact_number VARCHAR(60) NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    donation_message TEXT NULL,
    program_id INT NULL,
    payment_method VARCHAR(80) NOT NULL,
    transaction_reference VARCHAR(120) NULL,
    receipt_path VARCHAR(255) NULL,
    tracking_code VARCHAR(40) NULL UNIQUE,
    status VARCHAR(40) NOT NULL DEFAULT 'Pending Verification',
    rejection_reason TEXT NULL,
    email_verified_at TIMESTAMP NULL,
    verified_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE SET NULL
);

CREATE TABLE volunteers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(180) NOT NULL,
    email VARCHAR(180) NOT NULL,
    contact_number VARCHAR(60) NOT NULL,
    address TEXT NOT NULL,
    skills TEXT NULL,
    availability VARCHAR(160) NULL,
    emergency_contact TEXT NULL,
    reason TEXT NULL,
    photo_path VARCHAR(255) NULL,
    status ENUM('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
    rejection_reason TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE approved_volunteers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    volunteer_id INT NOT NULL UNIQUE,
    full_name VARCHAR(180) NOT NULL,
    email VARCHAR(180) NOT NULL,
    contact_number VARCHAR(60) NOT NULL,
    skills TEXT NULL,
    availability VARCHAR(160) NULL,
    approved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (volunteer_id) REFERENCES volunteers(id) ON DELETE CASCADE
);

CREATE TABLE reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(180) NOT NULL,
    report_type VARCHAR(100) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    published TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(180) NOT NULL,
    email VARCHAR(180) NOT NULL,
    subject VARCHAR(180) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(180) NOT NULL,
    slug VARCHAR(180) NOT NULL UNIQUE,
    content LONGTEXT NULL,
    status ENUM('Draft','Published','Unpublished') NOT NULL DEFAULT 'Draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(120) NOT NULL UNIQUE,
    setting_value TEXT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE media_library (
    id INT AUTO_INCREMENT PRIMARY KEY,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(40) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE backups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(180) NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NULL,
    action VARCHAR(255) NOT NULL,
    ip_address VARCHAR(80) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE visitors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(80) NULL,
    user_agent VARCHAR(255) NULL,
    visited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (username, password_hash, full_name, role) VALUES
('admin', '$2y$10$04E.emA04S9gQK9NmzSFQOMfUy7xdYTViAPoklCbPJ8Legq6fy3Wq', 'Default Administrator', 'admin');

INSERT INTO navigation (label, url, sort_order, visible) VALUES
('Home', 'index.php', 1, 1),
('About Us', 'about.php', 2, 1),
('Programs', 'programs.php', 3, 1),
('Donation', 'donation.php', 4, 1),
('Volunteer Sign Up', 'volunteer.php', 5, 1),
('Impact Reports', 'reports.php', 6, 1),
('Contact Us', 'contact.php', 7, 1),
('Admin Panel', 'admin/login.php', 8, 1);

INSERT INTO content_blocks (block_key, title, body) VALUES
('mission', 'Mission Statement', 'Helping Hands NGO supports communities through education, women empowerment, disaster relief, feeding programs, medical assistance, environmental protection, scholarship programs, and community development initiatives.'),
('vision', 'Vision Statement', 'A future where every community has access to hope, safety, opportunity, and transparent support.'),
('footer_text', 'Footer Content', 'Extending Hope, Changing Lives, Building Better Communities.'),
('announcement', 'Latest Announcement', 'New campaigns and impact reports will be posted regularly.');

INSERT INTO programs (name, description, goal_amount, raised_amount, beneficiaries, image_label) VALUES
('Education Support Program', 'School supplies, tutoring, and learning access for underserved children.', 100000, 62000, 180, '[PROGRAM IMAGE HERE]'),
('Women Empowerment Program', 'Livelihood, leadership, and safety programs for women in the community.', 120000, 54000, 95, '[PROGRAM IMAGE HERE]'),
('Feeding Program', 'Nutritious meals for children, seniors, and families facing food insecurity.', 90000, 73500, 420, '[PROGRAM IMAGE HERE]'),
('Disaster Relief Program', 'Rapid support packages, emergency supplies, and recovery assistance.', 150000, 88000, 260, '[PROGRAM IMAGE HERE]'),
('Medical Assistance Program', 'Medicine, consultations, and urgent health support for vulnerable families.', 130000, 71000, 140, '[PROGRAM IMAGE HERE]'),
('Environmental Protection Program', 'Cleanups, tree planting, and local sustainability education.', 80000, 39000, 300, '[PROGRAM IMAGE HERE]'),
('Scholarship Program', 'Tuition, transportation, and mentoring for promising students.', 200000, 119000, 55, '[PROGRAM IMAGE HERE]'),
('Community Development Program', 'Shared facilities, skills training, and neighborhood improvement projects.', 160000, 65000, 210, '[PROGRAM IMAGE HERE]'),
('Senior Citizen Assistance Program', 'Wellness, food packs, and companionship services for older adults.', 85000, 42000, 120, '[PROGRAM IMAGE HERE]'),
('Child Welfare Program', 'Protection, education, and health resources for children at risk.', 110000, 58000, 160, '[PROGRAM IMAGE HERE]');

INSERT INTO reports (title, report_type, file_path, published) VALUES
('Sample Annual Impact Report', 'Impact Reports', 'uploads/reports/[PDF REPORT PLACEHOLDER]', 1);
