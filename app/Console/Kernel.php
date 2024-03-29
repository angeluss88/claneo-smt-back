<?php

namespace App\Console;

use App\Models\Keyword;
use App\Models\Project;
use App\Services\GoogleAnalyticsService;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('sanctum:prune-expired --hours=24')->daily();
        $schedule->call(function () {
            DB::table('events')->whereDate('created_at', '<=', now()->subDays(90))->delete();
        })->monthly();

        $schedule->call(function () {
            $filepath = storage_path('test.txt');
            file_put_contents($filepath, date('H:i:s') . '_______', FILE_APPEND);
            $ga = new GoogleAnalyticsService();
            $projects = Project::all();

            foreach ($projects as $project) {
                $date = Carbon::now()->subWeek()->format('Y-m-d');
                if($project->strategy !== Project::NO_EXPAND_STRATEGY) {
                    $ga->expandGA($project, $project->urls, $date);
                }

                $keywordsToExpand = [];
                foreach (Keyword::all() as $keyword) {
                    $keywordsToExpand[] = $keyword['keyword'];
                }

                if($project->expand_gsc) {
                    $ga->expandGSC($project->urls, $keywordsToExpand, $project, $date);
                }
            }

        })->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
