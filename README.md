#There might be some errors on the site because I just did it in 2 days and learn a lot of new technique like symfony, nelmio
#set up
install git, docker on local machine <br/>
clone the project

#run the docker
cd qa-api <br/>
docker-compose up -d

#check to see if docker is running
docker ps

#pull the library for symfony
#open the terminal in qa-api folder
docker-compose exec php-fm bash<br/>
composer update<br/>

#run the db migration inside php-fpm container
php bin/console make:migration<br/>
php bin/console doctrine:migrations:migrate<br/>

#run the app for nelmio app
http://localhost:8000/api/doc<br/>

#use postman or any REST Client to test the qa API





