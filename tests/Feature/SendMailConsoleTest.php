<?php

namespace Tests\Feature;

use Tests\TestCase;

class SendMailConsoleTest extends TestCase
{
    public function testConsole()
    {
        $exitCode = $this->artisan('mail:send', [
            'fromName' => 'Christian',
            'fromEmail' => "itckoenig@gmail.com",
            'title' => "Console Stuff",
            'txt' => "Hooraay",
            'html' => "<h1> Hooraaay</h1>",
            'toEmail' => ['itckoenig@gmail.com'],
        ]);
        $this->assertEquals(0, $exitCode);
    }
}
