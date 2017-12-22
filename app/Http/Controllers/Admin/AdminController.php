<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 10/5/2017
 * Time: 6:12 AM
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;

abstract class AdminController extends Controller
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