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
require_once("supportcenter.php");

class SUPPORTCENTER_CTRL_Admin extends ADMIN_CTRL_Abstract {

	function myinit($menu) {	
		$this->setPageHeading( OW::getLanguage()->text('supportcenter', 'admin_settings_heading'));
		$this->setPageHeadingIconClass('ow_ic_gear_wheel');

		$this->addComponent('menu',$this->getMenu());
		$this->getComponent('menu')->getElement($menu)->setActive(true);

		$content = "			
		<div class=\"clearfix\"><div class=\"modal-content lefty\">
		<span class=\"close-button\">&star;</span>
		<h1>OXWALL/SKADATE PLANS</h1>
		<span style=\"font-size:12.3px;\">Get professional management, moderation, support, and modifications all at one cheap price @ <a href=\"https://jbtech.online/pricing\" target=\"_blank\" style=\"font-weight:bold;color:blue;\">JB Tech</a></span>
		</div>
		
		<div class=\"modal-content\">
		<span class=\"close-button\">&star;</span>
		<h1>LIKE THIS PLUGIN?</h1>
		<span style=\"font-size:12.3px;\">Please consider <a href=\"https://developers.oxwall.com/store/item/1518\" target=\"_blank\" style=\"font-weight:bold;color:blue;\">leaving a review!</a></span>
		</div></div></div>";

		$this->assign("content", $content);
	}
	private function getMenu() {

		$menu=array(
			//key (also lang string), route, icon
			array("general",		"general",		"ow_ic_gear_wheel"),
			array("tags",			"tags",			"ow_ic_calendar"),
			array("articles",		"articles",		"ow_ic_files"),
			array("ticket_categories",	"ticket-categories",	"ow_ic_tag"),
			array("departments",		"departments",		"ow_ic_house"),
			array("tickets",		"tickets",		"ow_ic_reply"),
			array("faq", "faqs", "ow_ic_info"),
			array("jbtech", "jbtech", "ow_ic_help"),
		);

		$menuItems = array();

		foreach($menu as $i=>$m) {
			$item=new BASE_MenuItem();
			$item->setLabel(OW::getLanguage()->text("supportcenter","admin_${m[0]}"));
			$item->setUrl(OW::getRouter()->urlForRoute("supportcenter-admin-${m[1]}"));
			$item->setKey($m[0]);
			$item->setIconClass($m[2]);
			$item->setOrder($i);
			array_push($menuItems,$item);
		}

		return new BASE_CMP_ContentMenu( $menuItems );
	}
	//forms
	function build_tag_form() {
		$language = OW::getLanguage();

		$form=new Form("supportcenter_form_add_tag");
		$form->setMethod(Form::METHOD_POST);
		$form->setEnctype(Form::ENCTYPE_MULTYPART_FORMDATA);

		$textField = new TextField("text");
		$textField->setLabel($language->text("supportcenter", "form_add_tag"));
		$textField->setHasInvitation(true);
		$textField->setRequired();

		$submit = new Submit("submit");
		$submit->setLabel($language->text("supportcenter", "form_add_tag_submit"));

		$form->addElement($textField);
		$form->addElement($submit);

		return $form;
	}
	function build_category_form() {
		$form=$this->build_tag_form();
		$form->getElement("text")->setLabel(OW::getLanguage()->text("supportcenter", "form_add_category"));
		return $form;
	}
	function build_department_form() {
		$form=$this->build_tag_form();
		$form->getElement("text")->setLabel(OW::getLanguage()->text("supportcenter", "form_add_department"));
		return $form;
	}
	function build_department_user_form() {
		$form=$this->build_tag_form();
		$form->getElement("text")->setLabel(OW::getLanguage()->text("supportcenter", "form_add_department_user"));
		return $form;
	}
	function build_article_form() {
		$language = OW::getLanguage();

		$form=new Form("supportcenter_form_article");
		$form->setMethod(Form::METHOD_POST);
		$form->setEnctype(Form::ENCTYPE_MULTYPART_FORMDATA);

		$text_title = new TextField("title");
		$text_title->setLabel($language->text("supportcenter", "form_article_title"));
		$text_title->setRequired();

		$buttons = array(
		    BOL_TextFormatService::WS_BTN_BOLD,
		    BOL_TextFormatService::WS_BTN_ITALIC,
		    BOL_TextFormatService::WS_BTN_UNDERLINE,
		    BOL_TextFormatService::WS_BTN_IMAGE,
			BOL_TextFormatService::WS_BTN_VIDEO,
		    BOL_TextFormatService::WS_BTN_LINK,
		    BOL_TextFormatService::WS_BTN_ORDERED_LIST,
		    BOL_TextFormatService::WS_BTN_UNORDERED_LIST,
		    BOL_TextFormatService::WS_BTN_MORE,
		    BOL_TextFormatService::WS_BTN_SWITCH_HTML,
		    BOL_TextFormatService::WS_BTN_HTML
		);
		$text_body = new WysiwygTextarea("body", $buttons);
		$text_body->setSize(WysiwygTextarea::SIZE_L);
		$text_body->setLabel($language->text("supportcenter", "form_article_body"));


		$categories=array();
		$cat_query=SupportCenterDB::tag_get();
		foreach($cat_query as $c) {
			$categories[$c["id"]]=$c["text"];
		}

		$cat1 = new Selectbox("cat1");
		$cat1->setLabel($language->text("supportcenter", "form_article_cat1"));
		$cat1->setInvitation("Select Tag");
		$cat1->setOptions($categories);
		$cat2 = new Selectbox("cat2");
		$cat2->setLabel($language->text("supportcenter", "form_article_cat2"));
		$cat2->setInvitation("Select Tag");
		$cat2->setOptions($categories);

		$submit = new Submit("submit");
		$submit->setLabel($language->text("supportcenter", "form_article_submit"));
		
		$form->addElement($text_title);
		$form->addElement($text_body);
		$form->addElement($cat1);
		$form->addElement($cat2);
		$form->addElement($submit);

		return $form;
	}
	function build_ticket_reply_form() {
		$language=OW::getLanguage();

		$form=new Form("supportcenter_form_ticket_reply");
		$form->setMethod(Form::METHOD_POST);
		$form->setEnctype(Form::ENCTYPE_MULTYPART_FORMDATA);

		$buttons = array(
		    BOL_TextFormatService::WS_BTN_BOLD,
		    BOL_TextFormatService::WS_BTN_ITALIC,
		    BOL_TextFormatService::WS_BTN_UNDERLINE,
		    BOL_TextFormatService::WS_BTN_IMAGE,
		    BOL_TextFormatService::WS_BTN_LINK,
		    BOL_TextFormatService::WS_BTN_ORDERED_LIST,
		    BOL_TextFormatService::WS_BTN_UNORDERED_LIST,
		    BOL_TextFormatService::WS_BTN_MORE,
		    BOL_TextFormatService::WS_BTN_SWITCH_HTML,
		    BOL_TextFormatService::WS_BTN_HTML
		);
		$text_body = new WysiwygTextarea("text", $buttons);
		$text_body->setSize(WysiwygTextarea::SIZE_L);
		$text_body->setLabel($language->text("supportcenter", "form_ticket_reply_text"));
		$text_body->setRequired();


		$submit = new Submit("submit");
		$submit->setLabel($language->text("supportcenter", "form_ticket_reply_submit"));

		$form->addElement($text_body);
		$form->addElement($submit);

		return $form;		
	}
	function build_settings_form() {
		$language=OW::getLanguage();

		$form=new Form("supportcenter_form_settings");

		$menu_pos = new Selectbox("menu_pos");
		$menu_pos->setLabel($language->text("supportcenter", "form_settings_menu_pos"));
		$menu_pos->setOptions(array(
			OW_Navigation::BOTTOM=>"Bottom",
			OW_Navigation::MAIN=>"Main"));

		$menu_pos->setRequired(true);
		
		//build 7 adding Email Notifications settings
		$email_n = new Selectbox("email_notif");
		$email_n->setLabel($language->text("supportcenter", "form_settings_email_notif"));
		$email_n->setOptions(array(
			"FALSE"=>"Off",
			"TRUE"=>"On"));
		$email_n->setRequired(true);
		
		//build 15 adding new 4 configs_email
		//@Help Forum ON/OFF Switch
		$help_f = new Selectbox("help_forum");
		$help_f->setLabel($language->text("supportcenter", "form_settings_help_forum"));
		$help_f->setOptions(array(
			"FALSE"=>"Off",
			"TRUE"=>"On"));
		$help_f->setRequired(true);		
		//@Help Forum URL
		$help_url = new TextField("help_url");
		$help_url->setLabel($language->text("supportcenter", "form_settings_help_forum_url"));
		$help_url->setHasInvitation(true);
		$help_url->setRequired(false);
		//@Article Share ON/OFF Switch
		$share = new Selectbox("article_share");
		$share->setLabel($language->text("supportcenter", "form_settings_article_shares"));
		$share->setOptions(array(
			"FALSE"=>"Off",
			"TRUE"=>"On"));
		$share->setRequired(true);
		//@Article Recommendations ON/OFF Switch
		$rec_art = new Selectbox("rec_art");
		$rec_art->setLabel($language->text("supportcenter", "form_settings_recommended_art"));
		$rec_art->setOptions(array(
			"FALSE"=>"Off",
			"TRUE"=>"ON"));
		$rec_art->setRequired(true);
		//b25 Add Knowledgebase Ordering Config
		$korder = new Selectbox("k_order");
		$korder->setLabel($language->text("supportcenter", "form_settings_korder"));
		$korder->setOptions(array(
			"ALPHA"=>$language->text("supportcenter", "form_settings_korder_alpha"),
			"DATE"=>$language->text("supportcenter", "form_settings_korder_date"),
			"TOP"=>$language->text("supportcenter", "form_settings_korder_top")));
		$korder->setRequired(true);
		// @b40 v 3.5.2 Add new setting to form
		$k_members = new Selectbox("k_members");
		$k_members->setLabel($language->text("supportcenter", "form_settings_k_members"));
		$k_members->setOptions(array(
			"TRUE"=>$language->text("supportcenter", "form_settings_k_members_all"),
			"FALSE"=>$language->text("supportcenter", "form_settings_k_members_mems")));
		$k_members->setRequired(true);
		//@v4.0 Add new settings to form @FAQ @Purge @Position
		$faq_toggle = new Selectbox("faq_toggle");
		$faq_toggle->setLabel($language->text("supportcenter", "form_settings_faq_toggle"));
		$faq_toggle->setOptions(array(
				"TRUE"=>"On",
				"FALSE"=>"Off"));
		$faq_toggle->setRequired(true);

		$auto_purge = new Selectbox("auto_purge");
		$auto_purge->setLabel($language->text("supportcenter", "form_settings_auto_purge"));
		$auto_purge->setOptions(array(
			"TRUE"=>"On",
			"FALSE"=>"Off"));
		$auto_purge->setRequired(true);

		$purge_limit = new Selectbox("purge_limit");
		$purge_limit->setLabel($language->text("supportcenter", "form_settings_purge_limit"));
		$purge_limit->setOptions(array(
			"1Week"=>$language->text("supportcenter", "form_settings_purge_limit_1week"),
			"3Months"=>$language->text("supportcenter", "form_settings_purge_limit_3months"),
			"6Months"=>$language->text("supportcenter", "form_settings_purge_limit_6months")));
		$purge_limit->setRequired(true);

		$ticket_info_p = new Selectbox("ticket_info_p");
		$ticket_info_p->setLabel($language->text("supportcenter", "form_settings_ticket_info_p"));
		$ticket_info_p->setOptions(array(
			"Side"=>$language->text("supportcenter", "form_settings_ticket_info_p_side"),
			"Top"=>$language->text("supportcenter", "form_settings_ticket_info_p_top")));
		$ticket_info_p->setRequired(true);
	
		$submit = new Submit("submit");
		$submit->setLabel($language->text("supportcenter", "form_ticket_reply_submit"));
		
		$form->addElement($menu_pos);
		$form->addElement($email_n);
		$form->addElement($help_f);
		$form->addElement($help_url);
		$form->addElement($share);
		$form->addElement($rec_art);
		$form->addElement($korder);
		$form->addElement($k_members);
		$form->addElement($faq_toggle);
		$form->addElement($auto_purge);
		$form->addElement($purge_limit);
		$form->addElement($ticket_info_p);
		$form->addElement($submit);
		return $form;
		
	}

