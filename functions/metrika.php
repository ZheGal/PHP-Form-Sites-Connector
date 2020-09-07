<?php

class Metrika
{
    public function __construct(General &$site)
    {
        $this->check_metrika($site->settings);
        return $site;
    }

    private function check_metrika(&$set)
    {
        if (!isset($set['yandex']) or $set['yandex'] == '' or $set['yandex'] == '0') {
            $code = $this->generate_new_code($set);
            $this->save_new_metrika($code);
            $set['yandex'] = $code;
        }
    }

    private function save_new_metrika($code)
    {
        $file = __DIR__ . '/../settings.json';
        $array = json_decode(file_get_contents($file), 1);
        $array['yandex'] = $code;
        $json = json_encode($array, JSON_PRETTY_PRINT);
        file_put_contents($file, $json);
    }

    private function generate_new_code($set)
    {
        $lang = $set['language'];
        $site = $set['sitename'];
        
        $title = $lang . ' - ' . $site;
        $arr = base64_encode(json_encode(['https://' . $_SERVER['HTTP_HOST'], $title]));
        $metrika_url = "http://paternii.pl/metrika/".$arr;
        $get = $this->get_contents_metrika($metrika_url, $lang, $site);
        return $get;
    }

    private function get_contents_metrika($url)
    {
        $sitename = $lang.' - '.$site;
        $data = [
            'sitename' => $sitename,
            'url' => $_SERVER['HTTP_HOST']
        ];
        $data = json_encode($data);
        $opts = array('http' => array('method'=>"POST",'content'=>$data));
        $context = stream_context_create($opts);
        $file = file_get_contents($url, false, $context);
        return intval($file);
    }
}