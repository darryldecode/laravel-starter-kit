<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 7/17/2017
 * Time: 12:51 AM
 */

namespace App\Components\File\Utilities;


class FileHelper
{
    /**
     * get icon path
     *
     * @param $mimeType
     * @return string
     */
    public static function getIconPath($mimeType)
    {
        $file_type_icons_path = 'img/file-type-icons/';

        switch($mimeType)
        {
            case 'image/jpeg':
                $icon_file = 'jpeg.png';
                break;
            case 'image/pjpeg':
                $icon_file = 'jpeg.png';
                break;
            case 'image/x-jps':
                $icon_file = 'jpeg.png';
                break;
            case 'text/html':
                $icon_file = 'html.png';
                break;
            case 'video/animaflex':
                $icon_file = 'fla.png';
                break;
            case 'audio/aiff':
                $icon_file = 'midi.png';
                break;
            case 'audio/x-aiff':
                $icon_file = 'midi.png';
                break;
            case 'text/asp':
                $icon_file = 'html.png';
                break;
            case 'application/x-troff-msvideo':
                $icon_file = 'avi.png';
                break;
            case 'video/avi':
                $icon_file = 'avi.png';
                break;
            case 'video/msvideo':
                $icon_file = 'avi.png';
                break;
            case 'video/x-msvideo':
                $icon_file = 'avi.png';
                break;
            case 'video/avs-video':
                $icon_file = 'avi.png';
                break;
            case 'image/bmp':
                $icon_file = 'bmp.png';
                break;
            case 'image/x-windows-bmp':
                $icon_file = 'bmp.png';
                break;
            case 'text/plain':
                $icon_file = 'conf.png';
                break;
            case 'text/css':
                $icon_file = 'css.png';
                break;
            case 'application/msword':
                $icon_file = 'docx.png';
                break;
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                $icon_file = 'docx.png';
                break;
            case 'image/gif':
                $icon_file = 'gif.png';
                break;
            case 'application/x-compressed':
                $icon_file = 'zip.png';
                break;
            case 'application/x-gzip':
                $icon_file = 'zip.png';
                break;
            case 'application/x-gtar':
                $icon_file = 'rar.png';
                break;
            case 'multipart/x-gzip':
                $icon_file = 'zip.png';
                break;
            case 'application/x-javascript':
                $icon_file = 'html.png';
                break;
            case 'application/javascript':
                $icon_file = 'html.png';
                break;
            case 'application/ecmascript':
                $icon_file = 'html.png';
                break;
            case 'text/javascript':
                $icon_file = 'html.png';
                break;
            case 'text/ecmascript':
                $icon_file = 'html.png';
                break;
            case 'audio/midi':
                $icon_file = 'midi.png';
                break;
            case 'video/mpeg':
                $icon_file = 'mpeg.png';
                break;
            case 'audio/mpeg':
                $icon_file = 'mpeg.png';
                break;
            case 'application/pdf':
                $icon_file = 'pdf.png';
                break;
            case 'image/png':
                $icon_file = 'png.png';
                break;
            case 'application/mspowerpoint':
                $icon_file = 'ms-pptx.png';
                break;
            case 'application/vnd.ms-powerpoint':
                $icon_file = 'ms-pptx.png';
                break;
            case 'application/powerpoint':
                $icon_file = 'ms-pptx.png';
                break;
            case 'application/excel':
                $icon_file = 'ms-xlsx.png';
                break;
            case 'application/x-excel':
                $icon_file = 'ms-xlsx.png';
                break;
            case 'application/x-msexcel':
                $icon_file = 'ms-xlsx.png';
                break;
            case 'application/vnd.ms-excel':
                $icon_file = 'ms-xlsx.png';
                break;
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                $icon_file = 'ms-xlsx.png';
                break;
            case 'application/zip':
                $icon_file = 'zip.png';
                break;
            case 'multipart/x-zip':
                $icon_file = 'zip.png';
                break;
            case 'image/vnd.adobe.photoshop':
                $icon_file = 'psd.png';
                break;
            case 'not-found':
                $icon_file = 'not-found.png';
                break;
            default:
                $icon_file = 'unknown.png';
                break;
        }
        return $file_type_icons_path.$icon_file;
    }

    /**
     * File helper to check if the given file is an image
     *
     * @param $fullPath
     * @return bool
     */
    public static function isImage($fullPath)
    {
        return @is_array(getimagesize($fullPath));
    }

    /**
     * helper to check if given type/mime type is a PSD
     *
     * @param $type
     * @return bool
     */
    public static function isPSD($type)
    {
        return $type==='image/vnd.adobe.photoshop';
    }

    /**
     * helper to format bytes
     *
     * @param $size
     * @param int $precision
     * @return string
     */
    public static function formatBytes($size, $precision = 2)
    {
        if($size===0) return 0;

        $base = log($size) / log(1024);
        $suffix = array("", "k", "M", "G", "T")[floor($base)];
        return round(pow(1024, $base - floor($base)),$precision) . $suffix;
    }

    /**
     * converts KB,MB,GB,TB,PB to bytes
     *
     * @param $from
     * @return bool|string
     */
    public static function convertToBytes($from)
    {
        $number=substr($from,0,-2);
        switch(strtoupper(substr($from,-2))){
            case "KB":
                return $number*1024;
            case "MB":
                return $number*pow(1024,2);
            case "GB":
                return $number*pow(1024,3);
            case "TB":
                return $number*pow(1024,4);
            case "PB":
                return $number*pow(1024,5);
            default:
                return $from;
        }
    }
}