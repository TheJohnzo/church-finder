## Church Finder Install Guide

The Church Finder is a standard Laraval app and uses environment variables to define the connection to a MySQL database.  

Checkout the source code from github.com/thejohnzo/church-finder into a folder connected to a webserver.

You will have to create an empty database with a username and password that can login with full permissions.  

Example values of environment variables:

```
DB_HOST='localhost'
DB_PORT='8889'
DB_DATABASE='church-finder-dev'
DB_USERNAME='sample'
DB_PASSWORD='sample'
ADMIN_EMAIL='your@email.com'
ADMIN_NAME='Your Name'
```

Once your MySQL database is setup and your environment variables are configured, run this command from the root of the application folder.  

`php artisan db:test`

If it doesn't return `CONNECTION OK`, something is not working correctly.  Check the previous steps and try again.  

## Setup Starter Data

#### Create Tables

After the database is connected, run this DB setup command.  This will create all the tables needed for the application to function. 

`php artisan migrate`
(reminder, all tables will be empty at this point)

#### Create Default User based on Env Variables

Next, run this command to create a basic admin user account so that you can log into the application:

`php artisan initialize`
(this will create a user with an email address and password "pleasechangemelater"  For security you should change this password IMMEDIATELY if you are on a production system.)

#### Import Additional Default data sets

Using a MYSQL tool (Sequel Pro or PhpMyAdmin etc), insert sample data SQL files from same_data_import/

    Required data, do not change:
    countries_insert.sql
    languages_insert.sql
.
   
    Default values, can be modified or added to later:
    church_size_insert.sql
    tags_insert.sql

#### For development purposes, import this file as base church data

OPTIONAL
To import a sample data set, use the provided XLSX file and import via this command:

`php artisan import:xls sample_data_import/church_import.xlsx`