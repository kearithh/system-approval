<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;

class BaseApiController extends Controller
{

    const HINT_CODE = "G}h{m2Dw;FW9JxbM";

    /**
     * Check request instead of Auth
     */
    public function checkHintUser($hint)
    {
        return $hint === self::HINT_CODE;
    }

    public function myResponse($data = [], $message = 'Success', $status = 200)
    {
        return response([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ])->header('Content-Type', 'application/json');
    }
}
