<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>fx</title>
    <link rel="shortcut icon" href="png/fxFavicon.png">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"> 
    <style>
        #whole{
          border-color: #ccc;
          border-width: 1.0px;
          border-style: solid;
          border-radius: 10px;
          margin-right: auto;
          margin-left : auto;
        }
        .articleImg{
          width:100px; 
          float:left; 
          margin-right: 10px;
        }
        @media screen and (min-width:500px){
          .articleImg{
          width:200px; 
          }
        }
        @media screen and (min-width:700px){
          #whole{
            width:700px;
          }  
        }
        @media screen and (min-width:1100px){
          #whole{
            margin-left:200px;
          }
        }
    </style>
  </head>
  <body>
  <div style="border-bottom: solid 1px #ccc;">
    <a href="index-<?php print $_GET['from_hl']; ?>.php">
    <img src="png/fxlogo.png" style="width:256px;" >
    </a>
  </div><br />
    <div id="whole"><br />
   
  <?php
          $lang = $_GET['hl'];
          if($lang == 'en'){
            $country = 'us';
            $zisa = '0';
            $title = 'Link of article';
          }else if($lang == 'ja'){
            $country = 'jp';
            $zisa = '9';
            $title = '記事のリンク';
          }else{
            $country = 'cn';
            $zisa = '8';
            $title = '文章链接';
          }
          print '<a style="font-size:40px;">' .$title .'</a>';
          $sql = null;$res = null;$dbh = null;
          try {	
              $dsn = 'mysql:dbname=fx;hoat=localhost';
              $user = 'root';
              $password='**password**';
              $dbh = new PDO($dsn,$user,$password);
            
              $sql = 'SELECT * FROM news' .$country .' ORDER BY date DESC';
              $res = $dbh->query($sql);      
              $dateTimeArray = array();
              foreach( $res as $value ) {	
                $date = new DateTime($value["date"]);
                $date->modify('+' .$zisa .' hours');
                $dateStr = $date->format('n/j G:i');
                print '<div style="overflow:hidden; border-top: solid 1px #aaa; padding: 0px 10px 0px 15px;">'; 
                print ' <p><img class="articleImg" src="' .$value["urlToImage"] .'" ></p>';
                print ' <p style="color: #666666;">' .$dateStr .' ○' .$value['source'] .'</p>';
                print ' <a href="' .$value["url"] .'"><p>' .$value["description"] .'</p></a>';             
                print '</div>';
                print '<br />';



              }

            } catch(PDOException $e) {
              print 'ただいま障害により大変ご迷惑をおかけします';
              exit();
            }

          $dbh = null;
      ?>
    </div>
  </body>
</html>