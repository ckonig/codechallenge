# Code Challenge

## Table of Contents

1. [todo](#todo)
1. [Important Issues](#important-issues)
1. [Choices and Revisions](#choices-and-revisions)
   1. [Laravel / Laradock](#laravel-/-laradock)
   1. [MailJet & Sendgrid connectors](#mailjet-&-sendgrid-connectors)
   1. [Artisan CLI commands](#artisan-cli-commands)
   1. [Queue & Data Storage](#queue-&-data-storage)
1. [Scalability](#scalability)
1. [Setup](#setup)
1. [Use](#use)
   1. [Web UI](#web-ui)
   1. [Console](#console)
   1. [JSON API (via Postman)](#json-api-(via-postman))
1. [Develop](#develop)

## todo

- [ ] after sending email, auto-refresh status of email (push instead of pull)
- [ ] dont forget frontend testing (component based and UI)
- [ ] bonus: implement mail detail view using routing
- [ ] Architectural review & critique

## Important Issues

- Find out how to deal with environment config and secrets. It's unacceptable to keep secrets under version control.

## Choices and Revisions

### Laravel / Laradock

I chose to use Laravel framework, because it's a good real-world way to learn working with this framework.  
Using Laradock as an out-of-the-box solution combining Docker and Laravel seemed like a great time-saver to get started.

Eventually, much of laradock's overhead was removed, and the app was reduced to its necessary dependencies and configurations.

### MailJet & Sendgrid connectors

I decided to use the composer packages provided by the mail service providers, instead of implementing an API connector for their REST Apis. Main reason here is to save time & energy.

Using the mailjet package eventually came with a downside though: Initially, I used laravel version 5.8, but I had to downgrade to 5.6 in order to use the mailjet/mailjet-laravel package. This was revised later on by creating a modified fork of the package and using the fork as dependency (However in the long term it would be better to contribute to the original repository). Using the modified fork allowed upgrading laravel/laravel to the latest version 6.

### Artisan CLI commands

I wasn't familiar with Artisan before, so in the initial planning I was unsure how to approach the requirement that the functionality of the microservice needs to be accessible through command line. After becoming more familiar with Laravel and it's possibilities, implementing the CLI commands for Artisan seemed the logical thing to do.

This turned out to be beneficial since Artisan console commands are integrated very well in Laravel, which allowed easy customization of the command and even integration testing.

### Queue & Data Storage

There is a data storage to save the emails, which makes it possible to track the status of the email, which is processed asynchronously.

Initially I used mySQL for the storage. It was interesting to work with the Eloquent ORM and leverage the power of the laravel framework. However I decided to throw away the database and to replace it with a redis based cache. The reasoning is that the requirements explicitly demand scalability and reliability, but not long term storage. Caching the email until it is processed (plus a few minutes to check the status) is sufficient and ensures the data storage doesn't fill up endlessly.

Initially I used the "database" driver for the queue as well. However, this was also later changed to use Redis, which should be easier to scale by using a cluster.

The mySQL database is now only used as a dead letter queue.

## Scalability

The requirements state that the service should be horizontally scalable. In the current solution there are limitations for that.

- The queue and data storage are running on Redis, which can be later replaced with an (external) Redis cluster for high performance scenarios.
- Laravel Horizon is used to run multiple queue workers inside one container. Horizon is started and supervised by the php-worker container, which can also be scaled up.
- The web/api application is exposed through the traefik load balancer, which will delegate the requests to the nginx instances in a round-robin approach. In a production scenario, an external load balancing service for dynamic traffic and a CDN for static content would be preferrable.
- If multiple instances of php-fpm are running, the balancing of the incoming traffic from nginx happens automatically.
- The Laradock template I started with is based on using docker-compose. But this can only deploy the containers on one host. To deploy this app in a cluster that spans multiple hosts, the .env file needs to be converted into something ```docker stack``` can understand. Otherwise the scalability stays limited to one host machine.
- mySQL is used as data storage for the dead letter queue. Under high load, this single-instance database can become a bottleneck as well, and an external, more scalable solution would be more suitable.

## Setup

```cli
//get sources
git clone https://github.com/ckonig/codechallenge
cd codechallenge

//start container
cd laradock
docker-compose up -d

//connect to workspace container and install dependencies
docker-compose exec workspace bash
composer install

//create database
php artisan migrate:fresh

//install npm dependencies & build UI
yarn install
npm install --global cross-env
yarn run dev
```

## Configure

Open .env file and edit the settings at the bottom.

```.env
MAILJET_APIKEY="your key"
MAILJET_APISECRET="your secret"
SENDGRID_API_KEY="your key"
```

Scale up

```cli
docker-compose up -d
    --scale php-worker=4
    --scale php-fpm=4
    --scale nginx=4
    nginx traefik mysql redis php-fpm php-worker
```

## Use

### Web UI

To use the web UI, open <https://localhost> in your browser.

### Console

```cli
//connect to workspace container
docker-compose exec workspace bash

//send an email
php artisan mail:send {fromName} {fromEmail} {subject} {txtContent} {htmlContent} {recipient(s)*}

//check status of an email
php artisan mail:get {id}
```

### JSON API (via Postman)

There is a Postman collection in this repository (root/postman) that can be used to test the JSON API manually.

Following requests can be made with this collection:

- POST /api/mail (to send an email defined in the request body)
- GET /api/mail/{id} (to retrieve the previously created email)
- GET /api/mail/{id}/status (to retrieve the status of the previously created email)

## Develop

### Watch and build UI

```cli
//watch and rebuild vue.js components on change
yarn run watch-poll
```

### Data Access

Open <http://localhost:9987/> to use Redis WebUI which gives access to data stored in queue and cache.

Open <http://localhost:8080> to use phpmyadmin to access the dead letter queue.

### Worker monitoring

Open <https://localhost/horizon> to use Horizon which allows monitoring of the workers.

### Logs

```docker-compose logs -f```

### Automated tests

Connect to the bash of the workspace container using ``docker-compose exec workspace bash``, then run ```phpunit```.
