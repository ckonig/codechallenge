# Code Challenge

## initial plan of approach

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
- [ ] Update static CLI mail command to take input from console (or file?)
- [ ] Make AggregateMailer do the retry & fallback parts
- [ ] Add database, generate IDs for mails. Allow checking email status by ID via web interface.
- [ ] Look at the listener tech that is already built in to laravel app, what can it do for us?
- [ ] Look into vertical scaling of background worker with regards to queue consumption, transaction safety.
- [ ] Decide on queueing technology, add queue to the mix, split app into foreground & background application
- [ ] Look into vue.js frontend application
- [ ] Look into traffic management & gateways for horizontal scaling of web endpoint

## Additional tasks / improvements / open questions

- Make informed choice about Command implementation in Command class vs. Closure based command implementation.
- How simple can Dependency Injection be, and how complex should it be? Do I need providers?
- Find out how to put environment config under version control - currently it's just on local laradock git submodule.
- Find proper way to deal with secrets.

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

## Setup

```cli
git clone https://github.com/ckonig/codechallenge
cd codechallenge
git submodule init
git submodule update
cd laradock

//@todo magic to restore .env config

docker-compose up -d nginx mysql
```

## Manual testing

### JSON API

There is a Postman collection in this repository (root/postman) that can be used to test the JSON API manually.

Following requests can be made with this collection:

- POST /api/mail (to send an email defined in the request body)

### Console

To send a (static) sample mail, you can use the sample Artisan command

```cli
docker-compose exec workspace bash // connect to bash in workspace
php artisan aggregatemailsample
```
