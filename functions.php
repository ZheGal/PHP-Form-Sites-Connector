<?php
require_once("./functions/general.php");
require_once("./functions/additional.php");
require_once("./functions/parameters.php");

$site = new General();
$adds = new Additional($site);
$parm = new Parameters($site);

$view = '';
$view = $site->render([
    'metrika' => $parm->code_metrika(),
    'metrika_thanks' => $parm->code_metrika_thanks(),
    'metrika_targetclick' => $parm->code_metrika_targetclick(),
    'pixel' => $parm->code_pixel("Lead"),
    'pixel_img' => $parm->code_pixel_img("Lead"),
    'partner' => $parm->get_partner_script($site->get_partner()),
    'neogara' => $parm->get_neogara_script($site->get_partner()),
    'number' => $parm->get_phone_code(),
    'utm_form' => $parm->get_utm_sendform()
]);
$adds->clean_comments($view);
// $adds->fake_classes($view);
// $adds->fake_divs($view);
$adds->clean_phone_form($view);

echo $view;
die;