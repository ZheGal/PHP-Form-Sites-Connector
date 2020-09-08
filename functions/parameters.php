<?php

class Parameters
{
    public function __construct(General $site)
    {
        foreach ($site as $item => $val) {
            $this->$item = $val;
        }
    }

    public function code_metrika()
    {
        if ($this->settings['yandex'] && $this->settings['yandex'] != '0') {
            $code = $this->render(__DIR__ . '/metrika_view.php', [
                'code' => $this->settings['yandex'],
                'user_ip' => $this->get_user_ip()
            ]);
            return $code;
        }
        return null;
    }

    public function code_pixel($event = "Lead")
    {
        if ($this->settings['facebook'] && $this->settings['facebook'] != '0') {
            $code = $this->render(__DIR__ . '/pixel_view.php', [
                'code' => $this->settings['facebook'],
                'event' => $event
            ]);
            return $code;
        }
        return null;
    }

    public function code_pixel_img($event = "Lead")
    {
        if ($this->settings['facebook'] && $this->settings['facebook'] != '0') {
            $code = $this->render(__DIR__ . '/pixel_img_view.php', [
                'code' => $this->settings['facebook'],
                'event' => $event
            ]);
            return $code;
        }
        return null;
    }

    public function get_partner_script($partner)
    {
        if ($partner == 'partner' or $partner == 'global') {
            $script = "./functions/send.js";
            return "<script src=\"{$script}\"></script>\n";
        }
        return false;
    }

    public function get_neogara_script($partner)
    {
        if ($partner == 'neogara') {
            $query = [];
            $query['offer'] = ($this->settings['offer']) ? $this->settings['offer'] : null;
            $query['pid'] = ($this->settings['pid']) ? $this->settings['pid'] : null;
            $query['pid_utm'] = ($this->settings['pid_utm']) ? $this->settings['pid_utm'] : null;
            $query['return'] = ($this->settings['return']) ? $this->settings['return'] : null;
            if (($query['return'] != null or $query['return'] != '') && !empty($_SERVER['QUERY_STRING'])) {
                $query['return'] .= '?' . $_SERVER['QUERY_STRING'];
            }
            $query = array_diff($query, array(''));
            $http_query = (!empty($query)) ? '?' . http_build_query($query) : null;
            $link = "https://admin.neogara.com/script/neo_form.js{$http_query}";
            return "<script src=\"{$link}\"></script>\n";
        }
        return false;
    }

    public function get_user_ip()
    {
        $c = false;
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $c = $_SERVER['HTTP_CF_CONNECTING_IP'];
        } else {
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                 $c = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
                 $c = $_SERVER['REMOTE_ADDR'];
            } else {
                 $c = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
        }
        return $c;
    }

    public function render($path, $opt)
    {
        extract($opt);
        ob_start();
        require_once($path);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    public function get_utm_params()
    {
        $val = [];
        if (!empty($_REQUEST)) {
            foreach($_REQUEST as $param => $item) {
                if ($param != 'firstname' && $param != 'lastname' && $param != 'email' && $param != 'phone_number') {
                    $val[] = "<input type=\"hidden\" id=\"post_{$param}\" name=\"{$param}\" value=\"{$item}\">";
                }
            }
        }
        return implode("\n",$val)."\n\n";
    }

    public function get_utm_sendform()
    {
        $parm = $_REQUEST;
        $url = [];
        if (isset($parm['utm_facebook'])) {
            $parm['pxl'] = $parm['utm_facebook'];
            unset($parm['utm_facebook']);
        }
        if (isset($parm['utm_yandex'])) {
            $parm['ynd'] = $parm['utm_yandex'];
            unset($parm['utm_yandex']);
        }
        if (isset($parm['pxl'])) {
            $url[] = 'pxl='.$parm['pxl'];
        }
        if (isset($parm['ynd'])) {
            $url[] = 'ynd='.$parm['ynd'];
        }
        $url_str = implode("&",$url);
        if (!empty($url_str)) {
            return '?'.$url_str;
        }
    }

    public function get_setting($str)
    {
        $set = $this->settings;
        if (isset($set[$str])) {
            return $set[$str];
        }
        return false;
    }
}