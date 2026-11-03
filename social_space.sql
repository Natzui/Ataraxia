-- =====================================================================
--  social_app.sql  -  Mini Social Networking Web Application
--  Saint Michael College of Caraga - Web Systems and Technologies (Final Output)
--
--  Contains: database creation and CREATE TABLE statements only.
--  All tables are empty (no sample data).
--  Import with phpMyAdmin (Import tab) or:   mysql -u root < sql/social_app.sql
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
--  Quick checks (optional) - all should return 0
--     SELECT COUNT(*) FROM users;
--     SELECT COUNT(*) FROM posts;
--     SELECT COUNT(*) FROM comments;
--     SELECT COUNT(*) FROM likes;
-- =====================================================================
