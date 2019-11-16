<?php

declare(strict_types=1);

namespace Appocular\Keeper\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array<string>
     */
    protected $commands = [
        Commands\Store::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    protected function schedule(Schedule $schedule): void
    {
    }
}
