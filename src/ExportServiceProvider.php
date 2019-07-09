<?php

namespace LWS\ExportActions;

use Illuminate\Support\ServiceProvider;
use Queue;
use LWS\ExportActions\Jobs\WritePdf;

class ExportServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__."/routes/web.php");
        $this->loadMigrationsFrom(__DIR__."/migrations/");

        // Queue::after(function(WritePdf $event){
        //     $headers = [
        //         'Content-Type' => 'application/pdf'
        //      ];
        //     return response()->download(storage_path().'/app/pdf.pdf',$headers)->deleteFileAfterSend();
        // }); 
    }

    public function register()
    {
        
    }
}