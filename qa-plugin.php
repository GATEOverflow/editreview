<?php

if(!defined('QA_VERSION'))
{
header('Location: ../../');
exit;
}

// admin
qa_register_plugin_module('module', 'editreview-admin.php', 'editreview_admin', 'editreview Admin');

// layer 
qa_register_plugin_layer('editreview-layer.php', 'editreview layer');

// event
//qa_register_plugin_module('event', 'editreview-event.php', 'editreview_event', 'editreview Event');

// lang
qa_register_plugin_phrases('editreview-lang.php', 'editreview');

// edit page
qa_register_plugin_module('page', 'editreview-page.php', 'editreview_page', 'editreview Page');

// ajax page
qa_register_plugin_module('page', 'editreview-ajax-page.php', 'editreview_ajax_page', 'editreview Ajax Page');

function custom_save_editreview($userid, $postid, $title, $content, $tags, $format, $categoryid, $edittime, $extra=null, $notify=null, $name=null)
{				  
	$tags = rtrim($tags," ");
	$tags = rtrim($tags,",");
	
	qa_db_query_sub('
		INSERT INTO `^q_editreview` (`userid`, `postid`, `title`, `content`, `tags`, `format`, `categoryid`, `edittime`, `extra`, `notify`, `name`) 
		VALUES (#, #, $, $, $, $, #, $, $, $, $)
		ON DUPLICATE KEY
		UPDATE `userid`=#, `title`=$, `content`=$, `tags`=$, `format`=$, `categoryid`=#, `edittime`=$, `extra`=$, `notify`=$, `name`=$
	', 
	$userid, $postid, $title, $content, $tags, $format, $categoryid, $edittime, $extra, $notify, $name,
	$userid, $title, $content, $tags, $format, $categoryid, $edittime, $extra, $notify, $name
	);
	
	return qa_db_last_insert_id();
	
}

//idle now
function custom_update_editreview($editid, $userid, $postid, $title, $content, $tags, $format, $categoryid, $edittime, $extra=null, $notify=null, $name=null)
{	
	$tags = rtrim($tags," ");
	$tags = rtrim($tags,",");
	
	qa_db_query_sub(
			"UPDATE ^q_editreview
			SET userid=#, postid=$, title=$, content=$, tags=$, format=$, categoryid=#, edittime=$, extra=$, notify=$, name=$
			WHERE editid = #",
			$userid, $postid, $title, $content, $tags, $format, $categoryid, $edittime, $extra, $notify, $name,
			$editid
		);
} 

function custom_get_editreview($postid) {
	$records = qa_db_read_one_assoc(qa_db_query_sub(
				'SELECT *
				FROM ^q_editreview
				WHERE postid = #',
				$postid
			 ), true);
	
	return $records;
}

function custom_get_orgpost($postid) {
	$records = qa_db_read_one_assoc(qa_db_query_sub(
				'SELECT *
				FROM ^posts
				WHERE postid = #',
				$postid
			 ), true);

	 return $records;
}

function show_changes($org, $edit){
	$diff = strcmp($org, $edit) !== 0;
	if( $diff ){
		$from_start = strspn($org ^ $edit, "\0");        
		$from_end = strspn(strrev($org) ^ strrev($edit), "\0");

		$org_end = strlen($org) - $from_end;
		$edit_end = strlen($edit) - $from_end;

		$start = substr($edit, 0, $from_start);
		$end = substr($edit, $edit_end);
		$new_diff = substr($edit, $from_start, $edit_end - $from_start);  
		$old_diff = substr($org, $from_start, $org_end - $from_start);

		$edit = "$start<ins style='background-color:#ccffcc'>$new_diff</ins>$end";
		$org = "$start<del style='background-color:#ffcccc'>$old_diff</del>$end";
		return array("org"=>$org, "edit"=>$edit);
	} else {
     return array("org"=>$org, "edit"=>$org);
    }
}

function custom_get_editreview_postids() {
	$records = qa_db_read_all_values(qa_db_query_sub(
				"SELECT postid
				FROM ^q_editreview"
			));
	
	return $records;
}

function custom_get_editreview_by_editid($editid) {
	$records = qa_db_read_one_assoc(qa_db_query_sub(
				'SELECT *
				FROM ^q_editreview
				WHERE editid = #',
				$editid
			 ), true);
	
	return $records;
}

function custom_delete_editreview($editid)
{
	qa_db_query_sub(
		"DELETE
		FROM ^q_editreview
		WHERE editid = #",
		$editid);
}

function custom_accept_editreview($editid) {	
	$editdata = custom_get_editreview_by_editid($editid);
	$pre_editdata = custom_get_orgpost($editdata['postid']);	
	require_once QA_INCLUDE_DIR . 'app/posts.php';
	require_once QA_INCLUDE_DIR . 'app/users.php';		
	qa_question_set_content($pre_editdata, $editdata['title'], $editdata['content'], $editdata['format'], null, $editdata['tags'], true, $editdata['userid'], qa_userid_to_handle($editdata['userid']), $cookieid = null, $extravalue = null, $name = null, $remoderate = false, $silent = false);
}

function custom_get_categoryname($categoryid)
{
	$result = qa_db_read_one_assoc(qa_db_query_sub(
			"SELECT title, tags, backpath
			FROM ^categories
			WHERE categoryid = #",
		$categoryid), false);
		
	return $result;
}

/*function custom_user_level_options($from, $to)
{
	require_once QA_INCLUDE_DIR.'qa-app-users.php';
	$options = array(
		QA_USER_LEVEL_BASIC => qa_lang_html('users/registered_user'),
		QA_USER_LEVEL_APPROVED => qa_lang_html('users/approved_user'),
		QA_USER_LEVEL_EXPERT => qa_lang_html('users/level_expert'),
		QA_USER_LEVEL_EDITOR => qa_lang_html('users/level_editor'),
		QA_USER_LEVEL_MODERATOR => qa_lang_html('users/level_moderator'),
		QA_USER_LEVEL_ADMIN => qa_lang_html('users/level_admin'),
		QA_USER_LEVEL_SUPER => qa_lang_html('users/level_super'),
	);
	
	$from = isset($from) ? $from : QA_USER_LEVEL_BASIC; 
	$to = isset($to) ? $to : QA_USER_LEVEL_SUPER;
	
	foreach ($options as $key => $label) {
		if ($key < $from || $key > $to)
			unset($options[$key]);
	}

	return $options;
}*/
