<?php
  $json_str = file_get_contents('php://input');   //接受 request的body
  $json_obj = json_decode($json_str);;            //轉成json格式
  
  $myfile = fopen("log.txt") or die("Unable to open file!");  //建立一個log.txt文字檔
  fwrite($myfile, '\xEF\xBB\xBF",$json_str);                  //在字串前面加上\xEF\xBB\xBF轉成tuf8格式
  
?>
