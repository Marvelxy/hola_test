## What I didn't cover much
* Tests: PHPUnit + code coverage >= 80
* Checks: PHPMD, PHP_CodeSniffer

## Setup
Clone the app and **cd** into the directory

##### Install dependencies:

    $ composeer install

##### Build the code:
    $ docker-compose build

##### Start the app:
    $ docker-compose up
Subsequently, if you run into permission issues trying to start the app, add __sudo__ to the **docker-compose build**:

    $ sudo docker-compose build

##### Setup the database:
Open a new terminal and do this:

##### Delete database if it exists:

    $ docker-compose exec app php bin/console doctrine:database:drop --force

###### Create a fresh database:
    $ docker-compose exec app php bin/console doctrine:database:create

######  Migrate database:
    $ docker-compose exec app php bin/console doctrine:migrations:migrate

###### Seed database with an admin:
    $ docker-compose exec app php bin/console doctrine:fixtures:load

Admin details:

    username: admin
    password: adminpassword

##### Visit the app:
Visit http://localhost:8000/

## Usage:
The REST API for the user entity is at:

    /users

It is secured with basic HTTP Authentication. The admin details would work both on the web login form and the API basic auth.

### API Routes:

**Create user**:

    Method: POST
    Route: /users

    Example body:
    {
        "name": "Noob Saibot",
        "username": "Noob",
        "role": "PAGE_2",
        "password": "noobpassword"
    }

**Get users**:

    Method: GET
    Route: /users

**Get user**:

    Method; GET
    Route: /users/{id}

    # Example:

    /users/4

**Update user**:

    Method: PUT
    Route: /users/{id}
    Example body:
    {
        "name": "Noob Saibot",
        "username": "Noob",
        "role": "PAGE_2",
        "password": "noobpassword"
    }

    # Example:

    /users/4
    Body:
    {
        "name": "Noob Saibot",
        "username": "Noob",
        "role": "PAGE_2",
        "password": "noobpassword"
    }


**Delete user**:

    Method: DELETE
    Route: /users/{id}

    # Example:

    /users/4

### Web Routes:
I added a dashboard with nav.

    Root page: /
    PAGE_1   : /page/1
    PAGE_2   : /page/2

##### Tests and checks:
**Create test database**:

    $ docker-compose exec app php bin/console doctrine:database:create --env test

**Migrate the tes database**:

    $docker-compose exec app php bin/console doctrine:migrations:migrate --env test

**Seed the test database**:

    $ docker-compose exec app php bin/console doctrine:fixtures:load --env test

**Run tests**:

    $ docker-compose exec -u 1000 app php bin/phpunit

**Check test coverage**:
    $ docker-compose exec app phpdbg -qrr ./vendor/bin/phpunit --coverage-text
