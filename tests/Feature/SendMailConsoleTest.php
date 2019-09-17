<?php

namespace Tests\Feature;

use Tests\TestCase;

class SendMailConsoleTest extends TestCase
{
    public function testConsole()
    {
        $payload = [
            'fromName' => 'Christian',
            'fromEmail' => "itckoenig@gmail.com",
            'title' => "Console Stuff",
            'txt' => "Hooraay",
            'html' => "<h1> Hooraaay</h1>",
            'toEmail' => ['itckoenig@gmail.com'],
        ];
        $this->artisan('mail:send', $payload)
            ->expectsOutput('Created Mail.')
            ->expectsOutput('Status: queued')
            ->assertExitCode(0)
            ->run();

        //@todo use randomized test data
        //@todo test mapping of input data by reading console output (how?)
        //@todo use ID to retrieve created mail using mail:get command
        //@todo advanced: use test email accounts as recipients and actually check if email arrived
    }
}
