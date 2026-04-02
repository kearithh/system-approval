<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Telegram\Bot\Laravel\Facades\Telegram;

class XmlController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        // Telegram::sendMessage([
        //     'chat_id' => env('TELEGRAM_CHANNEL_ID', '-490778022'),
        //     //'parse_mode' => 'HTML',
        //     'text' => 'hello Telegram'
        // ]);

        $lastUpdate = DB::table('individual')->first();
        $lastUpdate = $lastUpdate->Reportdate;
        $totalIndividual = DB::table('individual')->count();
        $totalEntity = DB::table('entity')->count();

        $data = DB::table('individual')
                ->whereNotNull('individual.FIRST_NAME');

        $keyword = \request()->keyword;
        if ($keyword) {
            $data = $data
                ->Where('individual.FIRST_NAME', 'like', "%$keyword%")
                ->orWhere('individual.SECOND_NAME', 'like', "%$keyword%")
                ->orWhere('individual.INDIVIDUAL_DOCUMENT', 'like', "%$keyword%")
                ->orWhere('individual.INDIVIDUAL_DATE_OF_BIRTH', 'like', "%$keyword%");
        }

        $data = $data->paginate(30);

        return view('xml.index', compact(
            'data',
            'lastUpdate',
            'totalIndividual',
            'totalEntity'
        ));
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update()
    {
        $client = new Client( array(
            'curl'            => array( CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => false ),
            'allow_redirects' => false,
            'cookies'         => true,
            'verify'          => false
        ) );
        $url = 'https://scsanctions.un.org/resources/xml/en/consolidated.xml';
        $res = $client->request('GET', $url);
        $data = $res->getBody();
        $xml = simplexml_load_string($data, "SimpleXMLElement", LIBXML_NOCDATA);
        ini_set("memory_limit", -1);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);

        // Individuals
        DB::table('individual')->delete();
        $dataIndividuals = $array['INDIVIDUALS']['INDIVIDUAL'];
        foreach ($dataIndividuals as $item){
            $dataInsert = [
                "DATAID" => @$item['DATAID'],
                "VERSIONNUM" => @$item['VERSIONNUM'],
                "FIRST_NAME" => @$item['FIRST_NAME'],
                "SECOND_NAME" => @$item['SECOND_NAME'],
                "THIRD_NAME" => json_encode(@$item['THIRD_NAME']),
                "UN_LIST_TYPE" => @$item['UN_LIST_TYPE'],
                "REFERENCE_NUMBER" => @$item['REFERENCE_NUMBER'],
                "LISTED_ON" => @$item['LISTED_ON'],
                "COMMENTS1" => json_encode(@$item['COMMENTS1']),
                "DESIGNATION" => json_encode(@$item['DESIGNATION']),
                "NATIONALITY" => json_encode(@$item['NATIONALITY']),
                "LIST_TYPE" => json_encode(@$item['LIST_TYPE']),
                "LAST_DAY_UPDATED" => json_encode(@$item['LAST_DAY_UPDATED']),
                "INDIVIDUAL_ALIAS" => json_encode(@$item['INDIVIDUAL_ALIAS']),
                "INDIVIDUAL_ADDRESS" => json_encode(@$item['INDIVIDUAL_ADDRESS']),
                "INDIVIDUAL_DATE_OF_BIRTH" => json_encode(@$item['INDIVIDUAL_DATE_OF_BIRTH']),
                "INDIVIDUAL_PLACE_OF_BIRTH" => json_encode(@$item['INDIVIDUAL_PLACE_OF_BIRTH']),
                "INDIVIDUAL_DOCUMENT" => json_encode(@$item['INDIVIDUAL_DOCUMENT']),
                "SORT_KEY" => json_encode(@$item['SORT_KEY']),
                "SORT_KEY_LAST_MOD" => json_encode(@$item['SORT_KEY_LAST_MOD']),
                "Reportdate" => Carbon::now(),
            ];
            DB::table('individual')->insert($dataInsert);
        }

        // Entity
        DB::table('entity')->delete();
        $dataEntities = $array['ENTITIES']['ENTITY'];
        foreach ($dataEntities as $item){
            $dataInsert = [
                "DATAID" => @$item['DATAID'],
                "VERSIONNUM" => @$item['VERSIONNUM'],
                "FIRST_NAME" => @$item['FIRST_NAME'],
                "UN_LIST_TYPE" => @$item['UN_LIST_TYPE'],
                "REFERENCE_NUMBER" => @$item['REFERENCE_NUMBER'],
                "LISTED_ON" => @$item['LISTED_ON'],
                "COMMENTS1" => json_encode(@$item['COMMENTS1']),
                "LIST_TYPE" => json_encode(@$item['LIST_TYPE']),
                "LAST_DAY_UPDATED" => json_encode(@$item['LAST_DAY_UPDATED']),
                "ENTITY_ALIAS" => json_encode(@$item['ENTITY_ALIAS']),
                "ENTITY_ADDRESS" => json_encode(@$item['ENTITY_ADDRESS']),
                "SORT_KEY" => json_encode(@$item['SORT_KEY']),
                "SORT_KEY_LAST_MOD" => json_encode(@$item['SORT_KEY_LAST_MOD']),
                "Reportdate" => Carbon::now(),
            ];
            DB::table('entity')->insert($dataInsert);
        }

        return redirect()->back()->with(['status' => 1]);
    }
}
