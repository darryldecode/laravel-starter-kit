<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * send response to ajax request
     *
     * @param string $message
     * @param null $data
     * @param int $statusCode
     * @param array $headers
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($message = '', $data = null, $statusCode = 200,$headers = [])
    {
        $d = [
            'message' => $message,
            'data' => $data
        ];

        return response($d,$statusCode,$headers);
    }
}