	function build_add_dep_user_form(){
		$language = OW::getLanguage();
		$form=new Form("supportcenter_form_add_dep_user");
		$form->setMethod(Form::METHOD_POST);
		$form->setEnctype(Form::ENCTYPE_MULTYPART_FORMDATA);

		$user = new TextField("user");
		$user->setLabel($language->text("supportcenter", "add_dep_user"));
		$user->setHasInvitation(true);
		$user->setInvitation($language->text("supportcenter", "add_dep_user_ph"));
		$user->setRequired(true);

		$submit = new Submit("submit");
		$submit->setLabel($language->text("supportcenter", "form_new_ticket_submit"));

		$form->addElement($user);
		$form->addElement($submit);

		return $form;
	}
	// @ v 4.0 b5x Build FAQ creation form
	function build_faq_form(){
		$language = OW::getLanguage();
		$form = new Form("supportcenter_form_faq");
		$form->setMethod(Form::METHOD_POST);
		$form->setEnctype(Form::ENCTYPE_MULTYPART_FORMDATA);

		$question = new TextField("question");
		$question->setLabel($language->text("supportcenter", "faq_question"));
		$question->setHasInvitation(true);
		$question->setRequired(true);

		$answer = new Textarea("answer");
		$answer->setLabel($language->text("supportcenter", "faq_answer"));
		$answer->setHasInvitation(true);
		$answer->setInvitation($language->text("supportcenter", "faq_answer_inv"));

		$submit = new Submit("submit");
		$submit->setLabel($language->text("supportcenter", "form_new_ticket_submit"));

		$form->addElement($question);
		$form->addElement($answer);
		$form->addElement($submit);

		return $form;
	}
	// @ b70 v 4.0 Build Manual Purge form
	function build_purge_form(){
		$language = OW::getLanguage();
		$form = new Form("supportcenter_form_purge");
		$form->setMethod(Form::METHOD_POST);
		$form->setEnctype(Form::ENCTYPE_MULTYPART_FORMDATA);

		$option = new Selectbox("manual_purge");
		$option->setLabel($language->text("supportcenter", "manual_purge"));
		$option->setOptions(array(
			"1week" => $language->text("supportcenter", "form_settings_purge_limit_1week"),
			"3months" => $language->text("supportcenter", "form_settings_purge_limit_3months"),
			"6months" => $language->text("supportcenter", "form_settings_purge_limit_6months")));
		$option->setRequired(true);
		$submit = new Submit("submit");
		$submit->setLabel($language->text("supportcenter", "form_new_ticket_submit"));

		$form->addElement($option);
		$form->addElement($submit);

		return $form;
	}



