# BileMo - API Rest - Project 7 [![Codacy Badge](https://app.codacy.com/project/badge/Grade/ee36a1908151458fb8d49469834ab47c)](https://www.codacy.com/gh/nayodahl/bilemo/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=nayodahl/bilemo&amp;utm_campaign=Badge_Grade)

API Rest project for a Smartphones seller company, done for OPENCLASSROOMS, made with Symfony

## What is this Project ?

This app is made of PHP, using Symfony 5.1.
All external libraries and bundles are allowed, except FOSRestBundle. Framework API Platform is also forbidden.

Here are the rules that needed to be followed : 

* Phones resellers (our clients) should be able to browse all phones, and the detail of a phone
* They also should be able to create, delete, browse a customer, and browse a list of customers
* Authentication is needed for all these requests, here I choose JWT.
* Level 3 of Richardson model should be followed.
* Data is published in JSON.
* Responses are cached when possible.

An exemple of this app is online here : https://bilemo.nayo.cloud and can be tested

## Want to clone and test this app ?

- Clone Repository on your web server
- Install backend dependancies using Composer with dev depandancies (composer install, https://getcomposer.org/doc/01-basic-usage.md). You may need to remove composer.lock file
- Create a database on your SQL server
- Configure access to this database on .env file at source of the project (user, password, name of db, address etc..)
- Run doctrines migrations (php bin/console doctrine:migration:migrate). You can check your migration status with php bin/console doctrine:migration:status
- Load initial dataset using Datafixtures (php bin/console doctrine:fixtures:load)
- Configure JWT Lexit bundle for authentication : 
> mkdir -p config/jwt
> openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
> openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
- Rewrite your chosen passphrase in .env file

## Let's go

- Users from datafixtures : admin@bilemo.com (has admin rights), dev@phonevendor.com
- Passwords are @dmIn123

## Author

**Anthony Fachaux** - Openclassrooms Student - Dev PHP/Symfony