# Code Challenge

## initial plan of approach / status

- [x] Create HelloWorld app using Docker & Laradock
- [x] Create Json controller & ClI app for same HelloWorld service
- [x] Write sample mailers for SendGrid & MailJet
- [x] Look into logging solution(s)
- [x] Make mailers reusable by extracting the sample data and making it parametrized
- [x] Implement AggregateMailer wrapping the common interface for the two mailers
- [x] Find out how to deal best with request models (e.g. mail class holding all mail attributes), and automatic controller input validation
- [x] Design & implement a basic, stubbed external interfaces (web&cli) for email service
- [x] Connect controller & mailer directly. Manually test the synchronous mail app from both interfaces.
- [x] Remove HelloWorld remnants
- [x] Update static CLI mail command to take input from console
- [x] Make AggregateMailer do the retry & backoff parts
- [x] Add database, generate IDs for every mail. Allow checking email status by ID via web / console interface and postman
- [x] Decide on queueing technology, add queue to the mix, split app into foreground & background application
- [ ] Look into vertical scaling of background worker with regards to queue consumption, transaction safety.
- [ ] Look into vue.js frontend application
- [ ] Look into traffic management & gateways for horizontal scaling of web endpoint

## Important Issues

- Find out how to deal with environment config and secrets. It's unacceptable to keep secrets under version control.

## Choices & Revisions

### Laravel / Laradock

I chose to use Laravel framework, because it's a good real-world way to learn working with this framework. Initially, I used version 5.8, but I had to downgrade to 5.6 in order to use the mailjet/mailjet-laravel package. Using Laradock as a solution for combining Docker and Laravel seemed like a great time-saver to get started.

### HelloWorld approach

Since most technology choices were unfamiliar to me, I decided to implement a simple HelloWorld scenario at first. This allowed me to get familiar with the technology stack, while not losing time on the details of the actual domain logic.

### MailJet & Sendgrid connectors

I decided to use the composer packages provided by the mail providers, instead of implementing an API connector for their REST Apis. Main reason here is to save time & energy. The packages I used were

```json
"mailjet/laravel-mailjet": "^2.0",
"sendgrid/sendgrid": "^7.3"
```

There is also a SendGrid connector that directly ties into the Laravel Mailer, but this would conflict with the requirement that the Laravel Mailer shouldn't be used.

### Artisan CLI commands

I wasn't familiar with Artisan before, so in the initial planning I was unsure how to approach the requirement that the functionality of the microservice needs to be accessible through command line. After becoming more familiar with Laravel and it's possibilities, implementing the CLI commands for Artisan seemed the logical thing to do.

This turned out to be beneficial since Artisan console commands are integrated very well in Laravel, which allowed easy customization of the command and even integration testing.

### Dependency Injection

There are multiple ways to implement Dependency Injection with Laravel. I decided to keep it as simple as possible by using the reflection approach without Service Providers or Contracts, until I end up in a situation where I *need* to go a more complicated route.

### Queue & Data Storage

MySql was relatively painless to setup, which also allowed to use the database driver for the Queue. Using the Eloquent ORM allows automatic generation / migration of the database. and relieves developers from writing SQL queries manually.

The used data structure is as flat as possible, in fact there is only one entity called "MailModel". It contains a json encoded array for the receiver email addresses. This array is intentionally not designed as a separate entity to keep the performance high.

## Setup

```cli
//get sources
git clone https://github.com/ckonig/codechallenge
cd codechallenge
git submodule init
git submodule update

//setup configuration
cp .\mailaway\setup\laradock.env .\laradock\.env
cp .\mailaway\setup\default.conf .\laradock\nginx\sites\default.conf
cp .\mailaway\setup\mailaway.env .\mailaway\.env

//start container
cd laradock
docker-compose up -d nginx mysql

//connect to workspace container and install dependencies
docker-compose exec workspace bash
composer install
```

## Running the app

```cli
//start the queue worker in a separate terminal
docker-compose exec workspace bash
php artisan queue:work database

//tail the logs in a separate terminal
docker-compose exec workspace bash
tail -f storage/logs/laravel-[current_date].log
```

## Testing

### Automated tests

To run the automated tests, connect to the bash of the workspace container, then run ```phpunit```. 

Note: the queue worker needs to be running for the tests to pass.

### Postman & JSON API

There is a Postman collection in this repository (root/postman) that can be used to test the JSON API manually.

Following requests can be made with this collection:

- POST /api/mail (to send an email defined in the request body)
- GET /api/mail/{id} (to retrieve a previously created email)
- GET /api/mail/{id}/status (to retrieve the status of a previously created email)

### Console

```cli
//connect to workspace container
docker-compose exec workspace bash

//send an email
php artisan mail:send {senderName} {senderEmail} {subject} {txtContent} {htmlContent} {recipient(s)*}

//check status of an email
php artisan mail:get {id}
```