	function general() {
		$this->myinit("general");

		$form=$this->build_settings_form();
		$this->addForm($form);

		if(OW::getRequest()->isPost() && $form->isValid($_POST)) {
			$values = $form->getValues();
			$form->reset();
			OW::getConfig()->saveConfig("supportcenter", "menu_pos", $values["menu_pos"]);
			OW::getConfig()->saveConfig("supportcenter", "email_notif", $values["email_notif"]);
			OW::getConfig()->saveConfig("supportcenter", "help_forum", $values["help_forum"]);
			OW::getConfig()->saveConfig("supportcenter", "help_forum_url", $values["help_url"]);
			OW::getConfig()->saveConfig("supportcenter", "article_share", $values["article_share"]);
			OW::getConfig()->saveConfig("supportcenter", "recommend_art", $values["rec_art"]);
			OW::getConfig()->saveConfig("supportcenter", "k_order", $values["k_order"]);
			OW::getConfig()->saveConfig("supportcenter", "k_members", $values["k_members"]);
			OW::getConfig()->saveConfig("supportcenter", "faq_toggle", $values["faq_toggle"]);
			OW::getConfig()->saveConfig("supportcenter", "auto_purge", $values["auto_purge"]);
			OW::getConfig()->saveConfig("supportcenter", "purge_limit", $values["purge_limit"]);
			OW::getConfig()->saveConfig("supportcenter", "ticket_info_p", $values["ticket_info_p"]);

			OW::getFeedback()->info(OW::getLanguage()->text("supportcenter", "settings_updated"));
		}

		if(isset($_GET["testpurge3"])){
			SupportCenterDB::delete_tickets_by_3_months();
		}
		if(isset($_GET["testpurge6"])){
			SupportCenterDB::delete_tickets_by_6_months();
		}
		if(isset($_GET["testpurge1"])){
			SupportCenterDB::delete_tickets_by_1_week();
		}

		$settings=OW::getConfig()->getValues("supportcenter");
		$form->getElement("menu_pos")->setValue( $settings["menu_pos"] );
		$form->getElement("email_notif")->setValue( $settings["email_notif"] );
		$form->getElement("help_forum")->setValue( $settings["help_forum"] );
		$form->getElement("help_url")->setValue( $settings["help_forum_url"] );
		$form->getElement("article_share")->setValue( $settings["article_share"] );
		$form->getElement("rec_art")->setValue( $settings["recommend_art"] );
		$form->getElement("k_order")->setValue( $settings["k_order"] );
		$form->getElement("k_members")->setValue( $settings["k_members"] );
		$form->getElement("faq_toggle")->setValue( $settings["faq_toggle"] );
		$form->getElement("auto_purge")->setValue( $settings["auto_purge"] );
		$form->getElement("purge_limit")->setValue( $settings["purge_limit"] );
		$form->getElement("ticket_info_p")->setValue( $settings["ticket_info_p"] );
		// @b40 v 3.5.2 Added new setting k_members
		
		OW::getNavigation()->deleteMenuItem("supportcenter","support");
		OW::getNavigation()->addMenuItem($settings["menu_pos"], 'supportcenter-support', 'supportcenter', 'support', OW_Navigation::VISIBLE_FOR_MEMBER);
	}


