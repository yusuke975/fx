<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>fx</title>
    <link rel="shortcut icon" href="png/fxFavicon.png">
        <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">    
    <style>
      html,body { height:100%; }
        .tablePNG{
          height:16px;
          border: 1px #cccccc solid;
        }

        #whole{
          height:100%;

          margin-right: auto;
          margin-left : auto;
        }
       
          .checkbox {
          display: none;
          }
            
          label{

            color: #888888;
            border: 1px solid #cccccc;
            border-radius: 5px;
            padding: 7px 15px 7px 15px;
        
          }
       
          #rate:checked + label{
            color: green;
            background: greenyellow;
            border: 3px solid green;
            padding: 10px 15px 10px 15px;
            box-shadow: 2px 2px 4px green;
          }
          #bid:checked + label{
            color: #0000ff;
            background: #99ccff;
            border: 3px solid blue;
            padding: 10px 15px 10px 15px;
            box-shadow: 2px 2px 4px blue;
          }
          #ask:checked + label{
            color: red;
            background: pink;
            border: 3px solid red;
            padding: 10px 15px 10px 15px;
            box-shadow: 2px 2px 4px red;
          }
      

        @media screen and (min-width:1000px){
          #whole{
              width:1000px;

            }
        }
   
        @media screen and (min-width:1100px){
          #whole{
            margin-left:200px;
          }

        }
    </style>
  </head>
  <body id="body" onLoad="createChart()">
  <div  style="border-bottom: solid 1px #ccc;">
    <a href="index-<?php print $_GET['hl']; ?>.php">
    <img src="png/fxlogo.png" style="width:256px;" >
    </a>
  </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>
    
    <div id="whole">
    
    <canvas id="chart" style="width=400; height=250"></canvas>
   
    <?php
          $lang = $_GET['hl'];
          $date = $_GET['date'];
          $localdate = new DateTime($date);
          if($lang == 'ja'){
            $timeZone = 'JST';
            $zisa = '9';
          }else if($lang == 'zh'){
            $timeZone = 'CST';
            $zisa = '8';
          }else{
            $timeZone = 'UTC';
            $zisa = '0';
          }
         
          print $date .$timeZone .'<br />';
          $localdate->modify('-' .$zisa .' hours'); 
          $startTime = $localdate->format('Y-n-j H:i:s');
          $localdate->modify('+23 hours');
          $localdate->modify('+59 minutes');
          $localdate->modify('+59 seconds');
          $endTime = $localdate->format('Y-n-j H:i:s');
        
          
          
          
          $exchange = $_GET['exchange'];
          print '<img src="png/' .$exchange  .'from.png" class="tablePNG"> <img src="png/' .$exchange .'to.png" class="tablePNG">';
          if($exchange === '0'){
            print ' USD/JPY';
          }else if($exchange === '1'){
            print ' EUR/JPY';
          }else if($exchange === '2'){
            print ' EUR/USD';
          }else if($exchange === '3'){
            print ' GBP/JPY';
          }else{
            print ' CNY/JPY';
          }
          print '<br /><br />';
          
          $sql = null;$res = null;$dbh = null;
          try {	
                  $dsn = 'mysql:dbname=fx;hoat=localhost';
                  $user = 'root';
                  $password='**passwoed**';
                  $dbh = new PDO($dsn,$user,$password);
                
                  $sql = 'SELECT * FROM rate where exchange = ' .$exchange .' AND time BETWEEN "' .$startTime .'" AND"' .$endTime .'"';
              
                  $res = $dbh->query($sql);
                  
                  $count = 0; 
      
                  $collectionTime = "";
                  $rateValue = array();
                  $bidValue = array();
                  $askValue = array();
                  $dateValue = array();
                  foreach( $res as $value ){ 
                      $utcTime = new DateTime($value["time"]);
                      $utcTime->modify('+' .$zisa .' hours');
                      $collectionTime = $utcTime->format('G:i') .$timeZone;
                      array_push($dateValue, $collectionTime);
                      array_push($rateValue, $value["rate"]);
                      array_push($bidValue, $value["bid"]);
                      array_push($askValue, $value["ask"]);
                  }

              } catch(PDOException $e) {
                  print 'ただいま障害により大変ご迷惑をおかけします';
                  exit();
              }

          $dbh = null;
       
      ?>

      <form name="form">
        <input id="rate" class="checkbox" type="checkbox" onchange="createChart();" checked="checked" />
          <label for="rate">rate</label>
        <input id="bid" class="checkbox" type="checkbox" onchange="createChart();" />
          <label for="bid">bid</label>
        <input id="ask" class="checkbox" type="checkbox" onchange="createChart();" />
      <label for="ask">ask</label>
    </form><br />
    
   
    </div> <!--id="whole" はここまで-->
    <script>
  
  function createChart() {
    removeElement();
    createElement();
    var exchangeType = createArrayOfType();
    var exchangeValue = createArrayOfExchange();
    var exchangeColor = createArrayOfColor();

    if (exchangeValue.length === 3) {
      draw3Line(exchangeType, exchangeValue, exchangeColor);
    }else if(exchangeValue.length === 2){
      draw2Line(exchangeType, exchangeValue, exchangeColor);
    }else if(exchangeValue.length === 1){
      draw1Line(exchangeType, exchangeValue, exchangeColor);
    }else{
      draw0Line();
    }


  }
  function removeElement(){
    document.getElementById('chart').remove();

  }
  function createElement(){
    //canvas要素を生成
    var canvasEle = document.createElement("canvas"); 
    canvasEle.style.width = document.getElementById('whole').clientWidth - 15 + 'px'; 
    canvasEle.style.height = document.getElementById('whole').clientHeight  -250 + 'px'; 
    canvasEle.id = 'chart'; 
    var divEle = document.getElementById("whole");
    divEle.appendChild(canvasEle);
    


  }

  function createArrayOfType(){
    //グラフの種類(rate, bid, ask)が格納された配列を生成。
    var exchangeType = [];  
    if( document.form.rate.checked === true){
      exchangeType.push('rate');
    }
    if(document.form.bid.checked === true){
      exchangeType.push('bid');
    }
    if(document.form.ask.checked === true){
      exchangeType.push('ask');
    }
    return exchangeType;

  }
  function createArrayOfExchange(){
    //グラフの値が格納された配列を生成
    var exchangeValue = [];  
    if( document.form.rate.checked === true){
      exchangeValue.push(<?php print json_encode($rateValue); ?>);
    }
    if(document.form.bid.checked === true){
      exchangeValue.push(<?php print json_encode($bidValue); ?>);
    }
    if(document.form.ask.checked === true){
      exchangeValue.push(<?php print json_encode($askValue); ?>);
    }
    return exchangeValue;


  }
  function createArrayOfColor(){
    //グラフの色が格納された配列を生成
    var exchangeColor = [];  
    if( document.form.rate.checked === true){
      exchangeColor.push('rgb(0, 255, 0)'); 
    }

    if(document.form.bid.checked === true){
      exchangeColor.push('rgb(0, 0, 255)'); 
    }
    
    if(document.form.ask.checked === true){
      exchangeColor.push('rgb(219, 68, 88)'); 
    }
    return exchangeColor;


  }
  function draw3Line(exchangeType, exchangeValue, exchangeColor){

    var ctx = document.getElementById("chart").getContext('2d');
    var ChartDemo = new Chart(ctx, {
    type: 'line',
    data: {
 
        labels: <?php print json_encode($dateValue); ?>,
        datasets: [
        {
            label: exchangeType[0],
            data: exchangeValue[0],
            borderColor: exchangeColor[0],
            backgroundColor: "rgba(255,255,255,0)",  
            
        },

        {
            label: exchangeType[1],
            data: exchangeValue[1],
            borderColor: exchangeColor[1],
            backgroundColor: "rgba(255,255,255,0)",  
       },
       {
            label: exchangeType[2],
            data: exchangeValue[2],
            borderColor: exchangeColor[2],
            backgroundColor: "rgba(255,255,255,0)",  
       },
        ]
    },
    options: {
        responsive: false,
        legend: {
          display: false
        },

        
        
    }
    
    });
  }
  function draw2Line(exchangeType, exchangeValue, exchangeColor){

    var ctx = document.getElementById("chart").getContext('2d');
    var ChartDemo = new Chart(ctx, {
    type: 'line',
    data: {
 
        labels: <?php print json_encode($dateValue); ?>,
        datasets: [
        {
            label: exchangeType[0],
            data: exchangeValue[0],
            borderColor: exchangeColor[0],
            backgroundColor: "rgba(255,255,255,0)",  
        },

        {
           
            label: exchangeType[1],
            data: exchangeValue[1],
            borderColor: exchangeColor[1],
            backgroundColor: "rgba(255,255,255,0)",  
       },
        ]
    },
    options: {
        responsive: false,
        legend: {
          display: false
        }
        
    }
    
    });

  }

  function draw1Line(exchangeType, exchangeValue, exchangeColor){
    var ctx = document.getElementById("chart").getContext('2d');
    var ChartDemo = new Chart(ctx, {
    type: 'line',
    data: {
 
        labels: <?php print json_encode($dateValue); ?>,
        datasets: [

        {
           
            label: exchangeType[0],
            data: exchangeValue[0],
            borderColor: exchangeColor[0],
            backgroundColor: "rgba(255,255,255,0)",  
       },
        ]
    },
    options: {
        responsive: false,
        legend: {
          display: false
        }
        
    }
    
    });
  }

  function draw0Line(){
    var ctx = document.getElementById("chart").getContext('2d');
    var ChartDemo = new Chart(ctx, {
    type: 'line',
    data: {
 
        labels: <?php print json_encode($dateValue); ?>,
        datasets: [
        ]
    },
    options: {
        responsive: false,
        scales: {
        yAxes: [
          {
            ticks: {
              suggestedMin: 0,
              suggestedMax: 130
            }
          }
          ]
        }
        
        
    }
    
    });
  }


</script>


  </body>
</html>