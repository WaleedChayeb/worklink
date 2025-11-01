<?php

namespace App\Console\Commands;

use App\Providers\InstallerServiceProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RunUITests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:ui';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Saves admin settings to laravel seeds';

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
     * Saves admin settings to laravel seeds.
     *
     * @return mixed
     */
    public function handle()
    {
        echo '[*]['.date('H:i:s')."] Running tests - Output will be shown at the end.\r\n";
//        TODO: Maybe chain a chrome and browserstack based run to get both screenshots in one run
        $appUrl = config('app.url');
        InstallerServiceProvider::appendToEnv("APP_URL=\"$appUrl\"");
        $result = handledExec('php artisan dusk');
        echo $result;
        InstallerServiceProvider::removeFromEnv("APP_URL=\"$appUrl\"", '');
        echo '[*]['.date('H:i:s')."] Tests finished running.\r\n";

        return 0;
    }

    protected function replaceInFile($file, $match, $replace, $recursive = true)
    {
        $fileContent = file_get_contents($file);
        if ($recursive == true) {
            while (is_int(strpos($fileContent, $match))) {
                $fileContent = str_replace($match, $replace, $fileContent);
            }
        } else {
            $fileContent = str_replace($match, $replace, $fileContent);
        }
        file_put_contents($file, $fileContent);
    }
}