	//tags
	function tagsDelete($params) {
		SupportCenterDB::tag_delete($params["id"]);
	        $this->redirect( OW::getRouter()->urlForRoute("supportcenter-admin-tags"));
		//OW::getFeedback()->info("Tag deleted");
		//$this->redirectToAction("tags");
	}
	function tags() {
		$this->myinit("tags");

		$form=$this->build_tag_form();
		$this->addForm($form);

		if(OW::getRequest()->isPost() && $form->isValid($_POST)) {
			$values = $form->getValues();
			$form->reset();
			SupportCenterDB::tag_add($values["text"]);
			OW::getFeedback()->info("Tag created");
		}

		$query=SupportCenterDB::tag_get();
		$tags=array();
		foreach($query as $data) {
			array_push($tags,array(
				"id" => $data["id"],
				"text" => $data["text"],
				"url_delete" => OW::getRouter()->urlForRoute("supportcenter-admin-tags-delete",array("id"=>$data["id"])),
				//"url_edit" => OW::getRouter()->urlForRoute("supportcenter-admin-tags-edit",array("id"=>$data["id"]))),
			));
		}
		$this->assign("items",$tags);
		$this->assign("text_label_list",OW::getLanguage()->text("supportcenter","tag_list"));
		$this->assign("text_label_add",OW::getLanguage()->text("supportcenter","tag_add"));
	}
	//articles
	function articles($params) {
		$this->myinit("articles");

		$a=SupportCenterDB::article_list();
		$articles=array();
		foreach($a as $article) {
			array_push($articles,array(
				"id" => $article["id"],
				"title" => $article["title"],
				"rating" => round($article["rating"],2),
				"url_edit" => OW::getRouter()->urlForRoute("supportcenter-admin-articles-edit",array("id"=>$article["id"])),
				"url_delete" => OW::getRouter()->urlForRoute("supportcenter-admin-articles-delete",array("id"=>$article["id"]))
			));
		}

		$this->assign("articles",$articles);
		$this->assign("url_new",OW::getRouter()->urlForRoute("supportcenter-admin-articles-new"));
	}
	function articlesNew() {
		$this->myinit("articles");

		$form=$this->build_article_form();
		$this->addForm($form);

		if(OW::getRequest()->isPost() && $form->isValid($_POST)) {
			$values = $form->getValues();
			$form->reset();
			// @b40 v 3.5.2 Allow articles to have 1 tag
			if(in_array("null", $values["cat1"])){
				$values["cat1"] = "NA";
			}if(in_array("null", $values["cat2"])){
				$values["cat2"] = "NA";
			}
			SupportCenterDB::article_add(array(
				"title" => $values["title"],
				"body" => $values["body"],
				"cat1" => $values["cat1"],
				"cat2" => $values["cat2"],
			));
		        $this->redirect( OW::getRouter()->urlForRoute("supportcenter-admin-articles"));
			//OW::getFeedback()->info("Article created");
			//$this->redirectToAction("articles");
			return;
		}
		$this->assign("url_cancel",OW::getRouter()->urlForRoute("supportcenter-admin-articles"));
	}
	function articlesDelete($params) {
		SupportCenterDB::article_delete($params["id"]);
	        $this->redirect( OW::getRouter()->urlForRoute("supportcenter-admin-articles"));
		//OW::getFeedback()->info("Article deleted");
		//$this->redirectToAction("articles");
	}
	function articlesEdit($params) {
		$this->myinit("articles");

		$form=$this->build_article_form();
		$this->addForm($form);

		$id=$params["id"];

		if(OW::getRequest()->isPost() && $form->isValid($_POST)) {
			$values=$form->getValues();
			$form->reset();

			SupportCenterDB::article_update(array(
				"id" => $id,
				"title" => $values["title"],
				"body" => $values["body"],
				"cat1" => $values["cat1"],
				"cat2" => $values["cat2"],
			));
		        $this->redirect( OW::getRouter()->urlForRoute("supportcenter-admin-articles"));
		}

		$article=SupportCenterDB::article_get($id);
		$form->getElement("title")->setValue($article["title"]);
		$form->getElement("body")->setValue($article["body"]);
		$form->getElement("cat1")->setValue($article["cat1"]);
		$form->getElement("cat2")->setValue($article["cat2"]);

		$this->assign("url_cancel",OW::getRouter()->urlForRoute("supportcenter-admin-articles"));
	}

