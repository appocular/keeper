<?php

declare(strict_types=1);

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

    /**
     * The image store to store image in.
     *
     * @var \Appocular\Keeper\ImageStore
     */
    protected $imageStore;

    /**
     * Create a new command instance.
     */
    public function __construct(ImageStore $imageStore)
    {
        $this->imageStore = $imageStore;
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): ?int
    {
        $kid = $this->imageStore->store(\file_get_contents($this->argument('file')));
        $this->line($kid);

        return null;
    }
}
