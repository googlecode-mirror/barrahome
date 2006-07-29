<?php
// ----------------------------------------------------------------------
// sNews v1.4
// Copyright(c) 2006, Solucija - All rights reserved
// http://www.solucija.com/
//
// sNews is licenced under a Creative Commons Licence,
// see http://creativecommons.org/licenses/by/2.5/ for more info.
// ----------------------------------------------------------------------

function s($variable) {
$s = Array();

//******************
// GLOBAL SETTINGS
//******************

$s['username'] = "bet0x"; // Enter your administration username
$s['password'] = "mypass"; // Enter your administration password
$s['theme'] = "default"; // Theme

$s['dbhost'] = "localhost"; // MySQL host
$s['dbname'] = "barracom_snews"; // Database name
$s['dbuname'] = "barracom"; // Database Username
$s['dbpass'] = "mydbpass"; // Database password
$s['dberror'] = "<b>There was an error while connecting to the database.</b> <br /> Check your database settings."; // Database error message
$s['prefix'] = ""; // Table prefix for multiple sNews systems on one database (if you don't need it just leave it blank)

$s['home'] = "servlet"; // Enter blog title like home or blog
$s['display_new_on_home'] = true; // Display new articles from all categories on home (True or False)
$s['website'] = "http://www.barrahome.com.ar/"; // Website url with the trailing slash
$s['website_title'] = "BarraHome"; // Website title
$s['website_email'] = "albertof@barrahome.com.ar"; // Contact email (info@yoursite.com)
$s['contact_subject'] = "Formulario de contacto"; // Subject of the contact form message
$s['image_folder'] = "img"; // Folder to save images
$s['charset'] = "iso-8859-2"; // Default charset
$s['display_num_categories'] = false; // Display number of articles next to a category name (True or False)
$s['new_timezone'] = "America/Rosario"; // Enter you timezome in GMT and please include the + or - sign
$s['date_format'] = "d.m.Y."; // Date format
$s['fp_date_format'] = "d.m."; // Date format for front page articles
$s['comments_order'] = "ASC"; // Order of displaying comments ASC or DESC (DESC - newer ones on top)
$s['results_per_page'] = "100"; // Number of comments to display per page
$s['display_comment_time'] = "YES"; // display date and time on comments (YES or NO)
$s['comment_dt_format'] = "d.m."; // Date and time format for comments
$s['rss_limit'] = "5"; // Limit RSS feed to a number of articles
$s['rss_date_format'] = "l dS \of F Y h:i:s A"; // RSS date format (DATE_RFC822)

$s['language'] = "spanish"; // Download the translation file from http://translations.solucija.com/ put it in the same directory with snews.php and write the language name.

$s['use_javascript'] = true; // Use Javascript functions in snews (YER or NO)

// If you want to enable word filtering you must provide a file with the words you want to remove. One word per line
$s['word_filter_enable'] = 'NO'; // Do you want to filter out bad words from your comments? YES / NO
$s['word_filter_file'] = 'bad_words.txt'; // This is a file with words you want to exclude from your comments.
$s['word_filter_change'] = 'XXXX'; // What you want to change words that have been filtered to?


//**************
// CSS SETTINGS
//**************

$s['css_error'] = "error";
$s['css_success'] = "success";
$s['date_class'] = "date"; // class used for info line (read more, comments, date, etc.)
$s['button'] = "button"; // button on submit comment, contact, etc.
$s['search_button'] = "searchbutton"; // search button


//***************
$s['username'] = md5($s['username']);
$s['password'] = md5($s['password']);
$s['home'] = strtolower($s['home']);

return $s[$variable];
}


//********************
// LANGUAGE VARIABLES
//********************
function l($variable) {
$l = Array();

if (strtolower((s('language'))!='default') AND (strtolower(s('language'))!='english') AND file_exists('lang/snews_'.s('language').'.php')) {
	include('lang/snews_'.s('language').'.php');
}
return $l[$variable];
}
?>