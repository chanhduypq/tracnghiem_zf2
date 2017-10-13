<?php

namespace Zend\Common;

class Download {

    /**
     * 
     * function common
     * @author Trần Công Tuệ <chanhduypq@gmail.com>
     * @param string $path
     * @param string $fileName
     */
    public static function download($path, $fileName = null) {
        if (!is_string($fileName) || trim($fileName) == '') {
            $fileNameForDownload = '';
            $files = scandir($path, 0);
            foreach ($files as $file) {
                if ($file != '.' || $file != '..') {
                    $fileNameForDownload = $file;
                }
            }
        } else {
            $fileNameForDownload = $fileName;
        }

        //download được file dung lượng lớn
        self::downloadFile($path, $fileNameForDownload);


        //cách này không download được file dung lượng lớn
//        header('Content-Description: File Transfer');
//        header('Content-Type: application/octet-stream');
//        header('Content-Disposition: attachment; filename="' . $fileNameForDownload . '"');
//        header('Expires: 0');
//        header('Cache-Control: must-revalidate');
//        header('Pragma: public');
//        header('Content-Length: ' . filesize(rtrim($path, '/') . '/' . $fileNameForDownload));
//        readfile(rtrim($path, '/') . '/' . $fileNameForDownload);
//        exit;
    }

    public static function downloadFile($path, $fileName, $maxSpeed = 100, $doStream = false) {

        $seek_start = 0;
        $seek_end = -1;
        $data_section = false;
        $buffsize = 2048;

        if (isset($_SERVER['HTTP_RANGE'])) {
            $seek_range = substr($_SERVER['HTTP_RANGE'], strlen('bytes='));

            $range = explode('-', $seek_range);

            if ($range[0] > 0) {
                $seek_start = intval($range[0]);
            }
            if ($range[1] > 0) {
                $seek_end = intval($range[1]);
            }

            $data_section = true;
        }

        ob_end_clean();
        $old_status = ignore_user_abort(true);
        set_time_limit(0);

        $size = filesize(rtrim($path, '/') . '/' . $fileName);

        if ($seek_start > ($size - 1)) {
            $seek_start = 0;
        }

        $res = fopen(rtrim($path, '/') . '/' . $fileName, 'rb');
        if ($seek_start) {
            fseek($res, $seek_start);
        }
        if ($seek_end < $seek_start) {
            $seek_end = $size - 1;
        }

        if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")) {
            $fileName = rawurlencode($fileName);
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        }

        header('Content-Type: application/octet-stream');
        /**
         * 
         */
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Last-Modified: ' . date('D, d M Y H:i:s \G\M\T', filemtime(rtrim($path, '/') . '/' . $fileName)));

        if ($data_section) {
            header("HTTP/1.0 206 Partial Content");
            header("Status: 206 Partial Content");
            header('Accept-Ranges: bytes');
            header("Content-Range: bytes $seek_start-$seek_end/$size");
            header("Content-Length: " . ($seek_end - $seek_start + 1));
        } else {
            header('Content-Length: ' . $size);
        }

        while (!( connection_aborted() || connection_status() == 1) && !feof($res)) {
            print(fread($res, $buffsize * $maxSpeed));

            flush();
            @ob_flush();
//            sleep(1);
        }

        fclose($res);

        ignore_user_abort($old_status);
        set_time_limit(ini_get('max_execution_time'));
    }

}
