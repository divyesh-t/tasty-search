<?php

namespace App\Console\Commands;

use App\Services\Ingest;
use Illuminate\Console\Command;

class IngestFromFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ingest:docs {filepath} {countOfDocsToIngest?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To add more docs to the collection, file path should be absolute';

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
        $time = time();
        if ($this->argument('countOfDocsToIngest')) $bar = $this->output->createProgressBar($this->argument('countOfDocsToIngest'));
        else $bar = null;
        $count = Ingest::ingestDocs($this->argument('filepath'), $this->argument('countOfDocsToIngest') ?? PHP_INT_MAX, $bar);
        if ($bar) $bar->finish();
        $this->info('ingest doc count => '. $count);
        $this->info(PHP_EOL. 'done in '. (time() - $time) . 's');
    }
}
