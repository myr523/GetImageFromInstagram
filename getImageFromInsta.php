<?php
//honoka -> 1426222753
//mayuri -> 1651805211
date_default_timezone_set('Asia/Tokyo');

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
      $videoURL = $this->json_data['head']['meta']['23']['@attributes']['content'];
      $vidFilename = basename($videoURL);

      $viddata = file_get_contents($videoURL);
      file_put_contents("/home/myr523/Videos/instagram/{$vidFilename}",$viddata, FILE_APPEND | LOCK_EX);
      echo "video successfully saved /home/myr523/Videos/instagram/{$vidFilename}\n";
    }

    //写真・サムネイル取得
    $url = parse_url($this->json_data['head']['meta']['10']['@attributes']['content']);
    $url = str_replace('/t51.2885-15/e35/', '', $url['path']);
    $picURL = "https://scontent.cdninstagram.com/t51.2885-15/s1080x1080/sh0.08/e35/{$url}";

    $picFilename = basename($picURL);

    $picdata = file_get_contents($picURL);
    file_put_contents("/home/myr523/Pictures/instagram/{$picFilename}",$picdata, FILE_APPEND | LOCK_EX);
    echo "image successfully saved /home/myr523/Pictures/instagram/{$picFilename}\n";
    echo "Done.";
  }
}

$get_image = new GetImageFromInsta($argv[1]);
$get_image->getURLandImage();

?>
