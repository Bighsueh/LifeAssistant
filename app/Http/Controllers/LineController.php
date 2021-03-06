<?php

namespace App\Http\Controllers;

use App\Services\ExpensesService;
use App\Services\GetApiService;
use App\Services\ValidateDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use mysql_xdevapi\Exception;

class LineController extends Controller
{
    //line-bot
    private $bot;
    private $channel_access_token;
    private $channel_secret;

    //Services
    private $getApiService;
    private $validateDataService;
    private $expensesService;

    public function __construct(GetApiService $getApiService, ValidateDataService $validateDataService, ExpensesService $expensesService)
    {
        //用env內的Channel_access_token & Channel secret建立一個LineBot物件
        $this->channel_access_token = env('CHANNEL_ACCESS_TOKEN');
        $this->channel_secret = env('CHANNEL_SECRET');
        $httpClient = new CurlHTTPClient($this->channel_access_token);
        $this->bot = new LINEBot($httpClient, ['channelSecret' => $this->channel_secret]);
        $this->client = $httpClient;
        $this->getApiService = $getApiService;
        $this->validateDataService = $validateDataService;
        $this->expensesService = $expensesService;
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
                            $messageBuilder = new LINEBot\MessageBuilder\MultiMessageBuilder();
                            $text = $event->getText(); //接收的訊息內容

                            //若包含鄉鎮市則帶出天氣
                            $countyName = $this->validateDataService->getCountyName($text);
                            if ($countyName !== 'none') {
                                $weather = new LINEBot\MessageBuilder\TextMessageBuilder(
                                    $this->getApiService->getThirtySixHoursWeather($countyName)
                                );
                                $messageBuilder->add($weather);
                                $bot->replyMessage($replyToken, $messageBuilder);
                                break;
                            }

                            //若包含"地震"則帶入地震圖片
                            if (str_contains($text, '地震')) {
                                $earthquake_text = new LINEBot\MessageBuilder\TextMessageBuilder('最近顯著有感地震：');
                                $messageBuilder->add($earthquake_text);

                                $image = new LINEBot\MessageBuilder\ImageMessageBuilder(
                                    $this->getApiService->getEarthquake(),
                                    $this->getApiService->getEarthquake()
                                );
                                $messageBuilder->add($image);
                                $bot->replyMessage($replyToken, $messageBuilder);
                                break;

                            }

                            //驗證字串為 空白+數字結尾
                            $format_text = preg_match("/\s\d{1,6}$/", $text);
                            //記帳功能
                            if ($format_text) {
                                //儲存狀態
                                $response = $this->expensesService->get_text($text);

                                //儲存成功
                                if ($response === 'success') $reply_text = "已收到記帳 " . $text;

                                //儲存錯誤
                                if ($response !== 'success') $reply_text = "出現錯誤，錯誤訊息：" . $response;

                                $result = new LINEBot\MessageBuilder\TextMessageBuilder($reply_text);
                                $messageBuilder->add($result);
                                $bot->replyMessage($replyToken, $messageBuilder);

                                break;
                            }


                            if (str_contains($text, '紀錄')) {
                                $reponse = null;
                                if (str_contains($text, "今日")) {
                                    $reponse = $this->expensesService->get_today();
                                }
                                if (str_contains($text, "本周")) {
                                    $reponse = $this->expensesService->get_currentWeek();
                                }
                                if (str_contains($text, "本月")) {
                                    $reponse = $this->expensesService->get_currentMonth();
                                }
                                if ($reponse !== null) {
                                    $result = new LINEBot\MessageBuilder\TextMessageBuilder($reponse);
                                    $messageBuilder->add($result);
                                    $bot->replyMessage($replyToken, $messageBuilder);
                                };

                                break;
                            }
                    }
                }
            }
        } catch
        (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
