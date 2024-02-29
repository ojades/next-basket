<?php

namespace App\Console\Commands;

use App\Services\RabbitMQService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class QueueConsumerCommand extends RabbitMQService
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mq:consume';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen for and consume messages from RabbitMQ';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->consume();
    }

    public function onMessage($msg)
    {
        Log::info($msg);
    }
}
