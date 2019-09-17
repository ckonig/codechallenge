<?php

namespace Tests\Unit;

use App\Jobs\SendMailJob;
use App\Models\MailRequest;
use App\Services\AggregateMailer;
use App\Services\SendMailCacheService;
use App\Services\SendMailQueueService;
use Mockery;
use Tests\TestCase;

class SendMailJobTest extends TestCase
{
    /**
     * Test that a mail is set to status='sent' after it was sent without problems
     */
    public function testBestCaseScenario()
    {
        $mail = TestData::getMail();
        $request = new MailRequest($mail);
        $id = 'foo-bar';
        $request->id = $id;
        $job = new SendMailJob($request);

        $aggregateMailer = $this->instance(AggregateMailer::class, Mockery::mock(AggregateMailer::class, function ($mock) use ($mail) {
            $mock->shouldReceive(['sendMail' => 1])->with($mail)->andReturn(true)->once();
        }));
        $sendMailCacheService = $this->instance(SendMailCacheService::class, Mockery::mock(SendMailCacheService::class, function ($mock) use ($id, $mail) {
            $mock->shouldReceive(['retrieve' => $id])->andReturn($mail)->once();
            $mock->shouldReceive(['insertOrUpdate' => $mail])->withArgs(function ($entity) {return $entity->status == 'sent';})->once();
        }));
        $sendMailQueueService = $this->instance(SendMailQueueService::class, Mockery::mock(SendMailQueueService::class, function ($mock) {
            $mock->shouldReceive(['dispatchMailRequest'])->never();
        }));

        $result = $job->handle($aggregateMailer, $sendMailCacheService, $sendMailQueueService);
    }

    /**
     * Test that a mail is enqueued again if sending doesn't work and the delay increases exponentially each time
     */
    public function testRetryCase()
    {
        $mail = TestData::getMail();

        $id = 'foo-bar-2';

        $aggregateMailer = $this->instance(AggregateMailer::class, Mockery::mock(AggregateMailer::class, function ($mock) use ($mail) {
            $mock->shouldReceive(['sendMail' => 1])->with($mail)->andReturn(false)->times(3);
        }));
        $sendMailCacheService = $this->instance(SendMailCacheService::class, Mockery::mock(SendMailCacheService::class, function ($mock) use ($id, $mail) {
            $mock->shouldReceive(['retrieve' => $id])->andReturn($mail)->times(3);
            $mock->shouldReceive(['insertOrUpdate' => $mail])->withArgs(function ($mail, $ttl) {return $mail->attempt == 1 && $mail->status == 'retry' && $ttl == 4;})->once();
            $mock->shouldReceive(['insertOrUpdate' => $mail])->withArgs(function ($mail, $ttl) {return $mail->attempt == 2 && $mail->status == 'retry' && $ttl == 8;})->once();
            $mock->shouldReceive(['insertOrUpdate' => $mail])->withArgs(function ($mail, $ttl) {return $mail->attempt == 3 && $mail->status == 'retry' && $ttl == 16;})->once();
        }));
        $sendMailQueueService = $this->instance(SendMailQueueService::class, Mockery::mock(SendMailQueueService::class, function ($mock) {
            $mock->shouldReceive(['dispatchMailRequest' => null])->withArgs(function ($mail, $ttl) {return $mail->attempt == 1 && $ttl == 4;})->once();
            $mock->shouldReceive(['dispatchMailRequest' => null])->withArgs(function ($mail, $ttl) {return $mail->attempt == 2 && $ttl == 8;})->once();
            $mock->shouldReceive(['dispatchMailRequest' => null])->withArgs(function ($mail, $ttl) {return $mail->attempt == 3 && $ttl == 16;})->once();
        }));

        $request1 = new MailRequest($mail);
        $request1->id = $id;
        $job = new SendMailJob($request1);
        $job->handle($aggregateMailer, $sendMailCacheService, $sendMailQueueService);

        $mail->attempt = 1;
        $request2 = new MailRequest($mail);
        $request2->id = $id;
        $job = new SendMailJob($request2);
        $job->handle($aggregateMailer, $sendMailCacheService, $sendMailQueueService);

        $mail->attempt = 2;
        $request3 = new MailRequest($mail);
        $request3->id = $id;
        $job = new SendMailJob($request3);
        $job->handle($aggregateMailer, $sendMailCacheService, $sendMailQueueService);
    }

    /**
     * Test that a mail is set to status='cancelled' and not enqueued again if no retries are left
     */
    public function testAbortCase()
    {
        $mail = TestData::getMail();
        $request = new MailRequest($mail);
        $id = 'foo-bar-2';
        $request->id = $id;
        $mail->attempt = 100;
        $job = new SendMailJob($request);

        $aggregateMailer = $this->instance(AggregateMailer::class, Mockery::mock(AggregateMailer::class, function ($mock) use ($mail) {
            $mock->shouldReceive(['sendMail' => 1])->with($mail)->andReturn(false)->once();
        }));
        $sendMailCacheService = $this->instance(SendMailCacheService::class, Mockery::mock(SendMailCacheService::class, function ($mock) use ($id, $mail) {
            $mock->shouldReceive(['retrieve' => $id])->andReturn($mail)->once();
            $mock->shouldReceive(['insertOrUpdate' => $mail])->withArgs(function ($entity) {return $entity->status == 'cancelled';})->once();
        }));
        $sendMailQueueService = $this->instance(SendMailQueueService::class, Mockery::mock(SendMailQueueService::class, function ($mock) {
            $mock->shouldReceive(['dispatchMailRequest' => null])->never();
        }));

        $thrown = false;
        try {
            $result = $job->handle($aggregateMailer, $sendMailCacheService, $sendMailQueueService);
        } catch (\Throwable $th) {
            $thrown = true;
        }

        $this->assertTrue($thrown);
    }

    //@todo test increase of queue delay
}
