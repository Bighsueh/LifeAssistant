<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;

class LineController extends Controller
{
    private $client;
    private $bot;
    private $channel_access_token;
    private $channel_secret;

    public function __construct()
    {
        //用env內的Channel_access_token & Channel secret建立一個LineBot物件
        $this->channel_access_token = env('CHANNEL_ACCESS_TOKEN');
        $this->channel_secret = env('CHANNEL_SECRET');
        $httpClient = new CurlHTTPClient($this->channel_access_token);
        $this->bot = new LINEBot($httpClient, ['channelSecret' => $this->channel_secret]);
        $this->client = $httpClient;
    }

    public function index(Request $request)
    {
        $params = $request->all();
        logger(json_encode($params, JSON_UNESCAPED_UNICODE));
//        error_log(json_encode('1'));
        return response('ok', '200');
    }

    public function webhook(Request $request)
    {
        $bot = $this->bot;
        $signature = $request->header(HTTPHeader::LINE_SIGNATURE);
        $body = $request->getContent();
        try {
            $events = $bot->parseEventRequest($body, $signature);
            foreach ($events as $event) {
                $replyToken = $event->getReplyToken();
                if ($event instanceof MessageEvent) {
                    $message_type = $event->getMessageType(); //接收的資料型態
                    Log::info($message_type);
                    switch ($message_type) {
                        case 'text':
                            $text = $event->getText(); //接收的訊息內容
                            if(str_contains($text,'嘉義')){
                                $locationName = '嘉義';
                                $weather = $this->getWeather();
                                $bot->replyText($replyToken, $weather);
                            }
                            $bot->replyText($replyToken, $text);
                        case 'sticker':
                            $bot->replyText($replyToken, 'sticker');
                    }

                }

            }

        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function getWeather()
    {
        //氣象局api -三十六小時天氣預報
        $api = "https://opendata.cwb.gov.tw/api/v1/rest/datastore/F-C0032-001?Authorization=CWB-00F3A987-1CDD-4249-90E1-168F817F5590&format=JSON";

        //get json
        $json_raw = file_get_contents($api);
        //JSON_UNESCAPED_UNICODE = json不進行轉碼
        $json = json_decode($json_raw, JSON_UNESCAPED_UNICODE);

        /*中央氣象局資料說明：
            Wx:天氣現象
            MaxT:最高溫度
            MinT:最低溫度
            CI:舒適度
            PoP:降雨機率
         */
        $r = [];
        foreach ($json['records']['location'] as $row){
            if($row['locationName'] = '桃園'){
                dd($row);
                break;
            }
        }





        return 'weather';
    }

}
