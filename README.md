# PHP User System - Basic Auth & Role Management

Un sistem simplu de autentificare și administrare a utilizatorilor, construit în PHP procedural + PDO. Include suport pentru roluri (`user`, `admin`, `root`), loguri de activitate și un panou de control pentru root admin.

---


---
Add profile.php
---


## 📁 Structura Proiect

'''
user_system
varianta 
user_system_25_05_02/

ls -R
.:
./  ../  app/  config/  logs/  public/  README.md  user_system.sql

./app:
./  ../  Controllers/  Core/  Models/  Views/

./app/Controllers:
./  ../  AuthController.php

./app/Core:
./  ../  Database.php  SessionHelper.php  Session.php  View.php

./app/Models:
./  ../  User.php

./app/Views:
./  ../  home.php  login_form.php  register_form.php

./config:
./  ../  config.php

./logs:
./  ../  user_logs.php

./public:
./       admin.php      index.php        login.php   phpinfo.php     root.php    style.css     user_logs.php
../      dashboard.php  install-old.php  logout.php  register.php    router.php  template.php
404.php  .htaccess      install.php      menu.php    root_admin.php  router.sh*  temp.php

'''

---


## ⚙️ Instalare

1. Clonează sau descarcă acest proiect.
2. Copiază în serverul local (`htdocs/` pentru XAMPP sau `www/` pentru Laragon).
3. Accesează în browser:
4. `http://localhost/user_system/public/`
   `http://localhost/user_system/public/install.php`
5. Sau porneste server
```
   /public
   php -S localhost:8000 router.php
```  
6. Accesează în browser:     
   `http://localhost:8000/install.php`   
7. Asta va crea:
   - Baza de date `user_system`
   - Tabelele `users` și `user_logs`
   - Un utilizator root implicit:
     - **Email:** `root@admin.com`
     - **Parolă:** `rootpass`

---

## 🔐 Funcționalități

- Înregistrare & autentificare utilizatori
- Protejare acces cu `$_SESSION`
- Management roluri: `user`, `admin`, `root`
- Panou `root` pentru schimbarea rolurilor
- Logare automată a acțiunilor în `user_logs`
- Vizualizare loguri:
  - `user`: doar propriile acțiuni
  - `admin`/`root`: toate acțiunile

---

## 👨‍💻 Conturi predefinite

| Email            | Parolă    | Rol   |
|------------------|-----------|--------|
| root@admin.com   | rootpass  | root   |

---

## 📬 Contact

Creat de **Vasilache Ionuț**  
✉️ vasilacheorionut@gmail.com

---

## 📄 Licență

Acest proiect este open-source și poate fi folosit sau modificat liber.
