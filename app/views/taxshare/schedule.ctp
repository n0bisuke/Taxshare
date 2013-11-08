<h4>予定登録</h4>
<?php echo $error; ?>
<div class="input-form">
<!--入力フォーム-->
<form method="post" action="schedule" accept-charset="utf-8">
	<span class="label label-important" >タイトル:</span>
	<select id="title" name="data[TsSchedule][title]" placeholder="タイトルを入力" >
		<option value="passenger">乗せて!</option>
		<option value="driver">乗せるよ!</option>
	</select>
	<br />
	
	<span class="label label-important" >予定時間:</span><i class="icon-time"></i>
	<input type="text" id="datepicker" name="data[TsSchedule][fixeddate][date]"  value="<?php echo date("Y/m/d"); ?>" placeholder="予定日時を入力" readOnly />
	<input type="text" id="start_time" name="data[TsSchedule][fixeddate][time]"  value="<?php echo date("H:i"); ?>" placeholder="予定時刻を入力" readOnly />
	<br /><br />
	<span class="label label-warning" >受付期限:</span><i class="icon-time"></i>
	<input type="text" id="limit_date" name="data[TsSchedule][limit][date]"  value="<?php echo date("Y/m/d"); ?>" placeholder="予定日時を入力" readOnly />
	<input type="text" id="limit_time" name="data[TsSchedule][limit][time]"  value="<?php echo date("H:i"); ?>" placeholder="予定時刻を入力" readOnly />
	<br /><br />
	
	<span class="label label-important" >場所:</span><i class="icon-road"></i>
	<select name="data[TsSchedule][startpoint]" id="select-choice-1">
      	<?php
      	foreach ($venue as $key => $value) {
      		echo '<option value="'.$value['TsVenue']['name'].' ">';
      		echo $value['TsVenue']['name'];
			echo '</option>';
		  }
	    ?>
     </select>
     周辺から
     <select name="data[TsSchedule][endpoint]" id="select-choice-1">
      	<?php
      	foreach ($venue as $key => $value) {
      		echo '<option value="'.$value['TsVenue']['name'].' ">';
      		echo $value['TsVenue']['name'];
			echo '</option>';
		  }
	    ?>
      </select>
    周辺まで
    <br /><br />

    <span class="label label-success" >備考</span>
    <textarea name="data[TsSchedule][note]" rows="2" cols="80" placeholder="備考を入力"></textarea>
	<input type="hidden" name="data[TsSchedule][user_id]" value="<?php echo $user['TsUser']['id']; ?>">
	<input type="hidden" name="data[TsSchedule][img]" value="<?php echo 'https://graph.facebook.com/'. $user['TsUser']['facebook_id'] . '/picture'; ?>">
	<br /><br />

	<i class="icon-exclamation-sign"></i>
	<span class="label label-warning" >重要度:</span>
	<select name="data[TsSchedule][important_lv]">
		<option value="3">緊急!!</option>
		<option value="2">急ぎ気味</option>
		<option selected="selected" value="1">普通</option>
	</select>
	<br /><br />
	<i class="icon-thumbs-up"></i>facebookでシェア
	<select name="fbshare">
		<option value="off">しない</option>
		<option value="on">する</option>
	</select>
	<br /><br />
	<?php //twitterと連携済みの場合は表示させます．
	if(!empty($user['TsUser']['twitter_id'])){
		echo '
		twitterでシェア
		<select name="twshare">
			<option value="on">する</option>
			<option value="off">しない</option>
		</select><br /><br />';
	}
	?>	
	<input id="signin_submit" value="登録" type="submit" />
</form>
</div>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/i18n/jquery.ui.datepicker-ja.min.js"></script>
<link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/ui-lightness/jquery-ui.css" rel="stylesheet" />
<script type="text/javascript">$(function(){$("#datepicker").datepicker();$("#limit_date").datepicker();});</script>
<?php echo $html->css('jquery.clockpick.1.2.9'); //時刻入力 ?>
<?php echo $this->Html->script('jquery.clockpick.1.2.9.min'); //時刻入力 ?>
<script type="text/javascript">$(function(){ $("#limit_time").clockpick({ starthour: 0,endhour: 23,showminutes: true,minutedivisions: 12,military: true}); $("#start_time").clockpick({ starthour: 0,endhour: 23,showminutes: true,minutedivisions: 12,military: true});});</script>