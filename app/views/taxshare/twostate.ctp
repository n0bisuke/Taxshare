<div class="detail-left">
	<?php
		//予定に参加するゲストユーザ	リスト
		$bottomcontent = "";
		foreach ($contact as $key => $value) {
		//accepter なら ユーザ確定
			if($user['TsUser']['id'] == $data['TsSchedule']['user_id']){
				$bottomcontent .= '<a href="beforedecide?
				id='.$data['TsSchedule']['id'].
				'&user_id='. $value['ts_contacts']['orderer_id'].
				'&user_fb_id='.$value['ts_users']['facebook_id'].'">
				<img src="https://graph.facebook.com/'. $value['ts_users']['facebook_id'] .'/picture" width="20px" />
				</a>';
				$bottomcontent .= "<br />";
				$mes = '<span class="blue">ドライブするユーザを選択して下さい．</span>';
			}else{
				//oredere(予定に参加予定の人)ならユーザのページを見せる
				//echo $value['ts_contacts']['orderer_id'];
				$bottomcontent .= '<a href="userprof?id='. $value['ts_contacts']['orderer_id']. '">
				<img src="https://graph.facebook.com/'. $value['ts_users']['facebook_id'] .'/picture" width="20px" />
				</a>';
				$bottomcontent .=  $value['ts_contacts']['comment'];
				$bottomcontent .=  "<br />";
				$mes = '<span class="red">通知が来るまでお待ち下さい</span>';
			}
		}
	echo '<div>'.$data['TsSchedule']['title'].'</div>';
	echo '<br /><br />';
	echo '予定時間<br />'.$data['TsSchedule']['fixeddate'];
	echo '<br /><br />';
	echo '受付期限<br />'.$data['TsSchedule']['limit'];
	echo '<br /><br />';
	echo $data['TsSchedule']['note'];
	echo '<br /><br />';
	echo '['.$data['TsSchedule']['startpoint'].']';
	echo '周辺から';
	echo '['.$data['TsSchedule']['endpoint'].']';
	echo '周辺まで<br /><br />';
		if($data['TsSchedule']['important_lv'] == 3){
			echo "緊急!";
		}elseif($data['TsSchedule']['important_lv'] == 2){
			echo "急ぎ目";
		}
	echo $mes;
	?>
	
	<?php
		//ログインユーザならば, マッチング確定ボタンを表示
		/*
		if($user['TsUser']['id'] == $data['TsSchedule']['user_id']){
			echo '<form method="post" action="contact" accept-charset="utf-8">
				<input type="text" name="comment" placeholder="コメントがあれば入力">
				<input type="hidden" name="id" value="'. $data['TsSchedule']['id'] .'">
				<input type="submit" value="コンタクトを取る" class="btn btn-success">
				</form>';
		}*/
	?>
	
</div>

<div class="detail-right">
<?php
/*
	echo '<form method="post" action="twostate" accept-charset="utf-8">
				<input type="text" name="comment" placeholder="カキコミ">
				<input type="hidden" name="id" value="'. $data['TsSchedule']['id'] .'">
				<input type="hidden" name="user_fb_id" value="'. $user['TsUser']['facebook_id'] .'">
				<input type="submit" value="投稿" class="btn btn-success" >
				</form>';
 * *
 */
?>
<hr>
<?php
	foreach ($board as $key => $value) {
		echo $value['TsBoard']['comment'];
		echo "<br />";
	}

?>
</div>



<!--下側-->
<div class="detail-bottom">
<b><?php echo (count($contact) + 1); ?>人が閲覧可能。</b>
<br />
<?php	
	//予定を登録したホストユーザ
	echo '<a href="userprof?id='. $hostuser['user_id'] . '">
			<img src="https://graph.facebook.com/'. $hostuser['user_fb_id'] .'/picture" width="30px" />
		</a>';
	echo "ホスト";
	echo "<br />";
	//予定に参加するゲストユーザ	リスト
	echo $bottomcontent;
?>

</div>