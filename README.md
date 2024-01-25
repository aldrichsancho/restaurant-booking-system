# Restaurant Table Booking System

This project is a simple booking system for restaurant tables. It allows users to book tables for a specified time period.

## Features
- **Table Booking**: Users can book tables for a specific date and time.
- **Time Validation**: Ensures that bookings are made for future times and that the end time is later than the start time.
- **Restaurant and Table Selection**: Users can select the restaurant and table for booking.
- **Online and Offline booking**: Offline booking handled by admin using app

## Technologies Used
- **Laravel**: The backend of the system is built using the Laravel PHP framework.
- **MySQL**: The database management system used for storing booking information.

## Database Design
This application has 4 main models.

![Restaurant Database Design](Restaurant%20Database%20Design.jpg)

### 1. User
The User table represents registered users of the application. It is responsible for storing information about individuals who use the booking system.

#### Fields
- **name**: The full name of the user.
- **role**: The role or type of the user (e.g., admin, user).
- **email**: The email address of the user.
- **password**: The hashed password for user authentication.

### 2. Restaurant
The Restaurant table represents information about different restaurants available in the system. It stores details related to the restaurants.

#### Fields
- **name**: The name of the restaurant.
- **address**: The physical address of the restaurant.
- **phone**: The contact phone number for the restaurant.

### 3. Table
The `Table` table represents individual tables within a restaurant. It stores details about each table, such as its capacity and name.

#### Fields
- **restaurant_id**: Foreign key referencing the id of the associated restaurant.
- **name**: The name or identifier of the table.
- **capacity**: The maximum number of customers the table can accommodate.

### 4. Table
The Booking table manages reservations made by users. It links users to specific tables in a particular restaurant and tracks details about the booking.

#### Fields
- **user_id**: Foreign key referencing the id of the associated user.
- **restaurant_id**: Foreign key referencing the id of the associated restaurant.
- **table_id**: Foreign key referencing the id of the associated table.
- **customer_name**: The name of the customer for whom the booking is made.
- **start_time**: The start time of the booking.
- **end_time**: The end time of the booking.
- **is_online**: Flag indicating whether the booking is for an online service.
- **status**: The status of the booking (e.g., pending, confirmed).
- **special_requests**: Any special requests or notes associated with the booking.


## Installation
1. Clone the repository
2. Install Dependencies: <br/>
```bash 
composer install
```
3. Create a copy of the .env.example file and rename it to .env. Configure your database connection and other settings in this file.
4. Run migrations and seed the database: <br/>
```bash 
php artisan migrate --seed
```
5. Start the development server: <br/>
```bash
php artisan serve
```

## Usage
1. **Register/Login**: Users need to register or log in to make a booking.
2. **Make a booking**: Create a booking using API.
3. **Confirm Booking**: Confirm a booking using API.
4. **Cancel Booking**: Cancel a booking using API.

For the complete API Documentation can be accessed in this link: [API Documentation](https://documenter.getpostman.com/view/2470070/2s9YypE3US)
