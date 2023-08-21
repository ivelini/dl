<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Modules\Product\Entities\Product;

class ProductDocumentationFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pdocf:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновление документации на продукт из XML файла (https://www.termoros.com/upload_files/documentation/files/file.xml)';

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
     * @return int
     */
    public function handle()
    {

      $products = Product::select('id', 'additional', 'external_id')->get();

      $curl = curl_init();
      curl_setopt_array($curl , [
        CURLOPT_URL => "https://www.termoros.com/upload_files/documentation/files/file.xml",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "GET",
      ]);

      $response = curl_exec($curl);
      curl_close($curl);

      $file = fopen('xmlData', 'w+');
      fwrite($file, $response);
      Storage::put('public/data.xml', $file);
      fclose($file);

      $xml = simplexml_load_file(Storage::path('public/data.xml'));

      Storage::delete('public/data.xml');

      foreach ($xml as $itemsXml) {

        $fileTypes = ['doc', 'docx', 'pdf', 'png', 'jpg'];

        $itemsXml = collect($itemsXml->attributes())->map(function ($volume) {
          return $volume->__toString();
        })->toArray();

        $productFromXml = $products->where('external_id', $itemsXml['ProductId'])->first();

        if($productFromXml) {

          $documentationFiles = collect($productFromXml->getFromAdditional('documentation_files'));

          foreach($fileTypes as $fileType) {

            $curl = curl_init();
            curl_setopt_array($curl , [
              CURLOPT_URL => 'https://www.termoros.com/upload_files/documentation/files/' . $itemsXml['Type'] . '/' . $itemsXml['Id'] . '.' . $fileType,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_CUSTOMREQUEST => "GET",
            ]);
            curl_exec($curl);
            $responseInfo = curl_getinfo($curl);

            if($responseInfo['http_code'] == 200) {

              $file = [
                'id'          => $itemsXml['Id'],
                'url'         => $responseInfo['url'],
                'extention'   => $fileType,
                'size'        => $responseInfo['size_download'],
                'type'        => $itemsXml['Type'],
                'updated_at'  => Carbon::now()->format('Y-m-d')
              ];

              if($documentationFiles->count() == 0){

                $documentationFiles = $documentationFiles->push($file);
              } else {

                $fileIsExist = false;
                $documentationFiles->map(function($item) use ($file, $itemsXml, $fileIsExist) {

                  if (!empty($item['id']) && $item['id'] == $itemsXml['Id']) {

                    $fileIsExist = true;
                    return $file;
                  }
                });

                if($fileIsExist == false) $documentationFiles = $documentationFiles->push($file);
              }
            }

            curl_close($curl);
          }

          $productFromXml->setInAdditional('documentation_files', $documentationFiles->toArray(), true);
        }
      }
    }
}
