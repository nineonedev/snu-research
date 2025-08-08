
-- 팀 테이블
CREATE TABLE IF NOT EXISTS no_teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    is_hidden TINYINT(1) DEFAULT 0,
    image VARCHAR(255) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- 관리자 테이블
CREATE TABLE IF NOT EXISTS no_admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT, 
    role_id INT, 
    name VARCHAR(255) NOT NULL,
    username VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (team_id) REFERENCES no_teams(id) ON DELETE SET NULL,
    UNIQUE (username) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 팀 다국어 테이블
CREATE TABLE IF NOT EXISTS no_team_langs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL,
    locale CHAR(2) NOT NULL,  -- 예: 'ko', 'en'
    name VARCHAR(100) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (team_id) REFERENCES no_teams(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 게시판 테이블
CREATE TABLE IF NOT EXISTS no_boards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT,
    skin VARCHAR(20) NOT NULL,  -- 게시판 렌더링 형태
    image TEXT,
    is_public TINYINT(1) NOT NULL DEFAULT 0,
    extra1 VARCHAR(255),
    extra2 VARCHAR(255),
    extra3 VARCHAR(255),
    extra4 VARCHAR(255),
    extra5 VARCHAR(255),
    extra6 VARCHAR(255),
    extra7 VARCHAR(255),
    extra8 VARCHAR(255),
    extra9 VARCHAR(255),
    extra10 VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (team_id) REFERENCES no_teams(id) ON DELETE SET NULL,
    INDEX (team_id)   
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 게시판 다국어 테이블
CREATE TABLE IF NOT EXISTS no_board_langs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    board_id INT NOT NULL,
    locale CHAR(2) NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (board_id) REFERENCES no_boards(id) ON DELETE CASCADE,
    INDEX (board_id),
    INDEX (locale)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE IF NOT EXISTS no_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    board_id INT NOT NULL,
    is_hidden TINYINT(1) DEFAULT 0,
    is_notice TINYINT(1) DEFAULT 0,
    image VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (board_id) REFERENCES no_boards(id) ON DELETE CASCADE,
    INDEX (board_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS no_post_langs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    locale CHAR(2) NOT NULL,
    title VARCHAR(255),
    views INT DEFAULT 0,
    content TEXT,
    extra1 VARCHAR(255),
    extra2 VARCHAR(255),
    extra3 VARCHAR(255),
    extra4 VARCHAR(255),
    extra5 VARCHAR(255),
    extra6 VARCHAR(255),
    extra7 VARCHAR(255),
    extra8 VARCHAR(255),
    extra9 VARCHAR(255),
    extra10 VARCHAR(255),
    image1 VARCHAR(255),
    image2 VARCHAR(255),
    image3 VARCHAR(255),
    image4 VARCHAR(255),
    image5 VARCHAR(255),
    image6 VARCHAR(255),
    image7 VARCHAR(255),
    image8 VARCHAR(255),
    image9 VARCHAR(255),
    image10 VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES no_posts(id) ON DELETE CASCADE,
    INDEX (post_id),
    INDEX (locale)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




CREATE TABLE IF NOT EXISTS no_banners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(20) NOT NULL,
    image VARCHAR(255),
    is_hidden TINYINT(1) DEFAULT 0,
    display_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS no_banner_langs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    banner_id INT NOT NULL,
    locale CHAR(2) NOT NULL, -- 예: 'ko', 'en'
    title VARCHAR(255),
    description TEXT,
    link VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (banner_id) REFERENCES no_banners(id) ON DELETE CASCADE,
    UNIQUE (banner_id, locale),
    INDEX (locale)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- 사이트 설정 테이블
CREATE TABLE IF NOT EXISTS no_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS no_setting_langs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_id INT NOT NULL,
    locale CHAR(2) NOT NULL,  -- 예: 'ko', 'en'
    tel VARCHAR(50),
    fax VARCHAR(50),
    address VARCHAR(255),
    youtube_link VARCHAR(255),
    site_name VARCHAR(255),
    meta_title VARCHAR(255),
    meta_description TEXT,
    meta_keywords VARCHAR(255),
    meta_image VARCHAR(255),

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (setting_id) REFERENCES no_settings(id) ON DELETE CASCADE,
    UNIQUE (setting_id, locale),
    INDEX (locale)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
