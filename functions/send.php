<?php

if($_POST){
    $app = new Sender;
    $debug = ($_GET['debug']) ? $_GET['debug'] : '';
    $crm = $app->send_to_crm();
    if ($crm) {
        $mail = $app->send_email('kovalenkojurij93@gmail.com');
        if ($debug == 'yes') {
            echo json_encode([$app->apicrm, $mail]);
        } else {
            echo $mail;
        }
    }
}

class Sender
{
    public $apicrm = [];

    public function __construct()
    {
        $this->get_settings();
        $this->check_partner();
        $this->get_index_vals();
        $this->get_partner_params();
    }

    private function data_global()
    {
        return [
            'url' => 'https://my.globalmaxis.com/',
            'api' => 'fs3TXHRU6j6QyE2',
            'additional' => '21'
        ];
    }

    private function data_partner()
    {
        return [
            'url' => 'https://my.partnergroupe.com/',
            'api' => 'yK2SxgJNS3FgLGea',
            'additional' => '22'
        ];   
    }

    private function get_settings()
    {
        $file = 'settings.json';
        $path = __DIR__.'/../'.$file;
        if(file_exists($path)){
            $settings = file_get_contents($path);
            $set_ar = json_decode($settings,1);
            $a = $set_ar['partners'];
            $this->settings = $a;
        }
        else{
            return false;
            die;
        }
    }

    private function check_partner()
    {
        if(!empty($this->settings)){
            foreach($this->settings as $a => $b){
                if($b == '1'){
                    $partner = $a;
                    break;
                }
            }
            if(!$partner){
                $partner = array_key_first($this->settings);
            }
            if(!empty($partner) OR $partner != '') $this->partner = $partner;
            $this->check_partner_data();
        }
        else{
            return false;
            die;
        }
    }

    private function check_partner_data()
    {
        if(!empty($this->partner)){
            $func = 'data_'.$this->partner;
            if(method_exists($this,$func)){
                $this->api = $this->$func();
            } else {
                return false; die;
            }
        } else {
            return false; die;
        }
    }

    private function get_index_vals()
    {
        $index = __DIR__.'/../settings.json';
        if(file_exists($index)){
            $a = file_get_contents($index);
            $json = json_decode($a,1);
            $this->sitename = $json['sitename'];
            $this->language = $json['language'];
        }
        else{
            return false;
            die;
        }
    }

    public function send_to_crm()
    {
        $params = [
            'key' => $this->api['api'],
            'rand_param' => rand(1000000, 99999999),
            'first_name' => htmlentities($_REQUEST["firstname"],ENT_COMPAT,'UTF-8'),
            'second_name' => htmlentities($_REQUEST["lastname"],ENT_COMPAT,'UTF-8'),
            'phone' => htmlentities($_REQUEST["phone_number"],ENT_COMPAT,'UTF-8'),
            'email' => htmlentities($_REQUEST["email"],ENT_COMPAT,'UTF-8'),
            'description' => 'description',
            'country' => $this->get_country(),
            'additionalField'.$this->api['additional'] => $this->get_additional(),
            'desk_id' => $this->desk_id,
            'responsible' => $this->responsible,
            'date_of_birth' => '',
            'additionalField20' => htmlentities($_REQUEST["pid"],ENT_COMPAT,'UTF-8'),
            'additionalField22' => '',
            'additionalField23' => '',
            'additionalField24' => '',
            'additionalField25' => '',
            'additionalField26' => htmlentities($_REQUEST["apsubid1"],ENT_COMPAT,'UTF-8'),
            'additionalField27' => htmlentities($_REQUEST["apsubid2"],ENT_COMPAT,'UTF-8'),
            'additionalField28' => htmlentities($_REQUEST["apsubid3"],ENT_COMPAT,'UTF-8'),
            'additionalField29' => htmlentities($_REQUEST["apsubid4"],ENT_COMPAT,'UTF-8'),
            'additionalField30' => htmlentities($_REQUEST["utm_source"],ENT_COMPAT,'UTF-8'),
            'additionalField31' => htmlentities($_REQUEST["utm_medium"],ENT_COMPAT,'UTF-8'),
            'additionalField32' => htmlentities($_REQUEST["utm_campaign"],ENT_COMPAT,'UTF-8'),
            'additionalField33' => htmlentities($_REQUEST["utm_content"],ENT_COMPAT,'UTF-8'),
            'additionalField34' => htmlentities($_REQUEST["sub1"],ENT_COMPAT,'UTF-8'),
            'additionalField35' => htmlentities($_REQUEST["sub2"],ENT_COMPAT,'UTF-8'),
            'additionalField36' => htmlentities($_REQUEST["sub3"],ENT_COMPAT,'UTF-8'),
            'additionalField37' => htmlentities($_REQUEST["sub4"],ENT_COMPAT,'UTF-8'),
            'additionalField38' => htmlentities($_REQUEST["txt"],ENT_COMPAT,'UTF-8'),
        ];
        $params = array_diff($params, array(''));
        $params['key'] = md5($params['key'] . $params['rand_param']);
        $params = array_diff($params,array(''));
        $url = $this->api['url'].'api/v_2/crm/CreateLead?'.http_build_query($params);
        $api = json_decode(file_get_contents($url),1);
        $this->apicrm = $api;
        if ($api['result'] == 'success') {
            return true;
        }
        return false;
    }

