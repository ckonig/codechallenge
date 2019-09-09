<?php

namespace Tests\Unit;

use App\Services\AggregateMailer;
use App\Services\MailjetMailer;
use App\Services\SendgridMailer;
use App\Models\MailModel;
use Mockery;
use Tests\TestCase;

class AggregateMailerTest extends TestCase
{
    public function testAggregateMailerReturnsFalseIfAllMailersDontWork()
    {
        $mailjet = $this->getMailjetMailer(function ($mock) {
            $mock->shouldReceive(['sendMail' => false])->once();
        });
        $sendgrid = $this->getSendgridMailer(function ($mock) {
            $mock->shouldReceive(['sendMail' => false])->once();
        });
        $aggregateMailer = new AggregateMailer($mailjet, $sendgrid);
        $result = $aggregateMailer->sendMail(new MailModel());
        $this->assertFalse($result);
    }

    public function testAggregateMailerReturnsTrueIfMailjetMailerWorks()
    {
        $mailjet = $this->getMailjetMailer(function ($mock) {
            $mock->shouldReceive(['sendMail' => true]);
        });
        $sendgrid = $this->getSendgridMailer(function ($mock) {
            $mock->shouldReceive(['sendMail' => false]);
        });
        $aggregateMailer = new AggregateMailer($mailjet, $sendgrid);
        $result = $aggregateMailer->sendMail(new MailModel());
        $this->assertTrue($result);
    }

    public function testAggregateMailerReturnsTrueIfSendgridMailerWorks()
    {
        $mailjet = $this->getMailjetMailer(function ($mock) {
            $mock->shouldReceive(['sendMail' => false]);
        });
        $sendgrid = $this->getSendgridMailer(function ($mock) {
            $mock->shouldReceive(['sendMail' => true]);
        });
        $aggregateMailer = new AggregateMailer($mailjet, $sendgrid);
        $result = $aggregateMailer->sendMail(new MailModel());
        $this->assertTrue($result);
    }

    public function testAggregateMailerReturnFalseIfBothMailersThrowExceptions()
    {
        $mailjet = $this->getMailjetMailer(function ($mock) {
            $mock->shouldReceive('sendMail')->andThrow('Exception');
        });
        $sendgrid = $this->getSendgridMailer(function ($mock) {
            $mock->shouldReceive('sendMail')->andThrow('Exception');
        });
        $aggregateMailer = new AggregateMailer($mailjet, $sendgrid);
        $result = $aggregateMailer->sendMail(new MailModel());
        $this->assertFalse($result);
    }

    private function getMailjetMailer($func)
    {
        return $this->instance(MailjetMailer::class, Mockery::mock(MailjetMailer::class, $func));
    }

    private function getSendgridMailer($func)
    {
        return $this->instance(SendgridMailer::class, Mockery::mock(SendgridMailer::class, $func));
    }

    //@todo test logging
    //@todo implement and test retry mechanism
    //@todo implement and test backoff strategy
}
