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

class SUPPORTCENTER_MCTRL_Support extends OW_MobileActionController {
	//Add Tabbed Menu
		public function __construct(){	
		
        parent::__construct();
        
        $general = new BASE_MenuItem();
        $general->setLabel(OW::getLanguage()->text('supportcenter', 'support_title'));
        $general->setUrl(OW::getRouter()->urlForRoute('supportcenter-support'));
        $general->setKey('support');
        $general->setIconClass('ow_ic_help');
        $general->setOrder(0);
		
		$mcheck = OW::getConfig()->getValue("supportcenter", "k_members");

        $kno = new BASE_MenuItem();
        $kno->setLabel(OW::getLanguage()->text('supportcenter', 'knowledgebase'));
        $kno->setUrl(OW::getRouter()->urlForRoute('supportcenter-knowledgebase'));
        $kno->setKey('knowledgebase');
        $kno->setIconClass('ow_ic_file');
		$kno->setOrder(1);

		
		$mt = new BASE_MenuItem();
		$mt->setLabel(OW::getLanguage()->text('supportcenter', 'my_tickets'));
		$mt->setUrl(OW::getRouter()->urlForRoute('supportcenter-tickets-my'));
		$mt->setKey('mytickets');
		$mt->setIconClass('ow_ic_folder');
		$mt->setOrder(2);

		if($mcheck == "TRUE"){
			if(!OW::getUser()->isAuthenticated()){
				$menu = new BASE_CMP_ContentMenu(array($general, $kno));
				$this->addComponent('menu', $menu);
			}else{
        	$menu = new BASE_CMP_ContentMenu(array($general, $kno, $mt));
			$this->addComponent('menu', $menu);	
			}
		}
		if($mcheck == "FALSE"){
			if(!OW::getUser()->isAuthenticated()){
				$menu = new BASE_CMP_ContentMenu(array($general));
				$this->addComponent('menu', $menu);
			}elseif(OW::getUser()->isAuthenticated()){
				$menu = new BASE_CMP_ContentMenu(array($general, $kno, $mt));
				$this->addComponent('menu', $menu);
			}
		}
		}



	function myinit() {
		$language = OW::getLanguage();
		OW::getDocument()->addStyleSheet( OW::getPluginManager()->getPlugin("supportcenter")->getStaticCssUrl()."supportcenter.css" );
                OW::getDocument()->setTitle($language->text("supportcenter","support_title"));
                OW::getDocument()->setDescription($language->text("supportcenter","support_description"));
                OW::getDocument()->setHeading($language->text("supportcenter","support_heading"));
	}

