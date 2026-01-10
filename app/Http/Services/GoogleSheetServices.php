<?php

namespace App\Http\Services;

use Google\Client;
use Google\Service\Sheets;
use Illuminate\Support\Facades\DB;

class GoogleSheetServices
{
    public $client;

    public $service;

    public $docId;

    public $range;

    public function __construct()
    {
        $google_sheet_settings = DB::table('google_sheet_settings')->where('id', 1)->first();
        $this->client = $this->getClient();
        $this->service = new Sheets($this->client);
        $this->docId = $google_sheet_settings->all_order_sheet_id;
        $this->range = 'A:Z';
    }

    public function getClient()
    {
        $client = new Client;
        $client->setApplicationName('Order');
        $client->setScopes(Sheets::SPREADSHEETS);
        $client->setAuthConfig('credentials.json');
        $client->setAccessType('offline');

        return $client;
    }

    public function readSheet()
    {
        //        $client = $this->getClient();
        //        $service = new Sheets($client);
        //        $documentID = '1vifdgSValA3HDsE7qO4R3VhzD-aqhwxCsR-dCuGlj6M';
        $doc = $this->service->spreadsheets_values->get($this->docId, $this->range);

        return $doc;
    }

    public function appendSheet($values)
    {
        $body = new Sheets\ValueRange([
            'values' => $values,
        ]);
        $param = [
            'valueInputOption' => 'USER_ENTERED',
        ];
        $result = $this->service->spreadsheets_values->append($this->docId, $this->range, $body, $param);

        return $result;
    }
}
