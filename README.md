# php-rest-crud
restful crud api with file upload, delete functionality & many to many relationship tables built using raw php and mysql.

# how to run

- install `XAMPP` (link: https://www.apachefriends.org/download.html)

- clone the repository inside your `<path-to-xampp-source>/xampp/htdocs`. By default this path will be: `C:\xampp\htdocs`

- open `XAMPP` and start `Apache` and `MySQL`

- open to `phpMyAdmin` from a browser with the link: http://localhost/phpmyadmin/index.php

- create a new database named: `font_group_system` and click on this database from the left menu

- click on the `Import` option and choose the .sql file from inside the project's: `resources/font_group_system.sql`

- go to the link: http://localhost/font-group-system/ from a browser you will see the text "server running..." 

- import the json file of the provided apis into `Postman` (or any other preferred rest client) from inside the project's: `resources/font-group-system.postman_collection.json`
