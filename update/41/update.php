<?php

/* Copyright 2019-2020 JB Tech
* jbtech.online
* Update file for build 41 version 3.5.2
*/

Updater::getLanguageService()->importPrefixFromZip(dirname(__FILE__).DS.'langs.zip', 'supportcenter');

if(!OW::getConfig()->configExists("supportcenter", "k_members")){
	OW::getConfig()->addConfig("supportcenter", "k_members", "FALSE", "Set knowledgebase to members only");
}

$auth = OW::getAuthorization();
$group = 'supportcenter';
$auth->addAction($group, 'use_tickets', true);