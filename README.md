# Code Challenge

## initial plan of approach

- [OK] Create HelloWorld app using Docker & Laradock
- [OK] Create Json controller & ClI app for same HelloWorld service
- Design & implement a basic, stubbed external interfaces (web&cli) for email service
- Find out how to deal best with domain/view/persistence models (e.g. mail class holding all mail attributes), think about automatic controller input validation
- Write mailers for SendGrid & MailJet
- Implement AggregateMailer that will do the retry & fallback parts
- Connect controller & mailer directly. Manually test the synchronous mail app from both interfaces.
- Look into logging solution(s)
- Add database, generate IDs for mails. Allow checking email status by ID via web interface.
- Look at the listener tech that is already built in to laravel app, what can it do for us?
- Look into vertical scaling of background worker with regards to queue consumption, transaction safety.
- Decide on queueing technology, add queue to the mix, split app into foreground & background application
- Look into vue.js frontend application
- Look into traffic management & gateways for horizontal scaling of web endpoint

## Additional tasks / improvements

- Find out how to properly split ServiceProvider and Service implementation into separate folders.
- Make informed choice about Command implementation in Command class vs. Closure based command implementation.
- Implement basic HTML view for HelloWorld web scenario
