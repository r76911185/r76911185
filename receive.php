<?php
  $json_str = file_get_contents('php://input'); //接收request的body
  $json_obj = json_decode($json_str); //轉成json格式
  
  $myfile = fopen("log.txt", "w+") or die("Unable to open file!"); //設定一個log.txt來印訊息
  fwrite($myfile, "\xEF\xBB\xBF".$json_str); //在字串前面加上\xEF\xBB\xBF轉成utf8格式
  
  $sender_userid = $json_obj->events[0]->source->userId; //取得訊息發送者的id
  $sender_txt = $json_obj->events[0]->message->text; //取得訊息內容
  $sender_replyToken = $json_obj->events[0]->replyToken; //取得訊息的replyToken
  
  $sender_txt=rawurlencode($sender_txt); //因為使用get的方式呼叫luis api，所以需要轉碼
  $ch = curl_init('https://westus.api.cognitive.microsoft.com/luis/v2.0/apps/0daa70f0-8f89-4255-aa33-ed8bf6ad0054?subscription-key=074625dffb1b471bbb06e37b2484f150&verbose=true&timezoneOffset=0&q='.$sender_txt);                                                                      
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                                                                          
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $result_str = curl_exec($ch);
  fwrite($myfile, "\xEF\xBB\xBF".$result_str); //在字串前加上\xEF\xBB\xBF轉成utf8格式
  $result = json_decode($result_str);
  $ans_txt = $result -> topScoringIntent -> intent;
  $response = array (
    "to" => $sender_userid,
    "messages" => array (
      array (
        "type" => "text",
        "text" => $ans_txt
      )
    )
  );
  
  
 fwrite($myfile, "\xEF\xBB\xBF".json_encode($response)); //在字串前面加上\xEF\xBB\xBF轉成utf8格式
  $header[] = "Content-Type: application/json";
  $header[] = "Authorization: Bearer YyafHFiABWcqIsLyrhMl/1umgfIRuIieBK1CZ27Pwl+YrCpDdzKbZ3W8no96k7ml7a5sImGYSO2gFFO7SscS9w0SgXvlTm30tI3v6wwTzdBp6oBkKN25ktkwJtkFj4QHYzyCFxrReNaR2RIzaLwoXwdB04t89/1O/w1cDnyilFU=";
  $ch = curl_init("https://api.line.me/v2/bot/message/");
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));                                                                  
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
  curl_setopt($ch, CURLOPT_HTTPHEADER, $header);                                                                                                   
  $result = curl_exec($ch);
  curl_close($ch);
?>
