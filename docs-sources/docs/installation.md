# Downloading Application
## Github

1. You can download .zip from application's [public github repository](https://github.com/Vjatches/etlproject).

2. Or you can clone branch by using 

    `git clone https://github.com/Vjatches/etlproject.git` 

# Environment Requirements

1. Apache or NginX web server
2. PhP v5.6.30 - v7.1.23 (higher versions were not tested) with following modules enabled
    * php_pdo_mysql
    * php_mongo
    * php_mongodb
3. Local MySQL Database client version v5.0.11 - v8.0.12 (higher version were not tested)

# Installation Instructions

1. Download Application from [git repository](https://github.com/Vjatches/etlproject)
 or by using `git clone https://github.com/Vjatches/etlproject.git`.
 
2. Unpack application into `public_html` folder of your web-server.

3. Open file `application/config/config.php` and change value of `BASE_URL` to base url of your web server.
Base URL has to point to `index.php` file of application.

4. Import SQL database with all required schemas and tables from `warehouse.sql` file which you will find in `sql` directory of the project.

5. Open URL which you provided at step 3 in browser to access application.