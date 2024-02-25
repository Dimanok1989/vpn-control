<?php

namespace App\Jobs;

use App\Models\IpsecConnection;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class UpDownIpsecJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected array $updownData)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $item = IpsecConnection::create($this->updownData);

        $appId = env('APP_CALLBACK_KEY');
        $url = env('APP_URL_CALLBACK');

        if (!$url) {
            return;
        }

        Http::withHeader('X-Application-Id', $appId)
            ->post($url, $item->toArray());
    }
}
