<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="index.css">
        <style>
          #languageIMG1{
            width: 90px;
          }
          #languageIMG2{
            width: 90px;
          }
        </style>
        <meta charset="utf-8">
        <title>fx</title>
        <link rel="shortcut icon" href="png/fxFavicon.png">
        <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">    
    </head>
    <body  id="body">
      <div style="border-bottom: solid 1px #ccc;">
        <img src="png/fxlogo.png" style="width:256px;" >
      </div>
      <br />
      <a href="language.php"><img id="languageIMG1" src="png/en2.png"></a>
      <div id="whole">
        <div id="sub" >
          <br/><br /><br/><br />
          <p><a href="select.php?hl=en">past data</a></p>
          <p><a href="news.php?hl=en&from_hl=en">News(English)</a></p>
          <p><a href="news.php?hl=ja&from_hl=en">News(Japanese)</a></p>
          <p><a href="news.php?hl=zh&from_hl=en">News(Chinese)</a></p>
          <p><a href="language.php">Change Language</a></p>
        </div>

      <div class="main">
        <a href="language.php"><img id="languageIMG2" src="png/en.png"></a>
        <br />
        <marquee scrollamount="5">
    <?php   
      $sql = null;$res = null;$dbh = null;
      try {	
              $dsn = 'mysql:dbname=fx;hoat=localhost';
              $user = 'root';
              $password='**password**';
              $dbh = new PDO($dsn,$user,$password);
              $sql = "SELECT * FROM newsus";
              $res = $dbh->query($sql);
              $count = 0; 
              foreach( $res as $value ) {	
                  print "◆" .$value["source"] ."◆";
                  print htmlspecialchars($value["description"], ENT_QUOTES, 'UTF-8');
                  print "　　　　　　　　　　　　　　　　　　　　　　　　　　　　　";
                  $count++;
                  
              }  
          } catch(PDOException $e) {
              print 'Failed';
              exit();
          }

      $dbh = null;
      ?>

      </marquee>
    <div class="link" style="text-align: right"><a href="news.php?hl=en&from_hl=en">details of News</a> 　</div>
    <br /><br />

    <table>
      <tr>
        <th> </th>
        <th>exchange rate</th>
        <th>bid</th>
        <th>ask</th>
      </tr>
      <?php
          $sql = null;$res = null;$dbh = null;
          try {	
                  $dsn = 'mysql:dbname=fx;hoat=localhost';
                  $user = 'root';
                  $password='**password**';
                  $dbh = new PDO($dsn,$user,$password);
                
                  $sql = "SELECT rateA.exchange, rateA.rate, rateA.bid, rateA.ask, rateA.time FROM rate AS rateA INNER JOIN (SELECT exchange, MAX(time) AS latestTime FROM rate GROUP BY exchange) AS rateB ON rateA.exchange = rateB.exchange AND rateA.time = rateB.latestTime;";
              
                  $res = $dbh->query($sql);
                  
                  $display = [
                    'USD/JPY',
                    'EUR/JPY',
                    'EUR/USD',
                    'GBP/JPY',
                    'CNY/JPY'
                    ];
                  $collectionTime = "";
                  $count = 0;    //foreachで使う変数
                  foreach($res as $value) {
                      print '<tr>';
                      print ' <td><img src="png/' .$count  .'from.png" class="tablePNG"> <img src="png/' .$count .'to.png" class="tablePNG"><a style="font-family:monospace; font-size:16px;">  ' .$display[$count] .'</a></td>';
                      print ' <td>' .$value['rate'] .'</td>';
                      print ' <td>' .$value['bid'] .'</td>';
                      print ' <td>' .$value['ask'] .'</td>';
                      print '</tr>';
                      $count++;
                      $collectionTime = $value["time"];
                      
                  }
                  $upload = new DateTime($collectionTime);
                  $upload->modify('+0 hours');  

                  print '</table>';
                  print '<div style="text-align: right"><a style="color:#999999;">Last updated: ' .$upload->format('n/j H:i') .' UTC 　</a></div>';
                  print '<div class="link" style="text-align: right"><a href="select.php?hl=en"> past data</a> 　</div><br /><br /></div><br/><br/>';

              
              } catch(PDOException $e) {
                  print 'Failed';
                  exit();
              }

          $dbh = null;
          
      ?>
      

      <div class="main" id="keiziban" style="padding-left:10px;"><br/><br />

      <?php

        $sql = null;$res = null;$dbh = null;
        try {	
                $dsn = 'mysql:dbname=fx;hoat=localhost';
                $user = 'root';
                $password='**password**';
                $dbh = new PDO($dsn,$user,$password);
              
                $sql = "SELECT * FROM comment ORDER BY date DESC";
            
                $res = $dbh->query($sql);
                
                print '<div style="font-weight: bold; border-bottom: solid 1px #eee;">'; 
                print $res->rowCount();
                print ' Comments <a style="font-size: 80%; color: #999999">(Comments older than a week will be deleted)</a></div>';
                foreach( $res as $value ) {	
                    print '<div style="border-bottom: solid 1px #eee;">';                 
                    $diff_minute = (strtotime(date("Y/m/d H:i:s")) - strtotime($value["date"])) /60;
                    print '<a style="color: #999999;">';   
                    $langcode = $value["language"];
                    print Locale::getDisplayName($langcode, 'en') ."　　";
                    if($diff_minute > (60 *  24)){
                      print floor($diff_minute/(60 *  24)) .' days ago</a><br />';
                    }else if($diff_minute > 60){
                      print floor($diff_minute/60) .' hours ago</a><br />';
                    }else{
                      print floor($diff_minute) .' minutes ago</a><br />';
                    }
                    print nl2br($value["commentEn"]);
                    print "<br /><br />";
                    if (isset($_COOKIE['goodbutton' .$value["id"]])) {
                      print '<img src="png/goodBlue.png" > ';
                    }
              
                    else{
                      print '<form method="post" action="good.php?hl=en" style="display: inline" >';
                      print '<input  type="hidden" name="dbid" value="' .$value["id"] .'">';
                      print '<input id="good" type="submit" value="">';
                      print '</form>';
                    }
                   
                    print $value["good"];
                    print '<input  type="hidden" name="dbid" value="' .$value["id"] .'">';
                    print '</form>';
                    print "</div>";        
                }
            
            } catch(PDOException $e) {
                print 'Failed';
                exit();
            }

        $dbh = null;

        ?>
        <br />
        <form id="formID" method="post" action="insert.php?hl=en" enctype="multipart/form-data">
            <textarea id="textarea" name="comment" rows="5" placeholder="Enter text"></textarea><br />
            <input id="submit" type="submit" value="" >
        </form>
        <br />
      </div>
      </div> <!--  id="whole"はここまで -->
     </body>
</html>
