<?php

namespace Tests\Unit;

use App\Services\MailjetMessageBuilder;
use Tests\TestCase;

class MailjetMessageBuilderTest extends TestCase
{
    /**
     * Test that data mapping from MailModel to Mailjet data structure is complete
     */
    public function testMessageConversion()
    {
        $mail = TestData::getMail();
        $builder = new MailjetMessageBuilder();
        $converted = $builder->getMessage($mail);
        $this->assertNotNull($converted);
        $this->assertNotNull($converted['body']);
        $this->assertNotNull($converted['body']['Messages']);
        $this->assertNotNull($converted['body']['Messages'][0]);
        $this->assertNotNull($converted['body']['Messages'][0]);
        $this->assertEquals($mail->fromName, $converted['body']['Messages'][0]['FromName']);
        $this->assertEquals($mail->fromEmail, $converted['body']['Messages'][0]['FromEmail']);
        $this->assertEquals($mail->title, $converted['body']['Messages'][0]['Subject']);
        $this->assertEquals(2, count($converted['body']['Messages'][0]['Recipients']));
        foreach (json_decode($mail->to) as $i => $to) {
            $recipient = $converted['body']['Messages'][0]['Recipients'][$i];
            $this->assertEquals($to, $recipient['Email']);
        }

        $this->assertEquals($mail->body_txt, $converted['body']['Messages'][0]['Text-Part']);
        $this->assertEquals($mail->body_html, $converted['body']['Messages'][0]['HTML-Part']);
    }
}
