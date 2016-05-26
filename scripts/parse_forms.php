<?php


$verbs = ['biec', 'chcieć', 'chorować', 'czytać',
  'dawać', 'grać', 'kochać', 'leżeć', 'malować',
  'nienawidzić', 'orać', 'otwierać', 'pisać',
  'pracować', 'przywozić', 'pukać', 'rąbać',
  'siedzieć', 'spać', 'spacerować', 'śpiewać',
  'strzelać', 'szukać', 'widzieć', 'wisieć',
  'zabijać', 'żądać', 'zamykać', 'zamawiać',
  'znajdować', 'umierać', 'kłaść', 'trzymać',
  'topnieć', 'gnić', 'tracić', 'palić',
  'rugać', 'płakać', 'kipieć', 'chrapać'
];


$forms = [];
foreach($verbs as $verb) {

  $ch  = curl_init();

  curl_setopt($ch, CURLOPT_URL, "http://sgjp.pl/edycja/ajax/search-by-form/?exponent=%22".$verb."%22&query_params={%22sort_rules%22:[%22a_fronte%22],%22filter%22:{%22group_op%22:%22AND%22,%22rules%22:[]},%22visible_vocabs%22:[%22SGJP%22],%22reader%22:true}&columns=[%22entry%22,%22abbr_pos%22,%22genders%22]");
  curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/sgjp_cookie0.txt');
  curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/sgjp_cookie0.txt');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_STDERR,  fopen('php://stdout', 'w'));
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

  $json = curl_exec($ch);

  $data = json_decode($json);

  foreach($data->rows as $row){
    $id = $row->id;
    /*
    $infs = [];
    foreach($data->rows as $row) {
      $infs[] = $row->entry;
    }

    var_dump($infs);
  */

    curl_setopt($ch, CURLOPT_URL, "http://sgjp.pl/edycja/ajax/inflection-tables/?lexeme_id=".$id."&variant=1");

    $json = curl_exec($ch);
    $data = json_decode($json);
    $decoded = html_entity_decode($data->html);

    $bedeArray = ['będę', 'będziemy', 'będziesz', 'będziecie', 'będzie', 'będą'];
    $prevK = 0;
    foreach($bedeArray as $bede) {

      $k = strpos($decoded, $bede, $prevK);
      while ($k !== false){
        $nKey = strpos($decoded, "\n",  $k+1);
        $form = substr($decoded, $k, $nKey-$k);
        $forms[$form] = true;

        $prevK = $k+1;
        $k = strpos($decoded, $bede,  $k+1);

      }
    }
    echo $row->entry."\n";
  }


}
file_put_contents("verb_forms2_unique.json", json_encode(array_keys($forms)));