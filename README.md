<h1 align="center">
   AWEPAY USER API 
</h1>

## Technoliges and packages used

AWEPAY user api is developed with PHP laravel 9.x version with Laravel passport for Token based authentication.
In additiona have used middlewares to limit the request of API endpoints and also resolve CORS issues.
These followings are the main backbone of the application:

- [PHP](https://www.php.net/)
- [Laravel](https://lumen.laravel.com/)
- [Laravel Passport](https://laravel.com/docs/9.x/passport)
- [MySQL](https://www.mysql.com/)
- [Docker](https://www.docker.com/)


## Environment Setup

To run the application along with above mentioned technologies we will require following packages to manage our repository, dependency and containarized the application.
- Git - [Download & Install Git](https://git-scm.com/downloads)
- Composer - [Download & Install Composer](https://getcomposer.org/download/).
- Docker - [Download & Install Docker](https://www.docker.com/products/docker-desktop/).


## APi Documentation

After successful start the application you may find the api documentation at following route
[Your domain name]/api/docs 

APi documentation is build with Swagger -[Swagger](https://swagger.io/). 
You may find few routes will need authorization. The beared token format should be Beared <token>


## Getting Started

```
Steps to install depencies and run the application

<!-- To setup env file -->
1. $ cp .env.local .env

<!-- for install the dependencies -->
2. $ composer install

<!-- to refresh the database & run all database seeds -->
3. $ php artisan migrate:fresh --seed

<!-- Install laravel passport to generate client ID and secret>
4. $php artisan passport:install

<!-- to generate the API documentation -->
$ php artisan l5-swagger:generate

<!-- to run the application -->
$ php artisan serve

if you want to run as docker container 

$docker-compose up -d 

```

### Unit Testing

```
For testing there is a seperate env file which consits DB driver and other credentials. You may choose Mysql, Sqlite or in memory data for testing as your preference.

<!-- to setup the testing environments -->
$ cp .env.testing .env

<!-- for install the dependencies -->
$ composer install

<!-- to run the test cases -->
$ php artisan test
```

#### deploy to server

To delploy to production or staging server the most easy and effient way to automate your deployment with github action. 

I have created deploy,sh and .yml file to automate workflow of deployment. 

