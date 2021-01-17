Example order management system
========================
**Overview**
This an example of a simple order management system including user authentication with JWT build with Symfony v3.4.

It includes the following entities:
* Customer 
* CustomerOrder 
* Item
* CustomerOrderItem: In order to store the relation between orders and items   

**Security**
* Authentication: It provides a customer authentication with JWT (https://jwt.io/)
* Symfony Security configuration

**Installation**
* Create and grant BD user: getnow
`GRANT ALL PRIVILEGES ON *.* TO 'getnow'@'localhost' IDENTIFIED BY 'getnow';`

* Run composer install:
`composer install`
* Create database with command:
`php bin/console doctrine:database:create`

* Run migrations:
`php bin/console doctrine:migrations:migrate`

**First steps**
We can create a customer and then login, it will return a JWT that should be used adding it on the headers with the parameter "X-AUTH-TOKEN"





