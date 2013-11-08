<!DOCTYPE html>
<html>
    <head>
    	<?php echo $html->charset();?>
        <meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="shortcut icon" href="../rsk/img/taxshare/icon_16.png" type="image/x-icon" />
		<link rel="icon" href="../rsk/img/taxshare/icon_16.png" type="image/x-icon" />
        <title>タクシェア</title>
		<?php echo $html->css('bootstrap'); //bootstrap 2012-8-29 bootstrap v2.1.0 ?>
		<?php echo $html->css('taxshare'); //rental ?>
		<link href='http://fonts.googleapis.com/css?family=Eater' rel='stylesheet' type='text/css'>
		<?php //echo $html->css('popupwindow'); //アラート用 ?>
		<script type="text/javascript">
       		if((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i))) {
       			if (document.cookie.indexOf("iphone_redirect=false") == -1) {
       				window.location = "http://www31092u.sakura.ne.jp/~offside_now/rsk/taxshare_m/";
       			}
       		}
       	</script>
		
       	<?php echo $this->Html->script('jquery-1.7.min'); //jquery本体 ?>
       	<?php echo $this->Html->script('bootstrap'); //bootstrap ?>
       	<?php echo $this->Html->script('jquery.random.text.return'); //テキストのraddom表示 ?>
       	<script type="text/javascript">$(function(){$('.random').randomTextReturn();});</script>
       	
       	<?php echo $this->Html->script('arttextlight'); //テキストのふらっしゅ表示 ?>
       	<?php //echo $this->Html->script('popupwindow'); //アラート用 ?>
       	<script type="text/javascript">jQuery('#arttextlight').artTextLight(); </script>
    </head>
    <body>
    	<div class="wrapper">
    		<h1>タクシェア</h1>
    		<div class="title">taxshare</div>
    		<br />
    		<img src="../img/taxshare/icon_128.png" alt="image" style="width: 128px; height: 128px;" />
    		<br />
    		<br />
			<!--<div>
			<?php echo $this->Session->flash('auth'); ?>
			<?php echo $this->Form->create('Taxshare'); ?>
			    <fieldset name="form">
			        <legend><?php echo __('Please enter your username and password'); ?></legend>
			    <?php 
			    	echo $this->Form->input('username');
			        echo $this->Form->input('password');
			    ?>
			    </fieldset>
			<?php echo $this->Form->submit(__('Login')); ?>
			</div>-->
    		<a href="http://www31092u.sakura.ne.jp/~offside_now/rsk/taxshare/facebook" class="btn btn-primary btn-large">Facebook経由で[ログイン/登録] <i class="icon-white icon-thumbs-up"></i></a>
    		<br />
 
    		<div class="random">
    			<div>[タクシェア]は日常生活で"車に乗せて欲しい"，"乗せても問題無いよ"</div>
    			<div>といった声をシェアする場です．</div>
     			<div>自家用車の空いている座席をシェアして電車賃と時間を浮かせて,交流をしましょう.</div>
     		</div>
     		<br />
     		推奨ブラウザ<br />
     		<img src="https://devimages.apple.com.edgekey.net/programs/safari/images/safari-logo-lg.png" alt="image" style="width: 30px; height: 30px;" />
     		<img src="http://exo.jp/keypersonq/g-chrome.jpg" alt="image" style="width: 30px; height: 30px;" />
    		<br />
    		・<br />
    		・<br />
    		・<br />
    		・<br />
    		・<br />
    		・<br />
    		・<br />
    		・<br />
    		・<br />
    		・<br />
    		・<br />
    		モバイル版はこちら<br />
    		・<br />
    		・<br />
    		・<br />
    		<a href="https://play.google.com/store/apps/details?id=is.pbl.taxshare&feature=nav_result#?t=W251bGwsMSwyLDNd"><img src="http://smhn.info/wp-content/uploads/2012/04/google-play-logo1.jpg" alt="image" style="width: 400px; height: 200px;" /></a>
    		<a href="http://www31092u.sakura.ne.jp/~offside_now/rsk/taxshare_m"> <img src="../img/taxshare/iphone4.png" alt="image" style="width: 128px; height: 128px;" /></a>
        </div>
    </body>
</html>