<h4>設定変更</h4>
<?php
	if(!empty($user['TsUser']['twitter_id'])){//twitterと連携済みの場合
		 $mes_a = '<span class="label label-success">現在 twitterアカウント連携済みです。</span><br />';
		 $mes_b = '<a href="http://www31092u.sakura.ne.jp/~offside_now/rsk/taxshare/twdeconnect">
			<button class="btn btn-warning">twitterアカウントとの連携をやめる。<i class="icon-ban-circle"></i>
			</button></a>';
	}else{//twitterと未連携の場合
		$mes_a = '<span class="label label-warning">twitterアカウントとまだ連携していません。</span><br />';
		$mes_b = '<a href="http://www31092u.sakura.ne.jp/~offside_now/rsk/taxshare/twitter">
			<button class="btn btn-info">twitterアカウントと連携する。 <i class="icon-refresh"></i>
			</button></a>';
	}
	
	echo $mes_a.$mes_b;
?>
