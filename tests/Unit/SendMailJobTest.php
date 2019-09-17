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
            $mock->shouldReceive(['insertOrUpdate' => $mail])->withArgs(function($entity) { return $entity->status == 'sent';})->once();
        }));
        $sendMailQueueService = $this->instance(SendMailQueueService::class, Mockery::mock(SendMailQueueService::class, function ($mock) {
            $mock->shouldReceive(['dispatchMailRequest'])->never();
        }));

        $result = $job->handle($aggregateMailer, $sendMailCacheService, $sendMailQueueService);
    }

    /**
     * Test that a mail enqueued again if sending doesn't work and it's the first attempt
     */
    public function testRetryCase()
    {
        $mail = TestData::getMail();
        $request = new MailRequest($mail);
        $id = 'foo-bar-2';
        $request->id = $id;
        $job = new SendMailJob($request);

        $aggregateMailer = $this->instance(AggregateMailer::class, Mockery::mock(AggregateMailer::class, function ($mock) use ($mail) {
            $mock->shouldReceive(['sendMail' => 1])->with($mail)->andReturn(false)->once();
        }));
        $sendMailCacheService = $this->instance(SendMailCacheService::class, Mockery::mock(SendMailCacheService::class, function ($mock) use ($id, $mail) {
            $mock->shouldReceive(['retrieve' => $id])->andReturn($mail)->once();
            $mock->shouldReceive(['insertOrUpdate' => $mail])->withArgs(function($entity) { return $entity->status == 'retry';})->once();
        }));
        $sendMailQueueService = $this->instance(SendMailQueueService::class, Mockery::mock(SendMailQueueService::class, function ($mock) {
            $mock->shouldReceive(['dispatchMailRequest' => null])->once();
        }));

        $result = $job->handle($aggregateMailer, $sendMailCacheService, $sendMailQueueService);
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
            $mock->shouldReceive(['insertOrUpdate' => $mail])->withArgs(function($entity) { return $entity->status == 'cancelled';})->once();
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
