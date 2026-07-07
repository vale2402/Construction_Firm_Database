# Construction Firm Database Management

A comprehensive web-based database management system designed to streamline the operations of a road construction company. This project digitizes the management of human resources, materials, machinery, clients, and suppliers across multiple construction sites.

## 🛠️ Tech Stack

* **Database:** Oracle Database 19c Enterprise Edition
* **Database Tools:** Oracle SQL Developer, SQL Data Modeler
* **Backend:** PHP (using the OCI8 extension for Oracle connectivity)
* **Frontend:** HTML5, CSS3, Bootstrap 5.3
* **UI/UX Libraries:** SweetAlert2 (for dynamic popups and delete confirmations) and Bootstrap Icons

## ✨ Key Features

* **Complete CRUD Operations:** Create, Read, Update, and Delete functionalities for all primary entities (Jobs, Employees, Clients, Projects, Machinery, Materials, Suppliers).
* **Relational Integrity:** Advanced constraints including `CHECK`, `UNIQUE`, and `ON DELETE CASCADE` to prevent orphaned records.
* **Smart Delete System:** A dependency analysis interface that warns users of connected records (e.g., active timesheets or resource consumptions) before executing a cascading delete.
* **Managerial Reports:**
  * *Complex Filtering:* Extracts aggregated data from at least 3 joined tables with multiple conditions (e.g., material consumption per client in a specific county).
  * *Group Functions:* Uses `SUM`, `COUNT`, and `HAVING` clauses to track employee performance and logged hours.
* **Database Views:**
  * *Compound Views (DML Enabled):* Allows updating employee data through a view built on joined tables (Key-Preserved Table logic).
  * *Complex Views (Read-Only):* Generates real-time financial statistics (total material costs per project) using aggregate functions.

## 📂 Repository Structure
The project is organized into the following main folders:

* **`Documentation/`**
  Contains the detailed project reports in PDF format. This includes the database design documentation (E/R diagrams, conceptual schemas, constraints) and the user interface guide with screenshots.
* **`SQL_Scripts/`**
  Contains the SQL files needed to initialize the database in Oracle. 
  * `creareBD.sql` handles the creation of tables, constraints, and views. 
  * `populareBD.sql` contains the `INSERT` statements to populate the database with initial sample data.
* **`src/`**
  Contains all the PHP source code for the web application's frontend and backend. This includes the database connection logic (`db.php`), the main dashboard (`index.php`), all CRUD interface pages (e.g., `angajati.php`, `material.php`), and the managerial reporting modules.

## 🚀 Setup & Installation

To run this project locally, you will need a local web server (like XAMPP) and an Oracle Database instance.

1. **Clone the repository:**
   \`\`\`bash
   git clone https://github.com/your-username/Construction_Firm_Database.git
   \`\`\`
2. **Database Setup:**
   * Open **Oracle SQL Developer**.
   * Run `SQL_Scripts/creareBD.sql` to generate the tables, constraints, and views.
   * Run `SQL_Scripts/populareBD.sql` to populate the database with sample data.
3. **Web Server Setup:**
   * Move the `src` folder into your web server's root directory (e.g., `htdocs` for XAMPP).
   * Ensure the **OCI8 extension** is enabled in your `php.ini` file (`extension=oci8_19`).
4. **Configure Connection:**
   * Open `src/db.php` (or wherever your connection logic is) and update the Oracle credentials (username, password, and connection string/localhost) to match your local Oracle setup.
5. **Run:**
   * Open your browser and navigate to `http://localhost/src/index.php`.

## 🎓 Academic Context
This project was developed for the **Databases** course at the University of Bucharest, Faculty of Mathematics and Computer Science (2026). It demonstrates practical knowledge of database modeling, normalization, advanced SQL queries, and full-stack web integration.

**Author:** Monceanu Mihaela-Valentina
