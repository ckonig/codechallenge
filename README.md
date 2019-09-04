# Code Challenge

## initial plan of approach

- [OK] Create HelloWorld app using Docker & Laradock
- [OK] Create Json controller & ClI app for same HelloWorld service
- [OK] Write sample mailers for SendGrid & MailJet
- [OK] Look into logging solution(s)
- [OK] Make mailers reusable by extracting the sample data and making it parametrized
- [OK] Implement AggregateMailer wrapping the common interface for the two mailers
- [OK] Find out how to deal best with request models (e.g. mail class holding all mail attributes), and automatic controller input validation
- [OK] Design & implement a basic, stubbed external interfaces (web&cli) for email service
- [OK] Connect controller & mailer directly. Manually test the synchronous mail app from both interfaces.
- Make AggregateMailer do the retry & fallback parts
- Add database, generate IDs for mails. Allow checking email status by ID via web interface.
- Look at the listener tech that is already built in to laravel app, what can it do for us?
- Look into vertical scaling of background worker with regards to queue consumption, transaction safety.
- Decide on queueing technology, add queue to the mix, split app into foreground & background application
- Look into vue.js frontend application
- Look into traffic management & gateways for horizontal scaling of web endpoint

## Additional tasks / improvements / open questions

- Make informed choice about Command implementation in Command class vs. Closure based command implementation.
- Implement basic HTML view for HelloWorld web scenario
