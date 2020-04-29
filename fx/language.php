<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>fx</title>
    <link rel="shortcut icon" href="png/fxFavicon.png">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <style>
      #whole{
        margin-right: auto;
        margin-left : auto;    
      }
      h1 {
        padding: 6px 10px;
      }
      img.Original{
        padding: 6px 10px;
        width:60px;
        height:60px;
      }
      img.country{
        display: inline;
        width:80px;
        height:80px;
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
    <img src="png/fxlogo.png" style="width:256px;" >
  </div>
  <br />
  <div id="whole">
    <a style="font-size:30px;">select your language</a><br /><br /><br />
    <div>
      <a href="index-orig.php"><img class="Original" src="png/globalLNG.png" align="middle">Original text/原文/原始文字</a>
    </div>
    <div>
      <a href="index-en.php"><img class="country" src="png/en.png" align="middle">English</a>
    </div>
    <div>
      <a href="index-ja.php"><img class="country" src="png/ja.png" align="middle">日本語</a>
    </div>
    <div>
      <a href="index-zh.php"><img class="country" src="png/zh.png" align="middle">中文</a>
    </div>
    <br />
    <br />
  </div>
  </body>
</html>