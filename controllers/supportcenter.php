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
class SupportCenterDB {

	public $result;

	static function table_tag() { return OW_DB_PREFIX."supportcenter_category"; }
	static function table_article() { return OW_DB_PREFIX."supportcenter_article"; }
	static function table_rating() { return OW_DB_PREFIX."supportcenter_rating"; }
	static function table_ticket_category() { return OW_DB_PREFIX."supportcenter_ticket_category"; }
	static function table_ticket() { return OW_DB_PREFIX."supportcenter_ticket"; }
	static function table_ticket_message() { return OW_DB_PREFIX."supportcenter_ticket_message"; }
	static function table_department() { return OW_DB_PREFIX."supportcenter_department"; }
	static function table_department_user() { return OW_DB_PREFIX."supportcenter_department_user"; }
	static function table_faq() { return OW_DB_PREFIX."supportcenter_faq"; }

	//tags
	static function tag_delete($id) {
		$d=OW::getDbo();
		$d->query("DELETE FROM ".SupportCenterDB::table_tag()." WHERE id='".$d->escapeString($id)."' LIMIT 1;");
	}
	static function tag_add($text) {
		$d=OW::getDbo();
		$d->query("INSERT INTO ".SupportCenterDB::table_tag()." (text) VALUES ('".$d->escapeString($text)."');" );
	}
	static function tag_get() {
		return OW::getDbo()->queryForList("SELECT * FROM ".SupportCenterDB::table_tag().";");
	}
	static function tag_get_by_id($id) {
		$d=OW::getDbo();
		$res=$d->queryForList("SELECT * FROM ".SupportCenterDB::table_tag()." WHERE id='".$d->escapeString($id)."';");
		return $res[0];
	}

