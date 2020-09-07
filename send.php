<?php

$utm = '';

if (isset($_SERVER['HTTP_REFERER'])) {
    $utm_ar = explode("?", $_SERVER['HTTP_REFERER']);
    array_shift($utm_ar);
    $utm = '?'.implode("?", $utm_ar);
}

if (!empty($_POST)) {
    $send = get_send();
    if (isset($send['SUCCESS']) && $send['SUCCESS'] == 1) {
        $file = __DIR__ . '/settings.json';
        $file_get = file_get_contents($file);
        $array = json_decode($file_get, 1);
        $return = $array['return'].$utm;
        header("Location:{$return}");
    } else {
        $back = '/'.$utm;
        header("Location:{$back}");
    } 
}

function get_send()
{
    ob_start();
    require_once("functions/send.php");
    $content = ob_get_contents();
    ob_end_clean();
    $json = json_decode($content, 1);
    if (is_array($json)) {
        return $json;
    }
    return $content;
}