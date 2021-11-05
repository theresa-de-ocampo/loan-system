# Multipurpose Cooperative Loan System
## Overview
A web-based application that automates the entire loan lifecycle of a cooperative. Ever since the emergence of the COVID-19, it has threatened the income of many Filipinos, and even businesses. Borrowing money in times like this has become the go-to solution when securing one's financial stability. Hence, this system was developed to aid in handling P2P lending. The business rules here are based from **Ciudad Nuevo Cooperative** of Naic, Cavite.

## Features
1. Tables
	1. Provision for printing.
	2. Provision for exporting to csv.
	3. Sorts date and time columns appropriately while displayed in long format.
	4. Sorts full name columns by last name.
2. Login & Account Management
	1. Password update *(to be implemented)*.
	2. Strong password requirement.
	3. Password confirmation.
	4. Only the latest auditor and treasurer can create accounts.
	5. Automatically revokes administrator access of old employees without deleting the account.
3. Validations
	1. Checks for incomplete inputs.
	2. Checks number of shares upon adding a new guarantor.
	3. Checks for valid money value.
	4. Checks balance payment.
	5. Checks guarantor outstanding on loan request.
	6. Checks if collateral is required on loan request.
	7. Checks username length on admin account request.
	8. Checks email on account request.
	9. Checks for duplicate entities on new cycle and upon loan request.
	10. File validations are based on MIME and EXIF.
4. Automation
	1. Automatically closes a loan.
	2. Automatically accrues interest every month.
	3. Automaticaly halts accrual of interest once the principal is paid in full.
	4. Automatically sets penalty for 7 days after the missed deadline.
	5. Automatically halts accrual of penalty if interest is paid in full during the grace period.
	6. Automatically sets processing fee every 3 months.
	7. Automatically halts accrual of processing fee once the principal is paid in full.
	8. Automatically calculates and records closing entries by the end of the business year.
	9. Automatically sets each of the guarantor's ROI by the end of the business year.
	10. Automatically sets the salary of employees by the end of the business year *(to be implemented)*.
5. Dashboard
	1. Financial Standing *(to be implemented)*
	2. Quick Stats *(under construction)*
6. Members
	1. Lists guarantors.
	2. Lists savings.
	3. Add guarantors to current cycle without data redundancy.
	4. Edit guarantor information.
	5. Data subject management *(to be implemented)*.
7. Transactions
	1. Lists loan disbursements.
	2. Lists appropriations.
	3. Display loan details including quick stats, and related documents.
	4. Accepts multiple principal payments in increments with receipt.
	5. Accepts multiple interest payments in increments with receipt.
	6. Accepts multiple penalty payments in increments with receipt.
	7. Accepts multiple processing fee payments in increments with receipt.
	8. Processes new loan.
8. Payroll *(This entire module is time-aware)*
	1. Displays quick stats of the cooperative's profit.
	2. Calculates principal summation.
	3. Calculates interest summation.
	4. Calculates interest summation.
	5. Lists shares per guarantor.
	6. Allows ROI claim.
	7. Displays claimed ROI.
	8. Salary and funds management *(to be implemented)*.
9. Cycle Switcher
	1. Lists history of business periods.
	2. Provision to add a new cycle *(under construction)*.
10. Public Portal
	1. Details borrower requirements.
	2. Details guarantor requirements.
	3. Details the cooperative's terms and conditions.
	4. Borrower and guarantor view with notifications *(under construction)*.
11. Others
	1. Includes empty states design.
	2. Prompts the user to read the "Terms, Data Policy, & Cookie Use" wherever appropriate.
	3. Audit Trail *(to be implemented)*.

## Requirements
- Apache Server 2.4.41 or higher.
- PHP 7.4.0 or higher.
- MySQL 8.0.21 or higher.

## Installation
1. Clone the repository.
	```bash
		git clone https://github.com/theresa-de-ocampo/loan-system.git
	```
2. Run SQL file through MySQL Console.
	```sql
		source your-path/config/setup-database.sql
		source your-path/config/setup-database-logic.sql
	```
3. Change DSN at ```your-path/config/config.php```.
	```php
		define("DB_HOST", "your-hosting-site");
		define("DB_USER", "your-username");
		define("DB_PASSWORD", "your-password");
	```
4. Open the admin portal (```index.php```) using any of the accounts from the next section.

## 2021 Accounts
| Position | Email | Password |
| --- | --- | --- |
| Auditor | ryan.nable@gmail.com | Green 0456 |
| Treasurer | carlo.robiso@gmail.com | Vincenzo, EP 2 |
| Asst. Treasurer | ma_theresa7@yahoo.com | Dear 2020 |