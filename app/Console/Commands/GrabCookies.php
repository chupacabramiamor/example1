<?php

namespace App\Console\Commands;

use App\Services\CookieBotGrabber;
use Illuminate\Console\Command;

class GrabCookies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grab:cookies {--from= : TXT-list of domains for scan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collect the cookies from the site list';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $rc = new \ReflectionClass(CookieBotGrabber::class);
        $statuses = [];

        foreach($rc->getConstants() as $key => $value) {
            if (preg_match('~^STATUS_~', $key)) {
                $statuses[$value] = $key;
            }
        }

        $domains = $this->obtainDomains();

        $handler = app(CookieBotGrabber::class)->setDomains($domains);

        $handler->onDomainHandled(function(string $domain, int $status) use($statuses) {
            $this->line(sprintf('Domain %s handled with status %s', $domain, $statuses[$status]));
        });

        $this->info('Parsing process started...');

        $handler->run();

        $this->info('All domains has been handled.');
    }

    private function obtainDomains() : array
    {
        return $this->option('from') ?
            $domains = array_map(function($item) {
                return trim($item);
            }, file($this->option('from')))
        : config('cookiebotgrabber.domains');
    }
}
