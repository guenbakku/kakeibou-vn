# Kakeibou - VND currency version

## Development memo
### Initialize docker container
```
docker-compose build
docker-compose up

# access into cli of php container
docker-compose exec php /bin/bash

# install required php libraries
composer install

# copy .env.example to .env
cp .env.example .env

# migrate database
vendor/bin/phinx migrate
```

### Access urls
1. app:  
    http://localhost:8080

    #### Initial login accounts

    NOTE: For development only. Remember to change this value to something appropriate for production.

    | # | username | password |
    | --- | --- | --- |
    | 1 | user1 | pass1234 |
    | 2 | user2 | pass1234 |

2. phpMyAdmin:  
    http://localhost:4040  
    (login not required)
