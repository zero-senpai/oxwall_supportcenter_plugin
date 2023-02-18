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
	array("faq", "support/faq", "faq"),
	
);
$admin_routes=array(
	array("general",			"general","general"),
	array("tags",			"tags","tags"),
	array("tags-delete",			"tags/delete/:id","tagsDelete"),
	array("tags-add",			"tags/add","tags_add"),

	array("articles",			"articles","articles"),
	array("articles-new",		"articles/new","articlesNew"),
	array("articles-delete",		"articles/delete/:id","articlesDelete"),
	array("articles-edit",		"articles/edit/:id","articlesEdit"),

	array("ticket-categories",		"ticket_categories","ticketCategories"),
	array("ticket-categories-delete",	"ticket_categories/delete/:id","ticketCategoriesDelete"),
	array("tickets",			"tickets","tickets"),
	array("tickets-view",		"tickets/view/:id","ticketsView"),
	array("tickets-delete",		"tickets/delete/:id","ticketsDelete"),
	array("departments",			"departments","departments"),
	array("departments-view",		"departments/view/:id","departmentsView"),
	array("departments-delete",		"departments/delete/:id","departmentsDelete"),
	array("departments-user-delete",	"departments/user/delete/:dep_id/:user_id","departmentsUserDelete"),

	array("faqs", "faq", "faq"),
	array("faqs-delete", "faq/delete/:id", "faqDelete"),
	array("faqs-edit", "faq/edit/:id", "faqEdit"),
	array("faqs-moveu", "faq/moveu/:id", "faqMoveUp"),
	array("faqs-moved", "faq/moved/:id", "faqMoveDown"),
);

foreach($routes as $val) {
	OW::getRouter()->addRoute(new OW_Route("supportcenter-".$val[0], $val[1], "SUPPORTCENTER_CTRL_Support", $val[2]));
}
foreach($admin_routes as $val) {
	OW::getRouter()->addRoute(new OW_Route("supportcenter-admin-".$val[0], "admin/supportcenter/".$val[1], "SUPPORTCENTER_CTRL_Admin", $val[2]));
}

OW::getRouter()->addRoute(new OW_Route('supportcenter-manage-tickets', 'support/manage', 'SUPPORTCENTER_CTRL_Manage', 'tickets'));
OW::getRouter()->addRoute(new OW_Route('supportcenter-manage-tickets-delete', 'support/manage/tickets/delete/:id', 'SUPPORTCENTER_CTRL_Manage', 'ticketsDelete'));
OW::getRouter()->addRoute(new OW_Route('supportcenter-manage-tickets-view', 'support/manage/tickets/view/:id', 'SUPPORTCENTER_CTRL_Manage', 'ticketsView'));
OW::getRouter()->addRoute(new OW_Route('supportcenter-manage-articles', 'support/manage/articles', 'SUPPORTCENTER_CTRL_Manage', 'articles'));
OW::getRouter()->addRoute(new OW_Route('supportcenter-manage-articles-new', 'support/manage/articles/new', 'SUPPORTCENTER_CTRL_Manage', 'articlesNew'));
OW::getRouter()->addRoute(new OW_Route('supportcenter-manage-articles-delete', 'support/manage/articles/delete/:id', 'SUPPORTCENTER_CTRL_Manage', 'articlesDelete'));
OW::getRouter()->addRoute(new OW_Route('supportcenter-manage-articles-edit', 'support/manage/articles/edit/:id', 'SUPPORTCENTER_CTRL_Manage', 'articlesEdit'));


OW::getRouter()->addRoute(new OW_Route('supportcenter-admin-jbtech', 'admin/supportcenter/jbtech', 'SUPPORTCENTER_CTRL_Tech', 'index'));

function supportcenter_notify(BASE_CLASS_EventCollector $e) {
    $e->add(array(
        'section' => 'supportcenter',
        'action' => 'ticketUpdated',
        'description' => OW::getLanguage()->text('supportcenter', 'notification_ticket_updated'),
        'selected' => true,
        'sectionLabel' => OW::getLanguage()->text('supportcenter', 'notification_section_label'),
        'sectionIcon' => 'ow_ic_reply'
	));
	$e->add(array(
        'section' => 'supportcenter',
        'action' => 'addedToDep',
        'description' => OW::getLanguage()->text('supportcenter', 'added_to_department'),
        'selected' => true,
        'sectionLabel' => OW::getLanguage()->text('supportcenter', 'added_to_department_label'),
        'sectionIcon' => 'ow_ic_reply'
	));

}
OW::getEventManager()->bind('notifications.collect_actions', 'supportcenter_notify');

if(OW::getUser()->isAuthorized("supportcenter", "manage")){
function supportcenter_content_itm( BASE_CLASS_EventCollector $event )
{


    $resultArray = array(
        BASE_CMP_AddNewContent::DATA_KEY_ICON_CLASS => 'ow_ic_help',
        BASE_CMP_AddNewContent::DATA_KEY_URL => OW::getRouter()->urlForRoute('supportcenter-manage-tickets'),
        BASE_CMP_AddNewContent::DATA_KEY_LABEL => OW::getLanguage()->text('supportcenter', 'support_manage_heading')
    );

    $event->add($resultArray);
}
OW::getEventManager()->bind(BASE_CMP_AddNewContent::EVENT_NAME, 'supportcenter_content_itm');

function supportcenter_console_itm( BASE_CLASS_EventCollector $event )
{
    $event->add(array('label' => OW::getLanguage()->text('supportcenter', 'support_manage_heading'), 'url' => OW_Router::getInstance()->urlForRoute('supportcenter-manage-tickets')));
}
OW::getEventManager()->bind('base.add_main_console_item', 'supportcenter_console_itm');
}




