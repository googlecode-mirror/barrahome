<?php
// ----------------------------------------------------------------------
// sNews v1.4
// Copyright(c) 2006, Solucija - All rights reserved
// http://www.solucija.com/
//
// sNews is licenced under a Creative Commons Licence,
// see http://creativecommons.org/licenses/by/2.5/ for more info.
// ----------------------------------------------------------------------
// For information how to set up the MySQL database, see readme.txt.
// Enter your settings below
// ----------------------------------------------------------------------

error_reporting (E_ALL ^ E_NOTICE);


//*******************************************************************************
//    END OF SETTINGS & VARIABLES, EDIT ONLY IF YOU KNOW WHAT YOU'RE DOING
//*******************************************************************************





// PERMANENT CONNECTION TO THE DATABASE
function connect_to_db() {
	$db = mysql_pconnect(s('dbhost'),s('dbuname'),s('dbpass'));
	mysql_select_db(s('dbname')) or die(s('dberror'));
}

// FIND CATEGORY'S SEF TITLE THROUGH ARTICLE'S CATEGORY ID
function find_cat_sef($categoryid) {
	$query = "SELECT seftitle FROM " .s('prefix'). "categories WHERE id = '$categoryid'";
	$result = mysql_query($query);
  		while ($r = mysql_fetch_array($result)) {
	  		$cat_sef = $r['seftitle'];   		
		}
		if (isset($cat_sef)) { 
			$cat_sef_title = $cat_sef; 
		} else {
			$cat_sef_title = s('home'); // if there's no such category - it's home
		}
	return $cat_sef_title;
}

// FIND ARTICLE'S SEF TITLE THROUGH ID
function find_article_sef($articleid) {
	$query = "SELECT seftitle FROM " .s('prefix'). "articles WHERE id = '$articleid'";
	$result = mysql_query($query);
  		while ($r = mysql_fetch_array($result)) {
	  		$article_sef_title = $r['seftitle'];   		
		}
	return $article_sef_title;
}

// FIND ARTICLE'S CATEGORY THROUGH ARTICLE'S ID
function find_article_cat($articleid) {
	$query = "SELECT category FROM " .s('prefix'). "articles WHERE id = '$articleid'";
	$result = mysql_query($query);
  		while ($r = mysql_fetch_array($result)) {
	  		$article_cat = $r['category'];   		
		}
	return $article_cat;
}

// CLEAN - cleaning query
function clean($query) {
	$query = mysql_real_escape_string(addslashes($query));
	return $query;
}
// CLEAN - XSS stuff clean.
function cleanXSS($text) {
	$allowedtags = '<b><i><br><a><ul><li><pre><hr><blockquote><img>';
	$notallowedattribs = array("@javascript:|onclick|ondblclick|onmousedown|onmouseup"
		."|onmouseover|onmousemove|onmouseout|onkeypress|onkeydown|onkeyup@si");
	$changexssto = '';
	$text = str_replace("\n", "<br />", $text);
	$text = preg_replace($notallowedattribs,$changexssto,$text);
	$text = strip_tags($text,$allowedtags);
	//Clean words if asked.
	$text = cleanWords($text);
	return $text;
}

// CLEAN - WORD FILTER
function cleanWords($text) {
	if ((strtolower(s('word_filter_enable')) == 'yes') AND (file_exists(s('word_filter_file')))) {
		$bad_words_from_what = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "", file(s('word_filter_file')));
		$bad_words_from_what = preg_replace('/^(.*)$/', '/\\1/i', $bad_words_from_what);
		echo $test[0];
		$bad_words_to_what = s('word_filter_change');
		$text = preg_replace($bad_words_from_what, $bad_words_to_what, $text);
		return $text;
	} else {
		return $text;
}}

//********
// CHECK IF UNIQUE
//********
function check_if_unique($what, $text, $not_id = 'x') {

	switch ($what) {
		case 'article_seftitle':
			$sql = "SELECT id FROM ".s('prefix')."articles WHERE seftitle = '".clean($text)."' AND id != '".$not_id."'";
			break;
		case 'article_title':
			$sql = "SELECT id FROM ".s('prefix')."articles WHERE title = '".clean($text)."' AND id != '".$not_id."'";
			break;
		case 'category_seftitle':
			$sql = "SELECT id FROM ".s('prefix')."categories WHERE seftitle = '".clean($text)."' AND id != '".$not_id."'";
			break;
		case 'category_name':
			$sql = "SELECT id FROM ".s('prefix')."categories WHERE name = '".clean($text)."' AND id != '".$not_id."'";
			break;
		};
	$rows = mysql_num_rows(mysql_query($sql));
	if ( $rows == 0 ) {
		return false;
	} else {
		return true;
	}
}

// JAVASCRIPT ECHO
function if_javascript_on($code, $do = 'echo') {
	if (strtolower(s('use_javascript')) == True) {
		if ($do=='echo') {
			echo $code;
		} else {
			return $code;
		}
	}
}

function if_javascript_off($code) {
	if (strtolower(s('use_javascript')) != True) {
		if ($do=='echo') {
			echo $code;
		} else {
			return $code;
		}
	}
}

// GET ID
function get_id($parameter) {
		$url = Array();
		$url = explode("/", clean($_GET['category']));
  		$get_id = Array();
		$get_id['category'] = $url['0'];
		if (isset($url['1'])) {
			$get_id['article'] = $url['1'];
		}
		if (isset($url['2'])) {
			
			$get_id['commentspage'] = $url['2'];
		}
	if (isset($get_id[$parameter])) {
	return $get_id[$parameter];
}}

// UPDATE ARTICLES (FUTURE POSTING)
function update_articles() {
	mysql_query("UPDATE ".s('prefix')."articles SET published=1 WHERE published=0 AND date <= '".date("Y-m-d H:i:s")."'");
}

//Make a clean SEF url
function cleanSEF($string) {
	$string = str_replace(' ', '-', $string);
	$string = preg_replace('/[^0-9a-zA-Z-_]/', '', $string); 
	$string = str_replace('-', ' ', $string);
	$string = preg_replace('/^\s+|\s+$/', '', $string);
	$string = preg_replace('/\s+/', ' ', $string);
	$string = str_replace(' ', '-', $string);
	return strtolower($string);
}

function cleancheckSEF($string) {
	if (!ereg("^[_a-zA-Z0-9-]+$", $string)) {   
		$ret="notok";
	} else {
		$ret="ok";
	} 
	return $ret;
}

// PHP Vs Mysql time difference for usage in articles update.
function phpvsmysqltimediff() {
    $mysql_datetime = mysql_query('SELECT now()');
	$mysql_datetime = mysql_fetch_row($mysql_datetime);
	$mysql_datetime = $mysql_datetime['0'];
	$mysql_datetime = strtotime($mysql_datetime);
	$php_datetime = strtotime("now");
	$timediff = $php_datetime-$mysql_datetime;
	$days=intval($timediff/86400);
	$remain=$timediff%86400;
	$hours=intval($remain/3600);
	$remain=$remain%3600;
	$mins=intval($remain/60);
	$secs=$remain%60;
	echo $timediff=$hours.' hours '.$mins.' minutes '.$secs.' seconds';
}

