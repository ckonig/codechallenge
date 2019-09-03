# Code Challenge 

## initial plan of approach

- create HelloWorld app using Docker & Laradock
- create Json controller & ClI app for same HelloWorld service
- look into logging solution
- design & implement a basic, stubbed external interfaces (web&cli) for email service
- write mailer for SendGrid & MailJet
- implement AggregateMailer that will do the retry & fallback parts
- connect controller & mailer directly. Manually test the synchronous mail app from both interfaces.
- add database, generate IDs for mails. Allow checking email status by ID via web interface.
- Look at the listener tech that is already built in to laravel app, what can it do for us?
- look into vertical scaling of background worker with regards to queue consumption, transaction safety.
- decide on queueing technology, add queue to the mix, split app into foreground & background application
- look into vue.js frontend application
- look into traffic management & gateways for horizontal scaling of web endpoint
