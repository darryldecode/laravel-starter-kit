<?php
/**
 * Created by PhpStorm.
 * User: darryldecode
 * Date: 2/21/2018
 * Time: 10:25 AM
 */

namespace App\Components\Core;


Trait ResponseHelpers
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

    /**
     * send ok response
     *
     * @param string $message
     * @param array $data
     * @param array $headers
     * @return \Illuminate\Http\Response
     */
    public function sendResponseOk(string $message = "Resource found.", array $data = [], array $headers = [])
    {
        return $this->sendResponse($message,$data,200,$headers);
    }

    /**
     * send a not found response
     *
     * @param string $message
     * @param array $headers
     * @return \Illuminate\Http\Response
     */
    public function sendResponseNotFound(string $message = "Resource not found.", array $headers = [])
    {
        return $this->sendResponse($message,[],404,$headers);
    }

    /**
     * send a bad request response
     *
     * @param string $message
     * @param array $headers
     * @return \Illuminate\Http\Response
     */
    public function sendResponseBadRequest(string $message = "Bad Request.", array $headers = [])
    {
        return $this->sendResponse($message,[],400,$headers);
    }

    /**
     * send created response
     *
     * @param string $message
     * @param array $data
     * @param array $headers
     * @return \Illuminate\Http\Response
     */
    public function sendResponseCreated(string $message = "Resource created.", array $data = [], array $headers = [])
    {
        return $this->sendResponse($message,$data,201,$headers);
    }

    /**
     * send updated response
     *
     * @param string $message
     * @param array $data
     * @param array $headers
     * @return \Illuminate\Http\Response
     */
    public function sendResponseUpdated(string $message = "Resource updated.", array $data = [], array $headers = [])
    {
        return $this->sendResponse($message,$data,200,$headers);
    }

    /**
     * send deleted response
     *
     * @param string $message
     * @param array $headers
     * @return \Illuminate\Http\Response
     */
    public function sendResponseDeleted(string $message = "Resource deleted.",array $headers = [])
    {
        return $this->sendResponse($message,[],200,$headers);
    }

    /**
     * send no content
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponseNoContent()
    {
        return response(null,204);
    }
}