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

    public function code_metrika_thanks()
    {
        if ($this->settings['yandex'] && $this->settings['yandex'] != '0') {
            return "<script>ym({$this->settings['yandex']},'reachGoal','formSubmit')</script>";
        }
        return null;
    }

    public function code_metrika_targetclick()
    {
        if ($this->settings['yandex'] && $this->settings['yandex'] != '0') {
            return "<script>ym({$this->settings['yandex']},'targetclick','formSubmit')</script>";
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
            $query['group'] = ($this->settings['group']) ? $this->settings['group'] : null;
            $query['offer'] = ($this->settings['offer']) ? $this->settings['offer'] : null;
            $query['pid'] = ($this->settings['pid']) ? $this->settings['pid'] : null;
            $query['pid_utm'] = ($this->settings['pid_utm']) ? $this->settings['pid_utm'] : null;
            $query['return'] = ($this->settings['return']) ? $this->settings['return'] : null;
            if (($query['return'] != null or $query['return'] != '') && !empty($_SERVER['QUERY_STRING'])) {
                $query['return'] .= '?' . $_SERVER['QUERY_STRING'];
            }
            if (isset($query['group']) && !empty($query['group']) && $query['group'] != '') {
                unset($query['offer']);
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
        require($path);
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
        $parm = $_GET;
        $url = [];
        if (isset($parm['utm_facebook'])) {
            $parm['pxl'] = $parm['utm_facebook'];
            unset($parm['utm_facebook']);
        }
        if (isset($parm['utm_yandex'])) {
            $parm['ynd'] = $parm['utm_yandex'];
            unset($parm['utm_yandex']);
        }
        if (!empty($parm)) {
            foreach ($parm as $item => $val) {
                $url[] = "{$item}={$val}";
            }
            $url_str = implode("&",$url);
        }
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

    public function get_phone_code()
    {
        $country = $this->get_country();
        $ccode = $country['country'];
        
        $codes = [7 => ["RU","KZ"], 20 => ["EG"], 27 => ["ZA"], 30 => ["GR"], 31 => ["NL"], 32 => ["BE"], 33 => ["FR"], 34 => ["ES"], 36 => ["HU"], 39 => ["IT","VA"], 40 => ["RO"], 41 => ["CH"], 43 => ["AT"], 44 => ["GB","GG","IM","JE"], 45 => ["DK"], 46 => ["SE"], 47 => ["NO","SJ"], 48 => ["PL"], 49 => ["DE"], 51 => ["PE"], 52 => ["MX"], 53 => ["CU"], 54 => ["AR"], 55 => ["BR"], 56 => ["CL"], 57 => ["CO"], 58 => ["VE"], 60 => ["MY"], 61 => ["AU","CC","CX"], 62 => ["ID"], 63 => ["PH"], 64 => ["NZ"], 65 => ["SG"], 66 => ["TH"], 81 => ["JP"], 82 => ["KR"], 84 => ["VN"], 86 => ["CN"], 90 => ["TR"], 91 => ["IN"], 92 => ["PK"], 93 => ["AF"], 94 => ["LK"], 95 => ["MM"], 98 => ["IR"], 211 => ["SS"], 212 => ["MA","EH"], 213 => ["DZ"], 216 => ["TN"], 218 => ["LY"], 220 => ["GM"], 221 => ["SN"], 222 => ["MR"], 223 => ["ML"], 224 => ["GN"], 225 => ["CI"], 226 => ["BF"], 227 => ["NE"], 228 => ["TG"], 229 => ["BJ"], 230 => ["MU"], 231 => ["LR"], 232 => ["SL"], 233 => ["GH"], 234 => ["NG"], 235 => ["TD"], 236 => ["CF"], 237 => ["CM"], 238 => ["CV"], 239 => ["ST"], 240 => ["GQ"], 241 => ["GA"], 242 => ["CG"], 243 => ["CD"], 244 => ["AO"], 245 => ["GW"], 246 => ["IO"], 247 => ["AC"], 248 => ["SC"], 249 => ["SD"], 250 => ["RW"], 251 => ["ET"], 252 => ["SO"], 253 => ["DJ"], 254 => ["KE"], 255 => ["TZ"], 256 => ["UG"], 257 => ["BI"], 258 => ["MZ"], 260 => ["ZM"], 261 => ["MG"], 262 => ["RE","YT"], 263 => ["ZW"], 264 => ["NA"], 265 => ["MW"], 266 => ["LS"], 267 => ["BW"], 268 => ["SZ"], 269 => ["KM"], 290 => ["SH","TA"], 291 => ["ER"], 297 => ["AW"], 298 => ["FO"], 299 => ["GL"], 350 => ["GI"], 351 => ["PT"], 352 => ["LU"], 353 => ["IE"], 354 => ["IS"], 355 => ["AL"], 356 => ["MT"], 357 => ["CY"], 358 => ["FI","AX"], 359 => ["BG"], 370 => ["LT"], 371 => ["LV"], 372 => ["EE"], 373 => ["MD"], 374 => ["AM"], 375 => ["BY"], 376 => ["AD"], 377 => ["MC"], 378 => ["SM"], 380 => ["UA"], 381 => ["RS"], 382 => ["ME"], 385 => ["HR"], 386 => ["SI"], 387 => ["BA"], 389 => ["MK"], 420 => ["CZ"], 421 => ["SK"], 423 => ["LI"], 500 => ["FK"], 501 => ["BZ"], 502 => ["GT"], 503 => ["SV"], 504 => ["HN"], 505 => ["NI"], 506 => ["CR"], 507 => ["PA"], 508 => ["PM"], 509 => ["HT"], 590 => ["GP","BL","MF"], 591 => ["BO"], 592 => ["GY"], 593 => ["EC"], 594 => ["GF"], 595 => ["PY"], 596 => ["MQ"], 597 => ["SR"], 598 => ["UY"], 599 => ["CW","BQ"], 670 => ["TL"], 672 => ["NF"], 673 => ["BN"], 674 => ["NR"], 675 => ["PG"], 676 => ["TO"], 677 => ["SB"], 678 => ["VU"], 679 => ["FJ"], 680 => ["PW"], 681 => ["WF"], 682 => ["CK"], 683 => ["NU"], 685 => ["WS"], 686 => ["KI"], 687 => ["NC"], 688 => ["TV"], 689 => ["PF"], 690 => ["TK"], 691 => ["FM"], 692 => ["MH"], 800 => ["001"], 808 => ["001"], 850 => ["KP"], 852 => ["HK"], 853 => ["MO"], 855 => ["KH"], 856 => ["LA"], 870 => ["001"], 878 => ["001"], 880 => ["BD"], 881 => ["001"], 882 => ["001"], 883 => ["001"], 886 => ["TW"], 888 => ["001"], 960 => ["MV"], 961 => ["LB"], 962 => ["JO"], 963 => ["SY"], 964 => ["IQ"], 965 => ["KW"], 966 => ["SA"], 967 => ["YE"], 968 => ["OM"], 970 => ["PS"], 971 => ["AE"], 972 => ["IL"], 973 => ["BH"], 974 => ["QA"], 975 => ["BT"], 976 => ["MN"], 977 => ["NP"], 979 => ["001"], 992 => ["TJ"], 993 => ["TM"], 994 => ["AZ"], 995 => ["GE"], 996 => ["KG"], 998 => ["UZ"]];
        foreach ($codes as $code => $country) {
            if (in_array($ccode, $country)) {
                return "+{$code}";
            }
        }
        return false;
    }

    public function get_country()
    {
        $c = false;
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $c = json_decode(file_get_contents("https://ipinfo.io/{$_SERVER['HTTP_CF_CONNECTING_IP']}"),1);
        } else {
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $c = json_decode(file_get_contents("https://ipinfo.io/{$_SERVER['HTTP_CLIENT_IP']}"),1);
            } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
                $c = json_decode(file_get_contents("https://ipinfo.io/{$_SERVER['REMOTE_ADDR']}"),1);
            } else {
                $c = json_decode(file_get_contents("https://ipinfo.io/{$_SERVER['HTTP_X_FORWARDED_FOR']}"),1);
            }
        }
        return $c;
    }
}