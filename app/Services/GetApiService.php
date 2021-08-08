<?php

namespace App\Services;

class GetApiService
{
    public function getWeather_36hours()
    {
        //氣象局api -三十六小時天氣預報
        $api = "https://opendata.cwb.gov.tw/api/v1/rest/datastore/F-C0032-001?Authorization=CWB-00F3A987-1CDD-4249-90E1-168F817F5590&format=JSON";
        $json_raw = file_get_contents($api);    //get json
        $json = json_decode($json_raw, JSON_UNESCAPED_UNICODE); //JSON_UNESCAPED_UNICODE = json不進行轉碼

        /*中央氣象局資料說明：
            Wx:天氣現象
            MaxT:最高溫度
            MinT:最低溫度
            CI:舒適度
            PoP:降雨機率
         */
        $early_morning = "今日凌晨：%u°C ~ %u°C";
        $day = "今日白天：%u°C ~ %u°C";
        $night = "今日晚上：%u°C ~ %u°C";

        $row = [];
        foreach ($json['records']['location'] as $row) {
            if ($row['locationName'] = '桃園') {

                break;
            }
        }
//        dd($row);
        //清晨天氣
        $early_morning = sprintf($early_morning,
            $row['weatherElement'][2]['time'][0]['parameter']['parameterName'],
            $row['weatherElement'][4]['time'][0]['parameter']['parameterName']
        );
        //白天天氣
        $day = sprintf($day,
            $row['weatherElement'][2]['time'][1]['parameter']['parameterName'],
            $row['weatherElement'][4]['time'][1]['parameter']['parameterName']
        );
        //晚上天氣
        $night = sprintf($night,
            $row['weatherElement'][2]['time'][2]['parameter']['parameterName'],
            $row['weatherElement'][4]['time'][2]['parameter']['parameterName']
        );

        $reply = "桃園市\n" . $early_morning . "\n" . $day . "\n" . $night;

//        dd($reply);

        return $reply;
    }
}
