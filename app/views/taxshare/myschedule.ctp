自分へのコンタクト<br />

<br />
<?php echo count($my)."件<br />"; ?>
<div class="myschedule">
<?php	
	foreach ($my as $key => $value) {
		
		echo '
			<a href="http://facebook.com/'.$value['ts_users']['facebook_id'].'">
			<img src="https://graph.facebook.com/'.$value['ts_users']['facebook_id'].'/picture" />
			</a>';
		echo '<a href="detail?id='. $value['ts_contacts']['schedule_id'] .'">';
		echo "・";
		echo $value['ts_users']['username'];
		echo "    ";
		echo $value['ts_contacts']['comment'];
		echo "</a><br />";
	}
?>
</div>