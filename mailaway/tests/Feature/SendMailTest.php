<?php

namespace Tests\Feature;

use Tests\TestCase;

class SendMailTest extends TestCase
{
    private $data = [
        'title' => 'Sent from Feature test via Mailaway',
        'body_txt' => 'You have received an email from the Mailaway service',
        'body_html' => '<h1> YAY </h1> Mailaway works fine',
        'from' => [
            'name' => 'Mailaway Service',
            'email' => 'noreply@mailaway.com',
        ],
        'to' => [
            0 => [
                'name' => 'Christian koenig',
                'email' => 'itckoenig@gmail.com',
            ],
        ],
    ];

    public function testPostWithCompleteDataReturns200()
    {
        $body = $this->data;
        $response = $this->json('POST', '/api/mail', $body);
        $response->assertStatus(200);
    }

    public function testPostWithMissingSubjectReturns422()
    {
        $body = $this->data;
        unset($body['title']);
        $response = $this->json('POST', '/api/mail', $body);
        $response->assertStatus(422);
    }

    public function testPostWithMissingTxtBodyReturns422()
    {
        $body = $this->data;
        unset($body['body_txt']);
        $response = $this->json('POST', '/api/mail', $body);
        $response->assertStatus(422);
    }

    public function testPostWithMissingHtmlBodyReturns422()
    {
        $body = $this->data;
        unset($body['body_html']);
        $response = $this->json('POST', '/api/mail', $body);
        $response->assertStatus(422);
    }

    public function testPostWithMissingSenderReturns422()
    {
        $body = $this->data;
        unset($body['from']);
        $response = $this->json('POST', '/api/mail', $body);
        $response->assertStatus(422);
    }

    public function testPostWithMissingRecipientReturns422()
    {
        $body = $this->data;
        unset($body['to']);
        $response = $this->json('POST', '/api/mail', $body);
        $response->assertStatus(422);
    }
}
