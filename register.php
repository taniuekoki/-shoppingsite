<?php
//最終更新日2023-01-23 02:26AM

//修正 2023-01-28 fujimoto
session_start();

require_once './module/functions.php';
require_once './module/UserLogic.php';


///ログイン時マイページへ
$result = UserLogic::checkLogin();
if($result) {
	header('Location: mypage.php');
	return;
}
/*
$login_err = isset($_SESSION['login_err']) ? $_SESSION['login_err'] : null;
unset($_SESSION['login_err']);
*/
//$err = $_SESSION["register_err"];	//これあかん

$err = isset($_SESSION['register_err']) ? $_SESSION['register_err'] : null;
unset($_SESSION['register_err']);
$_SESSION["register_err"] = array();

$register_userdata = isset($_SESSION['register_userdata']) ? $_SESSION['register_userdata'] : null;
unset($_SESSION['register_userdata']);
if(isset($register_userdata["nickname"])){
	$nickname = $register_userdata["nickname"];
}else{
	$nickname = "";
}


if($_SERVER["REQUEST_METHOD"]=="POST"){
	
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
  <title>新規登録</title>
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/register.css">
  <script src="js/jquery-2.1.4.min.js"></script>
  <script src="js/register.js"></script>
</head>

<body>
<div class="innerWrap">
		<header class="header">
			<?php include "header.php" ?>
		</header>
		
		<main>
		
			<h2>新規会員登録</h2>
				
				
			<div class="red">
			※このサイトはデモサイトです。<br>
			個人情報は入力しないでください。<br>
			メールアドレスのみ新規会員登録の際に必要になりますので
			正しく受け取れるメールアドレスを入力してください。<br>
			作成されたアカウント情報は、31日後に自動的に削除されます。
			</div>
			<br>
			
  			<form action="registercomplete.php" method="POST">
  				<div class="form">
					<p>ニックネーム</p>
					<div class="selectwrap">
					<select name="nickname">
						<option value="田中太郎" <?php if($nickname=="田中太郎") echo "selected"?>>田中太郎</option>
						<option value="佐藤五郎" <?php if($nickname=="佐藤五郎") echo "selected"?>>佐藤五郎</option>
						<option value="小川花子" <?php if($nickname=="小川花子") echo "selected"?>>小川花子</option>
						<option value="斉藤信子" <?php if($nickname=="斉藤信子") echo "selected"?>>斉藤信子</option>
						<option value="マイケル" <?php if($nickname=="マイケル") echo "selected"?>>マイケル</option>
					</select>
					</div>
					<br>
					
					<p>メールアドレス(ID)</p>
			    	<input type="email" name="user_id" value="<?php if (isset($register_userdata['user_id'])) echo $register_userdata['user_id']?>"><br>
					<?php if (isset($err['user_id'])) : ?>
    		    		<p style="color:red;"><?php echo $err['user_id']; ?></p>
    				<?php endif; ?>
    				<?php if (isset($err['overlap'])) : ?>
    		    		<p style="color:red;"><?php echo $err['overlap']; ?></p>
    				<?php endif; ?>
					<p>メールアドレス(確認)</p>
					<input type="email" name="user_id_conf" value="<?php if (isset($register_userdata['user_id_conf'])) echo $register_userdata['user_id_conf']?>"><br>
					<?php if (isset($err['user_id_conf'])) : ?>
    		    		<p style="color:red;"><?php echo $err['user_id_conf']; ?></p>
    				<?php endif; ?>
					<p>パスワード <span style="color:#808080;font-size:small;">英数字8～32文字</span></p>
					<input type="password" name="pass"><br>
					<?php if (isset($err['pass'])) : ?>
    		    		<p style="color:red;"><?php echo $err['pass']; ?></p>
    				<?php endif; ?>
					<p>パスワード(確認) <span style="color:#808080;font-size:small;">英数字8～32文字</span></p>
			    	<input type="password" name="pass_conf"><br>
			    	<?php if (isset($err['pass_conf'])) : ?>
    		    		<p style="color:red;"><?php echo $err['pass_conf']; ?></p>
    				<?php endif; ?>
  					<input type="hidden" name="csrf_token" value="<?php echo h(setToken()); ?>">
  					<br>
  					
  					<p>利用規約</p>
						<textarea name="kiyaku">
						
本規約はユーザーの個別の同意を要せず、本規約を変更することができるものとします。

（１）個人情報について
メールアドレスとパスワードは暗号化されて保存されております。
入力された項目については、当サイトの機能デモを利用する為の用途では使用いたしません。

（２）コンテンツについての権利の帰属
当サイトにあるコンテンツの著作権は当サイトに帰属します。
コンテンツを当サイトが通知なく修正、変更、廃止を行えるものとする。
投稿したコンテンツが消失した際、当サイトは賠償責任を負わないものとする。

（３）禁止事項
1.法令または公序良俗に違反する行為。
2.犯罪行為に関連する行為。
3.本サービスの内容等、本サービスに含まれる著作権、商標権ほか知的財産権を侵害する行為。
4.当サイト、ほかのユーザー、またはその他第三者のサーバーまたはネットワークの機能を破壊したり、妨害したりする行為。
5.コンテンツの二次配布。
6.当サイトのサービスの運営を妨害するおそれのある行為。
7.不正アクセスをし、またはこれを試みる行為。
8.他のユーザーに関する個人情報等を収集または蓄積する行為。
9.不正な目的を持って本サービスを利用する行為。
10.本サービスの他のユーザーまたはその他の第三者に不利益、損害、不快感を与える行為。
11.他のユーザーに成りすます行為。
12.当サイトが許諾しない本サービス上での宣伝、広告、勧誘、または営業行為。
13.当サイトのサービスに関連して、反社会的勢力に対して直接または間接に利益を供与する行為。
14.その他、当サイトが不適切と判断する行為。

（４）本サービスの提供の停止等
以下のいずれかの事由があると判断した場合、ユーザーに事前に通知することなく本サービスの全部または一部の提供を停止または中断することができるものとします。
1.本サービスにかかるコンピュータシステムの保守点検または更新を行う場合。
2.地震、落雷、火災、停電または天災などの不可抗力により、本サービスの提供が困難となった場合。
3.コンピュータまたは通信回線等が事故により停止した場合。
4.その他、当サイトが本サービスの提供が困難と判断した場合。
当サイトは、本サービスの提供の停止または中断により、ユーザーまたは第三者が被ったいかなる不利益または損害についても、一切の責任を負わないものとします。

（５）利用制限および登録抹消
ユーザーが以下のいずれかに該当する場合には、事前の通知なく、ユーザーに対して、本サービスの全部もしくは一部の利用を制限し、またはユーザーとしての登録を抹消することができるものとします。
1.本規約のいずれかの条項に違反した場合。
2.登録事項に虚偽の事実があることが判明した場合。
3.当サイトからの連絡に対し、一定期間返答がない場合。
4.本サービスについて、最終の利用から一定期間利用がない場合。
5.その他、当サイトが本サービスの利用を適当でないと判断した場合。
当サイトは、本条に基づき当サイトが行った行為によりユーザーに生じた損害について、一切の責任を負いません。

（６）退会
ユーザーアカウントは、新規登録から31日後に自動的に削除・退会処理されます。

（７）保証の否認および免責事項
1.当サイトは、本サービスに事実上または法律上の瑕疵（安全性、信頼性、正確性、完全性、有効性、特定の目的への適合性、セキュリティなどに関する欠陥、エラーやバグ、権利侵害などを含みます。）がないことを明示的にも黙示的にも保証しておりません。
2.当サイトは、本サービスに起因してユーザーに生じたあらゆる損害について、当サイトの故意又は重過失による場合を除き、一切の責任を負いません。

（８）準拠法・裁判管轄
本規約の解釈にあたっては，日本法を準拠法とします。
						
						</textarea><br><br>
						
						<div class="kiyaku">利用規約に同意する&emsp;<input id="ch2" class="kiyaku" type="checkbox" name="check" value="" style="transform:scale(1.5);"></div>
 					<br><br>
 					
 					<div class="bt">
			    	<input id="btn1" type="submit" value="送信">
			    	</div>
		    	</div>
			</form>
			
		</main>
	</div>
<footer>
			<?php include "footer.php" ?>
	</footer>
</body>
</html>
