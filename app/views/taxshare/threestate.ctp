<div class="detail-left">
	<?php
	//ユーザ判定
	if($user['TsUser']['id'] == $data['TsSchedule']['user_id']){// 予定登録者ならばコンタクトを取った側を表示
		$userstate = '<a href="userprof?id='. $contact[0]['ts_contacts']['orderer_id']. '">
		<img src="https://graph.facebook.com/'. $contact[0]['ts_users']['facebook_id'] .'/picture" width="100px" />
		</a>';
		$hostflag = 1;
	}else{ //コンタクト取った側なら予定登録者を表示
		$userstate = '<a href="userprof?id='. $hostuser['user_id']. '">
		<img src="https://graph.facebook.com/'. $hostuser['user_fb_id'] .'/picture" width="100px" />
	    </a>';
		$hostflag = 0;
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
		//乗客かドライバーかどうか
		$finish = 2; //d_point 登録済フラグ
		if(($data['TsSchedule']['dpflag'] == 0 && $hostflag == 1) || ($data['TsSchedule']['dpflag'] == 1 && $hostflag == 0)){
			$actor = "ドライバー";
			$pointflag = "data[TsContact][d_point]";
			if($contact[0]['ts_contacts']['d_point'] == 0){//d_point未登録なら
				$finish = 1; //評価済フラグ
			}
		}else{
			$actor = "乗客";
			$pointflag = "data[TsContact][p_point]";
			if($contact[0]['ts_contacts']['p_point'] == 0){//p_point未登録なら
				$finish = 1; //評価済フラグ
			}
		}
		
		echo $actor."を評価しよう！";
		echo "<br /><br />";
		echo $userstate;

		if($finish == 1){//$finish = 1なら未登録
			echo '<br /><br />
			<form method="post" action="evaluate" accept-charset="utf-8">
				<select name="'. $pointflag .'">
					<option value="1">まぁまぁ</option>
					<option value="2">良かった</option>
					<option value="3">メチャ良かった</option>
				</select>
				<input type="text" name="data[TsContact][comment]" placeholder="コメントがあれば入力">
				<br />
				<input type="hidden" name="data[TsContact][orderer_id]" value="'. $contact[0]['ts_contacts']['orderer_id'] .'">
				<input type="hidden" name="data[TsContact][id]" value="'. $contact[0]['ts_contacts']['id'] .'">
				<input type="hidden" name="data[TsContact][schedule_id]" value="'. $data['TsSchedule']['id'] .'">
				<input type="submit" value="評価する" class="btn btn-success">
			</form>';
		}else{
			echo "評価済みです。<br />ありがとうございます";
		}
	?>
</div>



<!--下側-->
<div class="detail-bottom">
	<?php
	echo '<a href="userprof?id='. $hostuser['user_id']. '">
		<img src="https://graph.facebook.com/'. $hostuser['user_fb_id'] .'/picture" width="30px" />
	    </a>';
	?>
	
<b>マッチングは終了しました．お互いに評価をして下さい。</b>
<br />
</div>