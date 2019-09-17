<?php

namespace Tests\Unit;

use App\Models\MailModel;
use App\Services\AggregateMailer;
use App\Services\Mailer;
use Mockery;
use Tests\TestCase;

class AggregateMailerTest extends TestCase
{
    public function testAggregateMailerReturnsFalseIfAllMailersDontWork()
    {
        $a = $this->getMockMailer(function ($mock) {
            $mock->shouldReceive(['sendMail' => false])->once();
            $mock->shouldReceive(['getName' => 'a']);
        });
        $b = $this->getMockMailer(function ($mock) {
            $mock->shouldReceive(['sendMail' => false])->once();
            $mock->shouldReceive(['getName' => 'b']);
        });
        $aggregateMailer = new AggregateMailer([$a, $b]);
        $result = $aggregateMailer->sendMail(new MailModel());
        $this->assertFalse($result);
    }

    public function testAggregateMailerReturnsTrueIfMailjetMailerWorks()
    {
        $a = $this->getMockMailer(function ($mock) {
            $mock->shouldReceive(['sendMail' => true]);
            $mock->shouldReceive(['getName' => 'a']);
        });
        $b = $this->getMockMailer(function ($mock) {
            $mock->shouldReceive(['sendMail' => false]);
            $mock->shouldReceive(['getName' => 'b']);
        });
        $aggregateMailer = new AggregateMailer([$a, $b]);
        $result = $aggregateMailer->sendMail(new MailModel());
        $this->assertTrue($result);
    }

    public function testAggregateMailerReturnsTrueIfSendgridMailerWorks()
    {
        $a = $this->getMockMailer(function ($mock) {
            $mock->shouldReceive(['sendMail' => false]);
            $mock->shouldReceive(['getName' => 'a']);
        });
        $b = $this->getMockMailer(function ($mock) {
            $mock->shouldReceive(['sendMail' => true]);
            $mock->shouldReceive(['getName' => 'b']);
        });
        $aggregateMailer = new AggregateMailer([$a, $b]);
        $result = $aggregateMailer->sendMail(new MailModel());
        $this->assertTrue($result);
    }

    public function testAggregateMailerReturnFalseIfBothMailersThrowExceptions()
    {
        $a = $this->getMockMailer(function ($mock) {
            $mock->shouldReceive('sendMail')->andThrow('Exception');
            $mock->shouldReceive(['getName' => 'a']);
        });
        $b = $this->getMockMailer(function ($mock) {
            $mock->shouldReceive('sendMail')->andThrow('Exception');
            $mock->shouldReceive(['getName' => 'b']);
        });
        $aggregateMailer = new AggregateMailer([$a, $b]);
        $result = $aggregateMailer->sendMail(new MailModel());
        $this->assertFalse($result);
    }

    private function getMockMailer($func)
    {
        return $this->instance('Mailer', Mockery::mock('Mailer', $func));
    }
}
