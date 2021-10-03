# Multipurpose Cooperative Loan System
## Overview
A web-based application that automates the entire loan lifecycle of a cooperative. Ever since the emergence of the COVID-19, it has threatened the income of many Filipinos, and even businesses. Borrowing money in times like this has become the go-to solution when securing one's financial stability. Hence, this system was developed to aid in handling P2P lending. The business rules here are based from **Ciudad Nuevo Cooperative** of Naic, Cavite.

## Features
1. Tables
	1. Provision for printing.
	2. Provision for exporting to csv.
2. Login & Account Management
	1. Password update *(to be implemented)*.
	2. Strong password requirement *(to be implemented)*.
	3. Only the master admin can create and revoke accounts *(to be implemented)*.
3. Validations *(to be implemented)*
	1. Added server-side validation to HTML5 validation *(to be implemented)*.
	1. Checks if collateral is required on loan request.
	2. File validations are based on MIME and EXIF.
4. Automation
	1. Automatically closes a loan.
	2. Automatically accrues interest every month.
	3. Automaticaly halts accrual of interest once the principal is paid in full.
	4. Automatically sets penalty for 7 days after the missed deadline.
	5. Automatically halts accrual of penalty if interest is paid in full during the grace period.
	6. Automatically sets processing fee every 3 months.
	7. Automatically halts accrual of processing fee once the principal is paid in full.
5. Dashboard
	1. Financial Standing *(to be implemented)*
	2. Quick Stats *(under construction)*
6. Members
	1. Lists guarantors.
	2. Lists savings.
	3. Add guarantors to current cycle without data redundancy.
7. Transactions
	1. Lists loan disbursements.
	2. Lists principal payments.
	3. Display loan details including quick stats, and related documents.
	4. Accepts multiple principal payments in increments with receipt.
	5. Accepts multiple interest payments in increments with receipt.
	6. Accepts multiple penalty payments in increments with receipt.
	7. Accepts multiple processing fee payments in increments with receipt.
	8. Processes new loan.
9. Cycle Switcher

## Requirements
- Apache Server 2.4.41 or higher.
- PHP 7.4.0 or higher.
- MySQL 8.0.21 or higher.

## Installation
1. Clone the repository.
	```bash
		git clone https://github.com/theresa-de-ocampo/muzon.git
	```
2. Run SQL file through MySQL Console.
	```sql
		source your-path/config/setup-database.sql;
		source your-path/config/setup-database-logic.sql;
	```
3. Change DSN at ```your-path/config/config.php```.
	```php
		define("DB_HOST", "your-hosting-site");
		define("DB_USER", "your-username");
		define("DB_PASSWORD", "your-password");
	```
4. Open the admin portal (```index.php```) using any of the accounts from the next section.

## Accounts
| Position | Email | Password |
| --- | --- | --- |
| Master Administrator | *(to be created)* | *(to be created)* |
| Treasurer | ma_theresa7@yahoo.com | Dear 2020 |
| Asst. Treasurer | *(to be created)* | *(to be created)* |
| Auditor | *(to be created)* | *(to be created)* |