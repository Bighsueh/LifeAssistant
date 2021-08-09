<?php

namespace App\Services;

class ValidateDataService
{
    public function getCountyName($county_name)
    {
        $result = '';
        //台臺轉換
        $county_name = str_replace('台', '臺', $county_name);

        //定義鄉鎮市名稱
        $county_list = [
            ['code' => 'F-D0047-001', 'countyName' => '宜蘭縣'],
            ['code' => 'F-D0047-005', 'countyName' => '桃園市'],
            ['code' => 'F-D0047-009', 'countyName' => '新竹縣'],
            ['code' => 'F-D0047-013', 'countyName' => '苗栗縣'],
            ['code' => 'F-D0047-017', 'countyName' => '彰化縣'],
            ['code' => 'F-D0047-021', 'countyName' => '南投縣'],
            ['code' => 'F-D0047-025', 'countyName' => '雲林縣'],
            ['code' => 'F-D0047-029', 'countyName' => '嘉義縣'],
            ['code' => 'F-D0047-033', 'countyName' => '屏東縣'],
            ['code' => 'F-D0047-037', 'countyName' => '臺東縣'],
            ['code' => 'F-D0047-041', 'countyName' => '花蓮縣'],
            ['code' => 'F-D0047-045', 'countyName' => '澎湖縣'],
            ['code' => 'F-D0047-049', 'countyName' => '基隆市'],
            ['code' => 'F-D0047-053', 'countyName' => '新竹市'],
            ['code' => 'F-D0047-057', 'countyName' => '嘉義市'],
            ['code' => 'F-D0047-061', 'countyName' => '臺北市'],
            ['code' => 'F-D0047-065', 'countyName' => '高雄市'],
            ['code' => 'F-D0047-069', 'countyName' => '新北市'],
            ['code' => 'F-D0047-073', 'countyName' => '臺中市'],
            ['code' => 'F-D0047-077', 'countyName' => '臺南市'],
            ['code' => 'F-D0047-081', 'countyName' => '連江縣'],
            ['code' => 'F-D0047-085', 'countyName' => '金門縣']];

        //抓出完整鄉鎮市名稱
        foreach ($county_list as $row) {
            if (str_contains($row['countyName'], $county_name)) {
                $result = $row['countyName'];
                break;
            } else {
                $result = "none";
            }
        }
        return $result;
    }


}
