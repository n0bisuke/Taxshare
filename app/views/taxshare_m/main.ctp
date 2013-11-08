<ul data-role="listview" class="ui-li-icon">
<?php
foreach ($data as $key => $value) {
	echo '<li><a href="detail?id='.$value['TsSchedule']['id'].'">';
?>
	<img src='<?php echo $value['TsSchedule']['img'];?>' alt="image" class="ui-li-icon ui-li-thumb" >
<?php
	echo $value['TsSchedule']['title'];
	echo $value['TsSchedule']['fixeddate'].' ';
	echo '</a></li>';
}
?>
</ul>
