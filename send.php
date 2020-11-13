<?php

// если метод не пост, редирект на главную
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    go_home();
}

// получаем настройки
$settings = check_settings();
// получаем данные из формы + utm
$post = get_all_post();

if (!$settings) {
    // если нет настроек, редиректим на главную
    go_home();
}

// получаем название партнёра
$partner = return_partner($settings);
$func_send = "send_{$partner}";

if (!function_exists($func_send)) {
    go_home();
}

$params = get_params();
$send = $func_send($post, $params, $settings);

if ($send) {
    $return = $settings['return'];
    if (empty($return)) {
        go_home();
    }
    header("Location:{$return}");
}



/***************************/

function get_utm()
{
    $result = [];
    $ref = $_SERVER['HTTP_REFERER'];
    $ref_ar = explode("?", $ref);
    if ($ref_ar[1]) {
        array_shift($ref_ar);
        $url = implode("?", $ref_ar);
        parse_str($url, $result);
    }
    return $result;
}

function get_all_post()
{
    $utm = get_utm();
	$result = [];
	if (isset($_REQUEST['phone_code'])) {
		$_REQUEST['phone_number'] = $_REQUEST['phone_code'].$_REQUEST['phone_number'];
		unset($_REQUEST['phone_code']);
	}
	
    foreach ($_REQUEST as $key => $val) {
        if ($key[0] != '_') {
            $result[$key] = $val;
        }
    }
    $result = array_merge($result, $utm);
    return $result;
}

function get_params()
{
    $result = [];
    $a = implode(DIRECTORY_SEPARATOR, [__DIR__, 'functions', 'params.json']);
    if (file_exists($a)) {
        $raw = file_get_contents($a);
        $result = json_decode($raw, 1);
    }
    return $result;
}

function check_settings()
{
    $file = implode(DIRECTORY_SEPARATOR, [__DIR__, 'settings.json']);
    if (file_exists($file)) {
        $raw = file_get_contents($file);
        return json_decode($raw, 1);
    }
    return false;
}

function go_home()
{
    header("Location:/");
}

function return_partner($array = [])
{
    $partner = $array['partners'];
    foreach ($partner as $key => $value) {
        if ($value == 1) {
            return $key;
        }
    }
    foreach ($partner as $key => $value) {
        return $key;
    }
}

function send_neogara($post = [])
{
	header("Location:/");
}

function send_global($post = [], $prm = [], $set = [])
{
    $me = 'global';
	$rand_param = rand(1000000, 99999999);
	$arFields = [
		'rand_param' => $rand_param,
		'first_name' => htmlentities($post["firstname"],ENT_COMPAT,'UTF-8'),
		'second_name' => htmlentities($post["lastname"],ENT_COMPAT,'UTF-8'),
		'phone' => htmlentities($post["phone_number"],ENT_COMPAT,'UTF-8'),
		'email' => htmlentities($post["email"],ENT_COMPAT,'UTF-8'),
		'description' => 'description',
		'country' => get_country(),
		'additionalField21' => $prm[$me]['campaign'][$set['sitename']],
		'desk_id' => $prm[$me]['desk'][$set['language']],
		'responsible' => $prm[$me]['responsible'][$set['language']],
		'date_of_birth' => '',
		'additionalField20' => htmlentities($post["pid"],ENT_COMPAT,'UTF-8'),
		'additionalField22' => '',
		'additionalField23' => '',
		'additionalField24' => '',
		'additionalField25' => '',
		'additionalField26' => htmlentities($post["apsubid1"],ENT_COMPAT,'UTF-8'),
		'additionalField27' => htmlentities($post["apsubid2"],ENT_COMPAT,'UTF-8'),
		'additionalField28' => htmlentities($post["apsubid3"],ENT_COMPAT,'UTF-8'),
		'additionalField29' => htmlentities($post["apsubid4"],ENT_COMPAT,'UTF-8'),
		'additionalField30' => htmlentities($post["utm_source"],ENT_COMPAT,'UTF-8'),
		'additionalField31' => htmlentities($post["utm_medium"],ENT_COMPAT,'UTF-8'),
		'additionalField32' => htmlentities($post["utm_campaign"],ENT_COMPAT,'UTF-8'),
		'additionalField33' => htmlentities($post["utm_content"],ENT_COMPAT,'UTF-8'),
		'additionalField34' => htmlentities($post["sub1"],ENT_COMPAT,'UTF-8'),
		'additionalField35' => htmlentities($post["sub2"],ENT_COMPAT,'UTF-8'),
		'additionalField36' => htmlentities($post["sub3"],ENT_COMPAT,'UTF-8'),
		'additionalField37' => htmlentities($post["sub4"],ENT_COMPAT,'UTF-8'),
		'additionalField38' => htmlentities($post["txt"],ENT_COMPAT,'UTF-8'),
    ];

	$params = [];
	foreach($arFields as $code => $value)
		if($value)
			$params[] = $code . '=' . $value;

	$key = md5('fs3TXHRU6j6QyE2' . $rand_param);

	$api = json_decode(file_get_contents(str_replace(' ','%20','https://my.globalmaxis.com/api/v_2/crm/CreateLead?key=' . $key . '&' . implode('&', $params))), true);
    
    if ($api['result'] == 'success') {
        return $api;
    }
    return false;
}

