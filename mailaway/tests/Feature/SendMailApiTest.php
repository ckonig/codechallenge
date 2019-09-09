<?php

namespace Tests\Feature;

use Tests\TestCase;

class SendMailApiTest extends TestCase
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
            'itckoenig@gmail.com',
        ],
    ];

    public function testHappyFlow()
    {
        $body = $this->data;
        $postresponse = $this->json('POST', '/api/mail', $body);
        $postresponse->assertStatus(200);
        $postcontent = json_decode($postresponse->getContent());
        $id = $postcontent->id;
        $status = $postcontent->status;
        $this->assertNotNull($id);

        $getresponse = $this->json('GET', '/api/mail/' . $id);
        $getresponse->assertStatus(200);
        $getcontent = json_decode($getresponse->getContent());
        $this->assertEquals('queued', $getcontent->status);

        sleep(5);

        $getresponse2 = $this->json('GET', '/api/mail/' . $id);
        $getresponse2->assertStatus(200);
        $getcontent2 = json_decode($getresponse2->getContent());

        $this->assertEquals('sent', $getcontent2->status);
        $this->assertEquals($id, $getcontent2->id);
        $this->assertEquals($body['from']['name'], $getcontent2->fromName);
        $this->assertEquals($body['from']['email'], $getcontent2->fromEmail);
        $this->assertEquals($body['title'], $getcontent2->title);

        //@todo this manual decoding looks wrong
        $this->assertEquals($body['body_txt'], json_decode($getcontent2->body_txt));
        $this->assertEquals($body['body_html'], json_decode($getcontent2->body_html));

        $getresponse3 = $this->json('GET', '/api/mail/' . $id . '/status');
        $getresponse3->assertStatus(200);
        $getcontent3 = json_decode($getresponse3->getContent());
        $this->assertEquals('sent', $getcontent3->status);
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
