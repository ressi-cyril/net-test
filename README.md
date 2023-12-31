# Net-Test

## Installation

1. Clone the Repository: ``$ git clone https://gitlab.com/ressi-cyril/net-test.git ``
2. Navigate to netinvestissement directory: ``$ cd .\netinvestissements``
3. Pull Docker Images  : ``$ docker-compose pull``
4. Fire Up the Containers: ``$ docker-compose up -d``
5. Enter the Project Container: ``$ docker exec -it www-net bash``
6. install Composer Dependencies: : ``$ composer install``
7. Create database: ``$ php bin/console doctrine:database:create``
8. Run Database migration: ``$ php bin/console doctrine:migration:migrate``
9. Load fixtures : ``$ php bin/console doctrine:fixtures:load``

For a detailed API guide, visit http://localhost/api/open/doc.

## Testing
To confirm the API operational status:
```
$ docker exec -it www-net bash
$ php bin/phpunit 
 ```

## Postman
A postman collection for testing is included in the 'netinvestissement' folder.  
Feel free to copy & use :)

## Technologies Used
- Symfony 6.3: the framework of choice
- PHP 8.1: the scripting standard
- FOSRestBundle: For RESTful API development
- Docker: For containerization and environment consistency
- OpenAPI / Nelmio: For comprehensive API documentation