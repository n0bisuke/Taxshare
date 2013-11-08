このユーザとマッチングでよろしいですか？
<br />
<img src="https://graph.facebook.com/<?php echo $params['user_fb_id'] ?>/picture" width="50px" />
<br />
<?php 
	echo '<a href="decide?
				id='.$params['id'].
				'&user_id='. $params['user_id'].
				'&user_fb_id='. $params['user_fb_id'].'
				">
				OK!
				</a>';
?>