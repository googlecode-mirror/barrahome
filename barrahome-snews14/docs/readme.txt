sNews v1.4
Copyright(c) 2005, Solucija - All rights reserved
http://www.solucija.com/
---------------------------------------------------------------------------
Welcome to sNews - a single file, template independent, PHP and MySQL 
powered, standards valid content management system.
---------------------------------------------------------------------------

Install your sNews v1.4 through these 3 easy steps:


1) Edit 'snews.php' and enter your settings at the top of the file,
   the default username and password are "test".

2) Copy files to your server and CHMOD 777 your folder where you'll upload
   your images. (default: 'img'), and rss.xml.

3) Create the MySQL database with this code:

CREATE TABLE articles (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(100) DEFAULT NULL,
  seftitle VARCHAR(100) DEFAULT NULL,
  text LONGTEXT,
  textlimit INT(5) NOT NULL DEFAULT '0',
  date DATETIME DEFAULT NULL,
  category INT(8) NOT NULL DEFAULT '0',
  position CHAR(3),
  displaytitle CHAR(3) NOT NULL DEFAULT 'YES',
  displayinfo CHAR(3) NOT NULL DEFAULT 'YES',
  commentable VARCHAR(5) NOT NULL,
  image varchar(30) DEFAULT NULL,
  published INT(3) NOT NULL DEFAULT '1'

);


CREATE TABLE categories (
  id int(8) PRIMARY KEY AUTO_INCREMENT,
  name varchar(40) NOT NULL,
  seftitle VARCHAR(100) DEFAULT NULL,
  description varchar(100) NOT NULL,
  published varchar(4) NOT NULL DEFAULT 'YES'
);


CREATE TABLE comments (
  id INT(11) PRIMARY KEY AUTO_INCREMENT,
  articleid INT(11) DEFAULT '0',
  name varchar(50) DEFAULT '',
  comment TEXT,
  time DATETIME DEFAULT NULL
);   



4) You are ready to go!
Log in and start writing your articles.

Please post bug reports, suggestions, comments, questions:
forum.solucija.com


LICENCE
sNews is licenced under a Creative Commons Licence,
see http://creativecommons.org/licenses/by/2.5/ for more info.