	//articles
	//default listing by creation
	static function article_list() {
		$t_article=SupportCenterDB::table_article();
		$t_rating=SupportCenterDB::table_rating();
		return OW::getDbo()->queryForList("
			SELECT $t_article.id,$t_article.title, coalesce(AVG($t_rating.rating),0) as rating 
			FROM $t_article
				LEFT JOIN $t_rating ON $t_article.id=$t_rating.article_id 
				GROUP BY $t_article.id
			ORDER BY $t_article.id DESC;");
	}
	//@b25 add new Knowledgebase listings LIST by ALPHABETICAL ORDER
	static function article_list_alpha(){
		$t_article = SupportCenterDB::table_article();
		return OW::getDbo()->queryForList("
			SELECT $t_article.id, $t_article.title FROM $t_article GROUP BY $t_article.id ORDER BY $t_article.title ASC;");
	}
	//@b25 Article list by Top ratings no limit
	static function article_list_top_all() {
		$t_article=SupportCenterDB::table_article();
		$t_rating=SupportCenterDB::table_rating();
		return OW::getDbo()->queryForList("
			SELECT $t_article.id,$t_article.title, coalesce(AVG($t_rating.rating),0) as rating 
			FROM $t_article
				LEFT JOIN $t_rating ON $t_article.id=$t_rating.article_id 
				GROUP BY $t_article.id
			ORDER BY rating DESC;");
	}
	
	static function article_list_top() {
		$t_article=SupportCenterDB::table_article();
		$t_rating=SupportCenterDB::table_rating();
		return OW::getDbo()->queryForList("
			SELECT $t_article.id,$t_article.title, coalesce(AVG($t_rating.rating),0) as rating 
			FROM $t_article
				LEFT JOIN $t_rating ON $t_article.id=$t_rating.article_id 
				GROUP BY $t_article.id
			ORDER BY rating DESC LIMIT 3;");
	}
	//list by alpha
	static function article_list_by_tag($tag_id) {
		$d=OW::getDbo();
		return $d->queryForList("SELECT id,title FROM ".SupportCenterDB::table_article()." WHERE cat1='".$d->escapeString($tag_id)."' OR cat2='".$d->escapeString($tag_id)."' ORDER BY title ASC;");
	}
	//list by date
	static function article_list_by_tag_date($tag_id) { //lists by date created
		$d=OW::getDbo();
		return $d->queryForList("SELECT id,title FROM ".SupportCenterDB::table_article()." WHERE cat1='".$d->escapeString($tag_id)."' OR cat2='".$d->escapeString($tag_id)."' ORDER BY id DESC;");
	}
	//@b25 function for viewing tag articles by top
	static function article_list_by_tag_top($tag_id) {
		$t_article = SupportCenterDB::table_article();
		$t_rating = SupportCenterDB::table_rating();
		$d=OW::getDbo();
		return $d->queryForList("
			SELECT $t_article.id, $t_article.title, coalesce(AVG($t_rating.rating),0) as rating 
			FROM $t_article 
			LEFT JOIN $t_rating ON $t_article.id=$t_rating.article_id
			WHERE $t_article.cat1='".$d->escapeString($tag_id)."' OR $t_article.cat2='".$d->escapeString($tag_id)."'
			GROUP BY $t_article.id
			ORDER BY rating DESC;");
		
	}
	
	static function article_suggested_get($id){
		$d = OW::getDbo();
		$t_article = SupportCenterDB::table_article();
		$t_tag = SupportCenterDB::table_tag();
		return $d->queryForList("
			SELECT id,title FROM $t_article WHERE cat1='".$d->escapeString($id["cat1"])."' OR cat2='".$d->escapeString($id["cat2"])."'
			ORDER BY title DESC LIMIT 5;
			
	");
		
	}
	/*static function article_suggested_get($article){
		$d = OW::getDbo();
		$t_article = SupportCenterDB::table_article();
		$t_tag = SupportCenterDB::table_tag();
		return $d->queryForList("
			SELECT $t_article.id,$t_article.title, $t_tag.id FROM $t_tag
			WHERE $t_article.id IN (".$d->escapeString($article["cat1"]).",".$d->escapeString($article["cat2"]).")
			LEFT JOIN $t_tag ON $t_article.id=$t_tag.id
			GROUP BY $t_article.id
		ORDER BY $t_article.title DESC LIMIT 3;
			
			
	");
		
	}*/
	static function article_search($text) {
		$d=OW::getDbo();
		return $d->queryForList("SELECT id,title FROM ".SupportCenterDB::table_article()." 
			WHERE MATCH (title,body) AGAINST ('".$d->escapeString($text)."' IN BOOLEAN MODE);");
	}
	static function article_get($id) {
		$d=OW::getDbo();
		$res=$d->queryForList("SELECT * FROM ".SupportCenterDB::table_article()." WHERE id='".$d->escapeString($id)."';");
		return $res[0];
	}
	static function article_delete($id) {
		$d=OW::getDbo();
		$d->query("DELETE FROM ".SupportCenterDB::table_article()." WHERE id='".$d->escapeString($id)."' LIMIT 1;");
	}
	static function article_update($article) {
		$d=OW::getDbo();
		$d->query("UPDATE ".SupportCenterDB::table_article()." SET ".
			"title='".$d->escapeString($article["title"])."', ".
			"body='".$d->escapeString($article["body"])."', ".
			"cat1='".$d->escapeString($article["cat1"])."', ".
			"cat2='".$d->escapeString($article["cat2"])."' ".
			"WHERE id='".$d->escapeString($article["id"])."';");
	}
	static function article_add($article) {
		$d=OW::getDbo();
		if($article["cat1"]=="") $article["cat1"]=-1;
		if($article["cat2"]=="") $article["cat2"]=-1;
		$d->query("INSERT INTO ".SupportCenterDB::table_article()." (title,body,cat1,cat2) VALUES (
			'".$d->escapeString($article["title"])."',
			'".$d->escapeString($article["body"])."', 
			'".$d->escapeString($article["cat1"])."', 
			'".$d->escapeString($article["cat2"])."'
			);");
	}
	static function article_get_tags($article) {
		$d=OW::getDbo();
		return $d->queryForList("SELECT id,text FROM ".SupportCenterDB::table_tag()." WHERE id IN (".$d->escapeString($article["cat1"]).",".$d->escapeString($article["cat2"]).");");
	}
	static function article_rating_get_average($article_id) {
		$d=OW::getDbo();
		$res=$d->queryForList("SELECT AVG(rating) as avg FROM ".SupportCenterDB::table_rating()." WHERE article_id='".$d->escapeString($article_id)."';");
		return $res[0]["avg"];
	}
	static function article_rating_get($article_id,$user_id) {
		$d=OW::getDbo();
		$r=$d->queryForList("SELECT rating FROM ".SupportCenterDB::table_rating()." WHERE article_id='".
			$d->escapeString($article_id)."' AND user_id='".
			$d->escapeString($user_id)."';");
		if(count($r)==0) {
			return 0;
		}
		return $r[0]["rating"];
	}
	static function article_rating_set($article_id,$user_id,$rating) {
		$d=OW::getDbo();
		$d->query("INSERT INTO ".SupportCenterDB::table_rating()." (article_id,user_id,rating) VALUES (
			'".$d->escapeString($article_id)."',
			'".$d->escapeString($user_id)."',
			'".$d->escapeString($rating)."'
			) ON DUPLICATE KEY UPDATE rating=values(rating);");
	}


	//ticket categories
	static function ticket_category_list() {
		return OW::getDbo()->queryForList("SELECT id,text FROM ".SupportCenterDB::table_ticket_category().";");
	}
	static function ticket_category_new($text) {
		$d=OW::getDbo();
		$d->query("INSERT INTO ".SupportCenterDB::table_ticket_category()." (text) VALUES ('".$d->escapeString($text)."');");
	}
	static function ticket_category_update($cat) {
		$d=OW::getDbo();
		$d->query("UPDATE ".SupportCenterDB::table_ticket_category()." SET text='
			".$d->escapeString($cat["text"])."' WHERE id='
			".$d->escapeString($cat["id"])."';");
	}
	static function ticket_category_delete($cat_id) {
		$d=OW::getDbo();
		$d->query("DELETE FROM ".SupportCenterDB::table_ticket_category()." WHERE id='".$d->escapeString($cat_id)."' LIMIT 1;");
	}
	static function ticket_category_get($cat_id) {
		$d=OW::getDbo();
		$cat_id=$d->escapeString($cat_id);
		return $d->queryForList("SELECT id,text FROM ".SupportCenterDB::table_ticket_category()." WHERE id='$cat_id';");
	}
	//tickets
	/*static function ticket_list() {
		return OW::getDbo()->queryForList("SELECT id,category_id,subject,requested_deletion,department_id,updated,status FROM ".SupportCenterDB::table_ticket()." INNER JOIN  ORDER BY updated DESC;");
	}*/
	static function ticket_list() {
    $table_ticket = SupportCenterDB::table_ticket();
	$dp = SupportCenterDB::table_department();
    return OW::getDbo()->queryForList("SELECT {$table_ticket}.id, category_id, subject, 
            requested_deletion, {$dp}.text, department_id, updated, status FROM {$table_ticket}
            JOIN {$dp} ON {$dp}.id = {$table_ticket}.department_id
            ORDER BY updated DESC;");
}
	static function ticket_list_by_author($id) {
		$d=OW::getDbo();
		$id=$d->escapeString($id);
		return $d->queryForList("SELECT id,subject,requested_deletion,updated,status FROM ".SupportCenterDB::table_ticket()." WHERE author_id='$id' ORDER BY updated DESC;");
	}
	static function delete_tickets_by_1_week(){
		$d = OW::getDbo();
		$d->query("DELETE FROM ".SupportCenterDB::table_ticket()." WHERE updated < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 7 DAY));");
	}
	static function delete_tickets_by_3_months(){
		$d = OW::getDbo();
		$d->query("DELETE FROM ".SupportCenterDB::table_ticket()." WHERE updated < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 90 DAY));");
	}
	static function delete_tickets_by_6_months(){
		$d = OW::getDbo();
		$d->query("DELETE FROM ".SupportCenterDB::table_ticket()." WHERE updated < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 180 DAY));");
	}
	/*static function ticket_list_manage($yo){
		$table_ticket = SupportCenterDB::table_ticket();
		$d=OW::getDbo();
		$dp = SupportCenterDB::table_department();
		$dpu = SupportCenterDB::table_department_user();
		$where = SupportCenterDB::get_department_by_user($yo);
		return $d->queryForList("SELECT {$table_ticket}.id, category_id, subject, 
				requested_deletion, {$dp}.text, {$table_ticket}.department_id, updated, status, {$dpu}.user_id FROM {$table_ticket}
				JOIN {$dp} ON {$dp}.id = {$table_ticket}.department_id
				JOIN {$dpu} ON {$table_ticket}.department_id = {$dpu}.user_id
				WHERE {$table_ticket}.department_id = '{$where}.department_id'
				ORDER BY updated DESC;");
	}*/
	static function get_mod_list($check_user){
		$d = OW::getDbo();
		$dep_id = $d->escapeString($check_user);
		$table_ticket = SupportCenterDB::table_ticket();
		$dp = SupportCenterDB::table_department();
		return $d->queryForList("SELECT {$table_ticket}.id, category_id, subject, 
		requested_deletion, {$dp}.text, {$table_ticket}.department_id, updated, status FROM {$table_ticket}
		JOIN {$dp} ON {$dp}.id = {$table_ticket}.department_id
		WHERE {$table_ticket}.department_id = '{$dep_id}'
		ORDER BY updated DESC;");
	}

	static function ticket_list_by_department($id) {
		$d=OW::getDbo();
		$id=$d->escapeString($id);
		return $d->queryForList("SELECT id,subject,requested_deletion,updated,status FROM ".SupportCenterDB::table_ticket()." WHERE department_id='$id' ORDER BY updated DESC;");
	}
	static function ticket_new($ticket) {
		$d=OW::getDbo();
		$now=time();
		$d->query("INSERT INTO ".SupportCenterDB::table_ticket()." (author_id,category_id,department_id,subject,text,status,created,updated,requested_deletion) VALUES (
			'".$d->escapeString($ticket["author_id"])."',
			'".$d->escapeString($ticket["category_id"])."',
			'".$d->escapeString($ticket["department_id"])."',
			'".$d->escapeString($ticket["subject"])."',
			'".$d->escapeString($ticket["text"])."',
			'0',
			'$now',
			'$now',
			'0');");
		return $d->getInsertId();
	}
	static function ticket_set_request_deletion($ticket_id,$request) {
		$d=OW::getDbo();
		$d->query("UPDATE ".SupportCenterDB::table_ticket()." SET requested_deletion='".$d->escapeString($request)."' WHERE id='".$d->escapeString($ticket_id)."';");
	}
	static function ticket_delete($ticket_id) {
		$d=OW::getDbo();
		$d->query("DELETE FROM ".SupportCenterDB::table_ticket()." WHERE id='".$d->escapeString($ticket_id)."' LIMIT 1;");
	}
	static function ticket_set_status($ticket_id,$status) {
		$d=OW::getDbo();
		//$now=time();
		//$d->query("UPDATE ".SupportCenterDB::table_ticket()." SET status='".$d->escapeString($status)."',updated='$now' WHERE id='".$d->escapeString($ticket_id)."';");
		$d->query("UPDATE ".SupportCenterDB::table_ticket()." SET status='".$d->escapeString($status)."' WHERE id='".$d->escapeString($ticket_id)."';");
	}
	static function ticket_status_get_text($status_id) {
		$texts=array("Unread","Read","Replied","Solved");
		return $texts[$status_id];
	}
	static function ticket_get($ticket_id) {
		$d=OW::getDbo();
		$dp = SupportCenterDB::table_department();
		$res=$d->queryForList("SELECT * FROM ".SupportCenterDB::table_ticket()." WHERE id='".$d->escapeString($ticket_id)."'");
		return $res[0];
	}
	static function ticket_get_department($ticket_id) {
		$d = OW::getDbo();
		$dp = SupportCenterDB::table_department();
		$table_ticket = SupportCenterDB::table_ticket();
		$res=$d->queryForList("SELECT {$dp}.text FROM {$dp} JOIN {$table_ticket} ON {$table_ticket}.department_id = {$dp}.id WHERE {$table_ticket}.id='".$d->escapeString($ticket_id)."'");
		return $res[0];
	}
	/*static function ticket_get($ticket_id) {
		$d=OW::getDbo();
		$table_ticket = SupportCenterDB::table_ticket();
		$dp = SupportCenterDB::table_department();
		$res=$d->queryForList("SELECT * FROM {$table_ticket} JOIN {$dp} ON {$dp}.id = {$table_ticket}.department_id WHERE {$table_ticket}.id='".$d->escapeString($ticket_id)."'");
		return $res[0];
	}*/
	static function ticket_add_message($message,$new_status) {
		$d=OW::getDbo();
		$now=time();
		$ticket_id=$d->escapeString($message["ticket_id"]);
		$d->query("INSERT INTO ".SupportCenterDB::table_ticket_message()." (ticket_id,author_id,text,created) VALUES (
			'$ticket_id',
			'".$d->escapeString($message["author_id"])."',
			'".$d->escapeString($message["text"])."',
			'$now' );");
		$d->query("UPDATE ".SupportCenterDB::table_ticket()." SET updated='$now',status='$new_status' WHERE id='$ticket_id';");
	}
	static function ticket_get_messages($ticket_id) {
		$d=OW::getDbo();
		return $d->queryForList("SELECT * FROM ".SupportCenterDB::table_ticket_message()." WHERE ticket_id='".$d->escapeString($ticket_id)."';");
	}
	//department
	static function department_list() {
		$d=OW::getDbo();
		return $d->queryForList("SELECT * FROM ".SupportCenterDB::table_department().";");
	}
	static function department_new($text) {
		$d=OW::getDbo();
		$d->query("INSERT INTO ".SupportCenterDB::table_department()." (text) VALUES ('".$d->escapeString($text)."');");
	}
	static function department_update($dep) {
		$d=OW::getDbo();
		$d->query("UPDATE ".SupportCenterDB::table_department()." SET text='".$d->escapeString($dep["text"])."' WHERE id='".$d->escapeString($dep["id"])."';");
	}
	static function department_delete($id) {
		$d=OW::getDbo();
		$d->query("DELETE FROM ".SupportCenterDB::table_department()." WHERE id='".$d->escapeString($id)."' LIMIT 1;");
	}
	static function department_get($id) {
		$d=OW::getDbo();
		$res=$d->queryForList("SELECT * FROM ".SupportCenterDB::table_department()." WHERE id='".$d->escapeString($id)."';");
		return $res[0];
	}
	static function department_name($id){
		$d=OW::getDbo();
		$res=$d->query("SELECT text FROM ".SupportCenterDB::table_department()." WHERE id='$id';");
		return $res;
	}
	static function department_get_users($id) {
		$d=OW::getDbo();
		return $d->queryForList("SELECT user_id FROM ".SupportCenterDB::table_department_user()." WHERE department_id='".$d->escapeString($id)."';");
	}
	static function get_department_by_user( $uid = null )
	{
		$uid = empty($uid) ? OW::getUser()->getId() : $uid;

		$sql = "SELECT `department_id` FROM ".self::table_department_user()." WHERE `user_id` = {$uid}";
		$result = OW::getDbo()->queryForColumn($sql);
	
		return $result;
	}

	static function checkExistingMod($uid = null){
		$uid = empty($uid) ? OW::getUser()->getId() : $uid;

		$sql = "SELECT `user_id` FROM ".self::table_department_user()." WHERE `user_id` = {$uid}";
		$result = OW::getDbo()->queryForColumn($sql);
	
		return $result;


	}

	static function department_user_add($id,$user_id) {
		$d=OW::getDbo();
		$d->query("INSERT INTO ".SupportCenterDB::table_department_user()." (department_id,user_id) VALUES ('".$d->escapeString($id)."','".$d->escapeString($user_id)."');");
	}
	static function department_user_delete($id,$user_id) {
		$d=OW::getDbo();
		$d->query("DELETE FROM ".SupportCenterDB::table_department_user()." WHERE department_id='".$d->escapeString($id)."' AND user_id='".$d->escapeString($user_id)."' LIMIT 1;");
	}
	/********************** FAQ DATABASE FUNCTIONS  ********************************8*/
	static function delete_faq($id) {
		$d=OW::getDbo();
		$d->query("DELETE FROM ".SupportCenterDB::table_faq()." WHERE id='".$d->escapeString($id)."' LIMIT 1;");
	}

	static function new_faq($faq){

		$d = OW::getDbo();

		$d->query("INSERT INTO ".SupportCenterDB::table_faq()." (id,question,answer,position) VALUES (
			'".$d->escapeString($faq["id"])."',
			'".$d->escapeString($faq["question"])."',
			'".$d->escapeString($faq["answer"])."',
			'".$d->escapeString($faq["position"])."');");
			return $d->getInsertId();

	}
	static function faq_update($faq) {
		$d=OW::getDbo();
		$d->query("UPDATE ".SupportCenterDB::table_faq()." SET ".
			"question='".$d->escapeString($faq["question"])."', ".
			"answer='".$d->escapeString($faq["answer"])."' ".
			"WHERE id='".$d->escapeString($faq["id"])."';");
	}
	static function get_faqs(){

		$d = OW::getDbo();

		return $d->queryForList("SELECT id,question,answer,position FROM ".SupportCenterDB::table_faq()." ORDER BY id ASC");
	}
	static function get_faq($id){

		$d = OW::getDbo();
		$res=$d->queryForList("SELECT * FROM ".SupportCenterDB::table_faq()." WHERE id='".$d->escapeString($id)."';");
		return $res[0];
	
	}

}


