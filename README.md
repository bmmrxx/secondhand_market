# A second hand market
In this project we created a platform where you can upload you're own products, search for products based on category's and buy them. 


## Tech stack
- PHP 8.4.2
- MySQL or MariaDB
- Apache
- Java
- Bcypt (password hasher)

# Features
- Users can add there own products for sale
- Users can buy products
- Users can delete and edit there own products
- You can search specificly for products based on their category
- Admins can monitor users and products
- Admins can delete and edit users and products
- Admins have a admin panel to overview all users, products and categories

# Start up guide
- Clone the repository
- Change the database credentials to you're own configuration ![Database connection file](/app/dashboard/connection.php)
- Import the data into your database ![Import SQL file](/app/database/import.sql)
- Insert the data into your database ![Insert SQL file](/app/database/insert.sql)
- Start a local server to host the application 


# Mock data
- With the insert of the data the passwords are not automatically hashed
- Go to you're database and change the password to the hashed version, for **testpassword** this would be;
    *$2a$12$kmAShddMyCVa4CyelWZf0OLgDl1mVioNTRAnv2HPiM63Yt/neR2zO*
- After this you can login with the credentials

## User
- username: testuser
- password: testpassword

## Admin
- username: admin
- password: testpassword

# Future features
- [ ] Add a cart
- [ ] Add a checkout
- [ ] Add a payment gateway
- [ ] Add a shipping gateway
- [ ] Add a payment gateway
- [ ] Add a shipping gateway