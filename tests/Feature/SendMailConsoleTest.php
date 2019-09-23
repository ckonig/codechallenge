<?php

namespace Tests\Feature;

use Tests\TestCase;

class SendMailConsoleTest extends TestCase
{
    public function testConsole()
    {
        $payload = [
            'fromName' => 'Mailaway Console Test',
            'fromEmail' => env('TEST_SENDER'),
            'title' => 'Console Stuff',
            'txt' => 'This is a test email',
            'html' => '<h1>Test</h1><p>This is a test email</p>',
            'toEmail' => [env('TEST_RECEIVER')],
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
