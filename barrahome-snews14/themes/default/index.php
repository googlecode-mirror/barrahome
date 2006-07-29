<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta http-equiv="Content-Language" content="English" />
	<meta name="Author" content="Solucija.com" />
	<meta name="Robots" content="index,follow" />
	<meta name="Description" content="sNews | Single file CSS and XHTML valid CMS" />
	<meta name="Keywords" content="snews, simple, cms, css, xhtml, valid" />
	<? title(); ?>
	<link rel="stylesheet" type="text/css" href="images/style.css" />
	<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="rss/" />
</head>
<body>
	<div class="content">
		<div class="header">
			<div class="hmenu">
				<? categories(); ?>
			</div>
			<h1><a href="/" title="sNews">BarraHome - Noticias para todos</a></h1>
		</div>
		<div class="left">
			<div class="menu">
				<? menu_items(); ?>
			</div>
						
			<div class="menu">
			<? searchform(); ?>
			</div>
			<div class="left_article">
				<? left(); ?>
			</div>
						
			<h2>Nuevos Articulos:</h2>
				<? new_articles(3); ?>
			<br />
			<h2>Articulos Antiguos:</h2>
				<? past_articles(4,3); ?>
		</div>
		
		<div class="center">
		<?php
		include ("core/modules.php");
		?>		
		</div>

		<div class="footer">
  			<div class="right">
    			<p>Powered by <a href="http://snews.solucija.com" title="Single file CSS and XHTML valid CMS">sNews</a></p>	
  				<p>&copy; Copyright <a href="http://www.barrahome.com.ar/" title="BarraHome">BarraHome</a>, All rights reserved <img src='images/arrow.gif' alt='' /> <? login_link(); ?></p>
    		</div>
    		<p><a href="rss/">RSS Feed</a></p>
    		<p><a href="http://jigsaw.w3.org/css-validator/check/referer" title="Validate CSS">CSS</a> and <a href="http://validator.w3.org/check/referer" title="Validate XHTML">XHTML</a></p>
		</div>
	</div>
</body>
</html>