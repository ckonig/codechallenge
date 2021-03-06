<?php

namespace Tests\Unit;

use App\Services\Sendgrid\SendgridMessageBuilder;
use Tests\TestCase;

class SendgridMessageBuilderTest extends TestCase
{
    /**
     * Test that data mapping from MailModel to Sendgrid data structure is complete
     */
    public function testMessageConversion()
    {
        $mail = TestData::getMail();
        $builder = new SendgridMessageBuilder();
        $converted = $builder->getMessage($mail);
        $this->assertNotNull($converted);
        $this->assertEquals($mail->fromName, $converted->getFrom()->getName());
        $this->assertEquals($mail->fromEmail, $converted->getFrom()->getEmail());
        foreach (json_decode($mail->to) as $i => $to) {
            $recipient = $converted->getPersonalizations()[0]->getTos()[$i];
            $this->assertEquals($to, $recipient->getEmail());
        }

        $this->assertEquals($mail->title, $converted->getGlobalSubject()->getSubject());
        $this->assertEquals($mail->body_txt, $this->getContent($converted, 'text/plain')->GetValue());
        $this->assertEquals($mail->body_html, $this->getContent($converted, 'text/html')->GetValue());
    }

    private function getContent(\SendGrid\Mail\Mail $email, string $type)
    {
        $contents = $email->getContents();
        foreach ($contents as $content) {
            if ($content->getType() == $type) {
                return $content;
            }
        }

        throw new Exception($type . ' content not found');
    }
}