// If javascript is on echos javascript functions under <title/>
function javascript_headers() {
if_javascript_on('
<script type="text/javascript">
//<![CDATA[
function makesef() {
text_from = document.getElementById(\'article_title\');
text_to = document.getElementById(\'article_sef\');
str = text_from.value;
ex = /\$|,|@|#|~|`|\%|\*|\^|\.|\&|\(|\)|\+|\=|\[|\]|\[|\}|\{|\;|\:|\'|\"|\<|\>|\?|\||\\\\|\!|\$|\//g;
str = str.replace(ex, "");
ex = /^\s+|\s+$/g;
str = str.replace(ex, "");
ex = /\s+/g;
str = str.replace(ex, "_");
str = str.toLowerCase();
text_to.value = str;
};
//]]>
</script>
   <script language="javascript" type="text/javascript" src="/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
        <script language="javascript" type="text/javascript">
        tinyMCE.init({
            mode : "textareas",
            theme : "advanced",
            editor_selector : "mceEditor",
            language : "es",
            theme_advanced_toolbar_location : "top",
            theme_advanced_toolbar_align : "left",
            theme_advanced_buttons1_add : "separator,insertdate,inserttime,preview,zoom,separator,forecolor,backcolor",
            width : "485",
            height : "300"
            });
   </script>');

};

//*******************************************************************************
//                               WEBSITE FUNCTIONS
//*******************************************************************************

// STARTUP
function snews_startup() {
	putenv("TZ=".s('new_timezone')."");
	connect_to_db();
	update_articles();
	if (isset($_POST['Submitted']) == "True") {
		if (md5(clean($_POST['Username'])) == s('username') && md5(clean($_POST['Password'])) == s('password')) {
			$_SESSION['Logged_In'] = "True";
    		$_SESSION['Username'] = s('username');
    		$_SESSION['Website'] = s('website');
		}
	}
};
snews_startup();

// TITLE
function title() {
    echo "<base href='" .s('website'). "' />";
    $article_title = get_id('article');
    $category_title = get_id('category');
        if ($article_title == "") {
        $category = $_GET['category'];
        if ($category_title == "") {
        $title = s('website_title'). " &raquo; "."Home";
        } elseif ($category == "contact") {
        $title = s('website_title'). " &raquo; "."Contact";
        } elseif ($category == "archives") {
        $title = s('website_title'). " &raquo; "."Archives";
        } else {
        $query = "SELECT * FROM " .s('prefix'). "categories WHERE seftitle = '$category_title'";
        $result = mysql_query($query);
          while ($r = mysql_fetch_array($result)) {
            $title = s('website_title'). " &raquo; ". $r['name'];
        }
    }
       
    } else {
        $query = "SELECT * FROM " .s('prefix'). "articles WHERE seftitle = '$article_title'";
        $result = mysql_query($query);
          while ($r = mysql_fetch_array($result)) {
            $title = s('website_title'). " &raquo; ". $r['title'];
        }
    }
    echo "<title>" .$title. "</title>";
    javascript_headers();
}

// LOGIN LOGOUT LINK
function login_link() {
	if (isset($_SESSION['Logged_In'])) {
		echo '<a href="'.s('website').'logout/" title="'.l('logout').'">'.l('logout').'</a>';
	} else {
		echo '<a href="'.s('website').'login/" title="'.l('login').'">'.l('login').'</a>';
	}
}
function breadcrumbs($title = '') {
    if ($title == '') { $title = s('website_title'); };
    if (get_id('article')) {
        echo '<a href="'.s('website').'">'.$title.'</a> &raquo; <a href="'.s('website').get_id('category').'/">'.find_cat_name_from_sef(get_id('category')).'</a> &raquo; <a href="'.s('website').get_id('category').'/'.get_id('article').'/">'.find_art_name_from_sef(get_id('article')).'</a>';
    } else if (get_id('category')) {
        echo '<a href="'.s('website').'">'.$title.'</a> &raquo; <a href="'.s('website').get_id('category').'/">'.find_cat_name_from_sef(get_id('category')).'</a>';
    } else {
        echo '<a href="'.s('website').'">'.$title.'</a>';
    };
}

// FIND CATEGORY'S NAME THROUGH SEF TITLE
function find_cat_name_from_sef($categorysef) {
    $query = "SELECT name FROM " .s('prefix'). "categories WHERE seftitle = '$categorysef'";
    $result = mysql_query($query);
    while ($r = mysql_fetch_array($result)) {
        $cat_name = $r['name'];   
    }
    return $cat_name;
}

// FIND ARTICLE'S NAME THROUGH SEF TITLE
function find_art_name_from_sef($seftitle) {
    $query = "SELECT title FROM " .s('prefix'). "articles WHERE seftitle = '$seftitle'";
    $result = mysql_query($query);
    while ($r = mysql_fetch_array($result)) {
        $art_name = $r['title'];   
    }
    return $art_name;
}

// DISPLAY CATEGORIES
function categories() { 
	echo "<ul>";
	echo "<li><a href='". s('website'). "' title='" .s('website_title'). "'>". l('home') ."</a></li>";
	$query = "SELECT * FROM " .s('prefix'). "categories WHERE published = 'YES' ORDER BY id"; 
	$result = mysql_query($query);
	while ($r = mysql_fetch_array($result)) {
		$calc_num_query = "SELECT * FROM " .s('prefix'). "articles WHERE position = 1 AND category = $r[id]"; 
		$cm_result = mysql_query($calc_num_query);
		$num_rows = mysql_num_rows($cm_result);
		$category_title = $r['seftitle'];
		if (s('display_num_categories') == True) {
			echo "<li><a href='" .s('website').$category_title. "/' title='". $r['description'] ."'>" .$r['name']. " (" .$num_rows. ") </a></li>"; }
		else { echo "<li><a href='" .s('website').$category_title. "/' title='". $r['description'] ."'>" .$r['name']. "</a></li>"; }}
		echo "</ul>";
}


// DISPLAY MENU ITEMS
function menu_items() { 
	echo "<ul class='menu'>";
	echo "<li><a href='" .s('website'). "archives/'>" .l('archives'). "</a></li>";		
	$query = "SELECT * FROM " .s('prefix'). "articles WHERE position = 3 ORDER BY id"; 
	$result = mysql_query($query);
	while ($r = mysql_fetch_array($result)) {
		echo "<li><a href='" .s('website'). "home/" .$r['seftitle'] ."/'>" .$r['title']. "</a></li>";}
	if (isset($_SESSION['Logged_In'])) { 
		echo "<li><a href='" .s('website'). "categories/'>". l('categories') ."</a></li>"; 
		echo "<li><a href='" .s('website'). "new/'>". l('new_article') ."</a></li>";
		echo "<li><a href='" .s('website'). "unpublished/'>". l('unpublished_articles') ."</a></li>";
		echo "<li><a href='" .s('website'). "images/'>". l('images') ."</a></li>"; } 
		echo "<li><a href='" .s('website'). "contact/'>" .l('contact'). "</a></li>"; ?>
	</ul> <?
}

// LEFT
function left() {	
	$query = "SELECT * FROM " .s('prefix'). "articles WHERE position = 5 AND published = 1 ORDER BY id DESC";
	$result = mysql_query($query);
	while ($r = mysql_fetch_array($result)) {
		if ($r['textlimit'] == 0) { $textlimit = 999000; } else { $textlimit = $r['textlimit']; }
    	if (isset($_SESSION['Logged_In'])) { echo l('edit'). " [ <a href='" .s('website'). "index.php?action=simpleedit&id=$r[id]'>". l('simple') ."</a> <img src='" .s('website'). "images/arrow.gif' alt='|' /> <a href='" .s('website'). "index.php?action=advancededit&id=$r[id]'>". l('advanced') ."</a> ] <img src='" .s('website'). "images/arrow.gif' alt='|' /> <a href='index.php?action=process&task=delete&id=". $r['id'] ."'".if_javascript_on(" onclick='return confirm(\"".l('warning_delete')."\");'",'return').">". l('delete_article') ."</a>"; }
		if ($r['displaytitle'] == "YES") { echo "<h2>". $r['title'] ."</h2>"; }
    	if ($r['textlimit'] == 0) { $textlimit = 999000; } else { $textlimit = $r['textlimit']; }
				
		// PHP files inclusion routine
			$fulltext = $r['text'];
			$findme  = "[include]";
			$pos = strpos($fulltext, $findme); 
			$findme  = "[/include]";
			$pos2 = strpos($fulltext, $findme); 
			$file = substr($fulltext, $pos + 9, $pos2 - 9);
			if ($pos2 > 0) {
				$text = str_replace("[include]", "|&|", $fulltext);
				$text = str_replace("[/include]", "|&|", $text);
				$text = explode("|&|", $text); 
				$num = count($text);
				for ($i = 0; ; $i++) {
					if ($i == $num) {
						break;
					}
					if (strpos($text[$i], '.php') === false AND strpos($text[$i], '.txt') === false AND strpos($text[$i], '.inc') === false) {
						echo substr(stripslashes($text[$i]), 0, $textlimit);
					} else {
						include $text[$i];
					}}} else {
						echo substr(stripslashes($fulltext), 0, $textlimit);
						
					
}}} 

// CENTER			
function center($article_limit) {
	if (isset($_GET['category'])) { $id = $_GET['category']; }
	if (isset($_GET['articleid'])) { $articleid = $_GET['articleid']; }
	if (isset($_POST['submit_text'])) { processing(); $processed = True; }
	if (isset($_POST['contactform'])) { contact(); $processed = True; }
	if (isset($_GET['category'])) { $action = $_GET['category']; } 
	else if (isset($_GET['action'])) { $action = $_GET['action']; }
	if (isset($processed) AND $processed == True) { unset($action); }
	switch ($action) {	
	case "archives": 
		archives();
	break;
	case "contact": 
		contact();
	break;
	case "rss": 
		rss();
	break;
	case "login": 
		login();
	break;
	case "categories": 
		if (isset($_SESSION['Logged_In'])) { view_categories(); }
	break;
	case "editcategory":
		if (isset($_SESSION['Logged_In'])) { edit_category(); }
	break;
	case "new":
		if (isset($_SESSION['Logged_In'])) { new_article(); }
	break;
	case "unpublished": 
		if (isset($_SESSION['Logged_In'])) { unpublished_articles(); }
	break;
	
	case "simpleedit":
		if (isset($_SESSION['Logged_In'])) { edit_article(simple); }
	break;
	case "advancededit":
		if (isset($_SESSION['Logged_In'])) { edit_article(advanced); }
	break;
	case "editcomment":
		if (isset($_SESSION['Logged_In'])) { edit_comment(); }
	break;
	case "images":
		if (isset($_SESSION['Logged_In'])) { images(); }
	break;
	case "process": 
		if (isset($_SESSION['Logged_In']) AND $display_further <> "NO") { processing(); }
	break;	
	case "logout":
    	session_start();
	    $_SESSION = array();
	    session_destroy();
        echo "<META HTTP-EQUIV='refresh' content='1; URL=" . $_SERVER['PHP_SELF'] . "'>";
        echo "<h2>" .l('log_out'). "</h2>";
    break; 
	default: 
		  			
	if (isset($_POST['search'])) { search(); } 
	else if (isset($_POST['comment'])) { comment("comment_posted");} 
	else if ($processed == False) {
		$article = get_id('article'); 
  		$category = get_id('category'); 
  		
  		if ($article <> "") {
	  		$query = "SELECT * FROM " .s('prefix'). "articles WHERE seftitle = '$article'";
	  		$shorten = 99990000;
		} else if (isset($category)){
			
			$query_catname = "SELECT * FROM " .s('prefix'). "categories";
			$result_catname = mysql_query($query_catname);
			while ($r_catname = mysql_fetch_array($result_catname)) {
				if (isset($num_cat)) {
				$num_cat++;
			}
				if ($r_catname['seftitle'] == $category) { $use_cat_id = $r_catname['id']; }
			}
			if ($category == "") { $use_cat_id = 0; $category = 0; }
						
			if (s('display_new_on_home') == True) {
				if ($use_cat_id <> 0) {
					$query = "SELECT * FROM " .s('prefix'). "articles WHERE position = 1 AND published = 1 AND category = $use_cat_id ORDER BY date DESC LIMIT $article_limit";					
				} else {
					$query = "SELECT * FROM " .s('prefix'). "articles WHERE position = 1 AND published = 1 ORDER BY date DESC LIMIT $article_limit";
				}
			} else {
				$query = "SELECT * FROM " .s('prefix'). "articles WHERE position = 1 AND published = 1 AND category = $use_cat_id ORDER BY date DESC LIMIT $article_limit";
			}
			if (isset($r['textlimit'])) {
			$shorten = $r['textlimit'];
		}
			if (isset($shorten) == 0) { $shorten = 9999000; }
		}
		$result = mysql_query($query);
		while ($r = mysql_fetch_array($result)) {
			if ($article == "") { 
		  		$shorten = $r['textlimit'];
		  		if ($shorten == 0) { $shorten = 99990000; }}
		  	$comments_num = 0;
	  		$comment_query = "SELECT * FROM " .s('prefix'). "comments WHERE articleid = $r[id]"; 
	  		$comment_result = mysql_query($comment_query);
  			while ($comment_r = mysql_fetch_array($comment_result)) { $comments_num++; }
      		$date = date(s('date_format'), strtotime($r['date']));
      		$fp_date_format = date(s('fp_date_format'), strtotime($r['date']));
      		$position = $r['position'];
	  		
			if ($category == "0") { $category = s('home'); }
			if ($r['displaytitle'] == "YES" AND $article == "") { 		
			echo "<h2><a href='" .s('website'). find_cat_sef($r['category']). "/" .$r['seftitle']. "/'>" .$r['title']. "</a></h2>"; 
			} else if ($r['displaytitle'] == "YES") {
				echo "<h2>" .$r['title']. "</h2>"; 
	  		}
			if ($r['image'] <> "") { ?>
				<div class="image">
					<img src="<? echo s('website') .s('image_folder'); ?>/<? echo $r['image']; ?>" alt="<? echo $r['title']; ?>" />
				</div><? }
     													
			// PHP files inclusion routine
			$fulltext = $r['text'];
			$findme  = "[include]";
			$pos = strpos($fulltext, $findme); 
			$findme  = "[/include]";
			$pos2 = strpos($fulltext, $findme); 
			$file = substr($fulltext, $pos + 9, $pos2 - 9);
			if ($pos2 > 0) {
				$text = str_replace("[include]", "|&|", $fulltext);
				$text = str_replace("[/include]", "|&|", $text);
				$text = explode("|&|", $text); 
				$num = count($text);
				for ($i = 0; ; $i++) {
					if ($i == $num) {
						break;
					}
					if (strpos($text[$i], '.php') === false AND strpos($text[$i], '.txt') === false AND strpos($text[$i], '.inc') === false) {
						echo substr(stripslashes($text[$i]), 0, $shorten);
					} else {
						include $text[$i];
					}}} else {
						echo substr(stripslashes($fulltext), 0, $shorten);
					}
			
				if (isset($numrows)) { $numrows++; }
      			if ($article == "" AND strlen($r['text']) > $shorten) { echo "...</p>"; }
      			$commentable = $r['commentable'];
      	   		if ($r['position'] <> 3 AND $r['position'] <> 4 OR isset($_SESSION['Username'])) {
      				
	      	   		if ($article == "") {
	      				if ($r['displayinfo'] == "YES") {
		      			echo "<p class='" .s('date_class'). "'>";
		      		if (strlen($r['text']) > $shorten) {		      			
		      			echo "<img src='" .s('website'). "images/more.gif' alt='' /> <a href='" .s('website'). $category. "/" .$r['seftitle']. "/'>". l('read_more') ."</a> ";
	      			}
	      				if ($commentable == "YES" or $commentable == "FREEZ") {
		      				echo "<img src='" .s('website'). "images/comment.gif' alt='' /> <a href='" .s('website'). $category. "/" .$r['seftitle']. "/'>". l('comments') ."(". $comments_num .")</a> ";
	      				}
	      				echo "<img src='" .s('website'). "images/timeicon.gif' alt='' /> " .$fp_date_format. "</p>"; 
      					}} else { 
	      				echo "<p class='" .s('date_class'). "'>";
	      				if (isset($_SESSION['Logged_In']))  {
	      					echo l('edit_article'). " [ <a href='" .s('website'). "index.php?action=simpleedit&id=$r[id]'>". l('simple') ."</a> <img src='" .s('website'). "images/arrow.gif' alt='|' /> <a href='" .s('website'). "index.php?action=advancededit&id=$r[id]'>". l('advanced') ." </a> ] <img src='" .s('website'). "images/arrow.gif' alt='|' /> <a href='" .s('website'). "index.php?action=process&task=delete&id=$r[id]'".if_javascript_on(" onclick='return confirm(\"".l('warning_delete')."\");'",'return').">". l('delete_article') ."</a> <img src='" .s('website'). "images/arrow.gif' alt='|' /> ";
	      					
	      					if ($r['commentable'] == "FREEZ") { echo "<a href='" .s('website'). "index.php?action=process&task=unfreezecomments&id=$r[id]'>". l('unfreeze_comments') ."</a> <img src='" .s('website'). "images/arrow.gif' alt='|' /> ";
	      					} else if ($r['commentable'] <> "NO") { echo "<a href='" .s('website'). "index.php?action=process&task=freezecomments&id=$r[id]'>". l('freeze_comments') ."</a> <img src='" .s('website'). "images/arrow.gif' alt='|' /> "; }
	      				}
      					if ($category <> s('home')) { $category = $category. "/"; }
	      				echo "<a href='" .s('website');
	      				if ($category <> s('home')) { echo $category. "'>";  } else { echo "'>"; }
	      				echo l('back') ."</a> <img src='". s('website'). "images/timeicon.gif' alt='' /> ". $date ."</p>"; 
      		}}}}
			if ($article <> "" AND $commentable == "YES") { comment("unfreezed"); } 
      		else if ($article <> "" AND $commentable == "FREEZ") { comment("freezed"); } 
}}
			
			
// COMMENTS
function comment($freeze_status) {
	$comments_order = s('comments_order');	
	$category = get_id('category');
	$article = get_id('article');
  	$commentspage = get_id('commentspage');
  	if (isset($_POST['commentspage'])) { $go_to_page = $_POST['commentspage']; }
  	$query = "SELECT * FROM " .s('prefix'). "articles WHERE seftitle = '$article'"; 
	$result = mysql_query($query);
  	while ($r = mysql_fetch_array($result)) {
    	$articleid = $r['id'];
    	$id = $r['id'];
    } 
	
	if ($commentspage == 0) { $commentspage = 1; }
	// if (isset($_POST['comment']) AND strlen($_POST['name']) > 2 AND strlen($_POST['comment']) > 5) {
  	if (isset($_POST['comment']) AND audit() AND strlen($_POST['name']) > 2 AND strlen($_POST['comment']) > 5) {		
		echo "<h2>". l('comment_sent') ."</h2>";
		if ($go_to_page > 1) { echo "<p><a href='" .s('website') .$_POST['category']. "/" .$_POST['article'].  "/" .$go_to_page. "/'>". l('backarticle'). "</a></p>"; }
		else { echo "<p><a href='" .s('website') .$_POST['category']. "/" .$_POST['article']. "/'>". l('backarticle'). "</a></p>"; }
		$name = $_POST['name'];
		$comment = $_POST['text'];
		$time = date('Y-m-d H:i:s');
		$articleid = $_POST['id'];
        mysql_query("INSERT INTO ". s('prefix')."comments(articleid,name,comment,time) VALUES('$articleid', '$name', '$comment', '$time')"); 
    } else if (isset($_POST['comment'])) {
		echo "<h2>". l('comment_error') ."</h2>";
		echo "<p>". l('ce_reasons') ."</p>";
		echo "<p><a href='index.php?id=" .$articleid. "&commentspage=" .$commentspage. "'>". l('back'). "</a></p>";
    } else { 	
  	$results_per_page = s('results_per_page');
	$pageNum = 1;
	if(isset($commentspage)) { $pageNum = $commentspage; }
	$offset = ($pageNum - 1) * $results_per_page;
	$totalrows  = "SELECT * FROM " .s('prefix'). "comments WHERE articleid = $articleid ORDER by id DESC";
	$rowsresult = mysql_query($totalrows) or die(s('dberror'));
	$numrows = mysql_num_rows($rowsresult);
	$query  = "SELECT * FROM " .s('prefix'). "comments WHERE articleid = $articleid ORDER by id $comments_order LIMIT $offset, $results_per_page";
	$result = mysql_query($query) or die(s('dberror'));
	while ($r = mysql_fetch_array($result)) {
			echo "<div class='comments'><p>" .cleanXSS($r['comment']). "</p>";
			$date = date(s('comment_dt_format'), strtotime($r['time']));
			echo "<p><img src='" .s('website'). "images/commentname.gif' alt='>' /> <b>" .cleanXSS($r['name']). "</b>";
		if  (date("Y", strtotime($r['time'])) == 1999 OR s('display_comment_time') == "NO") { 
			$date = ""; 
		} else {
			echo " <img src='" .s('website'). "images/arrow.gif' alt='|' /> ";
			echo $date;				
		}
		if (isset($_SESSION['Logged_In'])) { echo " <img src='" .s('website'). "images/arrow.gif' alt='|' /> "; ?>
			<a href="<? echo s('website'); ?>index.php?action=process&action=editcomment&commentid=<?php echo $r['id']; ?>"><? echo l('edit'); ?></a> <img src="<? echo s('website'); ?>images/arrow.gif" alt="|" /> <a href="<? echo s('website'); ?>index.php?action=process&task=deletecomment&articleid=<? echo $articleid; ?>&commentid=<? echo $r['id']; ?>"<? if_javascript_on(" onclick='return confirm(\"".l('warning_delete')."\");'"); ?>"><? echo l('delete_comment'); ?></a> <? 
		} echo "</p></div>"; }
			$maxPage = ceil($numrows/$results_per_page);
			$back_to_page = ceil(($numrows + 1)/$results_per_page);
			$self = $_SERVER['PHP_SELF'];
			if ($pageNum > 1) {
    			$page = $pageNum - 1;
    		if ($page == 1) {
    			$prev = " <a href='" .s('website'). $category. "/" .$article. "/'>< " .l('previous_page'). "</a> ";
			} else {
    			$prev = " <a href='" .s('website'). $category. "/" .$article. "/" .$page. "/'>< " .l('previous_page'). "</a> ";
			}
    			$first = " <a href='" .s('website'). $category. "/" .$article. "/'><< " .l('first_page')."</a>"; 
    		} else { $prev  = "< " .l('previous_page'); $first = "<< " .l('first_page'); }
				if ($pageNum < $maxPage) {
    				$page = $pageNum + 1;
    				$next = " <a href='" .s('website'). $category. "/" .$article. "/" .$page. "/'>" .l('next_page'). " ></a> ";
        			$last = " <a href='" .s('website'). $category. "/" .$article. "/" .$maxPage. "/'>" .l('last_page'). " >></a> ";
				} else { $next = l('next_page'). " > "; $last = l('last_page'). " >>"; }
					if ($maxPage > 1) { echo "<div class='date'>" .$first ." ". $prev . " <strong>  [$pageNum</strong> / <strong>$maxPage]  </strong> " . $next ." ". $last ."</div>"; }
						
					if ($freeze_status <> "freezed") { ?>
					<div class="commentsbox">
						<h2><? echo l('addcomment') ?></h2>	
						<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
  							<p><? echo l('comment'); ?></p>
  							<p><textarea name="text" class="text" rows="5" cols="5"></textarea></p>
							<p><? echo l('name'); ?></p>
							<p><input name="name" type="text" class="field" id="name" /></p>
							<!-- Mod commets validation by bramsyuur -->
							<p><? echo l('code_validation'); ?></p>
							<p><img width="120" height="30" src="core/button.php" alt="" /></p>
							<p><? echo l('enter_validation_code'); ?></p>
							<p><input maxlength="5" size="5" name="userdigit" type="text" class="field" value="" /></p>
							<!-- End comments validation by bramsyuur-->							
							<p><input name="category" id="category" type="hidden" value="<? echo get_id('category'); ?>" />
							<input name="id" id="id" type="hidden" value="<? echo $articleid; ?>" />
							<input name="article" id="article" type="hidden" value="<? echo get_id('article'); ?>" />
  							<input name="commentspage" id="commentspage" type="hidden" value="<? echo $back_to_page; ?>" /></p>
  							<p><input name="comment" type="submit" class="<? echo s('button'); ?>" value="<? echo l('sendcomment'); ?>" /></p>
  	    				</form>
						</div><?
}}}

// RIGHT
function right() {	
  	$query = "SELECT * FROM " .s('prefix'). "articles WHERE position = 2 AND published = 1 ORDER BY id DESC";
	$result = mysql_query($query);
  	while ($r = mysql_fetch_array($result)) {
	  	if ($r['textlimit'] == 0) { $textlimit = 999000; } else { $textlimit = $r['textlimit']; }
    	if (isset($_SESSION['Logged_In'])) { echo l('edit'). " [ <a href='" .s('website'). "index.php?action=simpleedit&id=$r[id]'>". l('simple') ."</a> <img src='" .s('website'). "images/arrow.gif' alt='|' /> <a href='" .s('website'). "index.php?action=advancededit&id=$r[id]'>". l('advanced') ."</a> ] <img src='" .s('website'). "images/arrow.gif' alt='|' /> <a href='index.php?action=process&task=delete&amp;id=". $r['id'] ."'".if_javascript_on(" onclick='return confirm(\"".l('warning_delete')."\");'",'return').">". l('delete_article') ."</a>"; }
    	if ($r['displaytitle'] == "YES") { echo "<h2>". $r['title'] ."</h2>"; }
    	// PHP files inclusion routine
			$fulltext = $r['text'];
			$findme  = "[include]";
			$pos = strpos($fulltext, $findme); 
			$findme  = "[/include]";
			$pos2 = strpos($fulltext, $findme); 
			$file = substr($fulltext, $pos + 9, $pos2 - 9);
			if ($pos2 > 0) {
				$text = str_replace("[include]", "|&|", $fulltext);
				$text = str_replace("[/include]", "|&|", $text);
				$text = explode("|&|", $text); 
				$num = count($text);
				for ($i = 0; ; $i++) {
					if ($i == $num) {
						break;
					}
					if (strpos($text[$i], '.php') === false AND strpos($text[$i], '.txt') === false AND strpos($text[$i], '.inc') === false) {
						echo substr(stripslashes($text[$i]), 0, $textlimit);
					} else {
						include $text[$i];
					}}} else {
						echo substr(stripslashes($fulltext), 0, $textlimit);
}}} 


// ARCHIVES
function archives() {
	echo "<h2>". l('archives') ."</h2>";
	echo "<br /><p><b>". l('home') ."</b></p>";
	$query = "SELECT * FROM " .s('prefix'). "articles WHERE position = 1 AND published = 1 AND category = 0 ORDER BY date DESC"; 
	$result = mysql_query($query);
	while ($r = mysql_fetch_array($result)) {
		$date = date(s('date_format'), strtotime($r['date']));
		
		echo "<p><img src='" .s('website'). "images/arrow.gif' alt='' /> <a href='" .s('website'). find_cat_sef($r['category']). "/" .$r['seftitle']. "/'>". $r['title'] ."</a> <img src='" .s('website') ."images/arrow.gif' alt='' /> ". $date ."</p>";
	}	
	$cat_query = "SELECT * FROM " .s('prefix'). "categories"; 
	$cat_result = mysql_query($cat_query);
	while ($c = mysql_fetch_array($cat_result)) {
		echo "<br /><p><b>". $c['name'] ."</b> <img src='" .s('website'). "images/arrow.gif' alt='' /> ". $c['description'] ."</p>";
		
		echo "<p><a href='" .$title. "'>" .$r['title']. "</a></p> ";
		$catid = $c['id'];
		$query = "SELECT * FROM " .s('prefix'). "articles WHERE position = 1 AND published = 1 AND category = $catid ORDER BY id DESC";
		$result = mysql_query($query);
			while ($r = mysql_fetch_array($result)) {
				$date = date(s('date_format'), strtotime($r['date']));
				echo "<p><img src='" .s('website'). "images/arrow.gif' alt='' /> <a href='" .s('website') .$c['seftitle']. "/" .$r['seftitle']. "/'>". $r['title'] ."</a> <img src='" .s('website'). "arrow.gif' alt='' /> ". $date ."</p>";
}}} 



// CONTACT
function contact() { 
if ($_POST['contactform'] == "") {?>
	<h2><? echo l('contact'); ?></h2>
	<form method="post" action="">
		<p><? echo l('name'); ?>:<br /></p>
		<p><input name="name" type="text" id="name" class="field" /></p>
		<p><br /><? echo l('email'); ?>:</p>
		<p><input name="email" type="text" id="email" class="field" /></p>
		<p><br /><? echo l('url'); ?>:</p>
		<p><input name="weblink" type="text" id="weblink" class="field" /></p>
		<p><br /><? echo l('message'); ?>:</p>
  		<p><textarea name="message" rows="4" cols="5" class="text"></textarea></p>
		<!-- Mod commets validation by bramsyuur -->
		<p><? echo l('code_validation'); ?></p>
		<p><img width="120" height="30" src="core/button.php" alt="" /></p>
		<p><? echo l('enter_validation_code'); ?></p>
		<p><input maxlength="5" size="5" name="userdigit" type="text" class="field" value="" /></p>
		<!-- End comments validation by bramsyuur-->  		
     	<p><br /><input name="contactform" type="submit" class="<? echo s('button'); ?>" value="<? echo l('send_message'); ?>" /></p>
	</form>
 	<?php }
if (isset($_POST['contactform'])) {
	$to = s('website_email');
	$subject = s('contact_subject');
	$body = l('name') .": ". $_POST['name'] ."\n";
	$body .= l('email') .": ". $_POST['email'] ."\n";
	$body .= l('url') .": ". $_POST['weblink'] ."\n\n";
	$body .= l('message') .": ". $_POST['message'] ."\n";
if (strlen(clean($_POST['name'])) > 1 AND strlen(clean($_POST['message'])) > 1 AND audit()) {
	mail($to, $subject, $body);
	echo "<h2>". l('contact_sent') ."</h2>";
  	echo "<p><a href='" .s('website'). "'>". l('backhome') ."</a></p>";
} else { 
	echo "<h2>". l('contact_not_sent') ."</h2>";
	echo "<p>". l('message_error') ."</p>";
	echo "<p><a href='" .s('website'). "'>". l('backhome') ."</a></p>";
}}}
	


// NEW ARTICLES
function new_articles($number) {
	$category = get_id('category');
	$query = "SELECT * FROM " .s('prefix'). "articles WHERE position = 1 AND published = 1 ORDER BY id DESC LIMIT 0, $number";
	$result = mysql_query($query);
	while ($r = mysql_fetch_array($result)) {
		echo "<p><a href='" .s('website'). find_cat_sef($r['category']). "/" .$r['seftitle']. "/'>" .$r['title']. "</a></p>";	
}}

// PAST ARTICLES
function past_articles($ts, $tl) {
	if (isset($_GET['category']) <> 0) { $category = $_GET['category']; } else { $category = 0; };
	$query = "SELECT * FROM " .s('prefix'). "articles WHERE position = 1 AND published = 1 AND category = 0 ORDER BY id DESC LIMIT $ts, $tl";
	$result = mysql_query($query);
	while ($r = mysql_fetch_array($result)) {
		$date = date(s('date_format'), strtotime($r['date']));
		echo "<p><a href='" .s('website'). find_cat_sef($r['category']). "/" .$r['seftitle']. "/'>$r[title]</a> <img src='" .s('website'). "images/arrow.gif' alt='' /> ";
		echo $date;
		echo "</p>"; }
}

// SEARCH FORM
function searchform() { ?>
	<form id="search_engine" method="post" action="" /> 
		<p><input name="search_query" type="text" alt="Search" class="text" id="search_query" />
  		<input type="submit" name="search" tabindex="100" class="<? echo s('search_button'); ?>" value="<? echo l('search_button'); ?>" /></p>
<? }

// SEARCH ENGINE
function search() {
	$search_query = clean($_POST['search_query']);
	$search_query_var = "%".$search_query."%";
	echo "<h2>". l(search_results) ."</h2>";
	if (strlen($search_query) < 4) {
		echo "<p>". l(charerror) ."</p>";
	} else {
		$query = "SELECT * FROM " .s('prefix'). "articles WHERE title LIKE '$search_query_var' || text LIKE '$search_query_var' AND position <> 2 AND published = 1 ORDER BY id DESC";
		$result = mysql_query($query);
		while ($r = mysql_fetch_array($result)) {
			$num++;
			$date = date(s('date_format'), strtotime($r['date']));
			echo "<p><a href='" .s('website'). find_cat_sef($r['category']). "/" .$r['seftitle']. "/'>" .$r['title']. "</a> <img src='" .s('website'). "images/arrow.gif' alt='>' /> " . $date. "</p>";
		}
	if ($num == "") { echo "<p>". l('noresults') ." <b> " . $search_query . "</b>.</p>";
		$num = "0";
	} else { echo "<br /><p><b>" . $num . "</b> ". l('resultsfound') ."<b> " . $search_query . "</b>.</p>"; }}
	echo "<p><br /><a href='" .s('website'). "'>". l('backhome') ."</a></p>";
}

// RSS FEED
function rss() {
	$limit = s('rss_limit');
	$query = "SELECT * FROM articles WHERE position = 1 ORDER BY date DESC LIMIT 0, $limit"; 
	$result = mysql_query($query);
	$filename = "rss.xml";
	$header = "<?xml version=\"1.0\" ?>";
	$header .= "<rss version=\"2.0\">";
	$header .= "<channel>";
	$header .= "<title>" .s('website_title'). "</title>";
	$header .= "<description>" .s('website_title'). "</description>";
	$header .= "<link>" .s('website'). "</link>";
	$header .= "<copyright>Copyright " .s('website_title'). "</copyright>";
	$footer = "</channel>";
	$footer .= "</rss>";
	$fh = fopen($filename, "w+");
	fwrite($fh, $header);
	while ($r = mysql_fetch_assoc($result)){
		$date = date(s('rss_date_format'), strtotime($r['date']));
   		$pattern="'<[\/\!]*?[^<>]*?>'si";
   		$replace="";
   		$description = preg_replace($pattern, $replace,	stripslashes($r['text']));
   		$item  ="<item>";
   		$item .= "<title>". $r['title'] ."</title>";
   		$item .= "<description>". $description ."</description>";
   		$item .= "<pubDate>". $date ."</pubDate>";
   		$item .= "<link>". s(website) . find_cat_sef($r['category']). "/" .$r['seftitle']. "/</link>";
   		$item .= "</item>";
   		fwrite($fh, $item);
  }
	fwrite($fh, $footer);
	fclose($fh);
	echo "<script>self.location='" .s('website'). "rss.xml';</script>";
}

//*******************************************************************************
//                             ADMINISTRATIVE FUNCTIONS
//*******************************************************************************

//********
// LOGIN 
//********
function login() {
if ($_SESSION['Logged_In'] != "True") {
    echo "<h2>Login</h2>";
echo "<form method='post' action='" .s('website'). "'>
    <p><br />". l(username) .":</p><p><input type='textbox' class='text' name='Username' /></p>
    <p>". l(password) .":</p><p><input type='password' class='text' name='Password' /></p>
    <p><input type='hidden' name='Submitted' value='True' /></p>
    <p><input type='Submit' name='Submit' class='" .s('button'). "' value='". l(login) ."' /></p>
    </form>";
} else {
echo "<h2>" .l('logged_in'). "</h2>";
echo "<p><a href='". s('website') ."logout/'>". l('logout') ."</a></p>";
}}



//************************
// CATEGORIES (ADD, VIEW)
//************************
function view_categories() { ?>
	<h2><? echo l(categories); ?></h2> 
	<p>Home</p> <?
	$query = "SELECT * FROM " .s('prefix'). "categories ORDER BY id"; 
	$result = mysql_query($query);
  	while ($r = mysql_fetch_array($result)) {
    	if (isset($_SESSION['Logged_In'])) { echo "<p>". $r['name'] ." <img src='" .s('website'). "images/arrow.gif' alt='|' /> <a title='". $r['description'] ."' href='" .s('website'). "index.php?action=editcategory&id=$r[id]'>". l(edit_category) ."</a> <img src='" .s('website'). "images/arrow.gif' alt='|' /> <a href='" .s('website'). "index.php?action=process&task=deletecategory&id=$r[id]'".if_javascript_on(" onclick='return confirm(\"".l('warning_delete')."\");'",'return').">". l(delete_category) ."</a> <img src='" .s('website'). "images/arrow.gif' alt='|' /> ";
    		if ($r['published'] == "YES") { echo l('published'); } else { echo l('unpublished'); }}
				echo "</p>";
    		} 
			echo "<br />"; ?>
	    	<fieldset>
			<legend><? echo l('add_category'); ?></legend>
			<form name="post-text" method="post" action=""> 
    			<p><? echo l('name'); ?>:</p>
      			<p><input type="text" value="<? echo $_POST['name']; ?>" id="article_title" class="field" name="name" /><?php if_javascript_on(' <a href="javascript:makesef();">'.l('make_sef_text').'</a>'); ?></p>
      			<p><? echo l('sef_title_cat'); ?>:</p>
    			<p><input type="text" name="seftitle" value="<? if ($_POST['name'] == '') { echo cleanSEF($_POST['name']); } else { echo cleanSEF($_POST['seftitle']); }; ?>" id="article_sef" class="field" /></p>
      			
      			<p><? echo l('description'); ?>:</p>
      			<p><input type="text" class="field" name="description" /></p>
				<p><input type="checkbox" value="YES" name="publish" checked> <? echo l('publish_category'); ?></p>
      			<p><input type="hidden" name="task" value="add_category" /></p>
    			<p><input type="submit" name="submit_text" value="<? echo l(add_category); ?>" /></p>
    		</form>
    		</fieldset><?
}

//***************			
// EDIT CATEGORY 
//***************
function edit_category() { ?>
	<h2><? echo l('edit_category') ?></h2><?
	$categoryid = $_GET['id'];
	$query = "SELECT * FROM " .s('prefix'). "categories WHERE id = $categoryid"; 
	$result = mysql_query($query);
  	while ($r = mysql_fetch_array($result)) {
    	if (isset($_SESSION['Logged_In'])) {
	    	echo "<p>". $r['name'] ." <img src='" .s('website'). "images/arrow.gif' alt='|' /> <a href='index.php?action=process&task=deletecategory&id=$r[id]'".if_javascript_on(" onclick='return confirm(\"".l('warning_delete')."\");'",'return').">". l(delete_category) ."</a> <img src='" .s('website'). "images/arrow.gif' alt='|' /> ";
    		if ($r['published'] == "YES") { echo l('published'); } else { echo l('unpublished'); }
				echo "</p>"; }
    		echo "<br />"; ?>
    		<fieldset>
			<legend><? echo l('edit_category'); ?></legend>
	    	<form name="post-text" method="post" action="index.php?action=process&task=edit_category"> 
    			<p><? echo l('name'); ?>:</p>
    			<p><input type="text" class="field" value="<?php echo $r['name']; ?>" id="article_title" name="name" /><?php if_javascript_on(' <a href="javascript:makesef();">'.l('make_sef_text').'</a>'); ?></p>
    			<p><? echo l('sef_title_cat'); ?>:</p>
    			<p><input type="text" class="field" value="<?php echo $r['seftitle']; ?>" id="article_sef" name="seftitle" /></p>
    			<p><? echo l('description'); ?>:</p>
    			<p><input type="text" class="field" value="<?php echo $r['description']; ?>" name="description" /><br /></p><?
      			if ($r['published'] == "YES") { ?>
	      			<p><input type="checkbox" value="YES" name="publish" checked /> <? echo l('publish_category'); ?></p><?
      			} else {	?>
	      			<p><input type="checkbox" value="YES" name="publish" /> <? echo l('publish_category'); ?></p>
	      			<? } ?>
	      		<p><input type="hidden" name="id" value="<?php echo $categoryid; ?>" /></p>
	      		<p><input type="hidden" name="task" value="edit_category" /></p>
	      		<p><input type="submit" name="submit_text" value="<? echo l('edit_category'); ?>" /></p>
    		</form>
      		</fieldset>
      		<? }
}


//*************
// NEW ARTICLE 
//*************
function new_article() { ?>
	<h2><? echo l('new_article'); ?></h2>
  	<form name="post-text" method="post" action="<? echo s('website') ?>index.php?action=process&task=new"> 
    	<fieldset>
			<legend><? echo l('article'); ?></legend>
  			<p><? echo l('title'); ?>:</p>
   		<p><input name="title" type="text" id="article_title" class="field" value="<? echo $_SESSION['temp']['title']; ?>" /><?php if_javascript_on(' <a href="javascript:makesef();">'.l('make_sef_text').'</a>'); ?>
   		</p>
    		<p><? echo l('sef_title'); ?>:</p>
    		<p><input name="seftitle" id="article_sef" type="text" class="field" value="<? echo cleanSEF($_SESSION['temp']['seftitle']); ?>" />
    		</p>
    	   	<p><? echo l('text'); ?>:</p>
      		<p><textarea name="text" class="mceEditor"><? echo $_SESSION['temp']['text']; ?></textarea></p>
      		<p><? echo l('limit_article'); ?>:</p>
      		<p><input type="text" name="text_limit" value="500" class="field" /></p>
      	  	<p><br /><input type="checkbox" value="ON" name="auto_html" checked> <? echo l('auto_html'); ?></p>
      		<p><br /><? echo l('category'); ?>:
      		<select name="category" class="text">
	    		<option value="0"><? echo l('home'); ?></option> <?
				$query = "SELECT * FROM " .s('prefix'). "categories ORDER BY id"; 
				$result = mysql_query($query);
				while ($r = mysql_fetch_array($result)) { echo "<option value='". $r['id'] ."'>". $r['name'] ."</option>"; } ?>
			</select></p>
    	</fieldset><br />
		<fieldset>
			<legend><? echo l('position'); ?></legend>
			<p><input type="radio" value="3" name="position"> <? echo l('display_menu_item'); ?></p>
      		<p><input type="radio" value="5" name="position"> <? echo l('left'); ?></p>
      		<p><input type="radio" value="1" checked name="position"> <? echo l('center'); ?></p>
      		<p><input type="radio" value="2" name="position"> <? echo l('right'); ?></p>
      	</fieldset><br />
   		<fieldset>
	   		<legend><? echo l('customize'); ?></legend>
          	<p><input type="checkbox" value="YES" name="display_title" checked> <? echo l('display_title'); ?></p>
    	 	<p><input type="checkbox" value="YES" name="display_info" checked> <? echo l('display_info'); ?></p>
      		<p><input type="checkbox" value="YES" name="commentable"> <? echo l('enable_commenting'); ?></p>
      		<p><input type="checkbox" value="ON" name="publish" checked> <? echo l('publish_article'); ?></p>
      	</fieldset><br />
      	<fieldset>
      		<legend><? echo l('future_posting') ?></legend>
      		<p><input type="checkbox" value="YES" name="fposting"> <? echo l('enable'); ?></p>   
      		<p><? echo l('server_time'). ": ". date(s('date_format')). " - ". date('h:i:s'); ?></p>
        	      		
      		<p><? echo l('day'). ":"; ?>
        	<select name="fposting_day"><? 
        		$thisDay = intval(date('d'));
        		for($i = 1; $i < 32; $i++) {
					echo '<option value="'. $i .'"';
            		if($i == $thisDay)
            			echo 'selected';
              			echo '>'. $i .'</option>';
        			} ?>
        	</select>
        	<? echo l('month'). ":"; ?>
        	<select name="fposting_month"><?
        		$thisMonth = intval(date('m'));
        		for($i = 1; $i < 13; $i++) {
              		echo '<option value="'. $i .'"';
              		if($i == $thisMonth)
                		echo 'selected';
              			echo '>'. $i .'</option>';
        			} ?>
        	</select>
        	<? echo l('year'). ":"; ?>
        	<select name="fposting_year"><? 
        	$thisYear = intval(date('Y'));
        		for($i = $thisYear; $i < $thisYear + 5; $i++) {
              		echo '<option value="'. $i .'"';
              		if($i == $thisYear)
                		echo 'selected';
              			echo '>'. $i .'</option>';
        			} ?>
        	</select>
           	<? echo l('hour'). ":"; ?>
        	<select name="fposting_hour"><?
        		$thisHour = intval(date('H'));
        		for($i = 0; $i < 24; $i++) {
              		echo '<option value="'. $i .'"';
              		if($i == $thisHour)
                		echo 'selected';
              			echo '>'. $i .'</option>';
        			} ?>
        	</select>
        	<? echo l('minute'). ":"; ?>
        	<select name="fposting_minute"><?
        		$thisMinute = intval(date('i'));
        		for($i = 0; $i < 60; $i++) {
              		echo '<option value="'. $i .'"';
              		if($i == $thisMinute)
                		echo 'selected';
              			echo '>'. $i .'</option>';
        			} ?>
        	</select></p>
        </fieldset><br />
        <fieldset>
      		<legend><? echo l('attach_image'); ?></legend>
      	   	<p><select name="image" class="text">
	    	<option value=""><? echo l('no_image'); ?></option> <?
				$upload_dir = s('image_folder') ."/";
				$rep=opendir($upload_dir);
				while ($file = readdir($rep)) {
					if($file != '..' && $file !='.' && $file !=''){
						if (!is_dir($file)){
    		    			$folder=substr($file, 0, -4);
							echo "<option value='$file'>$folder</option>";
        				}
					}
				}
				closedir($rep);
				clearstatcache(); ?>
			</select>				    				
    		<input type="hidden" name="task" value="new"></p>
    	</fieldset>
    	<p><br /><input type="submit" name="submit_text" value="<? echo l('submit_new_article'); ?>"></p>
    </form> <?
}
    
    
//**********************			
// UNPUBLISHED ARTICLES
//**********************
function unpublished_articles() {	
	echo "<h2>". l('unpublished_articles') ."</h2>";
  	$query = "SELECT * FROM " .s('prefix'). "articles WHERE position = 4 OR published = 0 ORDER BY id DESC"; 
	$result = mysql_query($query);
  	while ($r = mysql_fetch_array($result)) {
	  	$date = date(s('date_format'), strtotime($r['date']));
    	if (isset($_SESSION['Logged_In'])) { echo "<p><a href='" .s('website') . find_cat_sef($r['category']). "/". $r['seftitle']. "/'>" .$r['title']. "</a> <img src='" .s('website'). "images/arrow.gif' alt='|' /> ";
    		if ($r['published'] == 0) {
	    		echo l('future_posting'). " <img src='" .s('website'). "images/arrow.gif' alt='|' /> ";
    		}
    	echo l('edit'). " [ <a href='" .s('website'). "index.php?action=simpleedit&id=$r[id]'>". l('simple') ."</a> <img src='" .s('website'). "images/arrow.gif' alt='|' /> <a href='" .s('website'). "index.php?action=advancededit&id=$r[id]'>". l('advanced') ."</a> ] <img src='" .s('website'). "images/arrow.gif' alt='|' /> <a href='" .s('website'). "index.php?action=process&task=delete&amp;id=". $r['id'] ."'".if_javascript_on(" onclick='return confirm(\"".l('warning_delete')."\");'",'return').">". l('delete_article') ."</a></p>"; }
	} 
} 	
    
//**************			
// EDIT ARTICLE 
//**************
function edit_article($mode) { ?>
          	<h2><? echo l('edit_article'). " [" .$mode. "]"; ?></h2><?
			$id = $_REQUEST['id'];
  			$query = mysql_query("SELECT * FROM " .s('prefix'). "articles WHERE id='$id'");
  			$r = mysql_fetch_array($query);
  			$article_category = $r['category']; ?>
  			<fieldset>
			<legend><? echo l('article'); ?></legend>
  			<form name="post-text" method="post" action="index.php?action=process&task=edit&amp;id=<?php echo $id; ?>"> 
    		<p><? echo l('title'); ?>:</p>
    		<p><input type="text" name="title" id="article_title" value="<?php echo $_SESSION['temp']['title'] ? $_SESSION['temp']['title'] : $r['title']; ?>" class="field" /><? if ($mode == "advanced") { if_javascript_on(' <a href="javascript:makesef();">'.l('make_sef_text').'</a>'); }; ?></p>
    		<? if ($mode == "advanced") { ?>
    			<p><? echo l('sef_title'); ?>:</p>
    			<p><input type="text" name="seftitle" id="article_sef" class="field" value="<?php echo $_SESSION['temp']['seftitle'] ? cleanSEF($_SESSION['temp']['seftitle']) : $r['seftitle']; ?>" /></p>
  			<? } ?>
    	
      		<p><? echo l('text'); ?>:</p>
      		<? if ($mode == "advanced") {	$text = str_replace('&', '&amp;', $_SESSION['temp']['text'] ? $_SESSION['temp']['text'] : $r['text']); } 
      		   if ($mode == "simple") { $text = str_replace(array("<br />", "<p>", "</p>"), "" , $r[text]); } ?>
      		<p><textarea name="text" class="mceEditor"><? echo ($text); ?></textarea></p>
      		<p><? echo l('limit_article'); ?>:</p>
      		<p><input type="text" name="text_limit" value="<? echo $r['textlimit']; ?>" class="field" /></p>
      		<p><br /><? echo l('category'); ?>:
      		<select name="category" class="text">
      	   		<option value="category" <? if ($article_category == 0) { echo "selected"; } ?>><? echo l('home'); ?></option> <?
					$category_query = "SELECT * FROM " .s('prefix'). "categories ORDER BY id"; 
					$category_result = mysql_query($category_query);
					while ($cat = mysql_fetch_array($category_result)) { 
						echo "<option value='". $cat['id'] ."'";
						if ($article_category == $cat['id']) { echo "selected"; }						
						echo ">". $cat['name'] ."</option>"; } ?>
					</select></p>
			</fieldset><br />
			<fieldset>
			<legend><? echo l('position'); ?></legend>
      		<? if ($r['position'] == 3) { ?>
	      		<p><input type="radio" value="3" name="position" checked> <? echo l('display_menu_item'); ?></p>
    		<? } else { ?>
    			<p><input type="radio" value="3" name="position"> <? echo l('display_menu_item'); ?></p>
    		<? }
    		if ($r['position'] == 5) { ?>
	      		<p><input type="radio" value="5" name="position" checked> <? echo l('left'); ?></p>
    		<? } else { ?>
    			<p><input type="radio" value="5" name="position"> <? echo l('left'); ?></p>
    		<? }
    		if ($r['position'] == 1) { ?>
	      		<p><input type="radio" value="1" name="position" checked> <? echo l('center'); ?></p>
    		<? } else { ?>
    			<p><input type="radio" value="1" name="position"> <? echo l('center'); ?></p>
    		<? }
    		if ($r['position'] == 2) { ?>
	      		<p><input type="radio" value="2" name="position" checked> <? echo l('right'); ?></p>
    		<? } else { ?>
    			<p><input type="radio" value="2" name="position"> <? echo l('right'); ?></p>
    		<? } ?>
    		</fieldset><br />
    		<fieldset>
			<legend><? echo l('customize'); ?></legend>
    		<? if ($r['displaytitle'] == "YES") { ?>
	      		<p><input type="checkbox" value="YES" name="display_title" checked> <? echo l('display_title'); ?></p>
    		<? } else { ?>
    			<p><input type="checkbox" value="YES" name="display_title"> <? echo l('display_title'); ?></p>
    		<? }
    		if ($r['displayinfo'] == "YES") { ?>
	      		<p><input type="checkbox" value="YES" name="display_info" checked> <? echo l('display_info'); ?></p>
    		<? } else { ?>
    			<p><input type="checkbox" value="YES" name="display_info"> <? echo l('display_info'); ?></p>
    		<? }    		
    		if ($r['commentable'] == "YES") { ?>
	      		<p><input type="checkbox" value="YES" name="commentable" checked> <? echo l('enable_commenting'); ?></p>
    		<? } else { ?>
    			<p><input type="checkbox" value="YES" name="commentable"> <? echo l('enable_commenting'); ?></p>
    		<? }
    		if ($r['position'] == 4) { ?>
	      		<p><input type="checkbox" value="ON" name="publish"> <? echo l('publish_article'); ?></p>
    		<? } else { ?>
    			<p><input type="checkbox" value="ON" name="publish" checked> <? echo l('publish_article'); ?></p>
    		<? } ?>
    		</fieldset><br />
    		   		
    		<? if ($mode == "advanced") { ?>
    		<fieldset>
      		<legend><? echo l('publish_date') ?></legend>
      		<p><? echo l('day'). ":"; ?>
        	<select name="fposting_day"><? 
        		$thisDay = intval(date('d'));
        		for($i = 1; $i < 32; $i++) {
					echo '<option value="'. $i .'"';
            		if($i == substr($r['date'], 8, 2))
            			echo 'selected';
              			echo '>'. $i .'</option>';
        			} ?>
        	</select>
        	<? echo l('month'). ":"; ?>
        	<select name="fposting_month"><?
        		$thisMonth = intval(date('m'));
        		for($i = 1; $i < 13; $i++) {
              		echo '<option value="'. $i .'"';
              		if($i == substr($r['date'], 5, 2))
                		echo 'selected';
              			echo '>'. $i .'</option>';
        			} ?>
        	</select>
        	<? echo l('year'). ":"; ?>
        	<select name="fposting_year"><? 
        	$thisYear = intval(date('Y'));
        		for($i = $thisYear; $i < $thisYear + 5; $i++) {
              		echo '<option value="'. $i .'"';
              		if($i == substr($r['date'], 0, 4))
                		echo 'selected';
              			echo '>'. $i .'</option>';
        			} ?>
        	</select>
           	<? echo l('hour'). ":"; ?>
        	<select name="fposting_hour"><?
        		$thisHour = intval(date('H'));
        		for($i = 0; $i < 24; $i++) {
              		echo '<option value="'. $i .'"';
              		if($i == substr($r['date'], 11, 2))
                		echo 'selected';
              			echo '>'. $i .'</option>';
        			} ?>
        	</select>
        	<? echo l('minute'). ":"; ?>
        	<select name="fposting_minute"><?
        		$thisMinute = intval(date('i'));
        		for($i = 0; $i < 60; $i++) {
              		echo '<option value="'. $i .'"';
              		if($i == substr($r['date'], 14, 2))
                		echo 'selected';
              			echo '>'. $i .'</option>';
        			} ?>
        	</select></p>
        	</fieldset><br />
    		<? } ?>
    		<fieldset>
			<legend><? echo l('attach_image'); ?></legend>
    		<select name="image" class="text"><?
    		if (stripslashes($r['image']) == "") { echo "<option value='' selected>". l('no_image') ."</option>"; } 
    		else { echo "<option value=''>". l('no_image') ."</option>"; }
    				$upload_dir = s('image_folder') ."/";
					$rep=opendir($upload_dir);
					while ($file = readdir($rep)) {
						if($file != '..' && $file !='.' && $file !=''){
							if (!is_dir($file)){
    		    				$folder=substr($file, 0, -4);
					    	    		    			
    		    				if ($file == stripslashes($r['image'])) {		    				
	    		    		  		echo "<option value='$file' selected>$folder</option>";
    		    				} else if ($folder <> "Thumb") {
	    		       				echo "<option value='$file'>$folder</option>";
    		       			}}}} ?>
				</select></p>
			</fieldset>
    		<p><input type="hidden" name="id" value="<?php echo $id; ?>"></p> <?
    		if ($mode == "simple") { ?><p><input type="hidden" name="task" value="simpleedit"></p> <? }
    		if ($mode == "advanced") { ?><p><input type="hidden" name="task" value="advancededit"></p> <? } ?>
    		<p><input type="submit" name="submit_text" value="<? echo l('edit'); ?>"></p>
  			</form><? 
}


//**************			
// EDIT COMMENT 
//**************
function edit_comment() { ?>
          	<h2><? echo l('edit_comment') ?></h2><?
			$commentid = $_GET['commentid'];
			$query = mysql_query("SELECT * FROM " .s('prefix'). "comments WHERE id='$commentid'");
  			$r = mysql_fetch_array($query);
  			$text = $r['comment']; ?>
  			<form name="post-text" method="post" action=""> 
    			<p><br /><? echo l('comment'); ?>:</p>
      			<p><textarea name="editedcomment" class="text"><? echo stripslashes($text); ?></textarea></p>
    			<p><? echo l('name'); ?>:</p>
    			<p><input type="text" name="name" value="<?php echo $r['name']; ?>" class="field" /></p>
    			<p><input type="hidden" name="id" value=<? echo $r['articleid']; ?>">
    			<input type="hidden" name="commentid" value="<? echo $r['id']; ?>">
    			<input type="hidden" name="task" value="editcomment"></p>
				<p><input type="submit" name="submit_text" value="<? echo l('edit'); ?>"></p>
			</form>
    		
<? }

//*********************************************
// PROCESSING (CATEGORIES, ARTICLES, COMMENTS)
//*********************************************
function processing() {
	if ($_SESSION['Logged_In'] != True) {
		echo "<div class=\"".s('css_error')."\"><h2>". l('admin_error') ."</h2><p>".l('error_not_logged_in')."</p></div>";
		return 0;
	};
	
	$action = $_REQUEST['action'];
  	$id = $_REQUEST['id'];
  	$commentid = $_POST['commentid'];
  	$name = str_replace("\'", "&#39;", $_POST['name']);
  	$category = $_POST['category'];
  	$description = $_POST['description'];
  	$title = str_replace("\'", "&#39;", $_POST['title']);
  	$seftitle = $_POST['seftitle'];
   	$comment = str_replace("\'", "&#39;", $_POST['editedcomment']);
  	$text = str_replace("\'", "&#39;", $_POST['text']);
  	$text_limit = $_POST['text_limit'];
  	$auto_html = $_POST['auto_html'];
  	$date = date('Y-m-d H:i:s');
  	$display_title = $_POST['display_title'];
  	$display_info = $_POST['display_info'];
  	$commentable = $_POST['commentable'];
  	$publish = $_POST['publish'];
  	$publish_category = $_POST['publish'];
  	$position = $_POST['position'];
  	$display = $_POST['display'];
  	$image = $_POST['image'];
	$fpost_enabled = false;
    $fpublished = 1;
    if($_POST['fposting'] == "YES" OR $_POST['task'] == "advancededit") {
		$fpublished = 0;
        $fpost_enabled = true;
        $fpost_day = $_POST['fposting_day'];
        $fpost_month = $_POST['fposting_month'];
        $fpost_year = $_POST['fposting_year'];
        $fpost_hour = $_POST['fposting_hour'];
        $fpost_minute = $_POST['fposting_minute'];
    }
	if ($text_limit == "") { $text_limit = 0; }
  	if ($position == "") { $position = 1; }
  	if ($commentable == "") { $commentable = "NO"; }
  	if ($publish <> "ON") { $position = 4; }
  	if ($display_title == "") { $display_title = "NO"; }
  	if ($display_info == "") { $display_info = "NO"; }
	if ($fpost_enabled OR $_POST['task'] == "advancededit") { $date = $fpost_year .'-'. $fpost_month .'-'. $fpost_day .' '. $fpost_hour .':'. $fpost_minute .':00'; }
  	
   	if ($_POST['task'] == "add_category") {
    	if ($_POST['submit_text']) {
      		if ($name == "")  {
				echo "<div class=\"".s('css_error')."\"><h2>". l('admin_error') ."</h2><p>". l('error_category_name_empty') ."</p></div>";
				view_categories();
			} else if ($seftitle == "")  {
				echo "<div class=\"".s('css_error')."\"><h2>". l('admin_error') ."</h2><p>". l('error_category_seftitle_empty') ."</p></div>";
				view_categories();
			} else if ( check_if_unique('category_name', $name) ) {
				echo "<div class=\"".s('css_error')."\"><h2>". l('admin_error') ."</h2><p>" .l('error_category_name_exists'). "</p></div>";
				view_categories();
			} else if ( check_if_unique('category_seftitle', $seftitle) ) {
				echo "<div class=\"".s('css_error')."\"><h2>". l('admin_error') ."</h2><p>" .l('error_category_seftitle_exists'). "</p></div>";
				view_categories();
			} else if (cleancheckSEF($seftitle) == "notok")  {
				echo "<div class=\"".s('css_error')."\"><h2>". l('admin_error') ."</h2><p>". l('error_category_seftitle_illegal') ."</p></div>";
				view_categories();
			} else {
        		mysql_query("INSERT INTO ". s('prefix'). "categories(name,seftitle,description,published) VALUES('$name', '$seftitle', '$description', '$publish_category')"); 
        		echo "<div class=\"".s('css_success')."\"><h2>". l('operation_completed') ."</h2><p><a href='" .s('website'). "categories/'>". l('back') ."</a></p></div>";
        	}
		}
	} 
      			
    if ($_POST['task'] == "edit_category") {
    	if ($_POST['submit_text']) {
		if ($name == "")  {
				echo "<div class=\"".s('css_error')."\"><h2>". l('admin_error') ."</h2><p>". l('error_category_name_empty') ."</p></div>";
				edit_category();
			} else if ($seftitle == "")  {
				echo "<div class=\"".s('css_error')."\"><h2>". l('admin_error') ."</h2><p>". l('error_category_seftitle_empty') ."</p></div>";
				edit_category();
			} else if ( check_if_unique('category_name', $name, $id) ) {
				echo "<div class=\"".s('css_error')."\"><h2>". l('admin_error') ."</h2><p>" .l('error_category_name_exists'). "</p></div>";
				edit_category();
			} else if ( check_if_unique('category_seftitle', $seftitle, $id) ) {
				echo "<div class=\"".s('css_error')."\"><h2>". l('admin_error') ."</h2><p>" .l('error_category_seftitle_exists'). "</p></div>";
				edit_category();
			} else if (cleancheckSEF($seftitle) == "notok")  {
				echo "<div class=\"".s('css_error')."\"><h2>". l('admin_error') ."</h2><p>". l('error_category_seftitle_illegal') ."</p></div>";
				edit_category();
			} else {
	    		mysql_query("UPDATE ". s('prefix'). "categories SET name='$name' WHERE id='$id'");
	      		mysql_query("UPDATE ". s('prefix'). "categories SET seftitle='$seftitle' WHERE id='$id'");
	      		mysql_query("UPDATE ". s('prefix'). "categories SET description='$description' WHERE id='$id'");
	      		mysql_query("UPDATE ". s('prefix'). "categories SET published='$publish_category' WHERE id='$id'");
	      		echo "<div class=\"".s('css_success')."\"><h2>". l('operation_completed') ."</h2><p><a href='" .s('website'). "categories/'>". l('back') ."</a></p></div>";
    }}}
    			 					
  	else if ($_POST['task'] == "new") {
    	if ($_POST['submit_text']) {
			$_SESSION['temp']['title'] = $title;
			$_SESSION['temp']['seftitle'] = $seftitle;
			$_SESSION['temp']['text'] = $text;
      		if ($title == "") {
				echo "<div class=\"".s('css_error')."\"><h2>". l('admin_error') ."</h2><p>". l('error_article_title_empty') ."</p></div>";
				new_article();
				unset($_SESSION['temp']);
			} else if ($seftitle == "")  {
				echo "<div class=\"".s('css_error')."\"><h2>". l('admin_error') ."</h2><p>". l('error_article_seftitle_empty') ."</p></div>";
				$_SESSION['temp']['seftitle'] = $_SESSION['temp']['title'];
				new_article();
				unset($_SESSION['temp']);
			} else if (cleancheckSEF($seftitle) == "notok")  {
				echo "<div class=\"".s('css_error')."\"><h2>". l('admin_error') ."</h2><p>". l('error_article_seftitle_illegal') ."</p></div>";
				new_article();
				unset($_SESSION['temp']);
			} else if ( check_if_unique('article_title', $title) ) {
				echo "<div class=\"".s('css_error')."\"><h2>". l('admin_error') ."</h2><p>" .l('error_article_title_exists'). "</p></div>";
				new_article();
				unset($_SESSION['temp']);
			} else if ( check_if_unique('article_seftitle', $seftitle) ) {
				echo "<div class=\"".s('css_error')."\"><h2>". l('admin_error') ."</h2><p>" .l('error_article_seftitle_exists'). "</p></div>";
      			new_article();
				unset($_SESSION['temp']);
			} else {
        		if ($auto_html == "ON") {
	        	$text = str_replace('<p></p>', '', '<p>' . preg_replace('#\n|\r#', '</p>$0<p>', $text) . '</p>'); }
	        	$query = "INSERT INTO ". s('prefix'). "articles(title,seftitle,text,textlimit,date,category,position,displaytitle,displayinfo,commentable,image,published) VALUES('". $title ."', '". $seftitle ."', '". $text ."', '". $text_limit ."', '". $date ."', '". $category ."', '". $position ."', '". $display_title ."', '". $display_info ."', '". $commentable ."', '". $image ."', '". $fpublished ."')";
        		mysql_query($query);
        		echo "<div class=\"".s('css_success')."\"><h2>". l('operation_completed') ."</h2><p><a href='" .s('website'). "'>". l('back') ."</a></p></div>";
				unset($_SESSION['temp']);
	}}}

	else if ($_POST['task'] == "simpleedit") {
    	if ($_POST['submit_text']) {
			$_SESSION['temp']['title'] = $title;
			$_SESSION['temp']['text'] = $text;
      		if ($title == "") {
				echo "<div class=\"".s('css_error')."\"><h2>". l('admin_error') ."</h2><p>". l('error_article_title_empty') ."</p></div>";
				edit_article(simple);
				unset($_SESSION['temp']);
			} else if ( check_if_unique('article_title', $title, $id) ) {
				echo "<div class=\"".s('css_error')."\"><h2>". l('admin_error') ."</h2><p>" .l('error_article_title_exists'). "</p></div>";
				edit_article(simple);
				unset($_SESSION['temp']);
			} else {
		    	$text = str_replace('<p></p>', '', '<p>' . preg_replace('#\n|\r#', '</p>$0<p>', $text) . '</p>');
				mysql_query("UPDATE ". s('prefix'). "articles SET title='$title' WHERE id='$id'");
		      	mysql_query("UPDATE ". s('prefix'). "articles SET text='$text' WHERE id='$id'");
		      	mysql_query("UPDATE ". s('prefix'). "articles SET textlimit='$text_limit' WHERE id='$id'");
		      	mysql_query("UPDATE ". s('prefix'). "articles SET category='$category' WHERE id='$id'");
		      	mysql_query("UPDATE ". s('prefix'). "articles SET position='$position' WHERE id='$id'");
		      	mysql_query("UPDATE ". s('prefix'). "articles SET displaytitle='$display_title' WHERE id='$id'");
		      	mysql_query("UPDATE ". s('prefix'). "articles SET displayinfo='$display_info' WHERE id='$id'");
		      	mysql_query("UPDATE ". s('prefix'). "articles SET commentable='$commentable' WHERE id='$id'");
		      	mysql_query("UPDATE ". s('prefix'). "articles SET image='$image' WHERE id='$id'");
		      	echo "<div class=\"".s('css_success')."\"><h2>". l('operation_completed') ."</h2><p><a href='" .s('website'). find_cat_sef($category). "/" .find_article_sef($id). "/'>". l('back') ."</a></p></div>";
				unset($_SESSION['temp']);
			}
		}
    }
    else if ($_POST['task'] == "advancededit") {
    	if ($_POST['submit_text']) {
			$_SESSION['temp']['title'] = $title;
			$_SESSION['temp']['seftitle'] = $seftitle;
			$_SESSION['temp']['text'] = $text;
	   		if ($title == "") {
				echo "<div class=\"".s('css_error')."\"><h2>". l('admin_error') ."</h2><p>". l('error_article_title_empty') ."</p></div>";
				edit_article(advanced);
				unset($_SESSION['temp']);
			} else if ($seftitle == "")  {
				echo "<div class=\"".s('css_error')."\"><h2>". l('admin_error') ."</h2><p>". l('error_article_seftitle_empty') ."</p></div>";
				edit_article(advanced);
				unset($_SESSION['temp']);
			} else if (cleancheckSEF($seftitle) == "notok")  {
				echo "<div class=\"".s('css_error')."\"><h2>". l('admin_error') ."</h2><p>". l('error_article_seftitle_illegal') ."</p></div>";
				edit_article(advanced);
				unset($_SESSION['temp']);
			} else if ( check_if_unique('article_title', $title, $id) ) {
				echo "<div class=\"".s('css_error')."\"><h2>". l('admin_error') ."</h2><p>" .l('error_article_title_exists'). "</p></div>";
				edit_article(advanced);
				unset($_SESSION['temp']);
			} else if ( check_if_unique('article_seftitle', $seftitle, $id) ) {
				echo "<div class=\"".s('css_error')."\"><h2>". l('admin_error') ."</h2><p>" .l('error_article_seftitle_exists'). "</p></div>";
	      		edit_article(advanced);
				unset($_SESSION['temp']);
			} else {
		      	mysql_query("UPDATE ". s('prefix'). "articles SET title='$title' WHERE id='$id'");
		      	mysql_query("UPDATE ". s('prefix'). "articles SET seftitle='$seftitle' WHERE id='$id'");
		      	mysql_query("UPDATE ". s('prefix'). "articles SET text='$text' WHERE id='$id'");
		      	mysql_query("UPDATE ". s('prefix'). "articles SET textlimit='$text_limit' WHERE id='$id'");
		      	mysql_query("UPDATE ". s('prefix'). "articles SET date='$date' WHERE id='$id'");
		      	mysql_query("UPDATE ". s('prefix'). "articles SET category='$category' WHERE id='$id'");
		      	mysql_query("UPDATE ". s('prefix'). "articles SET position='$position' WHERE id='$id'");
		      	mysql_query("UPDATE ". s('prefix'). "articles SET displaytitle='$display_title' WHERE id='$id'");
		      	mysql_query("UPDATE ". s('prefix'). "articles SET displayinfo='$display_info' WHERE id='$id'");
		      	mysql_query("UPDATE ". s('prefix'). "articles SET commentable='$commentable' WHERE id='$id'");
		      	mysql_query("UPDATE ". s('prefix'). "articles SET image='$image' WHERE id='$id'");
		      	mysql_query("UPDATE ". s('prefix'). "articles SET published='$fpublished' WHERE id='$id'");
		      	echo "<div class=\"".s('css_success')."\"><h2>". l('operation_completed') ."</h2><p><a href='" .s('website'). find_cat_sef($category). "/" .find_article_sef($id). "/'>". l('back') ."</a></p></div>";
				unset($_SESSION['temp']);
			}
		}
	}
  	
    else if ($_GET['task'] == "delete") { 
    	mysql_query("DELETE FROM " .s('prefix'). "articles WHERE id='$id'");
    	echo "<h2>". l('deleted_success') ."</h2><p><a href='" .s('website'). "'>". l('backhome') ."</a></p>";
    }
  	
  	else if ($_POST['task'] == "editcomment") {
	  	mysql_query("UPDATE ". s('prefix'). "comments SET name='$name' WHERE id='$commentid'");
    	mysql_query("UPDATE ". s('prefix'). "comments SET comment='$comment' WHERE id='$commentid'");
    	echo "<div class=\"".s('css_success')."\"><h2>". l('operation_completed') ."</h2><p><a href='" .s('website'). find_cat_sef($categoryid). "/" .find_article_sef($id). "/'>". l('back') ."</a></p></div>";
   	}
  	
	else if ($_GET['task'] == "freezecomments") {
		$categoryid = find_article_cat($id);
      	mysql_query("UPDATE ". s('prefix'). "articles SET commentable='FREEZ' WHERE id='$id'");
      	echo "<div class=\"".s('css_success')."\"><h2>". l('operation_completed') ."</h2><p><a href='" .s('website'). find_cat_sef($categoryid). "/" .find_article_sef($id). "/'>". l('back') ."</a></p></div>";
    }

    else if ($_GET['task'] == "unfreezecomments") {
		$categoryid = find_article_cat($id);
    	mysql_query("UPDATE ". s('prefix'). "articles SET commentable='YES' WHERE id='$id'");
    	echo "<div class=\"".s('css_success')."\"><h2>". l('operation_completed') ."</h2><p><a href='" .s('website'). find_cat_sef($categoryid). "/" .find_article_sef($id). "/'>". l('back') ."</a></p></div>";
    }
  	
    else if ($_GET['task'] == "deletecomment") { 
      	$commentid = $_GET['commentid'];
    	$articleid = $_GET['articleid'];
       	mysql_query("DELETE FROM " .s('prefix'). "comments WHERE id='$commentid'");
    	echo "<h2>". l('deleted_success') ."</h2><p><a href='" .s('website'). find_cat_sef($categoryid). "/" .find_article_sef($articleid). "/'>". l('back') ."</a></p>";
    }
  	
  	else if ($_GET['task'] == "deletecategory") { 
	   	$categoryid = $_GET['categoryid'];
    	mysql_query("DELETE FROM " .s('prefix'). "categories WHERE id='$id'");
    	echo "<h2>". l('deleted_success') ."</h2><p><a href='" .s('website'). "categories/'>". l('back') ."</a></p>";
    }
}


//********
// IMAGES 
//********
function images() { 
	if (isset($_GET['image'])) {
		$file_to_delete = s('image_folder') ."/". $_GET['image'];
		unlink($file_to_delete); 
		echo "<h2>". l('deleted_success') ."</h2><p><a href='" .s('website'). "images/'>". l('back') ."</a></p>";
	} else { ?>
	<h2><? echo l('images'); ?></h2>
	<form name="imageformauthenticate" method="post" action="" enctype="multipart/form-data"></form>
	<form name="imageform" method="post" action="" enctype="multipart/form-data">
		<p><br /><? echo l('upload_image'); ?>:</p>
		<p><input type="file" name="imagefile" />
		<input type="submit" name="upload" value="<? echo l('upload'); ?>" /><br /></p>
	</form> <?
	if(isset( $_POST['upload'] )) {
		if ($_FILES['imagefile']['type']){ 
			$upload_dir = s('image_folder') ."/";	 
			copy ($_FILES['imagefile']['tmp_name'], $upload_dir .$_FILES['imagefile']['name']) or die ("Could not copy"); 
        	echo "<div class=\"".s('css_success')."\"><h2>" .l('operation_completed'). "</h2></div>";
			$kb_size = round(($_FILES['imagefile']['size'] / 1024), 1);
        	echo "<p><b>".$_FILES['imagefile']['name']. "</b>  [ " .$kb_size. " KB ] [ " .$_FILES['imagefile']['type']." ]";
    	} else {
            echo "<h2>" .l('admin_error'). "</h2>";
            echo "<p>" .l('image_error'). "</p>";
    	}
	} else {
		$upload_dir = s('image_folder') ."/";
    	$handle= opendir($upload_dir);
		$filelist = "";
		while ($file = readdir($handle)) {
   		if(!is_dir($file) && !is_link($file) && $file <> "Thumbs.db") {
	    	$filelist .= "<a href='$upload_dir$file'>".$file."</a> [ <a href='" .s('website'). "index.php?action=images&task=delete&image=" .$file. "'".if_javascript_on(" onclick='return confirm(\"".l('warning_delete')."\");'",'return').">Delete</a> ]<br />";
    }}
	echo "<h2>". l('saved_images') .":</h2>";
	echo "<p>" .$filelist. "</p>";
}}}
?>