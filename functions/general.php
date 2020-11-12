<?php

class General
{
    public $settings;
    public $home;

    public function __construct()
    {
        $this->import_settings();
        $this->check_utm_settings();
        $this->detect_home();
        $this->check_domain();
    }

    private function check_utm_settings()
    {
        $local = $this->settings;
        if (isset($_GET['pxl'])) {
            $_GET['utm_facebook'] = $_GET['pxl'];
            unset($_GET['pxl']);
        }
        if (isset($_GET['ynd'])) {
            $_GET['utm_yandex'] = $_GET['ynd'];
            unset($_GET['ynd']);
        }
        if (!empty($_GET)) {
            foreach ($_GET as $param => $item) {
                $check = explode("utm_", $param);
                if (empty($check[0])) {
                    $isAr = (isset($local[$check[1]]) && is_array($local[$check[1]]));
                    if (!$isAr) {
                        $get_prm = "utm_".$check[1];
                        $local[$check[1]] = $item;
                    }
                }
            }
        }
        $this->settings = $local;
    }

    private function check_domain()
    {
        if (isset($this->settings['domain']) && !empty($this->settings['domain'])) {
            if ($_SERVER['HTTP_HOST'] == $this->settings['domain']) {
                return false;
            }
            return $this->incorrect_domain();
        }
        return false;
    }

    public function render($options = [])
    {
        $file = $this->get_current_filepath();
        if ($file == '') {
            $file = $this->clean_filename();
        }
        if ($file != ''){
            $path = __DIR__ . "/..";
            $path = ($this->settings['dir']) ? "{$path}/{$this->settings['dir']}/" : "{$path}/";
            $filepath = $path.$file;
            $ext = end(explode(".", $filepath));
            if (file_exists($filepath)) {
                if ($ext == 'php' or $ext == 'html' or $ext == 'htm' or $ext == 'phtml') {
                    return $this->buferize($filepath, $options);
                } else {
                    return $this->not_script_file($filepath);
                }
            } else {
                return $this->file_not_found();
            }
        }
    }

    public function file_not_found()
    {
        header("HTTP/1.0 404 Not Found");
        die;
    }

    public function incorrect_domain()
    {
        header('HTTP/1.0 403 Forbidden');
        die;
    }

    public function not_script_file($file)
    {
        $ext = end(explode(".", $file));
        $size = filesize($file);
        if ($size >= 1000000) {
            if ($this->settings['dir'] != '') {
                $a = explode($this->settings['dir'], $this->home);
                $b = explode($_SERVER['HTTP_HOST'], $a['0']);
                array_shift($b);
                $c = implode($_SERVER['HTTP_HOST'], $b);
                $d = explode($c, $_SERVER['REQUEST_URI']);
                array_shift($d);
                $e = implode($c, $d);
                $filepath = 'https://'.$this->home.'/'.$e;
            } else {
                $name = explode('../', $file);
                $filename = end($name);
                $filepath = 'https://'.$this->home.'/'.$filename;
            }
            
            header("Location: {$filepath}");
            die;
        }

        $type = mime_content_type($file);
        if ($ext == 'css') {
            $type = 'text/css';
        } elseif ($ext == 'js') {
            $type = 'application/javascript';
        } elseif ($ext == 'svg') {
            $type= 'image/svg+xml';
        }
        header("Content-type: {$type}");
        echo file_get_contents($file);
        die;
    }

    public function buferize($path, $options)
    {
        $base = $this->check_if_not_func();
        extract($options);
        if (!$base) {
            ob_start();
            require_once($path);
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        } else {
            $file = file_get_contents($path);
            $file = $this->remove_local_connections($file);
            $temporary = uniqid().'.php';
            file_put_contents($temporary, $file);
            ob_start();
            require_once($temporary);
            $content = ob_get_contents();
            ob_end_clean();
            unlink($temporary);
            return $content;
        }
    }

    private function remove_local_connections($file)
    {
        $file = str_replace("\r\n","\n",$file);
        $remove = [
            '<?php if (file_exists("functions.php")) { require_once("functions.php"); }?>'
        ];
        $file = str_replace($remove, '', $file);
        $file = trim($file);
        return $file;
    }

