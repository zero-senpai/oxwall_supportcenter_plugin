<?php

if(!OW::getConfig()->configExists("supportcenter", "k_order")){
	OW::getConfig()->addConfig("supportcenter", "k_order", "ALPHA", "Default order of Knowledgebase");
}

Updater::getLanguageService()->importPrefixFromZip(dirname(__FILE__).DS.'langs.zip', 'supportcenter');