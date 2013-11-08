<div id="Contents" style="width:100%">
	<form method="post" action="favorite" accept-charset="utf-8">
		<div class="btn-toolbar">
			<div class="btn-group">
				<input type="submit" name="state1" value="コンタクト中" class="btn">
				<input type="submit" name="state2" value="交渉中" class="btn">
				<input type="submit" name="state3" value="完了" class="btn">
			</div>
    	</div>
    </form>
	
        <?php
        	echo $mes;
			echo "<br />";
        	if($data){//予定があれば．
        		foreach ($data as $key => $value) {
        			//重要度によって色を変化
        			if($value['ts_schedules']['important_lv'] == 3){
        				$color = "red"; //緊急はred
        			}elseif($value['ts_schedules']['important_lv'] == 2){
        				$color = "orange"; //急ぎ目はorenge
        			}else{
        				$color = "#86D511"; //普通はgreen
        			}
					//コンタクトかうんと
					/*if($value['ts_contacts']['id'] != null){
						$count = count($value);
					}else{
						$count = null;
					}*/
					
        			echo '<a href="detail?id='.$value['ts_schedules']['id'].'">';
        			$img = '<img src="'.$value['ts_schedules']['img'].'" >';
					$place = $value['ts_schedules']['startpoint'].'〜'.$value['ts_schedules']['endpoint'];
        			echo '<div id="'. $key .'" class="Tile" style="background-color: '. $color .'">'
        		 	. $img
        		 	. '<br />'
        		 	. '<span class="TileTitle">'. $value['ts_schedules']['title'] .'</span>'
        		 	. '<br />'
        		 	. '<span class="TilePlace">'. $place .'</span>'        		         		         		 
        		 	. '<br />'
        		 	. '<span class="TileNote">'. $value['ts_schedules']['note'] .'</span>'
        			// . $count
        		 	. '<span class="TileFixeddate">'. $value['ts_schedules']['fixeddate'] .'</span>'
        		 	.'</div>';
				 	echo '</a>';
				}
			}else{//予定がなければ
				echo "登録されている予定はありません。";
			}
		?>            
</div>