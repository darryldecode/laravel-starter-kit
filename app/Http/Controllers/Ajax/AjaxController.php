<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 10/6/2017
 * Time: 6:16 AM
 */

namespace App\Http\Controllers\Ajax;


use App\Http\Controllers\Controller;

abstract class AjaxController extends Controller
{
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