	//forms
	function build_search_form() {
		$language = OW::getLanguage();

		$form=new Form("supportcenter_form_search");
		$form->setMethod(Form::METHOD_POST);
		$form->setEnctype(Form::ENCTYPE_MULTYPART_FORMDATA);

		$textField = new TextField("text");
		$textField->setLabel($language->text("supportcenter", "form_search"));
		$textField->setHasInvitation(true);
		$textField->setInvitation($language->text('supportcenter', 'form_search_placeholder'));
		$textField->setRequired();

		$submit = new Submit("submit");
		$submit->setLabel($language->text("supportcenter", "form_add_tag_submit"));

		$form->addElement($textField);
		$form->addElement($submit);

		return $form;
	}
	function build_new_ticket_form() {
		$language=OW::getLanguage();

		$form=new Form("supportcenter_form_new_ticket");
		$form->setMethod(Form::METHOD_POST);
		$form->setEnctype(Form::ENCTYPE_MULTYPART_FORMDATA);

		$text_subject = new TextField("subject");
		$text_subject->setLabel($language->text("supportcenter", "form_new_ticket_subject"));
		$text_subject->setHasInvitation(true);
		$text_subject->setRequired();

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
		$text_body->setLabel($language->text("supportcenter", "form_new_ticket_text"));
		$text_body->setRequired();

		$category_query=SupportCenterDB::ticket_category_list();
		$categories=array();
		foreach($category_query as $data) {
			$categories[$data["id"]]=$data["text"];
		}

		$department_query=SupportCenterDB::department_list();
		$departments=array();
		foreach($department_query as $data) {
			$departments[$data["id"]]=$data["text"];
		}

		$cat = new Selectbox("category");
		$cat->setLabel($language->text("supportcenter", "form_new_ticket_category"));
		$cat->setInvitation("Select");
		$cat->setOptions($categories);
		$cat->setRequired(true);

		$dep = new Selectbox("department");
		$dep->setLabel($language->text("supportcenter", "form_new_ticket_department"));
		$dep->setInvitation("Select");
		$dep->setOptions($departments);
		$dep->setRequired();

		$submit = new Submit("submit");
		$submit->setLabel($language->text("supportcenter", "form_new_ticket_submit"));

		$form->addElement($text_subject);
		$form->addElement($text_body);
		$form->addElement($cat);
		$form->addElement($dep);
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
	//@b27 Add form for users to choose how to order articles on /knowledgebase and /article/tag
	function build_order_form(){
		$language = OW::getLanguage();
		
		$form = new Form("supportcenter_form_order");
		$form->setMethod(Form::METHOD_POST);
		
		$order = new Selectbox("order");
		$order->setLabel($language->text("supportcenter", "ordering_by"));
		$order->setHasInvitation(true);
		$order->setOptions(array(
			"ALPHA"=>$language->text("supportcenter", "form_settings_korder_alpha"),
			"DATE"=>$language->text("supportcenter", "form_settings_korder_date"),
			"TOP"=>$language->text("supportcenter", "form_settings_korder_top")));
		$order->setRequired(true);
		
		$submit = new Submit("submit");
		$submit->setLabel($language->text("supportcenter", "form_ticket_reply_submit"));
		
		$form->addElement($order);
		$form->addElement($submit);
		
		return $form;
	}
	
	
	public function index() {
		$this->myinit();

		$form=$this->build_search_form();
		$this->addForm($form);

		$fcheck = OW::getConfig()->getValue("supportcenter", "faq_toggle");
		if($fcheck == "TRUE"){
			$f = "TRUE";
		}elseif($fcheck == "FALSE"){
			$f = "FALSE";
		}

		if(OW::getRequest()->isPost() && $form->isValid($_POST)) {
			$values = $form->getValues();
			$form->reset();
			
		        //$this->redirect("/support/knowledgebase/search/".$values["text"]);
			$this->redirect(OW::getRouter()->urlForRoute("supportcenter-knowledgebase-search",array("text"=>$values["text"])));
		}

		$top_articles_query=SupportCenterDB::article_list_top();
		$top_articles=array();
		foreach($top_articles_query as $article) {
			array_push($top_articles,array(
				"title" => $article["title"],
				"rating" => round($article["rating"],2),
				"url_view" => OW::getRouter()->urlForRoute("supportcenter-knowledgebase-article",array("id"=>$article["id"]))
			));
		}
		//@b15 Adding variables for Help Forum and Welcome Message displays @b21 fix for Deleted user showing in promo
		if(!OW::getUser()->isAuthenticated()){
			$uName = OW::getLanguage()->text("supportcenter", "guest");
		}else{
			$uName = BOL_UserService::getInstance()->getDisplayName(OW::getUser()->getId());
		}
		
		$userName = OW::getLanguage()->text("supportcenter", "welcome_support_center", array('username' => $uName));
		$help_forum_con = OW::getConfig()->getValue("supportcenter", "help_forum");
		$help_forum_url = OW::getConfig()->getValue("supportcenter", "help_forum_url");
		$hf_promo = OW::getLanguage()->text("supportcenter", "help_forum", array('help_forum_url' => $help_forum_url));
		$community_help = OW::getLanguage()->text("supportcenter", "community_help");
		$t_article_label = OW::getLanguage()->text("supportcenter", "top_articles_label");
		$tickets_label = OW::getLanguage()->text("supportcenter", "department_ticket_list");
		$ch = OW::getConfig()->getValue("supportcenter", "k_members");
		$faqlabel = OW::getLanguage()->text("supportcenter", "faq");
		if($ch == "FALSE"){
			if(!OW::getUser()->isAuthenticated()){
				$search_guard = "TRUE";
			}else{
				$search_guard = "FALSE";
			}
		}else{
			$search_guard = "FALSE";
		}

		if(!OW::getUser()->isAuthenticated()){
			$authcheck = "FALSE";
		}elseif(OW::getUser()->isAuthenticated()){
			$authcheck = "TRUE";
		}
		$this->assign("searchguard", $search_guard);
		$this->assign("authcheck", $authcheck);
		$this->assign("community_help", $community_help);
		$this->assign("help_forum_con", $help_forum_con);
		$this->assign("hf_promo", $hf_promo);
		$this->assign("welcome_msg", $userName);
		$this->assign("top_articles_list",$top_articles);
		$this->assign("text_articles","Search Articles");
		$this->assign("top_articles", $t_article_label);
		$this->assign("text_article_menu", $tickets_label);
		$this->assign("url_knowledgebase",OW::getRouter()->urlForRoute("supportcenter-knowledgebase"));
		$this->assign("url_ticket_my",OW::getRouter()->urlForRoute("supportcenter-tickets-my"));
		$this->assign("url_ticket_new",OW::getRouter()->urlForRoute("supportcenter-tickets-new"));
		$this->assign("f", $f);
		$this->assign("faq_label", $faqlabel);
		$this->assign("url_faq", OW::getRouter()->urlForRoute("supportcenter-faq"));
	}
	public function articleSearch($params) {
		$this->myinit();
		$language = OW::getLanguage();
		$form=$this->build_search_form();
		$this->addForm($form);

				// @b40 v3.5.2 Add check based on setting
				$a = OW::getConfig()->getValue("supportcenter", "k_members");
				if($a == "FALSE"){
					if(!OW::getUser()->isAuthenticated()){
						OW::getFeedback()->error(OW::getLanguage()->text("supportcenter", "sign_in"));
						throw new AuthenticateException();
					}
				}elseif($a == "TRUE"){
					;
				}

		if(OW::getRequest()->isPost() && $form->isValid($_POST)) {
			$values = $form->getValues();
			$form->reset();
			
		        //$this->redirect("/support/knowledgebase/search/".$values["text"]);
			$this->redirect(OW::getRouter()->urlForRoute("supportcenter-knowledgebase-search",array("text"=>$values["text"])));

		}
		$article_data=SupportCenterDB::article_search($params["text"]);

		$articles=array();
		foreach($article_data as $a) {
			array_push($articles,array(
				"text"=>$a["title"],
				"url_view"=>OW::getRouter()->urlForRoute("supportcenter-knowledgebase-article",array("id"=>$a["id"]))
			));
		}
		$this->assign("items",$articles);
		$this->assign("url_back",OW::getRouter()->urlForRoute("supportcenter-support"));
	}
	public function knowledgebase($params) {
		$this->myinit();
		
		
		$form = $this->build_order_form();
		$this->addForm($form);

		// @b40 v3.5.2 Add check based on setting
		$a = OW::getConfig()->getValue("supportcenter", "k_members");
		if($a == "FALSE"){
			if(!OW::getUser()->isAuthenticated()){
				OW::getFeedback()->error(OW::getLanguage()->text("supportcenter", "sign_in"));
				throw new AuthenticateException();
			}
		}elseif($a == "TRUE"){
			;
		}

		if(OW::getRequest()->isPost() && $form->isValid($_POST)){
			$values = $form->getValues();
			
			
			$this->redirect(OW_URL_HOME."support/knowledgebase?".$values["order"]."");
			
			$default = $values["order"];
			$form->getElement("order")->setValue($default);
			
		}
		
		$default = OW::getConfig()->getValue("supportcenter", "k_order");
		$form->getElement("order")->setValue($default);
		
		if($default == "ALPHA"){
			$article_data = SupportCenterDB::article_list_alpha();
			$form->getElement("order")->setValue("ALPHA");
		}
		if($default == "DATE"){
			$article_data = SupportCenterDB::article_list();
			$form->getElement("order")->setValue("DATE");
		}
		if($default == "TOP"){
			$article_data = SupportCenterDB::article_list_top_all();
			$form->getElement("order")->setValue("TOP");
		}
		
		if(isset($_GET["ALPHA"])){
			$article_data = SupportCenterDB::article_list_alpha();
			$form->getElement("order")->setValue("ALPHA");
		}
		if(isset($_GET["DATE"])){
			$article_data = SupportCenterDB::article_list();
			$form->getElement("order")->setValue("DATE");
		}
		if(isset($_GET["TOP"])){
			$article_data = SupportCenterDB::article_list_top_all();
			$form->getElement("order")->setValue("TOP");
		}
		
		//$article_data=SupportCenterDB::article_list();
		$articles=array();

		foreach($article_data as $a) {
			array_push($articles,array(
				"text" => $a["title"],
				"url_view"=>OW::getRouter()->urlForRoute("supportcenter-knowledgebase-article",array("id"=>$a["id"]))
			));
		}
		
		$tags_q = SupportCenterDB::tag_get();
		$tags = array();
		
		foreach($tags_q as $t){
				array_push($tags,array(
					"text" => $t["text"],
					"id" => $t["id"],
					"url_view"=>OW::getRouter()->urlForRoute("supportcenter-knowledgebase-article-tag",array("id"=>$t["id"]))
				));
		}
		$knowledge_lbl = OW::getLanguage()->text("supportcenter", "knowledgebase");
		$browse_tag_label = OW::getLanguage()->text("supportcenter", "browse_by_tag");
		$this->assign("browse_tag_label", $browse_tag_label);
		$this->assign("knowledge_lbl", $knowledge_lbl);
		$this->assign("tags", $tags);
		$this->assign("items",$articles);
		$this->assign("url_back",OW::getRouter()->urlForRoute("supportcenter-support"));
	}
	public function article($params) {
		$this->myinit();
				// @b40 v3.5.2 Add check based on setting
				$a = OW::getConfig()->getValue("supportcenter", "k_members");
				if($a == "FALSE"){
					if(!OW::getUser()->isAuthenticated()){
						OW::getFeedback()->error(OW::getLanguage()->text("supportcenter", "sign_in"));
						throw new AuthenticateException();
					}
				}elseif($a == "TRUE"){
					;
				}
		//@b15 Adding floatbox script
		$script = "$('#sc_ajax_floatbox').click(function(){
            scAjaxFloatBox = OW.ajaxFloatBox('SUPPORTCENTER_CMP_Floatbox', {reload: false} , {width:380, iconClass: 'ow_ic_add', title: '".OW::getLanguage()->text('supportcenter', 'share_to')."'});
});";
		OW::getDocument()->addOnloadScript($script);
		OW::getDocument()->addStyleSheet( OW::getPluginManager()->getPlugin("supportcenter")->getStaticCssUrl()."rating.css" );
		OW::getDocument()->addScript( OW::getPluginManager()->getPlugin("supportcenter")->getStaticJsUrl()."jquery.rating.js" );

