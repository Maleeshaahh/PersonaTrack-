CREATE DATABASE IF NOT EXISTS personatrack CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE personatrack;

CREATE TABLE IF NOT EXISTS users (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    username     VARCHAR(100)  NOT NULL,
    email        VARCHAR(150)  NOT NULL UNIQUE,
    password     VARCHAR(255)  NOT NULL,           
    university   VARCHAR(200)  DEFAULT NULL,
    created_at   TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS todos (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT           NOT NULL,
    title       VARCHAR(255)  NOT NULL,
    category    VARCHAR(50)   DEFAULT 'Other',     
    priority    VARCHAR(20)   DEFAULT 'Low',        
    due_date    DATE          DEFAULT NULL,
    due_time    TIME          DEFAULT NULL,
    notes       TEXT          DEFAULT NULL,
    is_done     TINYINT(1)    DEFAULT 0,            
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS expenses (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT           NOT NULL,
    title       VARCHAR(255)  NOT NULL,
    amount      DECIMAL(10,2) NOT NULL,
    type        VARCHAR(20)   DEFAULT 'expense',   
    category    VARCHAR(50)   DEFAULT 'Other',     
    exp_date    DATE          DEFAULT NULL,
    notes       TEXT          DEFAULT NULL,
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS goals (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT           NOT NULL,
    title       VARCHAR(255)  NOT NULL,
    description TEXT          DEFAULT NULL,
    category    VARCHAR(50)   DEFAULT 'Other',
    target_date DATE          DEFAULT NULL,
    progress    INT           DEFAULT 0,           
    is_achieved TINYINT(1)    DEFAULT 0,
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS notes (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT           NOT NULL,
    title       VARCHAR(255)  NOT NULL,
    body        TEXT          NOT NULL,
    category    VARCHAR(50)   DEFAULT 'Other',     
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS reminders (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT           NOT NULL,
    title       VARCHAR(255)  NOT NULL,
    rem_type    VARCHAR(50)   DEFAULT 'Other',     
    priority    VARCHAR(20)   DEFAULT 'Normal',    
    rem_date    DATE          DEFAULT NULL,
    rem_time    TIME          DEFAULT NULL,
    notes       TEXT          DEFAULT NULL,
    is_done     TINYINT(1)    DEFAULT 0,
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS messages (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)  NOT NULL,
    email       VARCHAR(150)  NOT NULL,
    subject     VARCHAR(255)  DEFAULT NULL,
    message     TEXT          NOT NULL,
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS profiles (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT           NOT NULL UNIQUE,
    faculty     VARCHAR(150)  DEFAULT NULL,
    academic_yr VARCHAR(20)   DEFAULT NULL,
    phone       VARCHAR(20)   DEFAULT NULL,
    dob         DATE          DEFAULT NULL,
    updated_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
