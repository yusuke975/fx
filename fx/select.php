<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <style>
      .tablePNG{
          width:50px;
          border: 1px #cccccc solid;
        }
        #whole{
          margin-right: auto;
          margin-left : auto;
        }
        .select{
          border-bottom: solid 1px #eee; 
          text-align: center;
        }
        .select a{
          font-size:40px;
        }
        details{
          padding: 5px 5px 5px 20px;
        }
        details div{
          padding: 7px 0px 7px 0px;
          border-bottom: solid 1px #ccc;
        }
        @media screen and (min-width:350px){
          .radio {
          display: none;
          }
            
          label{
            font-size: 13px;
            color: #888888;
            border: 1px solid #cccccc;
            border-radius: 5px;
            padding: 7px 3px 7px 3px;
        
          }
          input[type="radio"]:checked + label {
            color: #000000;
            background: gold;
            border-radius: 5px;
            border: 3px solid #ffa500;
            padding: 14px 3px 14px 3px;
            box-shadow: 2px 2px 4px #ffa500;  
          }
        }
        @media screen and (min-width:700px){
          #whole{
              width:700px;
          }
          label{   
            font-size: 15px;
          }
        }
        @media screen and (min-width:1100px){
          #whole{
            margin-left:200px;
          }
        }

    </style>
    <title>fx</title>
    <link rel="shortcut icon" href="png/fxFavicon.png">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">    
  </head>
  <body >
  <div style="border-bottom: solid 1px #ccc;">
    <a href="index-<?php print $_GET['hl']; ?>.php">
    <img src="png/fxlogo.png" style="width:256px;" >
    </a>
  </div>
  <br />
  <br />
  
  <div id="whole">
 
    <br />
    <?php
      $sql = null;$res = null;$dbh = null;
      try {	
              $lang = $_GET['hl'];
              if($lang == 'ja'){
                $zisa = '9';
              }else if($lang == 'zh'){
                $zisa = '8';
              }else{
                $zisa = '0';
              }
              $dsn = 'mysql:dbname=fx;hoat=localhost';
              $user = 'root';
              $password='**password**';
              $dbh = new PDO($dsn,$user,$password);
            
              $sql = 'SELECT time FROM rate order by time';
              $res = $dbh->query($sql);      
              $timeArray = array();
              foreach( $res as $value ) {	

                  $time = new DateTime($value["time"]);
                  $time->modify('+' .$zisa .' hours');
                  $timeStr = $time->format('Y-m-d');
                  array_push($timeArray, $timeStr);

              }
              $timeArray = array_unique($timeArray);
              $timeArray = array_values($timeArray);

        } catch(PDOException $e) {
            print 'ただいま障害により大変ご迷惑をおかけします';
            exit();
        }

      $dbh = null;
      ?>
      <?php

      ?>
      
      <form  name="form">
        <input type="radio" name="exchange" id="radio0" class='radio' value="0" width= "20%" checked>
        <label for="radio0">USD/JPY</label>
        <input type="radio" name="exchange" id="radio1" class='radio' value="1">
          <label for="radio1">EUR/JPY</label>
        <input type="radio" name="exchange" id="radio2" class='radio' value="2"> 
          <label for="radio2">EUR/USD</label>
        <input type="radio" name="exchange" id="radio3" class='radio' value="3" >
          <label for="radio3">GBP/JPY</label>
        <input type="radio" name="exchange" id="radio4" class='radio' value="4">
          <label for="radio4">CNY/JPY</label>

      </form><br /><br /><br />
  
      <script>
        var timeArray  = <?php print json_encode($timeArray); ?>;
 
        for(var i = 0; i < timeArray.length; i++){
          createElementDetails(timeArray[i]);
        }     
        
        function createElementDetails(time){
          var timeDate = new Date(time);
          var year = timeDate.getFullYear();
          var month = timeDate.getMonth() + 1;
          var date = timeDate.getDate();
          var idStr =  "" + year + month; 
          if(document.getElementById(idStr) != null){             
            var canvasEle = document.getElementById(idStr);
          }else{
            var canvasEle = document.createElement("details"); 
            var summaryEle = document.createElement("summary"); 
            summaryEle.textContent = year + '-' + month;
            canvasEle.id = idStr;
            var wholeEle = document.getElementById("whole");
            canvasEle.appendChild(summaryEle);
            wholeEle.appendChild(canvasEle);
          }

          var divEle = document.createElement("div"); 
          var aEle = document.createElement("a");  
          divEle.addEventListener('click', jumpChartPhp, false);
          divEle.eventParam = + '' + year + "-" + month + "-" + date;
          aEle.addEventListener('click', jumpChartPhp, false);
          aEle.eventParam = + '' + year + "-" + month + "-" + date;
          aEle.textContent = month + '/' + date;
          
          
          document.getElementById(idStr);
          divEle.appendChild(aEle);
          canvasEle.appendChild(divEle);


  
        }
        function jumpChartPhp(yearMonthDate){
          for (var i = 0; i < document.form.exchange.length; i++) {
            if (document.form.exchange[i].checked) {
              exchange = document.form.exchange[i].value;    //この変数は何番目のラジオボタンが選択されているのかを表す。
            }
          }
          window.location.href = 'chart.php?hl=<?php print $_GET['hl']; ?>&exchange=' + exchange +'&date=' + yearMonthDate.target.eventParam;
        }
   
      </script>
      <br /> <br />
    </div>
  </body>
</html>