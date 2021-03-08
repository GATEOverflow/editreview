<?php

class editreview_ajax_page
{
	
	var $directory;
	var $urltoroot;
	
	function load_module($directory, $urltoroot)
	{
		$this->directory = $directory;
		$this->urltoroot = $urltoroot;
	}
	
	// for display in admin interface under admin/pages
	function suggest_requests() 
	{	
		return array(
			array(
				'title' => 'Edit/Review Ajax Page', // title of page
				'request' => 'ajax-editreview', // request name
				'nav' => 'null', // 'M'=main, 'F'=footer, 'B'=before main, 'O'=opposite main, null=none
			),
		);
	}
	
	// for url query
	function match_request($request)
	{
		if ($request=='ajax-editreview') 
		{
			return true;
		}
		return false;
	}
	
	function process_request($request)
	{	
		// only logged in users
		if(!qa_is_logged_in())
		{
			exit();
		}
		
		// receving data by AJAX post
		$erdata = qa_post_text('ajaxdrdata');
		
		if(!empty($erdata)) 
		{
			$editdata = json_decode($erdata, true);
			$editdata = str_replace('&quot;', '"', $editdata);
			
			$formdata = '';
			foreach ($editdata['formdata'] as $key => $value) {
				$formdata[$value['name']] = $value['value'];
			}
			
			$postid = isset($editdata['postid']) ? $editdata['postid'] : null;
			$userid = isset($editdata['userid']) ? $editdata['userid'] : null;
			
			$title = isset($formdata['q_title']) ? $formdata['q_title'] : null;
			$content = isset($formdata['q_content']) ? qa_sanitize_html($formdata['q_content']) : null;
			$tags = isset($formdata['q_tags']) ? str_replace(', ',',',$formdata['q_tags']) : null;
			$format = isset($formdata['q_editor']) ? 'html' : '';
			$categoryid = isset($formdata['q_category_1']) ? $formdata['q_category_1'] : null;
			$extra = isset($formdata['extra']) ? $formdata['extra'] : null;	
			$notify = isset($formdata['q_notify']) ? $formdata['q_notify'] : null;
			$name = isset($formdata['name']) ? $formdata['name'] : null;
			$edittime = date('Y-m-d H:i:s');
			
						
			if( empty($userid) || empty($title) || empty($content) ) {
				$reply = array( 'error' => "userid, title, content cannot be empty" );
				echo json_encode( $reply );
				return;
			} else {
				custom_save_editreview($userid, $postid, $title, $content, $tags, $format, $categoryid, $edittime, $extra=null, $notify=null, $name=null);
				$reply = 'Your edition has been successfully submited for review.';
					echo json_encode( $reply );
					return;
			}     										
		} // END AJAX RETURN
		else 
		{
			echo 'Unexpected error. No data transferred.';
			exit();
		}
		
		return;
	} // end process_request	
}; // END