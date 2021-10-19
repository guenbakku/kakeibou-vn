# Kakeibou - VND currency version

## For development
### Initialize docker container
```
docker-compose build
docker-compose up

# access into cli of php container
docker-compose exec php /bin/bash

# install required php libraries
composer install

# initialize database
vendor/bin/phinx migrate -e development
```

### Access urls
1. app:  
    http://localhost:8080
2. phpMyAdmin:  
    http://localhost:4040
