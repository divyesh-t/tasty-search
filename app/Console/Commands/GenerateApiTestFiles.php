<?php

namespace App\Console\Commands;

use App\Models\TokenDocMappingModel;
use Illuminate\Console\Command;

class GenerateApiTestFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:testFiles {count?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To generate test files in public folder';

    /**
     * Create a new command instance.
     *
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
        $fp = fopen(public_path('tests.txt'), 'w');
        $words = TokenDocMappingModel::getWords();
        $count = $this->argument('count') ?? 1000;
        for ($i = 1; $i <= $count; $i++) {
            $keywords = $this->arrayRandElements($words);
            $qParam = array_reduce($keywords, function($val, $in) {
                return $val . '&query[]='.$in;
            }, '');
            $req = 'http://localhost:8000/query?'.$qParam. ' POST';
            fwrite($fp,  $req . PHP_EOL);
        }
        fclose($fp);
    }

    private function arrayRandElements($array)
    {
        $queryLength = random_int(1, 20);
        $keys = [];
        $vals = [];
        for ($i = 0; $i < $queryLength; $i++){
            $key = array_rand($array);
            if (!in_array($key, $keys)) $vals[] = $array[$key];
            else $i--;
        }
        return $vals;
    }
}
