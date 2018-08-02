<!-- 森谷英和-->
<!--どの変数か分からなくなったら、変数で検索かけろ-->
<html>
<head>
<meta charset = "utf-8">
<title>mission 4-MySQL連携掲示板</title>
</head>
<body>
<h2>ようこそ！20-02B #7の掲示板へ！</h2>

<?php
date_default_timezone_set ('Asia/Tokyo') ; //まあこれはいいよね

//変数をじゃんじゃん指定して行く
$name = $_POST['name'] ; //新規投稿・名前
$comment = $_POST['comment'] ; // 新規投稿・コメント
$date=date ("Y/m/d H:i:s") ;
$pass = $_POST['pass'] ; //新規投稿・パスワード
$ID=$_POST['ID'] ; //編集・投稿番号
$button=$_POST['button'] ; //ボタンの種類、if分岐で必要(sending, edit, delete)

// データベースにログイン
$dsn = 'データベース名' ;
$user = 'ユーザー名' ; 
$password = 'パスワード' ;
$pdo = new PDO($dsn, $user, $password) ;

//条件分岐スタート！
if ( $button == 'sending'){ //新規投稿
	if(!empty($name) and !empty($comment) and !empty($pass)){
		$sql= "select max(id) from keijiban" ;
		$id= $pdo -> query($sql) ;
		foreach ($id as $a){
		$b=$a[0]+1;
		}
		/*if($b==0){
			$b=1 ;
		}*/
		$sql = $pdo -> prepare ("INSERT INTO keijiban (id, name, comment, date, password) VALUES('$b', :name, :comment, :date, :password)") ;
		$sql -> bindParam (':name', $name, PDO::PARAM_STR) ;
		$sql -> bindParam (':comment', $comment, PDO::PARAM_STR) ;
		$sql -> bindParam (':date', $date, PDO::PARAM_STR) ;
		$sql -> bindParam (':password', $pass, PDO::PARAM_STR) ;
		$sql -> execute() ;
	}else{
		echo '名前・コメント・パスワードを全て記述してください。'."<br />" ;
	}
}else if($button == 'edit'){ //編集
	if(!empty($name) and !empty($comment) and !empty($pass)){
		$sql="select * from keijiban where id = '$ID_e'" ;
		$ninsho = $pdo -> query ($sql) ;
		 foreach ($ninsho as $row) {
		 	$match=$row['password'] ;
		 }
		 if ($pass_e == $match){
		 	$sql = "update keijiban set name = '$name' , comment = '$comment', date = '$date' where id = '$ID' " ;
		 	$pdo -> query($sql) ;
		 }else{
		 	echo 'パスワードが違います。'."<br />" ;
		 }
	}else{
		echo '名前・コメント・パスワードを全て記述してください。'."<br />" ;
	}	
}else if ($button == 'delete'){ //削除
	$sql="select * from keijiban where id = '$ID'" ;
		$ninsho = $pdo -> query ($sql) ;
		 foreach ($ninsho as $row) {
		 	$match=$row['password'] ; 
		 }
		 if ($pass == $match){
		 	$sql = "delete from keijiban where id = '$ID' " ;
		 	$pdo -> query($sql) ;
		 	$sql= "select max(id) from keijiban" ;
			$id= $pdo -> query($sql) ;
			foreach($id as $a){
				$b=$a[0] ;
			}
			for ($i = $ID+1 ; $i<=$b ; $i++){
				$sql = "update keijiban set id = $i -1 where id = '$i' " ;
		 		$pdo -> query($sql) ;
		 	}
		 }else{
		 	echo 'パスワードが違います。'."<br />" ;
		 }
}

//表示
$sql = 'SELECT * FROM keijiban order by id' ;
$results = $pdo -> query ($sql) ;
foreach ($results as $row) {
	echo "[".$row['id']."]".'<>'.$row['name'].'<>'.$row['date']."<br />" ;
	echo nl2br($row['comment'])."<br />" ;
}
echo "<br />" ;
?>

<!--投稿フォーム-->
<form method = 'post' action = 'keijiban MySQL.php'>
名前①  <input type=text name= 'name' ><br>
コメント②<br>
<textarea name='comment' rows='5' cols='50' wrap='soft'></textarea><br>
パスワード③
<input type=text name='pass'>投稿番号④<select name='ID'>
<?php
$sql = 'select * from keijiban order by id' ;
$getid = $pdo -> query ($sql);
foreach ($getid as $row){
	echo '<option name="',$row['id'],'">',$row['id'],'</option>' ;
}
?>
</select><p>
<button type=submit name='button' value='sending'>新規</button>
<button type=submit name='button' value='edit'>編集</button>
<button type=submit name='button' value='delete'>削除</button></p>
<p>新規...①+②+③(設定)<br>
編集...①+②+③(照合)+④<br>
削除...③(照合)+④</p>
</body>
</html>