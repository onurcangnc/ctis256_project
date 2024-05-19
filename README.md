# 📊 CTIS 256 Introduction to Backend Development PROJECT

**🗓️ Spring 2024**

## 📝 Project Overview
**Title:** A Sustainability Project

**Objective:** Develop a multi-user, web-based application using PHP and MySQL to help reduce waste from expired products in markets by making it easier to sell products nearing their expiration date at a lower price.

**Team Size:** 4 students (from any sections)

**Deadline:** 19 May 2024, Sunday, 23:59

**Demonstration:** 21st of May, Tuesday. Each team will demonstrate their project on a laptop at the instructor's office. Any member who doesn’t attend the meeting will not be graded. The team should reserve an appropriate time slot from the course’s Moodle page.

**Submission:** All project source codes, assets, and database exports should be uploaded to Moodle by one team member by the deadline (19th of May).

---

## Project Requirements

### 🔧 Functional Requirements

#### Market User
1. **🔐 Registration:** A market can register with email, market name, password, city, district, and address.
2. **✏️ Update Information:** A market can update/edit its information.
3. **🆕 Add Product:** A market user can add a new product nearing its expiration date into its inventory, including title, stock, normal price, discounted price, expiration date, and an image showing the expiration date.
4. **🗑️ Delete/Edit Product:** A market user can delete and edit a product.
5. **⚠️ Mark Expired Products:** On login, expired products in the list are marked.

#### Consumer User
1. **🔐 Registration:** A consumer can register with an email address, fullname, city, district, and address.
2. **✏️ Update Information:** A consumer can update their information.
3. **🔍 Search Products:** Consumers can search products with keywords, filtering by city and district, excluding expired products, and paginating results with a page size of 4.
4. **🛒 Shopping Cart:** Consumers can add products to a session-based shopping cart, update the cart, and view the grand total.
5. **💳 Purchase Products:** The "Purchase" button in the shopping cart page empties the cart and removes the products from the system.

---

### 🔒 Non-Functional Requirements
1. **📝 Form Validation:** All forms are validated and offer sticky-form functionality.
2. **🛡️ Security:** Protection against SQL injection, XSS, and CSRF attacks.
3. **🎨 User Interface:** The system has a decent user interface using HTML templates, third-party jQuery plugins, and/or Bootstrap-like CSS frameworks.
4. **📊 Meaningful Data:** Use meaningful data for the demonstration.

---

### 🎁 Bonus
1. **📈 Meaningful Data:** Sufficient amount of meaningful data for the demonstration (no garbage test data).

---

### ⚠️ Penalties
1. **⏳ Late Submission:** 20 points penalty for late submission. Submission will be closed on 20th of May, 23:59.
2. **❌ Cheating:** Strictly prohibited, resulting in a zero grade for all team members.

---

### 👥 Team Work
- Each team member is responsible for a particular part of the project.
- The demonstration will involve questions to all team members about the project to assess grades based on individual contributions.

---

## Application Overview

### **Website:** [ctis256project.com.tr](http://ctis256project.com.tr)

### ⚙️ Project Setup and Installation
1. Clone the repository.
2. Set up the database using the provided SQL scripts.
3. Configure the database connection settings in the PHP files.
4. Run the application on a local server (e.g., XAMPP, WAMP).

### 🚀 Usage
1. **Register:** Users can register as either a Market or Consumer.
2. **Login:** Users & Market can login using their email and password.
3. **Market User:**
   - Add products nearing their expiration date.
   - Edit and delete products.
   - View a list of expired products.
4. **Consumer User:**
   - Search for products by keyword.
   - Add products to a session-based shopping cart.
   - Update the shopping cart (delete, change the amount).
   - Purchase products, which empties the cart and removes the products from the system.

### 🧑‍💻 Team Members
- **Arman Yılmazkurt:** Responsible for Fullstack Development, Security, Database Product Management
- **Ece Gülyüz:** Responsible for Frontend Development, User Interface Design
- **Agil Gumukov:** Responsible for Fullstack Development, User Interface Design, Product List Addition
- **Onurcan Genç:** Responsible for Backend Development, User Authentication, Security, Service Management, SMTP Deployment

### 📅 Demonstration
- **Date:** 21st of May, Tuesday
- **Location:** Instructor's office
- **Reservation:** Reserve a time slot on the course’s Moodle page

### 📥 Submission
- Upload all project source codes, assets, and database exports to Moodle by 19th of May, 23:59.

### 🛠️ Technologies Used
- **Hosting:** Infinityfree
- **Domain:** Metunic
- **Backend:** PHP, PHP Composer
- **Email Services:** SMTP
- **Frontend:** Bootstrap

---

### 📋 Application Features

#### Email Verification
1. **Send Verification Code:** Users receive a verification code via email upon registration.
2. **Validate Verification Code:** Users must enter the verification code to complete the registration process.

#### Shopping Cart
1. **Add to Cart:** Users can add products to the cart.
2. **Update Quantities:** Users can update product quantities in the cart.
3. **Remove Items:** Users can remove items from the cart.
4. **Proceed to Payment:** Users can proceed to the payment page, clearing the cart upon submission.
5. **Clear Cart:** Users can clear all items from the cart.

#### AI Assistant
1. **Add Products:** Users can use an AI assistant to add products to their cart based on natural language input.
2. **Voice Recognition:** The AI assistant can process voice commands to add products to the cart using the Web Speech API.

#### Security
1. **CSRF Protection:** Generate and validate CSRF tokens to protect forms from cross-site request forgery.
2. **SQL Injection Prevention:** Use prepared statements to prevent SQL injection attacks.
3. **XSS Protection:** Sanitize user inputs to prevent cross-site scripting (XSS) attacks.

#### Design
1. **Responsive Design:** Ensure the application is usable on various devices, including desktops, tablets, and smartphones.
2. **User-Friendly Interface:** Implement a clean, intuitive interface with easy navigation.
3. **Sticky Forms:** Maintain form data during validation to enhance user experience.