	//tickets
	function ticketCategories() {
		$this->myinit("ticket_categories");

		$form=$this->build_category_form();
		$this->addForm($form);

		if(OW::getRequest()->isPost() && $form->isValid($_POST)) {
			$values = $form->getValues();
			$form->reset();
			SupportCenterDB::ticket_category_new($values["text"]);
			OW::getFeedback()->info("Category created");
		}

		$query=SupportCenterDB::ticket_category_list();
		$cats=array();
		foreach($query as $data) {
			array_push($cats,array(
				"id" => $data["id"],
				"text" => $data["text"],
				"url_delete" => OW::getRouter()->urlForRoute("supportcenter-admin-ticket-categories-delete",array("id"=>$data["id"])),
				//"url_edit" => OW::getRouter()->urlForRoute("supportcenter-admin-ticket-categories-edit",array("id"=>$data["id"]))),
			));
		}
		$this->assign("items",$cats);
		$this->assign("text_label_list",OW::getLanguage()->text("supportcenter","category_list"));
		$this->assign("text_label_add",OW::getLanguage()->text("supportcenter","category_add"));
	}
	function ticketCategoriesDelete($params) {
		SupportCenterDB::ticket_category_delete($params["id"]);
	        $this->redirect(OW::getRouter()->urlForRoute("supportcenter-admin-ticket-categories"));
	}

