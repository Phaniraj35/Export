<?php

namespace LWS\ExportActions\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use SoapBox\Formatter\Formatter;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use LWS\ExportActions\Jobs\WritePdf;

class Exporter extends Controller
{
    public function export(Request $requestData)
    {
        

        $client = new Client();
        

        $request = new \GuzzleHttp\Psr7\Request('GET', $requestData->url);
        $promise = $client->sendAsync($request)->then(function ($responseData) {

            $formatter = Formatter::make($responseData->getBody(), Formatter::JSON);
            $csv   = $formatter->toCsv();

            Storage::disk('local')->put('csvfile.csv',$csv);

        });

        $promise->wait();

        $file_name = storage_path().'/app/csvfile.csv';
        
        switch(strtolower($requestData->format)) {
            case 'csv': return $this->csv($file_name);break;
            case 'excel': return $this->excel($file_name);break;
            case 'pdf' : return $this->pdf($file_name);break;
        } //switch
        
        

        
    }


    public function csv($file_name)
    {
        $headers = [
            'Content-Type' => 'text/csv'
         ];
        
        return response()->download($file_name,"export.csv",$headers);

    }


    public function excel($file_name)
    {
        $spreadsheet = new Spreadsheet();
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        
        /* Set CSV parsing options */
        $reader->setDelimiter(',');
        $reader->setEnclosure('"');
        $reader->setSheetIndex(0);
        
        /* Load a CSV file and save as a XLS */
        $spreadsheet = $reader->load($file_name);
        $writer = new Xlsx($spreadsheet);
        
        $writer->save('export.xlsx');
        
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
    }

    public function pdf($file_name)
    {

        $csv = array_map('str_getcsv',file($file_name));

        $thead = array_shift($csv);

        $lastKey = array_search(end($thead), $thead);

        

        $html = '
        <html>
        </head>
        <style>
        td, th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
          }
          
          th {

            font-family : bold;

          }
        
        </style>
        </head>
        ';

        $html .= '<body><table><thead><tr>';

        


        for($i = 0 ; $i<count($thead); $i++) {
            $html.= "<th bgcolor='#ccc'>$thead[$i]</th>";
        }
        
            
        if($i>=count($thead)) $html.= "</tr></thead>";

        foreach($csv as $tbody) {
            $html .= "<tr>";
            foreach ($tbody as $td ) {
                $html .= "<td>$td</td>";
            }
            $html .= "</tr>";
        }

        
        $html .= "</table></body>";

        PDF::SetTitle('Export Data');
        PDF::AddPage();
        //PDF::Write(0, $html);
        PDF::writeHTML($html);
        PDF::Output('hello_world.pdf');
        //WritePdf::dispatch($html);

    }

}