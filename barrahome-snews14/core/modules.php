		<?php
		if (file_exists($_SERVER['DOCUMENT_ROOT']."/modules/$mod.php")) {
        include($_SERVER['DOCUMENT_ROOT']."/modules/$mod.php");
		} else if (file_exists($_SERVER['DOCUMENT_ROOT']."/modules/$mod.php")) {
        include($_SERVER['DOCUMENT_ROOT']."/modules/$mod.php");
		} else {
        include($_SERVER['DOCUMENT_ROOT']."/modules/news.php");
		}
		?>	