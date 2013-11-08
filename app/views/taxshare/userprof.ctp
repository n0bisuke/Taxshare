<h3>プロフィール</h3><br />

<h4>名前:
<?php echo $data[0]['ts_users']['username']; ?>
<br />
性別:
<?php echo $data[0]['ts_users']['sex']; ?>
<br />
twitter:
	<a href="http://twitter.com/<?php echo $data[0]['ts_users']['twitter_id'];?>">
		<img src="http://api.twitter.com/1/users/profile_image?screen_name=<?php echo $data[0]['ts_users']['twitter_id'];?>&size=normal" />
	</a>
<br />
Facebook:
	<a href="http://facebook.com/<?php echo $data[0]['ts_users']['facebook_id']; ?>">
		<img src="https://graph.facebook.com/<?php echo $data[0]['ts_users']['facebook_id']; ?>/picture" />
	</a>

<br />
</h4>
<hr>
<br />
<h5>これまでの予定</h5><br />

<div class="alert alert-success">
<?php
	foreach ($data as $key => $value) {
		echo '<a href="detail?id='.$value['ts_schedules']['id'].'">
			<button class="close" data-dismiss="alert">&times;</button>
    		<strong>'.$value['ts_schedules']['title'].'</strong>'.$value['ts_schedules']['fixeddate'];
		echo "</a><br />";
	}
?>
</div>