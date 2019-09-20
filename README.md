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
   1. [UI test plan](#ui-test-plan)

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

### Automated tests

Connect to the bash of the workspace container using ``docker-compose exec workspace bash``, then run ```phpunit```.

### JSON API (via Postman)

There is a Postman collection in this repository (root/postman) that can be used to test the JSON API manually using the following requests:

- ``POST /api/mail`` (to send an email defined in the request body)
- ``GET /api/mail/{id}`` (to retrieve the previously created email)

Note: make sure to configure Postman to allow insecure certificates, as this solution uses a self signed certificate for "localhost".

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

### Add mailer implementations

To add a new mail service (for example mailgun) to the application:

- Implement the Interface ``App\Services\Mailer`` in a class ``MailgunMailer`` under namespace ``App\Services\Mailgun\``.
  - Make sure the implementation of ``sendMail`` accepts an instance of ``App\Model\MailModel``, converts it into the appropriate data structure and sends it to the mailgun API. The method returns true or false based on the response of the API.
  - The implementation of ``sendMail`` is allowed to throw Exceptions.
- Add the new Mailer implementation to the dependency binding in ``App\Providers\AppServiceProvider``.

## Scalability

The requirements state that the service should be horizontally scalable, which to some extent is possible with this solution, considering the following:

- The queue and data storage are running on Redis, which can be later replaced with an (external) Redis cluster for high performance scenarios.
- Laravel Horizon is used to run multiple queue workers inside one container. Horizon is started and supervised by the php-worker container, which can also be scaled up.
- The web/api application is exposed through the traefik load balancer, which will delegate the requests to the nginx instances in a round-robin approach. In a production scenario, an external load balancing service for dynamic traffic and a CDN for static content would be preferrable.
- If multiple instances of php-fpm are running, the balancing of the incoming traffic from nginx happens automatically.
- The Laradock template I started with is based on using docker-compose. But this can only deploy the containers on one host and does not support swarm mode. To deploy this app in a cluster that spans multiple hosts, the .env file needs to be converted into something that the ```docker stack``` command can understand. 
- mySQL is used as data storage for the dead letter queue. Under high load, this single-instance database can become a bottleneck as well, and an external, more scalable solution would be more suitable.

It's possible to scale up the application using docker-compose like this:

```cli
docker-compose up -d
    --scale php-worker=4
    --scale php-fpm=4
    --scale nginx=4
```

## Possible Improvements

It's clear that no piece of software is ever "100% done", unless we're talking about mathematically specified requirements. I've done my best to interpret the requirements and deliver a solution that "makes sense" in its context. Some things had to be deprioritized, as they would've taken too much time to implement, other things were not done because they weren't stated explicitly as requirements. 

As a result, this application is by no means "ready for production", and here are some bullet points that would need to be picked up if the development would continue. 

- After sending an email, the UI should auto-refresh the status of the email. This would replace the "refresh" button. Potentially this can be implemented using the laravel broadcast feature.
- The UI application could use URL routing to separate the different views from each other. This would make it easier to extend the UI later on.
- Currently the secrets are stored in the repository, which should definitely not be the case in a production setup.
- The application supports only one language, more work needs to be done before this can be used by people who don't speak english. 
- The automated tests could use an actual test email account, and check if the email has arrived as a more complete end-to-end validation. If that's not the case, the test environment that automated tests run on probably should not actually send emails. 
- The automated tests for the console application are rather minimalistic. It would be more useful if the console output would be read by the test program, so that a newly created email could be found back and the status transition from "queued" to "sent" could be asserted. 
- The application has no UI tests yet, neither component based nor end-to-end. To reach a production-ready maturity of the application, UI tests are necessary. The [UI test plan](#ui-test-plan) indicates how those tests would look like.


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

This turned out to be beneficial since Artisan console commands are integrated very well in Laravel, which allowed easy customization of the command and even integration testing (with some limitations).

### Queue & Data Storage

There is a data storage to save the emails, which makes it possible to track the status of the email, which is processed asynchronously.

Initially I used mySQL for the storage. It was interesting to work with the Eloquent ORM and leverage the power of the laravel framework. However I decided to throw away the database and to replace it with a redis based cache. The reasoning is that the requirements explicitly demand scalability and reliability, but not long term storage. Caching the email until it is processed (plus a few minutes to check the status) is sufficient and the automatic removal from the cache ensures the data storage doesn't fill up endlessly.

At first I also used the "database" driver for the queue as well. However, this was also later changed to use Redis, which should be easier to scale by using a cluster.

The mySQL database is now only used as a dead letter queue.

### UI Test Plan

I tried setting up unit tests for the UI components using Jest and I also tried setting up integration tests using Laravel Dusk, however the setups turned out to be much more tricky than expected and to make it work I would have to spend more time than reasonable. 

In a real-world situation I would search internal repositories for existing solutions and talk to colleagues to ask for help. In this artifical code-challenge situation I set myself a timeframe to not lose myself in endless research and experimentation. So at the end of the day, instead of actually writing the tests, I decided to write a test plan instead, which describes the kind of tests I would have written, had I managed to setup the UI test platform in time. 

#### Input composition and validation
- navigate to UI
- assert basic elements are present: header, search input field & button, form input elements, buttons for sending and resetting form
- try to send form without any input, assert error messages show up
- fill in one field, try to submit, vaidate the error message for that field disappeared
- enter valid email address in the newRecipient field, click "Add" button, assert address gets copied into recipients field
- repeat email address input, assert addresses are concatenated with ";" as separator.
- add an invalid email address, try to send form, assert that the invalid address is not accepted

#### Sending emails
- fill in form completely with valid data and submit
  - assert the success message shows up 
  - assert the box on the right hand of the screen shows up with status "queued" 
  - extract the ID of the message from the box
- click the "refresh" button, assert the status changed from "queued" to "sent"
- fill in form again and send a second email. 
  - assert there are now two boxes showing the two sent emails, one with status "sent" and one with status "quued"
  - extract and save the ID of the second email
  - click "refresh" button of second email, assert status changed to "sent"

#### Searching and showing Emails
- refresh the page
- search for first ID, make sure it shows up with ID and status "sent" 
- search for second ID, make sure both emails are shown
- click the "show" button of first email, assert the form disappears, and details of the first mail are shown
- click the "show" button of second email, and details of the second mail are shown
- close the detail view, assert the form is shown again

