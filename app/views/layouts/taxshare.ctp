<!DOCTYPE html>
	<html lang="ja">
    <head>
    	<?php echo $html->charset();?>
    	<link rel="shortcut icon" href="../img/taxshare/icon_16.png" type="image/x-icon" />
		<link rel="icon" href="../img/taxshare/icon_16.png" type="image/x-icon" />
        <title>タクシェア</title>
		<?php echo $html->css('bootstrap'); //bootstrap 2012-8-29 bootstrap v2.1.0 ?>
		<?php echo $html->css('taxshare'); //taxshare ?>
		<?php echo $html->css('jquery.alerts'); //alertsCss ?>
		<link href='http://fonts.googleapis.com/css?family=Eater' rel='stylesheet' type='text/css'>
       	<?php echo $this->Html->script('jquery-1.7.min'); //jquery本体 ?>
       	<?php echo $this->Html->script('bootstrap'); //bootstrap ?>
       	<!--タイマー-->
       	<script language="javascript">$(function(){setInterval(function(){var now = new Date();hou = toDD(now.getHours());min = toDD(now.getMinutes());sec = toDD(now.getSeconds());$('#mclock').text(hou +":"+ min +":"+ sec);},1000);});var toDD = function(num){num += "";if(num.length === 1){num = "0" + num;}return num;};</script>
		<!--<script type="text/javascript">$("#air_text").airport([ 'moscow', 'berlin', 'stockholm' ]);​</script>-->
    </head>
    <body>
    <div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<ul class="nav pull">
				
				<li><a href=""><img src="https://graph.facebook.com/<?php echo $user['TsUser']['facebook_id'] ?>/picture" alt="mypage" style="width: 25px; height: 25px;" />←</a></li>
				<li><a href=""></a></li><li><a href=""></a></li><li><a href=""></a></li><li><a href=""></a></li><li><a href=""></a></li>								
				<li <?php echo ($action == 'main') ? 'class="active"' : ''; ?>><a href="main">検索<i class="icon-search"></i></a></li>
				<li <?php echo ($action == 'schedule') ? 'class="active"' : ''; ?>><a href="schedule">予定登録<i class="icon-plus"></i></a></li>
				<li <?php echo ($action == 'myschedule') ? 'class="active"' : ''; ?>><a href="myschedule">自分の予定<i class="icon-user"></i></a></li>
				<li <?php echo ($action == 'favorite') ? 'class="active"' : ''; ?>><a href="favorite">コンタクトリスト<i class="icon-th-list"></i></a></li>
				<li <?php echo ($action == 'favorite') ? 'class="notice"' : ''; ?>><a href="favorite">お知らせ<i class="icon-gift"></i></a></li>
				<!--<li><a href="toReturn">返す</a></li>
				<li><a href="lend">貸す</a></li>-->
			</ul>
			<ul class="nav pull-right">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						メニュー<span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
						<li><a href="userprof">ユーザー情報</a></li>
						<li><a href="setting">アカウント設定</a></li>
						<li><a href="https://www.facebook.com/taxshare">ヘルプ</a></li>
						<li class="divider">aa</li>
						<li><a href="logout">ログアウト</a></li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
	</div>
    <div class="wrapper">
    	<div class="container-fluid">
    		<div class="row-fluid">
    			<div class="span2 well">
    				<a target="_blank" href="http://www.facebook.com/taxshare"><img src="../img/taxshare/icon_128.png" /> </a>
			   		誰か乗せてくれ！
    				<h1 style="font-family: 'Eater', cursive;">Tax<br />share<small> ver.β</small></h1>    			
    				<div class="alert alert-block alert-success">
						<small>point : ∞</small>
					</div>
					<div id="mclock"></div>
					<div id="air_text"></div>
    			</div>
    			<div class="span10">
    				<div class="content-main">
    				<?php echo $content_for_layout;?>    				
    				</div>
    			</div>
    		</div>
    	</div>
    </div>
    
    <script type="text/javascript" language="javascript">
		<!--
		//facebookのURLの末尾に#_=_が入るので対処
		if(document.URL.match(/#_=_/)){
			location.href='http://www31092u.sakura.ne.jp/~offside_now/rsk/taxshare/';
		}
		// -->
	</script>
    </body>
</html>