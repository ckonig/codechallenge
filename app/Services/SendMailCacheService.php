<?php

namespace App\Services;

use App\Models\MailModel;
use Illuminate\Support\Facades\Cache;

class SendMailCacheService
{
    public function insertOrUpdate(MailModel $mail, int $extraTtl = 0)
    {
        $ttl = env('CACHE_TTL', 600);
        Cache::store('redis')->put($mail->id, $mail, $ttl + $extraTtl);
    }

    public function retrieve(string $id) {
        return Cache::store('redis')->get($id);
    }
}
