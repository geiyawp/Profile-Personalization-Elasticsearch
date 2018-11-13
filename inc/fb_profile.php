<?php
include_once('simple_html_dom.php');

$mardhani = "https://www.facebook.com/mardhani.riasetiawan";
$budi = "https://www.facebook.com/buditriwibowoyuli";
$andra = "https://www.facebook.com/andra.armeda";
$m_mardhani = "https://m.facebook.com/mardhani.riasetiawan";


function get_html($url, $post=false, $gzip=true){
    $ch = curl_init();
    $header=array(
        'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language: en-us,en;q=0.5',
        'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7',
        'Keep-Alive: 115',
        'Connection: keep-alive',
    );
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
    if($post)
    {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            http_build_query($post));
    }
    curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if($gzip)
    curl_setopt($ch, CURLOPT_ENCODING, "gzip"); // the page encoding
    $server_output = curl_exec ($ch);
    curl_close ($ch);
    return $server_output;
}

function scrap_profile($url) {
    // create HTML DOM
    $content = get_html($url);
    if(!empty($content)) {
        $html = str_get_html($content);
        if (!empty($html)) {
        $ret['name'] = $html->find('h1', 0)->plaintext;
        $ret['location'] = $html->find('li[id=current_city] a', 0)->plaintext;
        $ret['hometown'] = $html->find('li[id=hometown] a', 0)->plaintext;

        $edu = $html->find('div[id=collection_wrapper_2327158227] div[class=_4qm1]', 1);
        if (!empty($edu)) {
        $education = $edu->find('li', 0)->find('div[class=fsm fwn fcg]', 0)->plaintext;
        preg_match_all("~.+?(?=\·)~", $education, $study);

        $ret['education'][0] = $study[0][0];
        $ret['education'][1] = $html->find('div[id=collection_wrapper_2327158227] div[class=_4qm1]', 1)->find('a', 1)->innertext;
        }

        $ret['work'] = $html->find('div[id=collection_wrapper_2327158227] a', 1)->innertext;
        unset($html);
        }
    }
    unset($content);
    return $ret;
}

// function m_scrap_profile($url) {
//     // create HTML DOM
//     $content = get_html($url);
//     if($content) {
//         $html = str_get_html($content);
//         $ret['name'] = $html->find('div[id=cover-name-root]', 0)->plaintext;
//         $ret['location'] = $html->find('div[id=u_0_1d] strong', 0)->plaintext;
//         $ret['hometown'] = $html->find('h4', 1)->plaintext;       
//         unset($html);
//     }
//     unset($content);
//     return $ret;
// }

print_r(scrap_profile($andra));


// $content = get_html($mardhani);
//     if($content) {
//         $html = str_get_html($content);
//         echo $html->find('h1', 0)->plaintext, "<br>";
//       //  echo $html->find('div[id=collection_wrapper_2327158227] a', 1)->innertext, "<br>";
//         echo $html->find('li[id=current_city] a', 0)->innertext, "<br>";
//         echo $html->find('li[id=hometown] a', 0)->innertext, "<br>";

//         echo $html->find('div[id=collection_wrapper_2327158227] div[class=_4qm1]', 1)->find('a', 1)->innertext, "<br>";

//         $stu = $html->find('div[id=collection_wrapper_2327158227] div[class=_4qm1]', 1)->find('li', 0)->find('div[class=fsm fwn fcg]', 0)->plaintext;
//         preg_match_all("([^\s]+\s+[^\s]+)", $stu, $study);
//         // print_r($study);
//         // echo "<br>";
//         $jurusan = $study[0][0];
//         print_r($jurusan);
//         echo "<br>";

        
//         unset($html);
//     }
//     unset($content);

// $crut = implode(",", $ret);
// echo $crut;






?>