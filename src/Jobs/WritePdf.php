<?php

namespace LWS\ExportActions\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use PDF;
use Illuminate\Support\Facades\Storage;

class WritePdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $html;
    

    public $timeout = 120;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($html)
    {
        $this->html = $html;
        
        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        PDF::SetTitle('Export Data.');
        PDF::AddPage();
        PDF::writeHTML($this->html);
        $pdfString = PDF::Output('hello_world.pdf','S');
        Storage::disk('local')->put('pdf.pdf',$pdfString);
    }

   
  
}