<?php
$GETlang = $_GET['hl']; //hlとは言語のこと
$dbid = $_POST['dbid']; //データベースのID
setcookie("goodbutton" .$dbid, "clicked", time()+3600);
$picture = $_POST['picture'];
unlink("picture/" .$picture);
$dsn = 'mysql:dbname=fx;hoat=localhost';
$user = 'root';
$password='**password**';
$dbh = new PDO($dsn,$user,$password);
$dbh->query('SET NAMES utf8');

$sql ='UPDATE comment SET good = good+1 WHERE id=?';
$stmt = $dbh->prepare($sql);
$data[] = $dbid;
$stmt->execute($data);

$dbh = null;
header( "Location: index-" .$GETlang .'.php') ;

?>
