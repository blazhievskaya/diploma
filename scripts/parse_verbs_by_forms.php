<?php

$json = file_get_contents("verb_forms_unique.json");

$data = json_decode($json,true);

/*foreach($data as $verb) {
  $content = @file_get_contents("forms/".$verb.'.html');
  file_put_contents("forms/".$verb.'.html', $content);
}*/

foreach($data as $verb) {
  echo 'start with '.$verb."\n";
  
  $content = @file_get_contents("forms/".$verb.'.html');
  if(!empty($content)) {
	echo 'skip as not empty - '.$verb."\n";
	continue;
  } 

  $query    = $verb;

  $URL = 'http://nkjp.pl/poliqarp/';

  $ch  = curl_init();

  curl_setopt($ch, CURLOPT_URL, $URL);
  curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/pol_cookie0.txt');
  curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/pol_cookie0.txt');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_STDERR,  fopen('php://stdout', 'w'));
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

  $page = curl_exec($ch);


  $postFields = array();
  $post = '';

  $postFields['query'] = $query;
  $postFields['corpus'] = 'nkjp1800';

  foreach($postFields as $key => $value) {
    $post .= $key . '=' . urlencode($value) . '&';
  }

  $post = substr($post, 0, -1);

  curl_setopt($ch, CURLOPT_URL, 'http://nkjp.pl/poliqarp/query/');
  curl_setopt($ch, CURLOPT_REFERER, $URL);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

  $page = curl_exec($ch); // make request
  echo "get page\n";
  //echo $page;

  $tries = 25;
  while (!preg_match("/<div class='export-form'>.*?<\/div>/is", $page, $form)) {
    $tries--;
    if($tries == 0) {
      echo $page;
      echo 'problem with '.$verb."\n";
      break;
    }
    sleep(5);
    curl_setopt($ch, CURLOPT_URL, 'http://nkjp.pl/poliqarp/nkjp1800/query/0+/');
    curl_setopt($ch, CURLOPT_POST, 0);
    $page = curl_exec($ch);
    echo "get page\n";

  }

  if($tries != 0) {
    curl_setopt($ch, CURLOPT_URL, 'http://nkjp.pl/poliqarp/nkjp1800/query/export/');
    curl_setopt($ch, CURLOPT_REFERER, $URL);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "format=html");
    $page = curl_exec($ch);

    file_put_contents("forms/".$verb.'.html', $page);
    echo 'ok with '.$verb."\n";
  }
  echo "waiting for next verb\n";
  sleep(5);
}