<?php

namespace App\Services;

use App\Models\Expenses;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExpensesService
{
    //記帳Service
    //接收字串
    public function get_text($text)
    {
        try {
            $origin_text = explode(' ', $text);
            $price = array_pop($origin_text); //價格
            $item = implode(' ', $origin_text); //品項

            Expenses::create([
                'item' => $item,
                'price' => $price
            ]);

            return "success";
        } catch (\Exception $exception) {
            return $exception;
        }

    }
    //本周紀錄
    public function get_today()
    {
        try {
            $now = Carbon::now();
            $data = Expenses::whereDate('created_at', '=', $now)
                ->get();

            $result = "";
            foreach ($data as $row) {
                $result .= "品項：{$row['item']}，金額：{$row['price']}\n";
            }
            $result .= "\n";
            $result .= $now->toDateString() . "， 今日合計：" . Expenses::whereDate('created_at', '=', $now)->sum('price');
            return $result;
        } catch (\Exception $exception) {
            return $exception;
        }
    }

    //本周紀錄
    public function get_currentWeek()
    {
        try {
            $now = Carbon::now();
            $data = Expenses::whereDate('created_at', '>', $now->subDays(7))
                ->get();

            $result = "";
            foreach ($data as $row) {
                $date = date('y-m-d', strtotime($row['created_at']));
                $result .= "{$date}，品項：{$row['item']}，金額：{$row['price']}\n";
            }
            $result .= "\n";
            $result .="本周合計：" . Expenses::whereMonth('created_at', '=', $now->subDays(7))->sum('price');
            return $result;
        } catch (\Exception $exception) {
            return $exception;
        }
    }

    //本月紀錄
    public function get_currentMonth()
    {
        try {
            $now = Carbon::now();
            $data = Expenses::whereMonth('created_at', '=', $now->month)
                ->get();

            $result = "";
            foreach ($data as $row) {
                $date = date('y-m-d', strtotime($row['created_at']));
                $result .= "{$date}，品項：{$row['item']}，金額：{$row['price']}\n";
            }
            $result .= "\n";
            $result .= $now->month . "月合計：" . Expenses::whereMonth('created_at', '=', $now->month)->sum('price');
            return $result;
        } catch (\Exception $exception) {
            return $exception;
        }
    }
}
