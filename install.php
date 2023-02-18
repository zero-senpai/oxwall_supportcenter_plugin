<?php
/**
 * This software is intended for use with Oxwall Free Community Software http://www.oxwall.org/ and is a proprietary licensed product. 
 * For more information see License.txt in the plugin folder.

 * ---
 * Copyright (c) 2018-2019, Jake Brunton
 * All rights reserved.
 * jbtech.business@gmail.com

 * Redistribution and use in source and binary forms, with or without modification, are not permitted provided.

 * This plugin should be bought from the developer. For details contact jbtech.business@gmail.com.

 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
require_once("controllers/supportcenter.php");

$path = OW::getPluginManager()->getPlugin('supportcenter')->getRootDir() . 'langs.zip';
BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'supportcenter');

//settings
OW::getConfig()->addConfig('supportcenter', 'settings', 'Support Center Settings');
OW::getPluginManager()->addPluginSettingsRouteName( 'supportcenter', 'supportcenter-admin-general' );

if(!OW::getConfig()->configExists("supportcenter", "menu_pos")) {
    OW::getConfig()->addConfig("supportcenter", "menu_pos", OW_Navigation::BOTTOM, "Position of menu entry");
}

if(!OW::getConfig()->configExists("supportcenter", "email_notif")){
	OW::getConfig()->addConfig("supportcenter", "email_notif", "FALSE", "Email Notifications");
}

if(!OW::getConfig()->configExists("supportcenter", "help_forum")){
	OW::getConfig()->addConfig("supportcenter", "help_forum", "FALSE", "Help Forum");
}

if(!OW::getConfig()->configExists("supportcenter", "help_forum_url")){
	OW::getConfig()->addConfig("supportcenter", "help_forum_url", "", "Help Forum URL");
}

if(!OW::getConfig()->configExists("supportcenter", "article_share")){
	OW::getConfig()->addConfig("supportcenter", "article_share", "TRUE", "Allow Article Sharing");
}

if(!OW::getConfig()->configExists("supportcenter", "recommend_art")){
	OW::getConfig()->addConfig("supportcenter", "recommend_art", "TRUE", "Recommend Articles");
}

if(!OW::getConfig()->configExists("supportcenter", "k_order")){
	OW::getConfig()->addConfig("supportcenter", "k_order", "ALPHA", "Default order of Knowledgebase");
}
if(!OW::getConfig()->configExists("supportcenter", "k_members")){
	OW::getConfig()->addConfig("supportcenter", "k_members", "FALSE", "Set knowledgebase to members only");
}
//added in build 50:
if(!OW::getConfig()->configExists("supportcenter", "faq_toggle")){
	OW::getConfig()->addConfig("supportcenter", "faq_toggle", "FALSE", "Turn the FAQ option on or off");
}
if(!OW::getConfig()->configExists("supportcenter", "auto_purge")){
	OW::getConfig()->addConfig("supportcenter", "auto_purge", "TRUE", "Auto-delete tickets after some time");
}
if(!OW::getConfig()->configExists("supportcenter", "purge_limit")){
	OW::getConfig()->addConfig("supportcenter", "purge_limit", "3Months", "Set the time limit to delete tickets");
}
if(!OW::getConfig()->configExists("supportcenter", "ticket_info_p")){
	OW::getConfig()->addConfig("supportcenter", "ticket_info_p", "Side", "Set position for Ticket Info bar");
}
//faq
OW::getDbo()->query("
	DROP TABLE IF EXISTS `".OW_DB_PREFIX."supportcenter_faq`;
	CREATE TABLE IF NOT EXISTS `".OW_DB_PREFIX."supportcenter_faq` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`question` text NOT NULL,
		`answer` text NOT NULL,
		`position` int(11) NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
//articles
OW::getDbo()->query("
	DROP TABLE IF EXISTS `".OW_DB_PREFIX."supportcenter_article`;
	CREATE TABLE IF NOT EXISTS `".OW_DB_PREFIX."supportcenter_article` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`title` text NOT NULL,
		`body` text NOT NULL,
		`cat1` int(11),
		`cat2` int(11),
		PRIMARY KEY (`id`),
		FULLTEXT (title,body)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");
OW::getDbo()->query("
	DROP TABLE IF EXISTS `".OW_DB_PREFIX."supportcenter_rating`;
	CREATE TABLE IF NOT EXISTS `".OW_DB_PREFIX."supportcenter_rating` (
		`article_id` int(11) NOT NULL,
		`user_id` int(11) NOT NULL,
		`rating` int NOT NULL,
		PRIMARY KEY (`article_id`,`user_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
OW::getDbo()->query("
	DROP TABLE IF EXISTS `".OW_DB_PREFIX."supportcenter_category`;
	CREATE TABLE IF NOT EXISTS `".OW_DB_PREFIX."supportcenter_category` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`text` text NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

//tickets
OW::getDbo()->query("
	DROP TABLE IF EXISTS `".OW_DB_PREFIX."supportcenter_ticket_category`;
	CREATE TABLE IF NOT EXISTS `".OW_DB_PREFIX."supportcenter_ticket_category` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`text` text NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

OW::getDbo()->query("
	DROP TABLE IF EXISTS `".OW_DB_PREFIX."supportcenter_ticket`;
	CREATE TABLE IF NOT EXISTS `".OW_DB_PREFIX."supportcenter_ticket` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`author_id` int(11) NOT NULL,
		`category_id` int(11) NOT NULL,
		`department_id` int(11) NOT NULL DEFAULT -1,
		`subject` text NOT NULL,
		`text` text,
		`status` int(11) NOT NULL,
		`created` int(11) NOT NULL,
		`updated` int(11) NOT NULL,
		`requested_deletion` tinyint(1),
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
OW::getDbo()->query("
	DROP TABLE IF EXISTS `".OW_DB_PREFIX."supportcenter_ticket_message`;
	CREATE TABLE IF NOT EXISTS `".OW_DB_PREFIX."supportcenter_ticket_message` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`ticket_id` int(11) NOT NULL,
		`author_id` int(11) NOT NULL,
		`text` text NOT NULL,
		`created` int(11),
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
OW::getDbo()->query("
	DROP TABLE IF EXISTS `".OW_DB_PREFIX."supportcenter_department`;
	CREATE TABLE IF NOT EXISTS `".OW_DB_PREFIX."supportcenter_department` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`text` text NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
OW::getDbo()->query("
	DROP TABLE IF EXISTS `".OW_DB_PREFIX."supportcenter_department_user`;
	CREATE TABLE IF NOT EXISTS `".OW_DB_PREFIX."supportcenter_department_user` (
		`department_id` int(11) NOT NULL,
		`user_id` int(11) NOT NULL,
		PRIMARY KEY (`department_id`,`user_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

/*
//XXX test data
$tags=["Technical","Fun","Art","Science"];
$ticket_categories=["Bug","Request","Question","Report"];
$departments=["Support","Development","Marketing","Design"];
$department_users=[3];
$articles=[
	[
		"title"=>"Coming to a screen near you",
		"body"=>"This morning we welcomed 6,000 developers to our 7th annual Google I/O developer conference. The crowd in San Francisco was joined by millions more watching on the livestream and 597 I/O Extended events, in 90+ countries on six continents.

We’re meeting at an exciting time for Google, and for our developer community. There are now one billion of you around the world who use an Android device. One billion. We estimate that’s more than 20 billion text messages sent every day. 1.5 trillion steps taken with an Android. And more importantly, a roughly estimated 93M selfies.

Today, developers got a preview of our most ambitious Android release yet. With more than 5,000 new APIs (for non-techies, that stands for application programming interfaces) and a new, consistent design approach called material design, we’re continuing to evolve the Android platform so developers can bring to life even more beautiful, engaging mobile experiences.

But, beyond the mobile phone, many of us are increasingly surrounded by a range of screens throughout the day--at home, at work, in the car, or even on our wrist. So, we got to thinking: how do we invest more in our two popular, open platforms—Android and Chrome—to make it easier for you to easily and intuitively move from your phone, tablet, laptop to your TV, car or even your watch?",
		"cat1"=>0,
		"cat2"=>1],
	[
		"title"=>"Celebrating Pride, Google Style",
		"body"=>"This June and throughout 2014, Google is thrilled to be celebrating Pride with the world in 35+ offices globally. With the ever increasing international focus on the LGBT community (searches for LGBT-related terms on Google have increased 41% since 2004 and started really picking up steam in March 2010) it has become even more evident that despite the marriage equality gains made in the United States, much more work needs to be done to ensure the safety and rights of the LGBT community everywhere. The challenges will continue, but so will the celebrations -- here are the top 5 ways we’re celebrating Pride Google-style.",
		"cat1"=>1,
		"cat2"=>2],
	[
		"title"=>"Through the Google lens: search trends Jun 13-19",
		"body"=>"The World Cup is well underway and people are searching for every match highlight and replay. Read on to learn what was trending on Google this past week.

There’s football … and then there’s everything else
The Internet is still gobbling up every last bit of the World Cup as searches for the sport reached near ravenous levels (who knew we were so starved of the beautiful game?) John Brooks, a previously obscure member of the USMNT, was on the top of the Internet’s head after using his own to score the game-winning goal against Ghana for the United States. From [england vs italy] to [brazil vs mexico] no match was left untouched, or unsearched.

But the [world cup] wasn’t the only sport that mattered this week (even though it might have seemed like it). The Stanley Cup winner LA Kings and recently crowned NBA champions San Antonio Spurs topped the charts just for one day. In more serious news, people checked in on Michael Schumacher, the Formula 1 driver who was put into a coma after a skiing accident, and mourned the loss of baseball player Tony Gwynn to cancer.",
		"cat1"=>2,
		"cat2"=>3],
];

foreach($tags as $tag) {
	SupportCenterDB::tag_add($tag);
}
foreach($ticket_categories as $cat) {
	SupportCenterDB::ticket_category_new($cat);
}
foreach($departments as $dep) {
	SupportCenterDB::department_new($dep);
}
foreach($articles as $article) {
	SupportCenterDB::article_add($article);
}
*/



// @b40 v 3.5.2 new role features

$auth = OW::getAuthorization();
$group = 'supportcenter';
$auth->addGroup($group);
$auth->addAction($group, 'manage');
$auth->addAction($group, 'use_tickets', true);


OW::getLanguage()->importPluginLangs(OW::getPluginManager()->getPlugin('supportcenter')->getRootDir() . 'langs.zip', 'supportcenter');





