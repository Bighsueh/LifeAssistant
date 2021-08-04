<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LineController extends Controller
{
    //
    public function index(Request $request)
    {
        $params = $request->all();
        logger(json_encode($params, JSON_UNESCAPED_UNICODE));
//        error_log(json_encode('1'));
        return response('ok', '200');
    }

}
