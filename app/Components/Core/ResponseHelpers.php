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
    public function sendResponse($data = null, $message = '', $statusCode = 200,$headers = [])
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
    public function sendResponseOk($data = [],string $message = "Resource found.", array $headers = [])
    {
        return $this->sendResponse($data,$message,200,$headers);
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
        return $this->sendResponse([],$message,404,$headers);
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
        return $this->sendResponse([],$message,400,$headers);
    }

    /**
     * send created response
     *
     * @param string $message
     * @param array $data
     * @param array $headers
     * @return \Illuminate\Http\Response
     */
    public function sendResponseCreated($data = [], string $message = "Resource created.", array $headers = [])
    {
        return $this->sendResponse($data,$message,201,$headers);
    }

    /**
     * send updated response
     *
     * @param string $message
     * @param array $data
     * @param array $headers
     * @return \Illuminate\Http\Response
     */
    public function sendResponseUpdated($data = [],string $message = "Resource updated.", array $headers = [])
    {
        return $this->sendResponse($data,$message,200,$headers);
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
        return $this->sendResponse([],$message,200,$headers);
    }

    /**
     * send forbidden response
     *
     * @param string $message
     * @param array $headers
     * @return \Illuminate\Http\Response
     */
    public function sendResponseForbidden(string $message = "Action forbidden.",array $headers = [])
    {
        return $this->sendResponse([],$message,403,$headers);
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