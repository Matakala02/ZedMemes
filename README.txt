ZEDMEMES – A Single Page Meme Sharing Platform
=======================================================

ZedMemes is a responsive, mobile-first single-page web app for uploading, viewing, and reacting to memes. 
It includes user authentication, dynamic reactions (like, upvote, share, download), and real-time updates via AJAX. 
But only for the user who triggered the action.

--------------------------------------------------------------
PROJECT INSTALLATION INSTRUCTIONS
--------------------------------------------------------------

1. REQUIREMENTS:
   - PHP 7.4+ (XAMPP or WAMP recommended)
   - MySQL Server
   - Web browser (Chrome, Firefox, Edge)
   - A text editor (VS Code, Sublime, Notepad++)
   - For low level usage like APIs... use Postman.

2. INSTALLATION:
   a. Clone or extract the ZedMemes folder into your web root directory:

      - For XAMPP users:
        C OR D:/XAMPP/htdocs/ZedMemes/

      - For WAMP users:
        C OR D:/wamp64/www/ZedMemes/

   b. Start your local server:
      - Launch XAMPP or WAMP
      - Start "Apache" and "MySQL"

   c. Import the database:
      -You have two options:

   -------------------------------
   OPTION A: phpMyAdmin (Easy)
   -------------------------------
      - Go to http://localhost/phpmyadmin
      - Click "Databases"
      - Create a database named: `zedmemes`
      - Click the new database → Go to the "Import" tab
      - Choose file: `zed_memes_schema.sql`
      - Click "Go" to import

   ----------------------------------------------------------
   OPTION B: MySQL via XAMPP Shell (Extra effort and skill)
   ----------------------------------------------------------
      - Open XAMPP Control Panel
      - Click the "Shell" button
      - Type and press ENTER:
        "mysql -u root -p"

      - At the `mysql>` prompt, run: 
        "SOURCE D:/XAMPP/htdocs/ZedMemes/zed_memes_schema.sql"

        (Make sure the path matches your actual file location)

      - Then:
        USE zedmemes;
        SHOW TABLES;

   d. Test your app: From the client side.
      - Open your browser and go to:
        http://localhost/ZedMemes/

   e. Log in using one of the test accounts listed below


---------------------------------------
DEFAULTS ACCOUNTS.
---------------------------------------

1. User 1 (Standard):
   - Username: John
   - Email: john@example.com
   - Password: test1234

2. User 2 (Standard):
   - Username: memelord
   - Email: memelord@example.com
   - Password: loard4321

You can log in with these accounts to test uploading and reacting to memes.

---------------------------------------
FOLDER AND FILE STRUCTURE.
---------------------------------------

ZedMemes/
├── assets/                # jQuery, icons, fonts
├── css/                   # Tailwind or custom styles
├── js/                    # Frontend JavaScript logic
├── php/
│   ├── auth/              # Auth endpoints: login, logout, session
│   ├── meme/              # Meme upload, react, share, download
│   └── db.php             # DB connection
├── uploads/               # Meme images uploaded
├── index.php              # Main UI Single page app
├── zed_memes_schema.sql   # MySQL DB setup
└── README.txt             # This file

---------------------------------------
API ENDPOINT SUMMARY.
---------------------------------------

AUTH:
- POST   /php/auth/signup.php         - Register new user
- POST   /php/auth/signin.php         - Log in
- GET    /php/auth/signout.php        - Log out
- GET    /php/auth/check_session.php  - Check login status

MEMES:
- GET    /php/meme/meme_fetch_all.php       - Get all memes
- POST   /php/meme/meme_upload.php          - Upload meme (form-data)
- POST   /php/meme/meme_react.php           - Like/Upvote meme (JSON)
- POST   /php/meme/meme_download.php        - Download meme
- POST   /php/meme/meme_share.php           - Share meme (copy, WhatsApp, etc.)
- POST   /php/meme/meme_delete.php          - Delete user's own meme

---------------------------------------
SECURITY.
---------------------------------------

- Passwords are securely hashed using "password_hash()".
- All actions require a valid session (except register/login).
- SQL Injection is prevented via prepared statements (PDO).
- Only image files (".jpg", ".png", ".gif") are allowed in uploads.

---------------------------------------
DEVELOPER TIPS.
---------------------------------------

- Use Postman or browser dev tools to test AJAX requests
- Frontend is expected to use jQuery for dynamic interaction

---------------------------------------------------------
DEVELOPED FOR CA'S PURPOSE BY TEAM TEN.
---------------------------------------------------------