<?php
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

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if($gzip)
    curl_setopt($ch, CURLOPT_ENCODING, "gzip"); // the page encoding
    $server_output = curl_exec ($ch);
    curl_close ($ch);
    return $server_output;
}

function scraping_detik($date,$page=1,$list=[]) {
    // create HTML DOM
    $datez = date ("m/d/Y", $date);
    $url = "https://news.detik.com/indeks/all/$page?date=$datez";
    $content = get_html($url);
    $html = str_get_html($content);
    $ret = $html->find('ul[id=indeks-container]');
    $totalpage = 0;
    if($html->find('div[class=paging]'))
    {
        $totalpage = $html->find('div[class=paging]',0)->find('a',-2)->plaintext;
    }
    $totalpage = (int) filter_var($totalpage, FILTER_SANITIZE_NUMBER_INT);
    $ret = $ret[0];

    // get title
    foreach($ret->find('li') as $article) {
        $url = $article->find('a', 0)->href;

        //exclude video only content
        if(!strpos($url,"detiktv"))
        {
            $list[] = $url;

        }
    }
    if($totalpage > $page)
    {
        $list= scraping_detik($date,$page+1,$list);
    }
    unset($html);
    unset($ret);
    return $list;
}

function scraping_detik_single($url) {
    // create HTML DOM
    $content = get_html($url);
    if($content) {
        $html = str_get_html($content);
        $ret['title'] = $html->find('h1', 0)->innertext;
        $ret['date'] = $html->find('meta[name=publishdate]', 0)->content;
        $date = strtotime($ret['date']);
        $ret['date'] = date("Y-m-d H:i:s", $date);
        $ret['description'] = $html->find('meta[itemprop=headline]', 0)->content;
        $ret['content'] = "";
        $ret['content'] = $html->find('div[id=detikdetailtext]', 0)->plaintext;
        $ret['keywords'] = $html->find('meta[itemprop=keywords]', 0)->content;
        unset($html);
    }
    unset($content);
    return $ret;
}


function scraping_metro($date,$page=0,$list=[]) {
    $tgl = date ("d", $date);
    $bln = date ("m", $date);
    $thn = date ("Y", $date);
    $url = "http://news.metrotvnews.com/index/$thn/$bln/$tgl/$page";

    //mendapatkan konten HTMLnya dari curl supaya gak dikira bot
    $content = get_html($url);
    if ($content) {
        $html = str_get_html($content);

        //mencari halaman terakhir
        $ret = $html->find('div[class=style_06]', 0);

        // mendapatkan semua link di halaman itu
        foreach ($ret->find('li') as $article) {
            $url = $article->find('a', 0)->href;
            $list[] = $url;
        }
        if ($html->find('a[rel=next]')) {
            $nextpage = (int)filter_var(basename(parse_url($html->find('a[rel=next]', 0)->href, PHP_URL_PATH)), FILTER_SANITIZE_NUMBER_INT);
            //jika halaman belum halaman terakhir akan terus looping sampai halaman terakhir
            $list = scraping_metro($date, $nextpage, $list);
        }
        unset($html);
        unset($ret);
        unset($content);
    }
    return $list;
}

function scraping_metro_single($url) {
    $content = get_html($url);
    if($content) {
        $html = str_get_html($content);
        $ret['title'] = $html->find('h1', 0)->innertext;
        $ret['date'] = $html->find('div[class=reg]', 0)->innertext;
        $ret['date'] = end(explode(',', $ret['date']));

        $date = strtotime($ret['date']);
        $ret['date'] = date("Y-m-d H:i:s", $date);
        $ret['description'] = $html->find('meta[property=og:description]', 0)->content;
        $ret['content'] = "";
       
        //$ret['content'] = $html->find('div[id=detikdetailtext]', 0)->innertext;
        unset($html);
        unset($content);
    }
    return $ret;
}

