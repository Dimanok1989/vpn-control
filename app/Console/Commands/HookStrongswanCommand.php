<?php

namespace App\Console\Commands;

use App\Jobs\UpDownIpsecJob;
use Illuminate\Console\Command;

class HookStrongswanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:ipsec-updown
                            {user?}
                            {--verb=}
                            {--connect=}
                            {--uniqueid=}
                            {--reqid=}
                            {--peer=}
                            {--ip=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обработка хука подключения/отключения клиента';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        UpDownIpsecJob::dispatch([
            'user' => $this->argument('user'),
            'verb' => $this->option('verb'),
            'connect' => $this->option('connect'),
            'uniqueid' => $this->option('uniqueid'),
            'reqid' => $this->option('reqid'),
            'peer' => $this->option('peer'),
            'ip' => $this->option('ip'),
        ]);
    }
}