    public function send_email($mail = ''){
        $view = __DIR__."/view.php";
        $message = '';
        if(file_exists($view)){
            ob_start();
            require_once($view);
            $content = ob_get_contents();
            ob_end_clean();
            $subject = strval($this->language.' '.$this->additional.' ' . htmlentities($_SERVER["SERVER_NAME"],ENT_COMPAT,'UTF-8'));
            $message = $content;
            $message = $this->cleanup_message($message);
            $form_mail = $this->cleanup_email($_REQUEST['email']);
            $headers = [
                'From:  info@'.$_SERVER["SERVER_NAME"],
                'Reply-To: ' . $form_mail,
                'X-Mailer: PHP/' . phpversion(),
                'Content-type: text/html; charset=utf-8'
            ];
			$headers = implode("\r\n",$headers);
            if($_REQUEST["phone_number"] != "" OR !empty($_REQUEST["phone_number"])){
                $sent = mail($mail, $subject, $message, $headers);
                if($sent){
                    $result = array(
                        "SUCCESS" => true,
                        "MESSAGE" => ''
                    );
                    return json_encode($result);
                }
				else{
					return $sent;
				}
            }
            else return false; die;
        }
        else{
            return false; die;
        }
        die;
    }

    private function cleanup_message($message = ''){
        $message = wordwrap($message, 70, "\r\n");
        return $message;
    }
    
    private function cleanup_email($email = ''){
        $email = htmlentities($email,ENT_COMPAT,'UTF-8');
        $email = preg_replace('=((<CR>|<LF>|0x0A/%0A|0x0D/%0D|\\n|\\r)\S).*=i', null, $email);
        return $email;
    }

    private function get_country()
    {
        $c = false;
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $c = json_decode(file_get_contents("https://ipinfo.io/{$_SERVER['HTTP_CF_CONNECTING_IP']}"))->country;
        } else {
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $c = json_decode(file_get_contents("https://ipinfo.io/{$_SERVER['HTTP_CLIENT_IP']}"))->country;
            } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
                $c = json_decode(file_get_contents("https://ipinfo.io/{$_SERVER['REMOTE_ADDR']}"))->country;
            } else {
                $c = json_decode(file_get_contents("https://ipinfo.io/{$_SERVER['HTTP_X_FORWARDED_FOR']}"))->country;
            }
        }
        return $c;
    }

    private function get_additional()
    {
        return $this->additional;
    }

    private function get_partner_params()
    {
        $file = "params.json";
        $path = __DIR__.'/'.$file;
        if(file_exists($path)){
            $a = file_get_contents($path);
            $b = json_decode($a,1);
            if(isset($b[$this->partner])){
                $now = $b[$this->partner];
                $this->desk_id = $now['desk'][$this->language];
                $this->responsible = $now['responsible'][$this->language];
                $this->additional = $now['campaign'][$this->sitename];
            }
        }
        else{
            return false; die;
        }
    }


}