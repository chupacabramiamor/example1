<?php

namespace App\Console\Commands;

use App\Services\SubscriptionService;
use Illuminate\Console\Command;

class SyncPlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:plans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronization of the subscription plans';

    private $subscriptionSvc;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->subscriptionSvc = app(SubscriptionService::class);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Sync started...');

        $stat = $this->subscriptionSvc->syncPlans();


        $this->line('Disabled plans: '. $stat->disabled);
        $this->line('Added plans: '. $stat->added);
        $this->line('Updated plans: '. $stat->updated);
        $this->line('Ignored plans: '. $stat->ignored);

        $this->info('Sync finished!');
    }
}