	function get_ticket_status($ticket) {
		$stat=array("New","Unread","Read","Answered");
		if($ticket["requested_deletion"]!=0) {
			return "To delete";
		}
		return $stat[$ticket["status"]];
	}
	function tickets() {
		$this->myinit("tickets");

		//@b70 v4.0 adding the manual purge form to the page
		$form=$this->build_purge_form();
		$this->addForm($form);
		if(OW::getRequest()->isPost() && $form->isValid($_POST)) {
			$values = $form->getValues();
			$form->reset();
			foreach ($values as $data){
			if($data["1week"]){
				SupportCenterDB::delete_tickets_by_1_week();
			}
			if(isset($_POST["3months"])){
				SupportCenterDB::delete_tickets_by_3_months();
			}
			if(isset($_POST["6months"])){
				SupportCenterDB::delete_tickets_by_6_months();
			}
			OW::getFeedback()->info(OW::getLanguage()->text("supportcenter", "tickets_deleted"));
		}
		}

		$tickets_q=SupportCenterDB::ticket_list();
		$tickets=array();
		foreach($tickets_q as $data) {
			array_push($tickets,array(
				"text"=>$data["subject"],
				"updated"=>$data["updated"],
				"department"=>$data["text"],
				"updated"=>$data["updated"],
				"status"=>$this->get_ticket_status($data),
				//"url_view"=>"/settings/tickets/view/${data["id"]}",
				"url_view"=>OW::getRouter()->urlForRoute("supportcenter-admin-tickets-view",array("id"=>$data["id"])),
				"url_delete"=>OW::getRouter()->urlForRoute("supportcenter-admin-tickets-delete",array("id"=>$data["id"])),
				"request_deletion"=>$data["requested_deletion"],
			));
		}
		$this->assign("items",$tickets);
		$this->assign("text_label_list",OW::getLanguage()->text("supportcenter","ticket_list"));
	}
	public function ticketsView($param) {
		$this->myinit("tickets");

		$ticket=SupportCenterDB::ticket_get($param["id"]);

		if($ticket["status"]!=3) {
			SupportCenterDB::ticket_set_status($param["id"],2);
		}

		$ticket["author"]=BOL_UserService::getInstance()->getDisplayName($ticket["author_id"]);
		//get original author id, than grab their email for sending
		$TID= BOL_UserService::getInstance()->findUserById($ticket["author_id"]);
		$tEmail = $TID->getEmail();
		$uName = $TID->getUsername();
		$tick_name = $ticket["subject"];
		$ticket_link = OW::getRouter()->urlForRoute("supportcenter-tickets-view",array("id"=>$ticket["id"]));

		$form=$this->build_ticket_reply_form();
		$this->addForm($form);
		if(OW::getRequest()->isPost() && $form->isValid($_POST)) {
			$values = $form->getValues();
			$form->reset();

			SupportCenterDB::ticket_add_message(array(
				"ticket_id"=>$param["id"],
				"author_id"=>OW::getUser()->getId(),
				"text"=>$values["text"]
			),3);

			OW::getFeedback()->info("Message posted");

		    $userId = OW::getUser()->getId();
			$avatars = BOL_AvatarService::getInstance()->getAvatarsUrlList(array($userId));
			$avatars['src'] = OW::getPluginManager()->getPlugin("supportcenter")->getStaticUrl()."img/ic_reply.png";
			$target_user=$ticket["author_id"];

			$notificationParams = array(
			    'pluginKey' => 'supportcenter',
			    'action' => 'ticketUpdated',
			    'entityType' => 'supportcenter-ticketUpdated',
			    'entityId' => $target_user,
			    'userId' => $target_user,
			    'time' => time()
			);
			$notificationData = array(
			    'string' => array(
				'key' => 'supportcenter+ticket_notification',
				'vars' => array(
				    'content' => $ticket["subject"]
				)
			    ),
			    'avatar' => $avatars,
				"url"=>OW::getRouter()->urlForRoute("supportcenter-tickets-view",array("id"=>$ticket["id"]))
			);

			//build 7:adding Email Notification if TRUE
			$configs_email = OW::getConfig()->getValue("supportcenter", "email_notif");
			
			$language = OW::getLanguage();
			
			
			if($configs_email == "TRUE"){
				//send mail
				$sendEmail = OW::getConfig()->getValue('base', 'site_email');
				$emailSub = $language->text("supportcenter", "email_ticket_notification");
				$emailBod = $language->text("supportcenter", "email_notif_update_body", array('username'=>$uName, 'tick_name'=>$tick_name, 'tick_link'=>$ticket_link)); // @ build 11 reutilizing text string
				$mail = OW::getMailer()->createMail();
				$mail->addRecipientEmail($tEmail);
				$mail->setReplyTo($sendEmail, 'Mail sender');
				$mail->setSender($sendEmail);
				$mail->setSubject($emailSub);
				$mail->setHtmlContent($emailBod);
				$mail->setTextContent($emailBod);
				
				OW::getMailer()->send($mail);
			}
			if($configs_email == "FALSE"){
				;
			}

			$event = new OW_Event('notifications.add',$notificationParams,$notificationData);
		        OW::getEventManager()->trigger($event);
		
		}
		$messages_q=SupportCenterDB::ticket_get_messages($ticket["id"]);
		$messages=array();
		foreach($messages_q as $data) {
			array_push($messages,array(
				"author"=>BOL_UserService::getInstance()->getDisplayName($data["author_id"]),
				"text"=>$data["text"],
				"created"=>$data["created"],
			));
			
		}
		
		//@b12 add User Url and get Department name for sidebar info
		$user_url = BOL_UserService::getInstance()->getUserUrl($ticket["author_id"]);
		$get_dep = SupportCenterDB::ticket_get_department($ticket["id"]);
		$departments = $get_dep["text"];
		
		$this->assign("departments", $departments);
		$this->assign("url_user",$user_url);
		$this->assign("ticket",$ticket);
		$this->assign("messages",$messages);
		$this->assign("url_back",OW::getRouter()->urlForRoute("supportcenter-admin-departments-view",array("id"=>$ticket["department_id"])));
		$this->assign("url_delete",OW::getRouter()->urlForRoute("supportcenter-admin-tickets-delete",array("id"=>$ticket["id"])));
	}
	function ticketsDelete($params) {
		SupportCenterDB::ticket_delete($params["id"]);
	        $this->redirect(OW::getRouter()->urlForRoute("supportcenter-admin-tickets"));
	}


