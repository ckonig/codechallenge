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
- [ ] Review & finetune retry/backoff solution
- [x] Add database, generate IDs for every mail. Allow checking email status by ID via web / console interface and postman
- [x] Decide on queueing technology, add queue to the mix, split app into foreground & background application
  - [ ] Can we use an external queue service to increase reliablity?
- [x] Look into scaling of background worker.
  - [x] Find and document a way to run multiple instances of the worker
  - [ ] Review multi-worker solution regarding transaction safety, double consumption, concurrency. Do we need transactions?
- [x] Look into scalability of the web app
  - [x] setup working traefik with multiple nginx instances
  - [x] look into scaling of php-fpm
  - [ ] document potential scaling of the database
- [ ] Look into vue.js frontend application
  - [x] Setup skeleton app
  - [x] implement quick & dirty forms for sending mails and checking email status
  - [x] add form validation
  - [x] make UI more visually appealing
  - [ ] after sending email, auto-refresh status of email (push instead of pull)
  - [ ] dont forget frontend testing (component based and UI)
  - [ ] bonus: implement mail detail view using routing
- [ ] Architectural review & critique

## Important Issues

- Find out how to deal with environment config and secrets. It's unacceptable to keep secrets under version control.

## Choices & Revisions

### Laravel / Laradock

I chose to use Laravel framework, because it's a good real-world way to learn working with this framework.  
Using Laradock as an out-of-the-box solution combining Docker and Laravel seemed like a great time-saver to get started.

Eventually, the overhead of laradock was removed, and the app was reduced to its necessary dependencies and configurations.

### MailJet & Sendgrid connectors

I decided to use the composer packages provided by the mail service providers, instead of implementing an API connector for their REST Apis. Main reason here is to save time & energy.

Using the mailjet package eventually came with a downside though: Initially, I used laravel version 5.8, but I had to downgrade to 5.6 in order to use the mailjet/mailjet-laravel package. This had to be revised later on, therefore a modified fork of the mailjet/laravel package was created and used as dependency.

### Artisan CLI commands

I wasn't familiar with Artisan before, so in the initial planning I was unsure how to approach the requirement that the functionality of the microservice needs to be accessible through command line. After becoming more familiar with Laravel and it's possibilities, implementing the CLI commands for Artisan seemed the logical thing to do.

This turned out to be beneficial since Artisan console commands are integrated very well in Laravel, which allowed easy customization of the command and even integration testing.

### Dependency Injection

There are multiple ways to implement Dependency Injection with Laravel. I decided to keep it as simple as possible by using the reflection approach without Service Providers or Contracts, until I end up in a situation where I *need* to go a more complicated route.

### Queue & Data Storage

MySql was relatively painless to setup, which also allowed to use the database driver for the Queue. Using the Eloquent ORM allows automatic generation / migration of the database. and relieves developers from writing SQL queries manually. However, in terms of scalability this means the database has become a single point of failure, and scaling / replicating SQL databases can be quite troublesome as well - it would be preferrable to use an external queue service.

The used data structure is as flat as possible, in fact there is only one entity called "MailModel". It contains a json encoded array for the receiver email addresses. This array is intentionally not designed as a separate entity to keep the performance high.

### Scalability

The requirements state that the service should be horizontally scalable. In the current solutions there are limitations for that.

- The worker is scalable, and it's possible to run multiple instances at the same time. It remains to be researched how the database queue driver supports this scenario, and thorough review should be done as to how laravel implements transations in queue processing.
- The web/api application is exposed through the traefik load balancer, which will delegate the requests to the nginx instances in a round-robin approach. However all nginx instances appear to be using the same php-fpm instance. (this needs further investigation)
- So far the only way I managed to run the application was using docker-compose. But compose is not compatible with swarm mode, and right now I have doubts whether this can actually be deployed accross physical hosts.

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

## Development / running the app

```cli
//watch and rebuild vue.js components on change
yarn run watch-poll
```

### Automated tests

To run the automated tests, connect to the bash of the workspace container, then run ```phpunit```.

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
php artisan mail:send {fromName} {fromEmail} {subject} {txtContent} {htmlContent} {recipient(s)*}

//check status of an email
php artisan mail:get {id}
```

### Web UI

To use the web UI, open 
