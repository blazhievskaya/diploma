<?php

//Read and parse json file with verb forms
$json = file_get_contents("verb_forms_unique.json");

$data = json_decode($json,true);
//Process by verb form
foreach($data as $verb) {
  echo 'start with '.$verb."\n";
  //Check if already exists
  $content = @file_get_contents("forms/".$verb.'.html');
  if(!empty($content)) {
	echo 'skip as not empty - '.$verb."\n";
	continue;
  } 

  $query    = $verb;

  $URL = 'http://nkjp.pl/poliqarp/';

  $ch  = curl_init();
  //Get cookie
  curl_setopt($ch, CURLOPT_URL, $URL);
  curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/pol_cookie0.txt');
  curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/pol_cookie0.txt');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_STDERR,  fopen('php://stdout', 'w'));
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

  $page = curl_exec($ch);

  //Prepare POST request  
  $postFields = array();
  $post = '';

  $postFields['query'] = $query;
  $postFields['corpus'] = 'nkjp1800';

  foreach($postFields as $key => $value) {
    $post .= $key . '=' . urlencode($value) . '&';
  }

  //Remove redundant symbols
  $post = substr($post, 0, -1);

  curl_setopt($ch, CURLOPT_URL, 'http://nkjp.pl/poliqarp/query/');
  curl_setopt($ch, CURLOPT_REFERER, $URL);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

  $page = curl_exec($ch); // make request
  echo "get page\n";
  //Set number of tries (corpus needs time to find all data)
  $tries = 25;
  //Search for export form on page. If not found, then try again
  while (!preg_match("/<div class='export-form'>.*?<\/div>/is", $page, $form)) {
    $tries--;
	//If no tries left, write error message 
    if($tries == 0) {
      echo $page;
      echo 'problem with '.$verb."\n";
      break;
    }
	//Wait for 5 sec between tries
    sleep(5);
	//Do a try
    curl_setopt($ch, CURLOPT_URL, 'http://nkjp.pl/poliqarp/nkjp1800/query/0+/');
    curl_setopt($ch, CURLOPT_POST, 0);
    $page = curl_exec($ch);
    echo "get page\n";

  }
  //If there are tries which means success then write data
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
  //Wait for 5 sec between verbs 
  echo "waiting for next verb\n";
  sleep(5);
}