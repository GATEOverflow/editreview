<?php

class editreview_event
{
	public function process_event($event, $userid, $handle, $cookieid, $params)
	{
		// catch only q edits
		/*if ($event == 'q_edit') {
			$edittime = date('Y-m-d H:i:s');
			$categoryid = @$params['oldquestion']['categoryid'];
			$notify = @$params['oldquestion']['notify'];			
			$titlechanged = @$params['titlechanged'];
			$contentchanged = @$params['contentchanged'];
			$tagschanged = @$params['tagschanged'];
			
			custom_store_pre_edit($userid, $params['postid'], @$params['oldtitle'], @$params['oldcontent'], @$params['oldtags'], @$params['oldformat'], $categoryid, $edittime, $params['extra'], $notify, $params['name'], $titlechanged, $contentchanged, $tagschanged);
		}*/
	}
}