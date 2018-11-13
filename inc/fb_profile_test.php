<?php

$ch = curl_init();

$url = 'https://m.facebook.com/mardhani.riasetiawan';

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);

$profile = array();

//match name
preg_match_all('!<a href="\/title\/.*?\/\?ref_=adv_li_tt"\n>(.*?)<\/a>!',$result,$match);
$profile['name'] = $match[1];





<?