function send_partner($post = [])
{
    $me = 'partner';
	$rand_param = rand(1000000, 99999999);
	$arFields = [
		'rand_param' => $rand_param,
		'first_name' => htmlentities($post["firstname"],ENT_COMPAT,'UTF-8'),
		'second_name' => htmlentities($post["lastname"],ENT_COMPAT,'UTF-8'),
		'phone' => htmlentities($post["phone_number"],ENT_COMPAT,'UTF-8'),
		'email' => htmlentities($post["email"],ENT_COMPAT,'UTF-8'),
		'description' => 'description',
		'country' => get_country(),
		'additionalField22' => $prm[$me]['campaign'][$set['sitename']],
		'desk_id' => $prm[$me]['desk'][$set['language']],
		'responsible' => $prm[$me]['responsible'][$set['language']],
		'date_of_birth' => '',
		'additionalField20' => htmlentities($post["pid"],ENT_COMPAT,'UTF-8'),
		'additionalField22' => '',
		'additionalField23' => '',
		'additionalField24' => '',
		'additionalField25' => '',
		'additionalField26' => htmlentities($post["apsubid1"],ENT_COMPAT,'UTF-8'),
		'additionalField27' => htmlentities($post["apsubid2"],ENT_COMPAT,'UTF-8'),
		'additionalField28' => htmlentities($post["apsubid3"],ENT_COMPAT,'UTF-8'),
		'additionalField29' => htmlentities($post["apsubid4"],ENT_COMPAT,'UTF-8'),
		'additionalField30' => htmlentities($post["utm_source"],ENT_COMPAT,'UTF-8'),
		'additionalField31' => htmlentities($post["utm_medium"],ENT_COMPAT,'UTF-8'),
		'additionalField32' => htmlentities($post["utm_campaign"],ENT_COMPAT,'UTF-8'),
		'additionalField33' => htmlentities($post["utm_content"],ENT_COMPAT,'UTF-8'),
		'additionalField34' => htmlentities($post["sub1"],ENT_COMPAT,'UTF-8'),
		'additionalField35' => htmlentities($post["sub2"],ENT_COMPAT,'UTF-8'),
		'additionalField36' => htmlentities($post["sub3"],ENT_COMPAT,'UTF-8'),
		'additionalField37' => htmlentities($post["sub4"],ENT_COMPAT,'UTF-8'),
		'additionalField38' => htmlentities($post["txt"],ENT_COMPAT,'UTF-8'),
    ];

	$params = [];
	foreach($arFields as $code => $value)
		if($value)
			$params[] = $code . '=' . $value;

	$key = md5('yK2SxgJNS3FgLGea' . $rand_param);

	$api = json_decode(file_get_contents(str_replace(' ','%20','https://my.partnergroupe.com/api/v_2/crm/CreateLead?key=' . $key . '&' . implode('&', $params))), true);
    
    if ($api['result'] == 'success') {
        return $api;
    }
    return false;
}

function get_country()
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