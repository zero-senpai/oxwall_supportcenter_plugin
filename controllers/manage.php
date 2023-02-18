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
//Controller for new Off-admin Ticket Manager

require_once("supportcenter.php");

class SUPPORTCENTER_CTRL_Manage extends OW_ActionController{
	const STATUS_ERROR = 'error';
//update build6 note: adding menu
		public function __construct(){	
		
        parent::__construct();
		
		$id = SupportCenterDB::get_department_by_user(OW::getUser()->getId());
		
		//menu
        $general = new BASE_MenuItem();
        $general->setLabel(OW::getLanguage()->text('supportcenter', 'manage_ticket_general'));
        $general->setUrl(OW::getRouter()->urlForRoute('supportcenter-manage-tickets'));
        $general->setKey('general');
        $general->setIconClass('ow_ic_gear_wheel');
        $general->setOrder(0);
        
        $art = new BASE_MenuItem();
        $art->setLabel(OW::getLanguage()->text('supportcenter', 'manage_ticket_article'));
        $art->setUrl(OW::getRouter()->urlForRoute('supportcenter-manage-articles'));
        $art->setKey('art');
        $art->setIconClass('ow_ic_picture');
        $art->setOrder(1);
        
        $menu = new BASE_CMP_ContentMenu(array($general, $art));
        $this->addComponent('menu', $menu);			

		}
		
//end menu
		public function init(){		
		$language = OW::getLanguage();
		OW::getDocument()->addStyleSheet( OW::getPluginManager()->getPlugin("supportcenter")->getStaticCssUrl()."supportcenter.css" );
                OW::getDocument()->setTitle($language->text("supportcenter","support_manage_title"));
                OW::getDocument()->setDescription($language->text("supportcenter","support_manage_description"));
                OW::getDocument()->setHeading($language->text("supportcenter","support_manage_heading"));
				
		if(!OW::getUser()->isAuthenticated()){
			throw new AuthenticateException();
		}
		
		if(!OW::getUser()->isAuthorized('supportcenter', 'manage')){
			OW::getFeedback()->error(OW::getLanguage()->text("supportcenter", "insufficient_perm"));
			OW::getApplication()->redirect(OW_URL_HOME."support");
		}
		
		$uid = OW::getUser()->getId();
		$check_user = SupportCenterDB::checkExistingMod($uid);
		if($uid != $check_user["user_id"]){
			OW::getFeedback()->error(OW::getLanguage()->text("supportcenter", "no_department_assigned"));
			OW::getApplication()->redirect(OW_URL_HOME."support");
		}
		else{
			;
		}
		
		
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
	//build6: adding article form
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
	// @ b70 v 4.0 Build Manual Purge form
	/*function build_purge_form(){
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
	}*/
	

	function get_ticket_status($ticket){
		$stat = array("New","Unread","Read","Answered");
		if($ticket["requested_deletion"]!=0){
			return "To delete";
		}
		return $stat[$ticket["status"]];
	}
	function get_ticket_department($ticket){				$dpn_q = SupportCenterDB::department_name($id);				$tq = SupportCenterDB::ticket_list();				$id = array();				foreach($tq as $data){					array_push($id,array(						"dname"=>$data["department_id"],					));				}				$this->assign("items",$id);	}				
	function tickets() {
		$this->init();

		//@b70 v4.0 adding the manual purge form to the page
		/*$form=$this->build_purge_form();
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
		}*/
		$uid = OW::getUser()->getId();
		$check_user = SupportCenterDB::get_department_by_user($uid);

		$tickets_q=SupportCenterDB::get_mod_list($check_user);
		$tickets=array();
		foreach($tickets_q as $data) {
			array_push($tickets,array(
				"text"=>$data["subject"],
				"updated"=>$data["updated"],
				"department"=>$data["text"],
				"status"=>$this->get_ticket_status($data),
				"url_view"=>OW::getRouter()->urlForRoute("supportcenter-manage-tickets-view",array("id"=>$data["id"])),
				"url_delete"=>OW::getRouter()->urlForRoute("supportcenter-manage-tickets-delete",array("id"=>$data["id"])),
				"request_deletion"=>$data["requested_deletion"],
			));								
		}				
		$this->assign("items",$tickets);
		$this->assign("test", $check_user);
		$this->assign("text_label_list",OW::getLanguage()->text("supportcenter","ticket_list"));
	}
	public function ticketsView($param) {
		$this->init("tickets");

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
			    "avatar" => $avatars,
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
		$user_url = BOL_UserService::getInstance()->getUserUrl($ticket["author_id"]);
		//@b12 add User Url and get Department name for sidebar info
		$get_dep = SupportCenterDB::ticket_get_department($ticket["id"]);
		$departments = $get_dep["text"];
		
		$this->assign("departments", $departments);
		$this->assign("url_user",$user_url);
		$this->assign("ticket",$ticket);
		$this->assign("messages",$messages);		$this->assign("url_back", OW::getRouter()->urlForRoute('supportcenter-manage-tickets'));
		$this->assign("url_delete",OW::getRouter()->urlForRoute("supportcenter-manage-tickets-delete",array("id"=>$ticket["id"])));	
	}
	function ticketsDelete($params) {
		SupportCenterDB::ticket_delete($params["id"]);
	        $this->redirect(OW::getRouter()->urlForRoute("supportcenter-manage-tickets"));
	}
	

//buil 6 note: adding Knowledgebase editor

	//articles
	function articles($params) {
		$this->init("articles");

		$a=SupportCenterDB::article_list();
		$articles=array();
		foreach($a as $article) {
			array_push($articles,array(
				"id" => $article["id"],
				"title" => $article["title"],
				"rating" => round($article["rating"],2),
				"url_edit" => OW::getRouter()->urlForRoute("supportcenter-manage-articles-edit",array("id"=>$article["id"])),
				"url_delete" => OW::getRouter()->urlForRoute("supportcenter-manage-articles-delete",array("id"=>$article["id"]))
			));
		}

		$this->assign("articles",$articles);
		$this->assign("url_new",OW::getRouter()->urlForRoute("supportcenter-manage-articles-new"));
	}
	function articlesNew() {
		$this->init("articles");

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
		        $this->redirect( OW::getRouter()->urlForRoute("supportcenter-manage-articles"));
			//OW::getFeedback()->info("Article created");
			//$this->redirectToAction("articles");
			return;
		}
		$this->assign("url_cancel",OW::getRouter()->urlForRoute("supportcenter-manage-articles"));
	}
	function articlesDelete($params) {
		SupportCenterDB::article_delete($params["id"]);
	        $this->redirect( OW::getRouter()->urlForRoute("supportcenter-manage-articles"));
		//OW::getFeedback()->info("Article deleted");
		//$this->redirectToAction("articles");
	}
	function articlesEdit($params) {
		$this->init("articles");

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
		        $this->redirect( OW::getRouter()->urlForRoute("supportcenter-manage-articles"));
		}

		$article=SupportCenterDB::article_get($id);
		$form->getElement("title")->setValue($article["title"]);
		$form->getElement("body")->setValue($article["body"]);
		$form->getElement("cat1")->setValue($article["cat1"]);
		$form->getElement("cat2")->setValue($article["cat2"]);

		$this->assign("url_cancel",OW::getRouter()->urlForRoute("supportcenter-manage-articles"));
	}












}