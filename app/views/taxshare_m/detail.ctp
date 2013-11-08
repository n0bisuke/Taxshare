<!--左側-->
<div class="detail-left">
	<?php
	//予定に参加するゲストユーザ
	$bottomcontent=""; 
	$contactflag=0; //コンタクト済みかどうか判断．
	foreach ($contact as $key => $value){
		//echo "<br />";
		//echo $value['ts_contacts']['orderer_id'];
		$bottomcontent .= '<a href="userprof?id='. $value['ts_contacts']['orderer_id']. '">
				<img src="https://graph.facebook.com/'. $value['ts_users']['facebook_id'] .'/picture" width="20px" />
			</a>';
		$bottomcontent .= $value['ts_contacts']['comment'];
		$bottomcontent .= "<br />";
		//既に登録してるかどうかの振り分け
		if($value['ts_contacts']['orderer_id'] == $user['TsUser']['id'])$contactflag=1;
	}
	//<下で使います．>
	
	if($data != null){//締切前
		echo '<h2>'.$data['TsSchedule']['title'].'</h2>';
		echo '<b>予定時間</b>'.$data['TsSchedule']['fixeddate'];
		echo '<br />';
		echo '<b>受付期限</b>'.$data['TsSchedule']['limit'];
		echo '<br />';
		echo $data['TsSchedule']['note'];
		echo '<br />';
		echo '['.$data['TsSchedule']['startpoint'].']';
		echo '周辺から';
		echo '['.$data['TsSchedule']['endpoint'].']';
		echo '周辺まで<br />';
		if($data['TsSchedule']['important_lv'] == 3){
			echo "緊急!";
		}elseif($data['TsSchedule']['important_lv'] == 2){
			echo "急ぎ目";
		}

		echo '<br />';
 		//ログインユーザが登録した予定の場合、編集と削除が可能となる．
		if($data['TsSchedule']['user_id'] == $user['TsUser']['id']){
			echo '<p><a href="edit?id='. $data['TsSchedule']['id'] .'" class="btn btn-primary">編集</a>
			<a href="delete?id='. $data['TsSchedule']['id'] .'" class="btn btn-danger">削除</a></p>';
			//if()
			echo '<p>
			<form method="post" action="limit" accept-charset="utf-8">
				<input type="hidden" name="id" value="'. $data['TsSchedule']['id'] .'">
				<input type="submit" value="締め切る" class="btn btn-inverse">
    		</form>';
		}else{ //他のユーザが登録した予定に対してはコンタクトを取る事が可能
			if($contactflag != 1){//未コンタクト
				echo '<form method="post" action="contact" accept-charset="utf-8">
				<input type="text" name="comment" placeholder="コメントがあれば入力">
				<input type="hidden" name="id" value="'. $data['TsSchedule']['id'] .'">
				<input type="submit" value="コンタクトを取る" class="btn btn-success">
				</form>';
			}else{//既にコンタクトしている．
				echo '<span class="red">既にコンタクト済みです．<br /> 通知をお待ち下さい．</span>';
			}
			
		}
	}else{//締切後
		echo "この投稿は閉め切られました<br />";
		echo "投稿者とコンタクトを取ったユーザのみが閲覧可能です．<br />";
		echo '<p>
			<form method="post" action="twostate" accept-charset="utf-8">
				<input type="hidden" name="id" value="'. $this->params['url']['id'] .'">
				<input type="hidden" name="user_id" value="'. $commiter['TsUser']['id'] .'">
				<input type="hidden" name="user_fb_id" value="'. $commiter['TsUser']['facebook_id'] .'">
				<input type="submit" value="閲覧する" class="btn">
    		</form>';
	}
	?>
</div>

<!--右側-->
<div class="detail-right">
	<hr>
	<b>登録者:</b><?php echo $commiter['TsUser']['username'];?>
	<br />
	<b>性別:</b><?php echo $commiter['TsUser']['sex'];?>
	<br />
	<span class="detail-in">facebook:</span>
	<a href="http://facebook.com/<?php echo $commiter['TsUser']['facebook_id'];?>">
		<img src="https://graph.facebook.com/<?php echo $commiter['TsUser']['facebook_id'];?>/picture" width="20px" />
	</a>

	<span class="detail-in">twitter:</span>
	<a href="http://twitter.com/<?php echo $commiter['TsUser']['twitter_id'];?>">
		<img width="20px" src="http://api.twitter.com/1/users/profile_image?screen_name=<?php echo $commiter['TsUser']['twitter_id'];?>&size=normal" />
	</a>
</div>

<!--下側-->
<div class="detail-bottom">
 
<b><?php echo (count($contact) + 1); ?>人が閲覧可能。</b>
<br />
<?php	
	//予定を登録したホストユーザ
	echo '<a href="userprof?id='. $commiter['TsUser']['id'] . '">
			<img src="https://graph.facebook.com/'. $commiter['TsUser']['facebook_id'] .'/picture" width="20px" />
		</a>';
	echo "ホスト";
	echo "<br />";
	//予定に参加するゲストユーザ
	echo $bottomcontent;
?>

</div>