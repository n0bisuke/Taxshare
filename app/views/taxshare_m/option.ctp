<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="shortcut icon" href="../img/taxshare/icon_16.png" type="image/x-icon" />
		<link rel="icon" href="../img/taxshare/icon_16.png" type="image/x-icon" />
		<title>タクシェア！</title>
		<?php echo $html -> css('jquery.mobile-1.2.0.min');//rental?>
		<link rel="stylesheet" type="text/css" href="http://dev.jtsage.com/cdn/datebox/latest/jqm-datebox.min.css" />
		<?php echo $this -> Html -> script('jquery-1.8.2.min');//jquery本体?>
		<?php echo $this -> Html -> script('jquery.mobile-1.2.0.min');//jquerymobile本体?>
		<script type="text/javascript" src="http://dev.jtsage.com/cdn/datebox/latest/jqm-datebox.core.min.js"></script>
		<!-- 以下は日本語表記のために必要 -->
		<script type="text/javascript" src="http://dev.jtsage.com/cdn/datebox/i18n/jquery.mobile.datebox.i18n.ja.utf8.js"></script>
		.containing-element .ui-slider-switch { width: 9em }
	</head>
	<body>
		<div data-role="page" id="page1">
			<!--ヘッダー-->
			<div data-theme="a" data-role="header" data-position="inline">
				<a href="#" data-icon="back" class="ui-btn-left" data-rel="back">Back</a>
				<h2>taxshare</h2>
			</div>
			<div data-role="content" style="padding: 15px">
				<!--入力フォーム-->
				<form method="post" action="checkschedule" accept-charset="utf-8">
					<center>
						<div class="containing-element">
							<IMG SRC="http://www31092u.sakura.ne.jp/~offside_now/rsk/img/taxshare/facebook.png" width="64" height="64">
							<select name="slider" id="flip-Facebook" data-role="slider">
								<option value="off">Facebook Off</option>
								<option value="on">Facebook On</option>
							</select>
						</div>
					</center>

					<br />

					<center>
						<div class="containing-element">
							<IMG SRC="http://www31092u.sakura.ne.jp/~offside_now/rsk/img/taxshare/twitter.png" width="64" height="64">
							<select name="slider" id="flip-Twitter" data-role="slider">
								<option value="off">Twitter Off</option>
								<option value="on">Twitter On</option>
							</select>
						</div>
					</center>
				</form>
			</div>
			<div data-role="footer">
				<div data-role="navbar" data-iconpos="right">
					<ul>
						<li><a href="#"　data-role="button" data-icon="check">設定変更</a></li>
					</ul>
				</div><!-- /navbar -->
			</div><!-- /footer -->
			<a href="logout">ログアウト</a> <?php echo $user['TsUser']['username']; ?>
		</div>
	</body>
</html>