		$aid=$params["id"];

		$art=SupportCenterDB::article_get($aid);
		$this->assign("article",$art);

		$rate_url=OW::getRouter()->urlForRoute("supportcenter-knowledgebase-article-rate",array("id"=>$aid));
		$rating_current=SupportCenterDB::article_rating_get($aid,OW::getUser()->getId());
		$rate_script="$('#rate').rating('$rate_url',{maxvalue:5,curvalue:$rating_current});";
		OW::getDocument()->addOnloadScript($rate_script);

		$tags=array();
		$atags=SupportCenterDB::article_get_tags($art);
		foreach($atags as $a) {
			array_push($tags,array("text" => $a["text"],"id" => $a["id"]));
		}
		//@b15 Show Recommended Articles
		$recommended_q = SupportCenterDB::article_suggested_get($aid);
		$rec = array();
		foreach($recommended_q as $article){
			array_push($rec,array(
				"title" => $article["title"],
				"url_view" => OW::getRouter()->urlForRoute("supportcenter-knowledgebase-article",array("id"=>$article["id"]))
			));
		}
		
		$rec_con = OW::getConfig()->getValue("supportcenter", "recommend_art");
		$share_con = OW::getConfig()->getValue("supportcenter", "article_share");
		
		$this->assign("share_con", $share_con);
		$this->assign("rec_con", $rec_con);
		$this->assign("recommended_articles", $rec);
		$rec_arts = OW::getLanguage()->text("supportcenter", "form_settings_recommended_art");
		$this->assign("rec_arts", $rec_arts);
		$this->assign("url_back",OW::getRouter()->urlForRoute("supportcenter-knowledgebase"));
		$this->assign("tags",$tags);
	}
	public function articleTag($params) {
		$this->myinit();
				// @b40 v3.5.2 Add check based on setting
				$a = OW::getConfig()->getValue("supportcenter", "k_members");
				if($a == "FALSE"){
					if(!OW::getUser()->isAuthenticated()){
						OW::getFeedback()->error(OW::getLanguage()->text("supportcenter", "sign_in"));
						throw new AuthenticateException();
					}
				}elseif($a == "TRUE"){
					;
				}
		//@b27 add order form
		$form = $this->build_order_form();
		$this->addForm($form);
		
		if(OW::getRequest()->isPost() && $form->isValid($_POST)){
			$values = $form->getValues();
			
			
			$this->redirect(OW_URL_HOME."support/knowledgebase/article/tag/".$params["id"]."?".$values["order"]."");
			
			$default = $values["order"];
			$form->getElement("order")->setValue($default);
			
		}
		
		$default = OW::getConfig()->getValue("supportcenter", "k_order");
		$form->getElement("order")->setValue($default);
		
		if($default == "ALPHA"){
			$articles = SupportCenterDB::article_list_by_tag($params["id"]);
			$form->getElement("order")->setValue("ALPHA");
		}
		if($default == "DATE"){
			$articles = SupportCenterDB::article_list_by_tag_date($params["id"]);
			$form->getElement("order")->setValue("DATE");
		}
		if($default == "TOP"){
			$articles = SupportCenterDB::article_list_by_tag_top($params["id"]);
			$form->getElement("order")->setValue("TOP");
		}
		
		if(isset($_GET["ALPHA"])){
			$articles = SupportCenterDB::article_list_by_tag($params["id"]);
			$form->getElement("order")->setValue("ALPHA");
		}
		if(isset($_GET["DATE"])){
			$articles = SupportCenterDB::article_list_by_tag_date($params["id"]);
			$form->getElement("order")->setValue("DATE");
		}
		if(isset($_GET["TOP"])){
			$articles = SupportCenterDB::article_list_by_tag_top($params["id"]);
			$form->getElement("order")->setValue("TOP");
		}

		//$articles=SupportCenterDB::article_list_by_tag_date($params["id"]);
		$art=array();
		foreach($articles as $a) {
			array_push($art,array(
				"text"=>$a["title"],
				"url_view"=>OW::getRouter()->urlForRoute("supportcenter-knowledgebase-article",array("id"=>$a["id"]))
			));
		}
		$tag=SupportCenterDB::tag_get_by_id($params["id"]);
		$this->assign("tag",$tag["text"]);
		$this->assign("items",$art);
		$this->assign("url_back",OW::getRouter()->urlForRoute("supportcenter-support"));
	}
	public function articleRate($params) {
		SupportCenterDB::article_rating_set($params["id"],OW::getUser()->getId(),$_POST["rating"]);
	}

	//tickets
	public function ticketsMy() {
		//quick build add at 11
		if(!OW::getUser()->isAuthenticated()){
			throw new AuthenticateException();
		}
		$this->myinit();

		$tickets_q=SupportCenterDB::ticket_list_by_author(OW::getUser()->getId());
		$tickets=array();

		foreach($tickets_q as $data) {
			array_push($tickets,array(
				"text"=>$data["subject"],
				"requested_deletion"=>$data["requested_deletion"],
				"url_view"=>OW::getRouter()->urlForRoute("supportcenter-tickets-view",array("id"=>$data["id"])),
			));
		}

		$this->assign("items",$tickets);		
		$this->assign("url_back",OW::getRouter()->urlForRoute("supportcenter-support"));
	}
	public function ticketsView($param) {
		
		if(!OW::getUser()->isAuthenticated()){
			throw new AuthenticateException();
		} //@ b40 v 3.5.2 now monetize tickets 
		if(!OW::getUser()->isAuthorized('supportcenter', 'use_tickets')){
			OW::getFeedback()->error(OW::getLanguage()->text("supportcenter", "upgrade_membership"));
			OW::getApplication()->redirect(OW_URL_HOME."support");
		}
		$this->myinit();

		$form=$this->build_ticket_reply_form();
		$this->addForm($form);

		$ticket=SupportCenterDB::ticket_get($param["id"]);
		if($ticket["author_id"]!=OW::getUser()->getId()) {
		        $this->redirect(OW::getRouter()->urlForRoute("supportcenter-tickets-my"));
			return;
		}
		$ticket["author"]=BOL_UserService::getInstance()->getDisplayName($ticket["author_id"]);
		

		if(OW::getRequest()->isPost() && $form->isValid($_POST)) {
			$values = $form->getValues();
			$form->reset();
			SupportCenterDB::ticket_add_message(array(
				"ticket_id"=>$param["id"],
				"author_id"=>OW::getUser()->getId(),
				"text"=>$values["text"]
			),1);
			OW::getFeedback()->info("Message posted");


		        $userId = OW::getUser()->getId();
			$avatars = BOL_AvatarService::getInstance()->getAvatarsUrlList(array($userId));
		


			$notificationParams = array(
			    'pluginKey' => 'supportcenter',
			    'action' => 'ticketUpdated',
			    'entityType' => 'supportcenter-ticketUpdated',
			    //'entityId' => $target_user,
			    //'userId' => $target_user,
			    'time' => time()
			);
			$notificationData = array(
			    'string' => array(
				'key' => 'supportcenter+ticket_notification',
				'vars' => array(
				    'content' => $ticket["subject"]
				)
			    ),
			    'avatar' => OW::getPluginManager()->getPlugin("supportcenter")->getStaticUrl()."img/ic_reply.png",
				"url"=>OW::getRouter()->urlForRoute("supportcenter-manage-tickets-view",array("id"=>$ticket["id"]))
			);

			$query_users=SupportCenterDB::department_get_users($ticket["department_id"]);
			
			$users=array();
			foreach($query_users as $u) {
				array_push($users,$u["user_id"]);
			}
			if(!in_array(1,$users)) {
				array_push($users,1);
			}
			foreach($users as $user) {
				$notificationParams["entityId"]=$user;
				$notificationParams["userId"]=$user;
				$event = new OW_Event('notifications.add',$notificationParams,$notificationData);
				OW::getEventManager()->trigger($event);
			}
			
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
		//@b12 add User Url and get Department name for sidebar info / @b22 Got actual User Url
		$user_url = BOL_UserService::getInstance()->getUserUrl($ticket["author_id"]);
		$get_dep = SupportCenterDB::ticket_get_department($ticket["id"]);
		$departments = $get_dep["text"];
		
		
		
		$this->assign("departments", $departments);
		$this->assign("url_user",$user_url);
		$this->assign("ticket",$ticket);
		$this->assign("messages",$messages);
		$this->assign("url_back",OW::getRouter()->urlForRoute("supportcenter-tickets-my"));
		$this->assign("url_deletion",OW::getRouter()->urlForRoute("supportcenter-tickets-delete",array("id"=>$ticket["id"])));
		$this->assign("url_cancel_deletion",OW::getRouter()->urlForRoute("supportcenter-tickets-delete-cancel",array("id"=>$ticket["id"])));
	}
	public function ticketsNew() {
		if(!OW::getUser()->isAuthenticated()){
			throw new AuthenticateException();
		} // @b40 v 3.5.2 monetize add ticket
		if(!OW::getUser()->isAuthorized('supportcenter', 'use_tickets')){
			OW::getFeedback()->error(OW::getLanguage()->text("supportcenter", "upgrade_membership"));
			OW::getApplication()->redirect(OW_URL_HOME."support");
		}
		$this->myinit();

		$form=$this->build_new_ticket_form();
		$this->addForm($form);

		$url_back=OW::getRouter()->urlForRoute("supportcenter-tickets-my");

		if(OW::getRequest()->isPost() && $form->isValid($_POST)) {
			$values = $form->getValues();
			$form->reset();

			$ticket_id=SupportCenterDB::ticket_new(array(
				"author_id"=> OW::getUser()->getId(),
				"subject" => $values["subject"],
				"text" => $values["text"],
				"department_id" => $values["department"],
				"category_id" => $values["category"],
			));



		    $userId = OW::getUser()->getId();
			$avatars = BOL_AvatarService::getInstance()->getAvatarsUrlList(array($userId));
			$avatars['src'] = OW::getPluginManager()->getPlugin("supportcenter")->getStaticUrl()."img/ic_reply.png";

			$notificationParams = array(
			    'pluginKey' => 'supportcenter',
			    'action' => 'ticketUpdated',
			    'entityType' => 'supportcenter-ticketUpdated',
			    //'entityId' => $target_user,
			    //'userId' => $target_user,
			    'time' => time()
			);
			$notificationData = array(
			    'string' => array(
				'key' => 'supportcenter+ticket_notification_new',
				'vars' => array(
				    'content' => $values["subject"]
				)
			    ),
			    'avatar' => $avatars,
			    "url"=>OW::getRouter()->urlForRoute("supportcenter-manage-tickets-view",array("id"=>$ticket_id))
			);

			$query_users=SupportCenterDB::department_get_users($values["department"]);
			$users=array();
			foreach($query_users as $u) {
				array_push($users,$u["user_id"]);
			}
			if(!in_array(1,$users)) {
				array_push($users,1);
			}
			foreach($users as $user) {
				$notificationParams["entityId"]=$user;
				$notificationParams["userId"]=$user;
				$event = new OW_Event('notifications.add',$notificationParams,$notificationData);
				OW::getEventManager()->trigger($event);
			}


		        $this->redirect($url_back);
			return;
		}
		$this->assign("url_cancel",$url_back);
	}
	public function ticketsDelete($params) {
		SupportCenterDB::ticket_set_request_deletion($params["id"],1);
	        $this->redirect(OW::getRouter()->urlForRoute("supportcenter-tickets-view",array("id"=>$params["id"])));
	}
	public function ticketsDeleteCancel($params) {
		SupportCenterDB::ticket_set_request_deletion($params["id"],0);
	        $this->redirect(OW::getRouter()->urlForRoute("supportcenter-tickets-view",array("id"=>$params["id"])));
	}

	/**************** User-end F.A.Q functions ********************/

	public function faq(){
		$this->myinit();

		$fcon = OW::getConfig()->getValue("supportcenter", "faq_toggle");
		if($fcon == "FALSE"){
			OW::getFeedback()->warning(OW::getLanguage()->text("supportcenter", "not_available"));
			OW::getApplication()->redirect(OW_URL_HOME."support");
		}

		OW::getDocument()->setTitle(OW::getLanguage()->text("supportcenter", "faq_title"));
		OW::getDocument()->setHeading(OW::getLanguage()->text("supportcenter", "faq_title"));
		OW::getDocument()->setDescription(OW::getLanguage()->text("supportcenter", "faq_desc"));
		
		$get_faqs = SupportCenterDB::get_faqs();
		$faqs = array();
		foreach($get_faqs as $data){
			array_push($faqs,array(
				"id"=>$data["id"],
				"question"=>$data["question"],
				"answer"=>$data["answer"]));
			}
		$this->assign("faqs", $faqs);
		$this->assign("url_back",OW::getRouter()->urlForRoute("supportcenter-support"));




	}

}


