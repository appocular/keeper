<?php

namespace Appocular\Keeper\Console\Commands;

use Appocular\Keeper\ImageStore;
use Illuminate\Console\Command;

class Store extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'keeper:store {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store an image and output its ID.';

    protected $imageStore;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ImageStore $imageStore)
    {
        $this->imageStore = $imageStore;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $kid = $this->imageStore->store(file_get_contents($this->argument('file')));
        $this->line($kid);
    }
}
