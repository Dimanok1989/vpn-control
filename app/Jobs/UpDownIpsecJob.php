<?php

namespace App\Jobs;

use App\Models\IpsecConnection;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        IpsecConnection::create($this->updownData);
    }
}
