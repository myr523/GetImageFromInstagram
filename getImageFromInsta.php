#!/bin/php
<?php
define("SAVE_DIR","/Users/myr523/Pictures/MYR/instagram/");
class GetImageFromInsta{
  public $json_data;
  public function __construct($input){
    error_reporting(E_ALL & ~E_NOTICE); //stop warning notice
    $json_data = file_get_contents($input);
    @$domDocument = new DOMDocument();
    @$domDocument->loadhtml($json_data);
    $xmlString = $domDocument->saveXML();
    $xmlObject = simplexml_load_string($xmlString);
    $array = json_decode(json_encode($xmlObject), true);
    $this->json_data = $array;  //JSONデータ
    //var_dump($this->json_data);
  }
  public function getURLandImage(){
    //動画だった場合の処理
    if ($this->json_data['head']['meta']['22']['@attributes']['content'] == "video") {
      $videoURL = $this->json_data['head']['meta']['24']['@attributes']['content'];
      $vidFilename = basename($videoURL);
      $viddata = file_get_contents($videoURL);
      $result = file_put_contents(SAVE_DIR."{$vidFilename}",$viddata, FILE_APPEND | LOCK_EX);
      if ($result !== false) {
        echo "video successfully saved.\n";
      } else {
        echo "Error was occured.\n";
      }
    }
    //写真・サムネイル取得
    $url = parse_url($this->json_data['head']['meta']['11']['@attributes']['content']);
    $url = str_replace('/t51.2885-15/e35/', '', $url['path']);
    $picURL = "https://scontent.cdninstagram.com/t51.2885-15/s1080x1080/sh0.08/e35/{$url}";
    $picFilename = basename($picURL);
    $picdata = file_get_contents($picURL);
    $result = file_put_contents(SAVE_DIR."{$picFilename}",$picdata, FILE_APPEND | LOCK_EX);
    if ($result !== false) {
      echo "image successfully saved.\n";
    } else {
      echo "Error was occured.\n";
    }
  }
}
if ($argv[1] == NULL) {
  echo "input args.\n";
  echo "exit.\n";
  exit(1);
}
$get_image = new GetImageFromInsta($argv[1]);
$get_image->getURLandImage();
?>
