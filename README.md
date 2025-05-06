# 🎁 Giftbox – PHP Console Project with Eloquent ORM

This project was developed as part of a **university assignment at IUT Nancy-Charlemagne** for the PHP/Eloquent TD series (TD1).

It is a **console-based PHP application** that interacts with a MySQL database using **Eloquent ORM**, simulating the core features of a gift box management system.

---

## 📚 Project Context

- 📍 Institution: **IUT Nancy-Charlemagne**
- 🧑‍🏫 Module: Advanced PHP – Object-Oriented Programming & ORM
- 📘 TD: **TD1 – Modeling and querying with Eloquent**
- 🧑‍💻 DeveloperS: Nathan, Robin, Paul

---

## 🚀 Features

### ✅ Implemented models (Eloquent)

- `Categorie`
- `Prestation`
- `CoffretType`
- `Box` (structure prepared)
- `Theme` (associated with CoffretType)

### ✅ Console scripts (CLI)

#### Categories & Prestations
- `show_categorie_with_presta.php`: displays a category (e.g., ID 3) with its prestations
- `list_prestations_with_category.php`: lists all prestations with their associated categories

#### Coffrets
- `list_coffret_types.php`: displays all available coffret types
- `list_coffrets_with_prestations.php`: shows each coffret type with its suggested prestations

---

## 🧩 Technical Stack

- 🐘 PHP 8+
- ⚙️ Composer for dependency management
- 💾 MySQL 8 (or MariaDB)
- 🌐 Eloquent ORM (standalone, not full Laravel)
- 🔁 PSR-4 Autoloading

---

## 🧪 Running CLI Scripts

- php gift.appli/src/console/list_prestations_with_category.php
- php gift.appli/src/console/show_categorie_with_presta.php
- php gift.appli/src/console/list_coffret_types.php
- php gift.appli/src/console/list_coffrets_with_prestations.php

---

## 📄 Author & Acknowledgments

    Developed by Nathan (Slophil), Robin (CaretteRobin), Paul (paul5400)

    Supervised as part of coursework at IUT Nancy-Charlemagne

    Project base inspired by academic exercises from TD1

--- 

## 📦 License

This project is educational and not intended for production use. No license is applied by default.

---