	function departments() {
		$this->myinit("departments");

		$form=$this->build_department_form();
		$this->addForm($form);

		if(OW::getRequest()->isPost() && $form->isValid($_POST)) {
			$values = $form->getValues();
			$form->reset();
			SupportCenterDB::department_new($values["text"]);
			OW::getFeedback()->info("Department created");
		}

		$query=SupportCenterDB::department_list();
		$deps=array();
		foreach($query as $data) {
			array_push($deps,array(
				"id" => $data["id"],
				"text" => $data["text"],
				"url_delete" => OW::getRouter()->urlForRoute("supportcenter-admin-departments-delete",array("id"=>$data["id"])),
				"url_view" => OW::getRouter()->urlForRoute("supportcenter-admin-departments-view",array("id"=>$data["id"])),
				//"url_edit" => OW::getRouter()->urlForRoute("supportcenter-admin-departments-edit",array("id"=>$data["id"]))),
			));
		}
		$this->assign("items",$deps);
		$this->assign("text_label_list",OW::getLanguage()->text("supportcenter","department_list"));
		$this->assign("text_label_add",OW::getLanguage()->text("supportcenter","department_add"));

	}
	function departmentsView($params) {
		$this->myinit("departments");

		$dep_id=$params["id"];

		$form=$this->build_add_dep_user_form();
		$this->addForm($form);
		if(OW::getRequest()->isPost() && $form->isValid($_POST)) {
			$values = $form->getValues();
			$form->reset();
			$query_dept=SupportCenterDB::department_get($dep_id);
			$depname=$query_dept["text"];

			$user = BOL_UserService::getInstance()->findByUsername($values["user"]);
			$uid = $user->getId();
			$check_user = SupportCenterDB::checkExistingMod($uid);


			$userIDx = $user->id;
			if ($user === null) {
				OW::getFeedback()->error(OW::getLanguage()->text("supportcenter", "user_not_found"));
			}
			if($uid == $check_user["user_id"]){
				OW::getFeedback()->error(OW::getLanguage()->text("supportcenter", "user_already_in_dep"));
			}
			else {
				
				SupportCenterDB::department_user_add($dep_id,$user->getId());
				$avatars = BOL_AvatarService::getInstance()->getAvatarsUrlList(array($user->getId()));
				$avatars['src'] = OW::getPluginManager()->getPlugin("supportcenter")->getStaticUrl()."img/ic_reply.png";
				
				$notificationParams = array(
					'pluginKey' => 'supportcenter',
					'action' => 'addedToDep',
					'entityType' => 'supportcenter-addedToDep',
					'entityId' => $userIDx,
					'userId' => $userIDx,
					'time' => time()
				);
				$notificationData = array(
					'string' => array(
					'key' => 'supportcenter+added_to_department',
					'vars' => array(
						'content' => $depname
					)
					),
					'avatar' => $avatars,
					"url"=>OW::getRouter()->urlForRoute("supportcenter-manage-tickets")
				);
				$event = new OW_Event('notifications.add',$notificationParams,$notificationData);
				OW::getEventManager()->trigger($event);
				$this->reset;
				OW::getFeedback()->info(OW::getLanguage()->text("supportcenter", "user_add_department"));
			}
		}

		$query_dept=SupportCenterDB::department_get($dep_id);
		$query_users=SupportCenterDB::department_get_users($dep_id);
		$query_tickets=SupportCenterDB::ticket_list_by_department($dep_id);

		$users=array();
		foreach($query_users as $data) {
			array_push($users,array(
				"text"=>BOL_UserService::getInstance()->getDisplayName($data["user_id"]),
				"user_url"=>BOL_UserService::getInstance()->getUserUrl($data["user_id"]),
				"url_delete"=>OW::getRouter()->urlForRoute("supportcenter-admin-departments-user-delete",array("dep_id"=>$params["id"],"user_id"=>$data["user_id"])),
			));
		}
		$tickets=array();
		foreach($query_tickets as $data) {
			array_push($tickets,array(
				"text"=>$data["subject"],
				"updated"=>$data["updated"],
				"status"=>$this->get_ticket_status($data),
				"url_delete"=>OW::getRouter()->urlForRoute("supportcenter-admin-tickets-delete",array("id"=>$data["id"])),
				"url_view"=>OW::getRouter()->urlForRoute("supportcenter-admin-tickets-view",array("id"=>$data["id"])),
			));
		}

		$department_name=$query_dept["text"];

		$this->assign("text_ticket_list",$department_name." ".OW::getLanguage()->text("supportcenter","department_ticket_list"));



		$this->assign("tickets",$tickets);
		$this->assign("items",$users);
		$this->assign("text_label_list",OW::getLanguage()->text("supportcenter","department_user_list"));
		$this->assign("text_label_add",OW::getLanguage()->text("supportcenter","department_user_add")." to ${department_name} department");
	}
	function departmentsDelete($params) {
		SupportCenterDB::department_delete($params["id"]);
	        $this->redirect(OW::getRouter()->urlForRoute("supportcenter-admin-departments"));
	}
	function departmentsUserDelete($params) {
		SupportCenterDB::department_user_delete($params["dep_id"],$params["user_id"]);
	        $this->redirect(OW::getRouter()->urlForRoute("supportcenter-admin-departments-view",array("id"=>$params["dep_id"])));
	}

