# Kakeibou - Phiên bản VNĐ
Clone từ app kakeibou, phiên bản VNĐ

## For development
### Initialize docker container
```
docker-compose build
docker-compose up

# in cli of php container
# install required php libraries
composer install

# in cli of php container
# initialize database
vendor/bin/phinx migrate -e development
```

## For deployment
