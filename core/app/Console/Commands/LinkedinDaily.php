<?php

namespace App\Console\Commands;

use App\Services\Linkedin\LinkedinOutreach;
use Illuminate\Console\Command;

class LinkedinDaily extends Command
{
    protected $signature = 'linkedin:daily';

    protected $description = 'Prepare the daily Crownmaire Capital LinkedIn queue and publish a due post';

    public function handle(LinkedinOutreach $outreach): int
    {
        $outreach->runIfDue();
        $this->info('LinkedIn daily check finished.');

        return self::SUCCESS;
    }
}
