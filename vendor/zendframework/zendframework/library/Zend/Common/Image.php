<?php

namespace Zend\Common;

class Image {

    /**
     * function common
     * view image theo đúng tỉ lệ file gốc
     * @author Trần Công Tuệ <chanhduypq@gmail.com>
     * @param string $path
     * @param string|int $width_for_view
     * @param string|int $height_for_view
     * @param string $order ưu tiên width hay height nếu $width_for_view/$height_for_view không đúng tỉ lệ file gốc
     * @return array 
     */
    public static function echoImage($image_path, $width_for_view, $height_for_view, $order = "width", $html_option = array()) {
        if (!is_string($order) || ($order != "width" && $order != "height")) {
            echo "";
            return;
        }
        if (!is_string($image_path) || trim($image_path) == "") {
            echo "";
            return;
        }
        if ($image_path[0] != '/') {
            if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $image_path) || !is_file($_SERVER['DOCUMENT_ROOT'] . '/' . $image_path)) {
                echo "";
                return;
            }
            $url = $image_path;
        } else if ($image_path[0] == '/') {
            if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $image_path) || !is_file($_SERVER['DOCUMENT_ROOT'] . $image_path)) {
                echo "";
                return;
            }
            $url = ltrim($image_path, '/');
        }
        if (file_exists($url)) {
            list($width_orig, $height_orig) = getimagesize($url);
        } else {
            list($width_orig, $height_orig) = getimagesize($_SERVER['DOCUMENT_ROOT'] . '/' . $image_path);
        }

//        if($width_orig>=$height_orig){
//            $width = $width_for_view;
//            $ratio = $height_orig / $width_orig;
//            $height = ceil($width * $ratio);
//        }
//        else{
//            $height = $height_for_view;       
//            $ratio = $width_orig / $height_orig;
//            $width = ceil($height * $ratio);
//        }
        if ($order == "width") {
            if ($width_orig > $width_for_view) {
                $width = $width_for_view;
            } else {
                $width = $width_orig;
            }
            $ratio = $height_orig / $width_orig;
            $height = ceil($width * $ratio);
        } else if ($order == "height") {
            if ($height_orig > $height_for_view) {
                $height = $height_for_view;
            } else {
                $height = $height_orig;
            }
            $ratio = $width_orig / $height_orig;
            $width = ceil($height * $ratio);
        }
        if ($image_path[0] != '/') {
            $image_path = '/' . $image_path;
        }
        $attr_string = "";
        foreach ($html_option as $key => $value) {
            $attr_string .= " $key='$value'";
        }
        echo '<img' . $attr_string . ' style="margin: 0 auto;width: ' . $width . 'px;height: ' . $height . 'px;" src="' . $image_path . '"/>';
    }

}
