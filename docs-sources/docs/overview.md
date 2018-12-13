# Getting Started

This application has been developed using CodeIgniter PHP application framework.

This documentation mostly focuses on an application itself and keeps trying to provide as little framework-specific explanations as possible.

For any questions regarding framework functionality this documentation will try to refer to official [CodeIgniter User Guide](https://www.codeigniter.com/user_guide/general/welcome.html).

## Quick chapter overview

The following **Overview** chapter covers general overview of application and implemented functionality as well as an MVC structure of an application.

More in-depth process-flow, diagrams and structures are presented in **Application Flow** chapter of this documentation.

If you want a quick screenshot-based overview of functionality please refer to **HOW-TO Use** chapter.

Chapters **Controller**, **Models** and **Views** contain technical and detailed explanation with code samples how the main back-end structure of an application is programmed.

Chapters **Libraries** and **Helpers** cover secondary functions and classes which are used during main application flow.

**Routing** chapter covers rules and patterns which are used to configure URI building for application pages.

**Frontend** and **Stylesheet** chapters refer to frontend logic and libraries used to implement styling and some of the presentation logic.


# ETL application at a Glance

This application was developed as a part of university project during first year of CS Masters studies
at Cracow University of Economics during **"Hurtownie Danych"** course. The purpose of this application is **educational only**
and it is not intended for any commercial or scientific research use. 

The aim of this application is to demonstrate
that project team understands what ETL process is and how to implement it in chosen technology according to [course
syllabus](https://e-uczelnia.uek.krakow.pl/mod/resource/view.php?id=176807)


## What does it do? - Extract Transform Load

The main purpose of an application is to provide web-based, GUI-based service which implements nad demonstrates
basic ETL processes: Extract, Transform and Load.

ETL Processes are executed in sequential manner although it is possible to configure application to be able to repeat
each module (Extract, Transform and Load) before proceeding to next one.

![alt text](img/ETL.png)


Additionally, it present user an option to monitor temporary and target databases to observe ETL process during it lifecycle.
This function is implemented as a basic CRUD-like module which allows to browse databases and perform simple queries and filters.

To the full description of application's functionality please refer to **HOW-TO Use** chapter of this documentation.

## What does it cover? - Allegro

ETL application was originally designed by project team, according to course requirements, to perform web-scraping of **one** of the categories
of popular e-commerce platform [allegro.pl](https://allegro.pl). But during the development process it evolved into 
flexible and universal parser which covers **the whole platform**.

Web scraping is still category-based but category can be changed from application GUI so the user can perform
ETL process for the range of products that he is the most interested in.

## How was it made? - Stack of Technology Used

**ETL Project** is mostly a PHP-based full-stack application. It was developed in [CodeIgniter](https://www.codeigniter.com/) web application framework.

Following libraries and technologies were used during application development:

1. **Frontend**
    * [jQuery v3.3.1](https://jquery.com/download/)
    * [jQuery JSON-Viewer Plugin](https://github.com/abodelot/jquery.json-viewer)
    * [jQuery Confirm v3.3.0](https://craftpip.github.io/jquery-confirm/)
2. **Styling**
    * [Bootstrap v3.3.7 JS CSS HTML Framework](https://getbootstrap.com/docs/3.3/)
    * [SCSS Extension](https://sass-lang.com)
    * [ScoutAPP v2.12.12 SASS/SCSS processor](http://scout-app.io)
3. **Backend**
    * [CodeIgniter v3.1.9 Application Framework](https://www.codeigniter.com/)
    * [MongoDB PHP Library v1.4.2](https://github.com/mongodb/mongo-php-library)
    * [Symfony/DOM-Crawler](https://symfony.com/doc/current/components/dom_crawler.html)
    * [Clue/ReactPHP PHP Multithreading](https://github.com/clue/reactphp-buzz)
4. **Databases**
    * [MySQL Community Server v5.6.39](https://dev.mysql.com/downloads/mysql/5.7.html)
    * [MongoDB Atlas Cloud Based DaaS](https://www.mongodb.com/cloud)
5. **WAMP Stack**
    * [Bitnami WAMP Stack: Apache+MySQL+PHP](https://bitnami.com/stack/wamp)
6. **Version Control**
    * [Github Git Platform](https://github.com)
    * [GitKraken Git Client](https://www.gitkraken.com)    
7. **Integrated Development Environment**
    * [JetBrains PhpStorm](https://www.jetbrains.com/phpstorm/)
8. **Documentation Engine**
    * [MkDocs - Project Documentation with Markdown](https://www.mkdocs.org)


# Implemented Techniques

Following functionality and techniques has been implemented in **ETL Project** application.

* WEB scraping.
* Distributed Page Application Architecture.
* CRUD Operations on Relational and Non-Relational databases.
* MongoDB Aggregation Framework.
* Database-based application configuration.
* CSV report generation.
* Scalable application template.
* MVC pattern.
* Multi-threading, queued requests.
* NON-Relational to Relational DBS Data Migration and Transformation.
* Configurable application flow.
* Flexible URI routing

# Model-View-Controller

ETL application was built with MVC programming pattern in mind. 

It was designed around one central controller and several models each of which handles logic of an
independent module. 

The following graphic illustrates how data flows through the system. 

![alt text](img/flowchart.gif)

Source: [CodeIgniter Documentation](https://www.codeigniter.com/user_guide/overview/appflow.html)

1. The index.php serves as the front controller, initializing the base resources needed to run CodeIgniter.
2. The Router examines the HTTP request to determine what should be done with it.
3. If a cache file exists, it is sent directly to the browser, bypassing the normal system execution.
4. Security. Before the application controller is loaded, the HTTP request and any user submitted data is filtered for security.
5. The Controller loads the models, core libraries, helpers, and any other resources needed to process the specific request.
6. The finalized View is rendered then sent to the web browser to be seen. If caching is enabled, the view is cached first so that on subsequent requests it can be served.