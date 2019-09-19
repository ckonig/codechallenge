# Mailaway: Code Challenge

## Contents

1. [Setup](#setup)
1. [Use](#use)
   1. [Web UI](#web-ui)
   1. [Console](#console)
   1. [JSON API (via Postman)](#json-api-(via-postman))
1. [Develop](#develop)
1. [Scalability](#scalability)
1. [Possible Improvements](#possible-improvements)
1. [Choices and Revisions](#choices-and-revisions)
   1. [Laravel / Laradock](#laravel-/-laradock)
   1. [Mail API connectors](#mail-api-connectors)
   1. [CLI commands](#cli-commands)
   1. [Queue & Data Storage](#queue-&-data-storage)

## Setup


Get sources
```cli
git clone https://github.com/ckonig/codechallenge
cd codechallenge
```

Start container

```cli
cd laradock
docker-compose up -d
```

Connect to workspace container and install dependencies

```cli
docker-compose exec workspace bash
composer install
```

Create database

```cli
php artisan migrate:fresh
```

Install npm dependencies & build UI

```cli
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

## Use

### Web UI

To use the web UI, open <https://localhost> in your browser.

### Console

To run the application from the console, first connect to the workspace container

```cli
docker-compose exec workspace bash
```

Send an email

```cli
php artisan mail:send {fromName} {fromEmail} {subject} {txtContent} {htmlContent} {recipient(s)*}
```

Check the status of a previously sent email

```cli
php artisan mail:get {id}
```

### JSON API (via Postman)

There is a Postman collection in this repository (root/postman) that can be used to test the JSON API manually using the following requests:

- ``POST /api/mail`` (to send an email defined in the request body)
- ``GET /api/mail/{id}`` (to retrieve the previously created email)

## Develop

### Watch and build UI

```cli
yarn run watch-poll
```

### Data Access

Open <http://localhost:9987> to use Redis WebUI which gives access to data stored in queue and cache. Find the appropriate login data in laradock/.env file. 

Open <http://localhost:8080> to use phpmyadmin to access the dead letter queue. Find the appropriate login data in laradock/.env file. 

### Monitor workers

Open <https://localhost/horizon> to use the Horizon UI which allows monitoring of the workers.

### View Logs

```docker-compose logs -f```

### Automated tests

Connect to the bash of the workspace container using ``docker-compose exec workspace bash``, then run ```phpunit```.

### Add mailer implementations

To add a new mail service (for example mailgun) to the application:

- Implement the Interface ``App\Services\Mailer`` in a class ``MailgunMailer`` under namespace ``App\Services\Mailgun\``.
  - Make sure the implementation of ``sendMail`` accepts an instance of ``App\Model\MailModel``, converts it into the appropriate data structure and sends it to the mailgun API. It returns true or false based on the response.
  - The implementation of ``sendMail`` is allowed to throw Exceptions.
- Add the Mailer implementation to the dependency binding in ``App\Providers\AppServiceProvider``.

## Scalability

The requirements state that the service should be horizontally scalable, which to some extent is possible with this solution, considering the following:

- The queue and data storage are running on Redis, which can be later replaced with an (external) Redis cluster for high performance scenarios.
- Laravel Horizon is used to run multiple queue workers inside one container. Horizon is started and supervised by the php-worker container, which can also be scaled up.
- The web/api application is exposed through the traefik load balancer, which will delegate the requests to the nginx instances in a round-robin approach. In a production scenario, an external load balancing service for dynamic traffic and a CDN for static content would be preferrable.
- If multiple instances of php-fpm are running, the balancing of the incoming traffic from nginx happens automatically.
- The Laradock template I started with is based on using docker-compose. But this can only deploy the containers on one host. To deploy this app in a cluster that spans multiple hosts, the .env file needs to be converted into something ```docker stack``` can understand. Otherwise the scalability stays limited to one host machine.
- mySQL is used as data storage for the dead letter queue. Under high load, this single-instance database can become a bottleneck as well, and an external, more scalable solution would be more suitable.

It's possible to scale up the application using docker-compose like this:

```cli
docker-compose up -d
    --scale php-worker=4
    --scale php-fpm=4
    --scale nginx=4
    nginx traefik mysql redis php-fpm php-worker
```

## Possible Improvements

- After sending an email, the UI should auto-refresh the status of the email. This would replace the "refresh" button. Potentially this can be implemented using the laravel broadcast feature.
- The application has no UI tests yet, neither component based nor end-to-end. I tried setting up Laravel Dusk as a solution to implement integration tests, but couldn't find a way to make ChromeDriver accept the self-signed certificate within reasonable time limits.
- The UI application could use URL routing to separate the different views from each other. This would make it easier to extend the UI later on.
- Currently the secrets are stored in the repository, which should definitely not be the case in a production setup.
- The automated tests could use an actual test email account, and check if the email has arrived as a more complete end-to-end validation. If that's not the case, the test environment that automated tests run on probably should not actually send emails. 


## Choices and Revisions

### Laravel / Laradock

The Laravel framework was chosen, because it's a good real-world way to learn working with this framework.  

Using Laradock as an out-of-the-box solution combining Docker and Laravel seemed like a great time-saver to get started.
Eventually, much of laradock's overhead was removed, and the app was reduced to its necessary dependencies and configurations.

### Mail API connectors

Instead of directly using the REST APIs of the mail services, I decided to use the composer packages provided by the mail service providers. Main reason here was to save time & energy.

Using the mailjet package however came with a downside: Initially, I used laravel version 5.8, but I had to downgrade to 5.6 in order to use the mailjet/mailjet-laravel package. This was revised later on by creating a [modified fork](https://github.com/ckonig/laravel-mailjet) of the package and using the fork as dependency (However in the long term it would be better to contribute to the original repository). Using the modified fork then allowed upgrading laravel framework to the latest version 6.

### CLI commands

I wasn't familiar with Artisan before, so in the initial planning I was unsure how to approach the requirement that the functionality of the microservice needs to be accessible through command line. After becoming more familiar with Laravel and it's possibilities, implementing the CLI commands for Artisan seemed the logical thing to do.

This turned out to be beneficial since Artisan console commands are integrated very well in Laravel, which allowed easy customization of the command and even integration testing.

### Queue & Data Storage

There is a data storage to save the emails, which makes it possible to track the status of the email, which is processed asynchronously.

Initially I used mySQL for the storage. It was interesting to work with the Eloquent ORM and leverage the power of the laravel framework. However I decided to throw away the database and to replace it with a redis based cache. The reasoning is that the requirements explicitly demand scalability and reliability, but not long term storage. Caching the email until it is processed (plus a few minutes to check the status) is sufficient and the automatic removal from the cache ensures the data storage doesn't fill up endlessly.

At first I also used the "database" driver for the queue as well. However, this was also later changed to use Redis, which should be easier to scale by using a cluster.

The mySQL database is now only used as a dead letter queue.