  /************************** FAQ ADMIN FUNCTIONS ***********************************/
	//ver 4.0 b5x FAQ function
	function faq(){
		$this->myinit("faq");

		$form=$this->build_faq_form();
		$this->addForm($form);

		if(OW::getRequest()->isPost() && $form->isValid($_POST)){		//submit new pack to the database
			$values = $form->getValues();
			$form->reset();

			$faq = SupportCenterDB::new_faq(array(
				"id" => NULL,
				"question" => $values["question"],
				"answer" => $values["answer"],
				"position" => NULL
			));



			OW::getFeedback()->info(OW::getLanguage()->text("supportcenter", "faq_added"));
			OW::getApplication()->redirect(OW_URL_HOME."admin/supportcenter/faq");
			return;
		}

		$faqs_q=SupportCenterDB::get_faqs();
		$faqs=array();
		foreach($faqs_q as $data) {
			array_push($faqs,array(
				"id" => $data["id"],
				"question" => $data["question"],
				"answer" => $data["answer"],
				"position" => $data["position"],
				"url_delete" => OW::getRouter()->urlForRoute("supportcenter-admin-faqs-delete",array("id"=>$data["id"])),
				"url_edit" => OW::getRouter()->urlForRoute("supportcenter-admin-faqs-edit",array("id"=>$data["id"]))

			));
		}
		$this->assign("items",$faqs);
	}

	function faqEdit($params){
		$this->myinit("faq");

		$form=$this->build_faq_form();
		$this->addForm($form);

		$id=$params["id"];

		if(OW::getRequest()->isPost() && $form->isValid($_POST)) {
			$values = $form->getValues();
			$form->reset();

			$faq = SupportCenterDB::faq_update(array(
				"id" => $id,
				"question" => $values["question"],
				"answer" => $values["answer"]));
			OW::getFeedback()->info(OW::getLanguage()->text("supportcenter", "faq_updated"));
			OW::getApplication()->redirect(OW_URL_HOME."admin/supportcenter/faq");
		}

		$faqq=SupportCenterDB::get_faq($id);
		$form->getElement("question")->setValue($faqq["question"]);
		$form->getElement("answer")->setValue($faqq["answer"]);
		$this->assign("url_cancel",OW::getRouter()->urlForRoute("supportcenter-admin-faqs"));

	}

	function faqDelete($params) {
		SupportCenterDB::delete_faq($params["id"]);
			$this->redirect( OW::getRouter()->urlForRoute("supportcenter-admin-faqs"));
			OW::getFeedback()->info(OW::getLanguage()->text("supportcenter", "faq_deleted"));
	}
}





