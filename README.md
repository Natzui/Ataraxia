<div align="center">

# Social Space

**A mini social networking web application built with PHP (custom MVC), MySQL and Bootstrap 5.**

![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-MariaDB-4479A1?logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?logo=bootstrap&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-Vanilla-F7DF1E?logo=javascript&logoColor=black)
![Architecture](https://img.shields.io/badge/Architecture-MVC-4f6cf0)

</div>

---

## About

**Social Space** is a simplified social platform where users can create an account, share posts with optional images, comment, like, search for people, and manage their profile.

It was built **without a framework**: the router, base controller, model layer and security helpers are all written by hand to show how MVC, routing and web security work underneath.

> Final output for **Web Systems and Technologies**, Saint Michael College of Caraga.

---

## Features

| Area | What you can do |
|---|---|
| **Authentication** | Register with validation, log in and out, passwords hashed with `password_hash()`, optional profile picture |
| **Profile** | View avatar, name, username, bio and your posts; edit name, bio and photo |
| **Posts** | Create posts with optional image, edit and delete your own posts |
| **Comments** | Comment on any post, edit and delete your own comments inline |
| **Likes** | Like and unlike with a live counter (AJAX); one like per user per post |
| **Newsfeed** | All posts, latest first, with pagination |
| **Search** | Find people by name or username, or find posts by keyword |
| **UI** | Responsive Bootstrap layout, icons, light and dark theme saved per browser |

---

## Tech stack

| Layer | Technology |
|---|---|
| Front-end | HTML5, CSS3, Bootstrap 5.3, Bootstrap Icons, vanilla JavaScript |
| Back-end | PHP 7.4+ / 8.x, custom MVC, PDO |
| Database | MySQL / MariaDB (4 InnoDB tables with foreign keys) |
| Server | Apache (XAMPP) or the PHP built-in server |

---

## Getting started

### Requirements
- PHP 7.4 or newer (with `pdo_mysql`, `mbstring`, `fileinfo`)
- MySQL or MariaDB
- XAMPP (recommended) or any Apache + PHP stack

### Installation (XAMPP)

1. **Clone or download** this repository into `C:\xampp\htdocs\`:
   ```bash
   git clone https://github.com/YOUR-USERNAME/YOUR-REPO.git Social_Space
   ```
2. **Start Apache and MySQL** in the XAMPP Control Panel.
3. **Import the database:** open phpMyAdmin, go to **Import**, and choose `sql/social_app.sql`. It creates the `social_app` database, the tables and sample data.
4. **Check the database settings** in `config/database.php` (defaults match XAMPP: user `root`, empty password).
5. **Open the app:** <http://localhost/Social_Space/public/>

### Without Apache

```bash
mysql -u root < sql/social_app.sql
php -S localhost:8000 -t public
```
Then open <http://localhost:8000>.


> Bootstrap and Bootstrap Icons load from the jsDelivr CDN, so an internet connection is needed for styling.

---

## Project structure

```
Social_Space/
├── index.php                 redirects to /public
├── config/
│   ├── config.php            app name, timezone, page size, upload limit
│   └── database.php          MySQL credentials
├── public/                   web root
│   ├── index.php             front controller
│   ├── assets/               css/, js/, img/
│   └── uploads/              avatars/, posts/
├── app/
│   ├── core/                 Router, Controller, Model, View, Database, Auth, Input, Upload, helpers
│   ├── models/               UserModel, PostModel, CommentModel, LikeModel
│   ├── controllers/          Auth, Feed, Profile, Post, Comment, Like, Search
│   └── views/                layouts, partials and page templates
├── sql/
│   └── social_app.sql        tables + sample data
└── docs/
    └── ERD.png               entity-relationship diagram
```

---

## How the MVC works

```
Browser -> public/index.php -> Router -> Controller -> Model -> MySQL
                                             |
                                             +-> View (HTML) -> Browser
```

| Role | Responsibility |
|---|---|
| **Model** | Only SQL, using PDO prepared statements. No HTML. |
| **View** | Only presentation. Every dynamic value is escaped with `e()`. |
| **Controller** | Validates input, checks permissions, calls models, picks the view. |
| **Router** | Turns `index.php?url=post/edit/5` into `PostController::edit(5)`. |

---

## Database

Four tables with foreign keys and `ON DELETE CASCADE`.

| Table | Columns |
|---|---|
| `users` | id, username (unique), password, full_name, bio, profile_image, created_at |
| `posts` | id, user_id, content, image, created_at |
| `comments` | id, post_id, user_id, content, created_at |
| `likes` | id, post_id, user_id, **unique (post_id, user_id)** |

---

## Security

| Threat | Defense |
|---|---|
| SQL injection | PDO real prepared statements; values are always bound, never concatenated |
| XSS | All output escaped with `htmlspecialchars()`; input trimmed and cleaned |
| Password theft | bcrypt via `password_hash()` / `password_verify()` |
| Session attacks | Session ID regenerated on login/logout; HttpOnly + SameSite cookies |
| CSRF | Per-session token on every form; state-changing actions are POST only |
| Unauthorized edits | Ownership checked in the controller (403) and again in SQL (`WHERE id = ? AND user_id = ?`) |
| Malicious uploads | Size limit, real image check, type whitelist, random file names, PHP execution disabled in `uploads/` |

---

## Main routes

| Route | Method | Description |
|---|---|---|
| `auth/register` | GET / POST | Create an account |
| `auth/login` | GET / POST | Log in |
| `auth/logout` | POST | Log out |
| `feed/index` | GET | Newsfeed |
| `post/create` | POST | Create a post |
| `post/edit/{id}`, `post/update/{id}` | GET / POST | Edit own post |
| `post/delete/{id}` | POST | Delete own post |
| `comment/store` | POST | Add a comment |
| `comment/update/{id}`, `comment/delete/{id}` | POST | Edit or delete own comment |
| `like/toggle` | POST | Like or unlike |
| `profile/show/{id}` | GET | View a profile |
| `profile/edit`, `profile/update` | GET / POST | Edit own profile |
| `search/index` | GET | Search people or posts |

---

## Troubleshooting

| Problem | Fix |
|---|---|
| "Could not connect to the database" | Start MySQL, import `sql/social_app.sql`, check `config/database.php` |
| Page has no styling | Open the site through the `/public/` URL and check your internet (CDN) |
| Image upload fails | Make sure `public/uploads/avatars` and `public/uploads/posts` are writable; check `upload_max_filesize` and `post_max_size` in `php.ini` |
| Wrong "x minutes ago" times | Keep `timezone` and `db_timezone` in `config/config.php` consistent |
| Forgot a password | There is no email reset; an administrator replaces the bcrypt hash in the `users` table |

---

## Future improvements

- Follow system and a personalized feed
- Notifications
- Password reset by email and email verification
- Login rate limiting
- Infinite scroll

---

## Author

**Natzui Alyx and Stanlye**

---

<div align="center">

This project was created for academic purposes.

</div>
