
# 🛍️ CLICK & CALL (E-COMMERCE WEBSITE PROJECT)

---

## 📌 Project Overview

This project is a dynamic **e-commerce web application** built using **PHP**, **MySQL**, **HTML/CSS**, and **JavaScript**. It facilitates user registration, product browsing, cart management, and order placement. It includes both front-end and back-end components that work together to deliver a complete shopping experience.

---

## 🧰 Technologies Used

| Technology     | Purpose                                  |
|----------------|-------------------------------------------|
| **PHP**        | Server-side scripting and logic handling  |
| **MySQL**      | Database management (defined in `DDL.txt`)|
| **HTML/CSS**   | Structure and styling of web pages        |
| **JavaScript** | Client-side interaction                   |
| **Apache**     | Web server environment (e.g., XAMPP/WAMP) |
| **JSON**       | Configuration file for VS Code            |

---

## 🎨 Front-End Details

The front-end delivers a user-friendly interface and enables interaction with the back-end logic through PHP and JavaScript.

### 📄 Page Structure

| Page             | Description |
|------------------|-------------|
| `home.php`       | Landing page with featured products and banners |
| `shop.php`       | Product listing and browsing |
| `cart.php`       | Shopping cart page |
| `checkout.php`   | Checkout form and order confirmation |
| `search_page.php`| Displays filtered product results |
| `header.php`     | Navigation bar |
| `footer.php`     | Footer section |

Modular components (`header.php`, `footer.php`) are reused across pages using PHP includes.

### 🎨 Styling (`style.css`)

- Uses flexbox layouts and custom styling
- Defines styles for buttons, cards, forms, and page layouts
- Basic responsiveness with media queries

### 🖱️ Interactivity (`js/script.js`)

- Handles UI events like menu toggles
- Manages client-side behavior for basic interactivity
- No external libraries used (lightweight scripting)

### 🖼️ Static Assets

- `/uploaded_img/`: Contains uploaded product images
- `/images/`: Contains homepage banners and layout images

---

## 🛠️ Back-End Details

The back-end manages application logic, data processing, and interaction with the database using PHP and MySQL.

### 🔐 User Authentication

**Files**: `register.php`, `login.php`, `logout.php`

- Registers and logs in users using session-based authentication
- **Security Note**: Passwords are stored in plain text (needs hashing)

### 🛒 Cart and Checkout

**Files**: `add_to_cart.php`, `cart.php`, `checkout.php`

- Adds products to user cart stored in database
- Displays cart contents and calculates totals
- Processes checkout and transfers data to `orders` table

### 📦 Product Management

**Files**: `shop.php`, `orders.php`, `search_page.php`, `home_admin.php`

- Retrieves products from `products` table for display
- Admin view for orders and potentially user management
- Search functionality using keyword matching

### 🗃️ Database Design (`DDL.txt`)

Defines the following tables:
- `users`
- `products`
- `cart`
- `orders`

Tables use primary keys and store data directly (e.g., product details in cart). Some normalization issues exist.

---

## 🔒 Security and Optimization Suggestions

| Concern            | Recommendation                                  |
|--------------------|--------------------------------------------------|
| SQL Injection       | Use prepared statements                         |
| Password Storage    | Use `password_hash()` and `password_verify()`   |
| Session Management  | Regenerate session IDs and enforce timeouts     |
| Data Redundancy     | Normalize schema (use product references)       |
| Input Validation    | Sanitize and validate user input                |

---

## ✅ Strengths

- Full-stack implementation
- Modular and maintainable layout
- Functional user and product flow
- Easy to deploy and test locally


---

## 📈 Conclusion

This project demonstrates a solid understanding of full-stack web development principles using the PHP ecosystem. It provides essential e-commerce functionality while leaving room for enhancements in security, performance, and scalability.

