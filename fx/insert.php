<?php

try{
    $GETlang = $_GET['hl']; //hlとは言語のこと
    $comment = $_POST['comment'];
    if (strcmp($comment, "") == 0 ) {     //コメントを送らなかった場合
        header("Location: error.php?hl=" .$GETlang);
        exit;   
    }
    $comment = htmlspecialchars($comment, ENT_QUOTES, 'UTF-8');
    $commentEn  = translate($comment, "en");
    $commentJa = translate($comment, "ja");
    $commentZh = translate($comment, "zh");

    $dsn = 'mysql:dbname=fx;hoat=localhost';
    $user = 'root';
    $password='**password**';
    $dbh = new PDO($dsn,$user,$password);
    $dbh->query('SET NAMES utf8');

    $sql ='INSERT INTO comment (commentOrig,commentEn,commentJa,commentZh, good, date, language) VALUES(?,?,?,?,?,?,?)';
    $stmt = $dbh->prepare($sql);
    $data[] = $comment;
    $data[] = $commentEn;
    $data[] = $commentJa;
    $data[] = $commentZh;
    $data[] = 0;
    $data[] = date("Y/m/d H:i:s");
    $data[] = $userLang;
    $stmt->execute($data);
    header( "Location: index-" .$GETlang .'.php') ;
    $dbh = null;

}catch(\Throwable $e ) {
    
    echo 'PDOException: ' . $e->getMessage();
    
}
    
    function translate($comment, $language){             
        $encode = urlencode($comment);
        $url = 'https://translation.googleapis.com/language/translate/v2?target=' .$language .'&key=AIzaSyCqxWZ9yJ7CERujEJ7e0HqaSX4uUdeSqE0&q=' .$encode; // リクエストするURLとパラメータ
    
        $curl = curl_init($url);
    
        // リクエストのオプションをセットしていく
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET'); // メソッド指定
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 証明書の検証を行わない
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // レスポンスを文字列で受け取る
    
        // レスポンスを変数に入れる
        $response = curl_exec($curl);
        $json = json_decode( $response , true ) ;
        $json = $json["data"]["translations"];
        global $userLang;                    //ユーザーがツイートした言語コード
        $userLang = $json[0]["detectedSourceLanguage"]; 
        
        
        if($userLang == $language){  //もし、もとのコメントと翻訳結果が同じ言語だったらもとのコメントを返す。
            return $comment;   
        }
        
        
        curl_close($curl);
    
        return $json[0]["translatedText"];

    }


?>