function scraping_okezone($date,$page=0,$list=[]) {
    $tgl = date ("d", $date);
    $bln = date ("m", $date);
    $thn = date ("Y", $date);
    $url = "https://index.okezone.com/bydate/index/$thn/$bln/$tgl/$page";

    //mendapatkan konten HTMLnya dari curl supaya gak dikira bot
    $content = get_html($url);
    if($content) {
        $html = str_get_html($content);
        $ret = $html->find('div[class=news-content]', 0);
        $totalpage = 0;
        //mencari halaman terakhir
        if ($html->find('div[class=pagination-indexs]')) {
            $totalpage = $html->find('div[class=pagination-indexs]', 0)->find('a', -1)->href;
        }
        $totalpage = (int)filter_var(basename($totalpage), FILTER_SANITIZE_NUMBER_INT);

        // mendapatkan semua link di halaman itu
        foreach ($ret->find('li') as $article) {
            $url = $article->find('a', 0)->href;
            $list[] = $url;
        }

        if ($totalpage > $page) {
            //jika halaman belum halaman terakhir akan terus looping sampai halaman terakhir
            $list = scraping_okezone($date, $page + 15, $list);
        }
        unset($html);
        unset($ret);
        unset($content);
    }
    return $list;
}

function scraping_okezone_single($url) {
    // create HTML DOM
    $content = get_html($url);
    if($content) {
        $html = str_get_html($content);
        if($html->find('div[class=title] h1', 0))
        {
            $ret['title'] = $html->find('div[class=title] h1', 0)->innertext;
            //$ret['date'] = $html->find('meta[itemprop=datePublished]', 0)->content;
            //$date = strtotime($ret['date']);
            preg_match('/"datePublished": "(.*?)"/', $content, $date);
            $date = strtotime($date[1]);
            $ret['date'] = date("Y-m-d H:i:s", $date);
            $ret['description'] = $html->find('meta[property=og:description]', 0)->content;
        }
        $ret['content'] = "";
        $ret['content'] = $html->find('div[id=contentx]', 0)->plaintext;
        $key = [];
        $key[0] = $html->find('div[class=detail-tag] a', 0)->plaintext;
        $key[1] = $html->find('div[class=detail-tag] a', 1)->plaintext;
        $key[2] = $html->find('div[class=detail-tag] a', 2)->plaintext;
        $ret['keywords'] = implode(",", $key);
        unset($html);
        unset($content);
    }
    return $ret;
}

function scraping_sindo($date,$page=0,$list=[]) {
    $tgl = date ("d", $date);
    $bln = date ("m", $date);
    $thn = date ("Y", $date);
    $url = "https://index.sindonews.com/index/0/$page?t=$thn-$bln-$tgl";

    //mendapatkan konten HTMLnya dari curl supaya gak dikira bot
    $content = get_html($url);

    if($content) {
        $html = str_get_html($content);
        $totalpage = 0;
        //mencari halaman terakhir
        if ($html->find('div[class=pagination]', 0)) {
            $totalpage = $html->find('div[class=pagination]', 0)->find('a', -1)->href;
        }
        $totalpage = (int)filter_var(basename(parse_url($totalpage, PHP_URL_PATH)), FILTER_SANITIZE_NUMBER_INT);

        // mendapatkan semua link di halaman itu
        foreach ($html->find('div[class=indeks-title]') as $article) {
            $url = $article->find('a', 0)->href;
            $list[] = $url;
        }

        if ($totalpage > $page) {
            //jika halaman belum halaman terakhir akan terus looping sampai halaman terakhir
            $list = scraping_sindo($date, $page + 15, $list);
        }

        unset($html);
        unset($ret);
        unset($content);
    }
    return $list;
}

function scraping_sindo_single($url) {
    $content = get_html($url);
    if($content) {
        $html = str_get_html($content);
        preg_match('/"datePublished": "(.*?)"/', $content, $date);
        $ret['title'] = $html->find('.article h1', 0)->innertext;

        //   $ret['date'] = $html->find('time', 0)->innertext;
        $date = strtotime($date[1]);
        $ret['date'] = date("Y-m-d H:i:s", $date);
        $ret['description'] = $html->find('meta[property=og:description]', 0)->content;
        $ret['content'] = "";
     
        //$ret['content'] = $html->find('div[id=contentx]', 0)->innertext;
        unset($html);
        unset($content);
    }
    return $ret;
}

function scraping_tribun($date,$page=1,$list=[],$count=0) {
    $tgl = date ("d", $date);
    $bln = date ("m", $date);
    $thn = date ("Y", $date);
    $url = "http://www.tribunnews.com/index-news?date=$thn-$bln-$tgl&page=$page";
    //mendapatkan konten HTMLnya dari curl supaya gak dikira bot
    $content = get_html($url,false,false);
    $html = str_get_html($content);


    // mendapatkan semua link di halaman itu
    foreach($html->find('li[class=ptb15] h3') as $article) {
        $url = $article->find('a', 0)->href;
        $list[] = $url;
    }

    if(strpos($content, ">Next</a>"))
    {
        //jika halaman belum halaman terakhir akan terus looping sampai halaman terakhir
        if($count <= 5)
        {
           $list = scraping_tribun($date,$page+1,$list,$count+1);
    }
    else{
            $list["page"] = $page+1;
    }
    }
unset($content);
unset($html);
    return $list;
}

