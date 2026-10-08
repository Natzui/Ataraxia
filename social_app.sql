-- =====================================================================
--  social_app.sql  -  Mini Social Networking Web Application
--  Saint Michael College of Caraga - Web Systems and Technologies (Final Output)
--
--  Contains: database creation, CREATE TABLE statements and sample data.
--  Import with phpMyAdmin (Import tab) or:   mysql -u root < sql/social_app.sql
--
--  Sample accounts (all use the password:  password123 )
--     juan_dela_cruz | maria_santos | carlo_reyes | ana_lopez | demo_user
-- =====================================================================

CREATE DATABASE IF NOT EXISTS social_app
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE social_app;

-- ---------------------------------------------------------------------
--  Drop old tables (children first) so the script can be re-run safely
-- ---------------------------------------------------------------------
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS likes;
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
--  Table: users
-- ---------------------------------------------------------------------
CREATE TABLE users (
    id            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    username      VARCHAR(30)   NOT NULL,
    password      VARCHAR(255)  NOT NULL,                    -- password_hash() output (bcrypt)
    full_name     VARCHAR(100)  NOT NULL,
    bio           VARCHAR(255)  DEFAULT NULL,
    profile_image VARCHAR(255)  DEFAULT NULL,                -- file name inside public/uploads/avatars/
    created_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  Table: posts   (users 1 --- N posts)
-- ---------------------------------------------------------------------
CREATE TABLE posts (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id    INT UNSIGNED NOT NULL,
    content    TEXT         NOT NULL,
    image      VARCHAR(255) DEFAULT NULL,                    -- file name inside public/uploads/posts/
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_posts_user (user_id),
    KEY idx_posts_created (created_at),
    CONSTRAINT fk_posts_user FOREIGN KEY (user_id)
        REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  Table: comments   (posts 1 --- N comments, users 1 --- N comments)
-- ---------------------------------------------------------------------
CREATE TABLE comments (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    post_id    INT UNSIGNED NOT NULL,
    user_id    INT UNSIGNED NOT NULL,
    content    TEXT         NOT NULL,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_comments_post (post_id, created_at),
    KEY idx_comments_user (user_id),
    CONSTRAINT fk_comments_post FOREIGN KEY (post_id)
        REFERENCES posts (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_comments_user FOREIGN KEY (user_id)
        REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  Table: likes   (one like per user per post -> UNIQUE (post_id, user_id))
-- ---------------------------------------------------------------------
CREATE TABLE likes (
    id      INT UNSIGNED NOT NULL AUTO_INCREMENT,
    post_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_likes_post_user (post_id, user_id),
    KEY idx_likes_user (user_id),
    CONSTRAINT fk_likes_post FOREIGN KEY (post_id)
        REFERENCES posts (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_likes_user FOREIGN KEY (user_id)
        REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
--  SAMPLE DATA
--  (timestamps are relative to NOW() so the feed always looks fresh)
-- =====================================================================

-- Passwords: every sample user has the password  password123  (bcrypt hashes)
INSERT INTO users (id, username, password, full_name, bio, profile_image, created_at) VALUES
(1, 'juan_dela_cruz', '$2y$12$ytiuShes0sjzfrWAE6teae2DbtipAkUW4/5mfcw1CB9EdyhF3tkIe', 'Juan Dela Cruz', 'BSIT student at SMCC Nasipit. Coffee, code and basketball.', NULL, NOW() - INTERVAL 30 DAY),
(2, 'maria_santos',   '$2y$12$bSQwX5o5XzDxTaHKK59ApOOHIdM25KlH8x15WOQb3QpC28Xkxeel6', 'Maria Santos',   'Aspiring UI/UX designer. I turn caffeine into wireframes.',      NULL, NOW() - INTERVAL 28 DAY),
(3, 'carlo_reyes',    '$2y$12$Cs9pVidlX08WIvUuJEIBeO8jGDNPfEper/l8Ey9eFbBmakorQsZKS', 'Carlo Reyes',    'Backend enthusiast. PHP + MySQL all day.',                       NULL, NOW() - INTERVAL 21 DAY),
(4, 'ana_lopez',      '$2y$12$Qf1aQ9DVRcG9xbwKtaLeaO3169ClSpapbqUzjyPVrbV5gjEbBoXv.', 'Ana Lopez',      'Nasipit, Agusan del Norte. Love sunsets by the bay.',            NULL, NOW() - INTERVAL 14 DAY),
(5, 'demo_user',      '$2y$12$aV6SoAn5f0bkUROQIGAIy.ikf8hhUdguw97VaH80hRPMXdh23V9Pe', 'Demo User',      NULL,                                                             NULL, NOW() - INTERVAL 7 DAY);

INSERT INTO posts (id, user_id, content, image, created_at) VALUES
(1,  1, 'Hello everyone! This is my first post on Mini Social. Excited to build this app for our Web Systems final project!', NULL, NOW() - INTERVAL 6 DAY),
(2,  2, 'Spent the whole afternoon polishing the login page. A clean form with good spacing makes such a difference.', NULL, NOW() - INTERVAL 5 DAY),
(3,  3, 'Reminder: always use prepared statements. Never concatenate user input into your SQL queries!', NULL, NOW() - INTERVAL 4 DAY),
(4,  4, 'The sunset at Nasipit port today was unreal. Wish I could share a photo of it right now.', NULL, NOW() - INTERVAL 3 DAY),
(5,  1, 'MVC finally clicked for me: models talk to the database, controllers make the decisions, views just show the result.', NULL, NOW() - INTERVAL 2 DAY),
(6,  5, 'Just testing the app with the demo account. Feel free to like and comment on my posts!', NULL, NOW() - INTERVAL 30 HOUR),
(7,  2, 'Tip of the day: escape every output with htmlspecialchars() to stop XSS before it starts.', NULL, NOW() - INTERVAL 20 HOUR),
(8,  3, 'Anyone up for a study group this weekend? We can review sessions, authentication and password hashing.', NULL, NOW() - INTERVAL 9 HOUR),
(9,  4, 'Good morning, SMCC! Coffee first, debugging later.', NULL, NOW() - INTERVAL 3 HOUR),
(10, 1, 'Just finished adding likes and comments to my project. Time for some well-deserved sleep!', NULL, NOW() - INTERVAL 40 MINUTE);

INSERT INTO comments (id, post_id, user_id, content, created_at) VALUES
(1,  1, 2, 'Welcome, Juan! Looking forward to seeing your project.',                 NOW() - INTERVAL 6 DAY + INTERVAL 1 HOUR),
(2,  1, 3, 'Good luck with the final output!',                                       NOW() - INTERVAL 5 DAY - INTERVAL 20 HOUR),
(3,  3, 1, 'Noted! I already switched everything to PDO prepared statements.',       NOW() - INTERVAL 4 DAY + INTERVAL 2 HOUR),
(4,  3, 2, 'Also hash passwords with password_hash(), never plain text.',            NOW() - INTERVAL 3 DAY - INTERVAL 20 HOUR),
(5,  4, 1, 'Nasipit sunsets are the best.',                                          NOW() - INTERVAL 2 DAY - INTERVAL 20 HOUR),
(6,  5, 4, 'Great way to put it. Mind if I borrow that explanation?',                NOW() - INTERVAL 1 DAY - INTERVAL 22 HOUR),
(7,  5, 3, 'Exactly. Keep SQL out of the views!',                                    NOW() - INTERVAL 1 DAY - INTERVAL 18 HOUR),
(8,  6, 1, 'Welcome to Mini Social, Demo User!',                                     NOW() - INTERVAL 28 HOUR),
(9,  7, 3, 'Yes! Escape on output, validate on input.',                              NOW() - INTERVAL 18 HOUR),
(10, 8, 2, 'Count me in!',                                                           NOW() - INTERVAL 8 HOUR),
(11, 8, 1, 'I will bring snacks.',                                                   NOW() - INTERVAL 7 HOUR),
(12, 10, 4, 'Congrats Juan! Get some rest.',                                         NOW() - INTERVAL 20 MINUTE);

INSERT INTO likes (post_id, user_id) VALUES
(1, 2), (1, 3), (1, 4), (1, 5),
(2, 1), (2, 3),
(3, 1), (3, 2), (3, 4), (3, 5),
(4, 1), (4, 2),
(5, 3), (5, 4),
(6, 1), (6, 2),
(7, 1), (7, 3), (7, 4),
(8, 1), (8, 4),
(9, 1),
(10, 2), (10, 3);

-- =====================================================================
--  Quick checks (optional)
--     SELECT COUNT(*) FROM users;      -- 5
--     SELECT COUNT(*) FROM posts;      -- 10
--     SELECT COUNT(*) FROM comments;   -- 12
--     SELECT COUNT(*) FROM likes;      -- 24
-- =====================================================================
