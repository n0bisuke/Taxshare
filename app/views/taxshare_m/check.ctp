<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
       	<link rel="shortcut icon" href="../img/taxshare/icon_16.png" type="image/x-icon" />
		<link rel="icon" href="../img/taxshare/icon_16.png" type="image/x-icon" />
		<!--<title>タクシェア！</title>-->
        <?php echo $html->css('jquery.mobile-1.2.0.min'); //rental ?>  
        <link rel="stylesheet" type="text/css" href="http://dev.jtsage.com/cdn/datebox/latest/jqm-datebox.min.css" /> 
        <?php echo $this->Html->script('jquery-1.8.2.min'); //jquery本体 ?>
     	<?php echo $this->Html->script('jquery.mobile-1.2.0.min'); //jquerymobile本体 ?>
     	<script type="text/javascript" src="http://dev.jtsage.com/cdn/datebox/latest/jqm-datebox.core.min.js"></script>
     	<!-- 以下はCalBox modeの時に必要 -->
     	<script type="text/javascript" src="http://www31092u.sakura.ne.jp/~g031j080/jqm-datebox.mode.calbox.custom.js"></script>
     	<!-- 以下は日本語表記のために必要 -->
     	<script type="text/javascript" src="http://dev.jtsage.com/cdn/datebox/i18n/jquery.mobile.datebox.i18n.ja.utf8.js"></script>
    </head>
    <body>
    <div data-role="page">
    	<div data-role="content" data-theme="b">
		    	この内容で良いですか？<br />
			<a href="schedule" data-role="button" data-rel="back" data-inline="true" data-theme="c">Cancel</a>
			<a href="main" data-role="button" data-inline="true" data-theme="b" data-theme="b">OK</a>
		</div>
	</div>

    </body>
</html>