function scraping_tribun_single($url)
{
    $content = get_html($url);
    $html = str_get_html($content);
    preg_match("/'publish_date': '(.*?)',/", $content, $date);
    $ret['title'] = $html->find('div[id=article] h1', 0)->plaintext;
    //   $ret['date'] = $html->find('time', 0)->innertext;
    $date = strtotime($date[1]);
    $ret['date'] = date("Y-m-d H:i:s", $date);
    $ret['description'] = $html->find('meta[property=og:description]', 0)->content;
    $ret['content'] = "";
 
    //$ret['content'] = $html->find('div[id=contentx]', 0)->innertext;
    return $ret;

}

function scraping_bbc($url) {

    //mendapatkan konten HTMLnya dari curl supaya gak dikira bot
    $content = get_html($url);
    $html = str_get_html($content);


    // mendapatkan semua link di halaman itu
    foreach($html->find('div[class=eagle-item]') as $article) {
        $url = $article->find('a', 0)->href;
        $list[] = "http://www.bbc.com".$url;
    }


    return $list;
}
function scraping_bbc_single($url) {
    $content = get_html($url);
    $html = str_get_html($content);
    preg_match('/"datePublished":"(.*?)"/',  $content, $date);
    //   $ret['date'] = $html->find('time', 0)->innertext;
    $date = str_replace("T"," ",explode("+",$date[1])[0]);

    $date = strtotime($date);
    $ret['date'] = date ("Y-m-d H:i:s", $date);
    $ret['title'] = $html->find('meta[property=og:title]', 0)->content;
    $ret['description'] = $html->find('meta[property=og:description]', 0)->content;
    $ret['content'] = "";
   
    //$ret['content'] = $html->find('div[id=contentx]', 0)->innertext;
    return $ret;
}
    function save_to_db($list_pages,$dbh){
    $count=0;
    foreach ($list_pages as $val){
        $count++;
        $sql = "SELECT COUNT(*) FROM berita_baru WHERE link = '$val'";
        if ($res = $dbh->query($sql)) {
            if ($res->fetchColumn() > 0) {
                echo "$count. already exist $val\n";
            }
            else {
                unset($sql);
                unset($res);
                if(strpos($val,"detik.com"))
                {
                    $source = "detik.com";
                    $ret = scraping_detik_single($val);
                }
                elseif(strpos($val,"metrotvnews.com"))
                {
                    $source = "metrotvnews.com";
                    $ret = scraping_metro_single($val);
                }        elseif(strpos($val,"okezone.com"))
                {
                    $source = "okezone.com";
                    $ret = scraping_okezone_single($val);

                }        elseif(strpos($val,"bbc.com"))
                {
                    $source = "bbc.com";
                    $ret = scraping_bbc_single($val);

                }        elseif(strpos($val,"sindonews.com"))
                {
                    $source = "sindonews.com";
                    $ret = scraping_sindo_single($val);

                }        elseif(strpos($val,"tribunnews.com"))
                {
                    $source = "tribunnews.com";
                    $ret = scraping_tribun_single($val);
                }else{
                    $source = "unknown";
                    echo "$count. unknown url $val\n";
                    continue;
                }

                if(!$ret["title"])
                {
                    echo "$count. fail get data $val\n";
                    continue;
                }
                $sql = "INSERT INTO berita_baru (source, title,sub_title, link, description, content, date, keywords)
VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $value = array($source,$ret["title"],$ret["title"],$val,$ret["description"],$ret["content"],$ret["date"],$ret["keywords"]);
                try
                {
                    $stmt = $dbh->prepare($sql);
                    $stmt->execute($value);
                    echo "$count. sukses $val\n";
                    unset($sql);
                    unset($stmt);
                    unset($ret);
                }
                catch(PDOException $e)
                {
                    echo "$count. fail insert $val\n";
                    echo $e->getMessage()."\n";
                    unset($sql);
                    unset($stmt);
                    unset($ret);
                }

            }
        }
        else{
            echo "$count. fatal fail $val\n";
        }
    }
    unset($list_pages);
}