    private function check_if_not_func()
    {
        $file = trim($_SERVER['PHP_SELF'], '\/ ');
        return ($file != 'functions.php');
    }

    private function clean_filename()
    {
        $path = __DIR__ . "/..";
        $path = trim(($this->settings['dir']) ? "{$path}/{$this->settings['dir']}" : $path, " \/?#");
        $files = ['index.php', 'index.html', 'index.htm'];
        foreach ($files as $file) {
            $filewind = "{$path}/{$file}";
            $filepath = "/{$path}/{$file}";
            if (file_exists($filepath)) {
                return $file;
            break;
            }
            elseif (file_exists($filewind)) {
                return $file;
            break;
            }
        }
        // return null;
    }

    private function get_current_filepath()
    {
        $dir = explode("functions.php", $_SERVER['PHP_SELF']);
        if (isset($dir['1'])){
            array_pop($dir);
        }
        $dir = implode("functions.php", $dir);
        $request = explode("?", $_SERVER['REQUEST_URI']);
        $request = $request[0];
        if ($dir != $request) {
            $request = explode($dir, $_SERVER['REQUEST_URI']);
            array_shift($request);
            $request = implode($dir, $request);
        } else {
            $request = trim($dir, '\/ ');
        }
        $request = explode("?", $request);
        return $request['0'];
    }

    private function import_settings()
    {
        $filepath = __DIR__ . "/../settings.json";
        $settings = $this->get_template_settings();
        if (file_exists($filepath)) {
            $array = json_decode(file_get_contents($filepath), 1);
            $array = array_diff($array, array(''));
            $settings = array_merge($settings, $array);
        }
        $this->settings = $settings;
    }

    private function get_template_settings()
    {
        return [
            "group" => "0",
            "offer" => "0",
            "pid" => "tyr7dc",
            "return" => "thanks.php",
            "yandex" => "",
            "facebook" => "",
            "partners" => [
                "global" => "1",
                "neogara" => "0",
                "partner" => "0"
            ],
            "language" => "",
            "sitename" => "",
            "cloakit" => "",
            "dir" => ""
        ];
    }

    private function detect_home()
    {
        $url = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
        if ($_SERVER['PHP_SELF'] == 'functions.php') {
            $array = explode("functions.php", $url);
            array_pop($array);
            $url = trim(implode("functions.php", $array), ' \/?');
        } else {
            $array = explode("/", $_SERVER['PHP_SELF']);
            $arr = explode(end($array), $url);
            array_pop($arr);
            $url = trim(implode(end($array), $arr), ' \/?');
        }
        $url = ($this->settings['dir']) ? "{$url}/{$this->settings['dir']}" : $url;
        return $this->home = $url;
    }

    public function get_partner()
    {   // получить имя используемого партнёра из настроек
        $partner = 'global';
        if ($this->settings['partners']) {
            foreach ($this->settings['partners'] as $partner => $val) {
                if ($val == 1) {
                    break;
                }
            }
            if ($val == 0) {
                $partner = array_key_first($this->settings['partners']);
            }
        }
        return $partner;
    }

    public function get_offer()
    {
        return ($this->settings['offer']) ? $this->settings['offer'] : null;
    }

    public function get_pid()
    {
        return ($this->settings['pid']) ? $this->settings['pid'] : null;
    }

    public function get_pid_utm()
    {
        return ($this->settings['pid_utm']) ? $this->settings['pid_utm'] : null;
    }

    public function get_return()
    {
        return ($this->settings['return']) ? $this->settings['return'] : null;
    }

    public function get_yandex()
    {
        return ($this->settings['yandex']) ? $this->settings['yandex'] : null;
    }

    public function get_facebook()
    {
        return ($this->settings['facebook']) ? $this->settings['facebook'] : null;
    }

    public function get_language()
    {
        return ($this->settings['language']) ? $this->settings['language'] : null;
    }

    public function get_sitename()
    {
        return ($this->settings['sitename']) ? $this->settings['sitename'] : null;
    }
}