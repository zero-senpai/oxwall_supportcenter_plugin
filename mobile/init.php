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
$routes=array(
	//id	url	func
	array("support",			"support","index"),
	array("knowledgebase",		"support/knowledgebase","knowledgebase"),
	array("knowledgebase-article",	"support/knowledgebase/article/:id","article"),
	array("knowledgebase-article-rate",	"support/knowledgebase/article/rate/:id","articleRate"),
	array("knowledgebase-article-tag",	"support/knowledgebase/article/tag/:id","articleTag"),
	array("knowledgebase-search",	"support/knowledgebase/search/:text","articleSearch"),
	array("tickets-my",			"support/tickets/my","ticketsMy"),
	array("tickets-view",		"support/tickets/view/:id","ticketsView"),
	array("tickets-new",			"support/tickets/new","ticketsNew"),
	array("tickets-delete",		"support/tickets/delete/:id","ticketsDelete"),
	array("tickets-delete-cancel",	"support/tickets/delete_cancel/:id","ticketsDeleteCancel"),
	array("faq", "support/faq", "faq")
);


foreach($routes as $val) {
	OW::getRouter()->addRoute(new OW_Route("supportcenter-".$val[0], $val[1], "SUPPORTCENTER_MCTRL_Support", $val[2]));
}


function supportcenter_notify(BASE_MCLASS_EventCollector $e) { //change MCLASS to CLASS
    $e->add(array(
        'section' => 'supportcenter',
        'action' => 'ticketUpdated',
        'description' => OW::getLanguage()->text('supporcenter', 'notification_ticket_updated'),
        'selected' => true,
        'sectionLabel' => OW::getLanguage()->text('supporcenter', 'notification_section_label'),
        'sectionIcon' => 'ow_ic_reply'
    ));

}
OW::getEventManager()->bind('notifications.collect_actions', 'supportcenter_notify');


