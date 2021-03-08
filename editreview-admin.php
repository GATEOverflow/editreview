<?php
/*
	Plugin Name: EditReview Posts
*/

class editreview_admin
{
	// initialize db-table 'eventlog' if it does not exist yet
	public function init_queries($tableslc) 
	{	
		$table1 = qa_db_add_table_prefix('q_editreview');
				
		if(!in_array($table1, $tableslc)) 
		{		
			return '
				CREATE TABLE `^q_editreview` (
				  `editid` int(10) NOT NULL AUTO_INCREMENT,
				  `userid` int(10) UNSIGNED NOT NULL,
				  `postid` int(10) UNSIGNED NOT NULL,
				  `title` varchar(800) DEFAULT NULL,
				  `content` varchar(12000) DEFAULT NULL,
				  `tags` varchar(800) DEFAULT NULL,
				  `format` varchar(800) NULL,
				  `categoryid` INT UNSIGNED,
				  `edittime` datetime NULL,
				  `extra` varchar(800) DEFAULT NULL,				  
				  `notify` varchar(80) NULL,	
				  `name` varchar(40) NULL,
				  PRIMARY KEY (editid),
				  UNIQUE (postid)
				) 
				ENGINE=MyISAM DEFAULT CHARSET=utf8;
			';
		}
				
		return null;
		
	} 

	// option's value is requested
	public function option_default($option) 
	{
		switch($option) 
		{
			case 'editreview_enabled':
			 	return 1;
			case 'editreview_exclude_css':
			 	return 0;
			case 'editreview_userlevel':
			 	return QA_USER_LEVEL_BASIC;
			case 'editreview_modlevel':
			 	return QA_USER_LEVEL_ADMIN;
			default:
				return null;
		}
	}
	
	public function allow_template($template)
	{
		return ($template!='admin');
	}       
		
	public function admin_form(&$qa_content)
	{
		// process the admin form when admin hits save button
		$saved = qa_clicked('editreview_save');
		
		require QA_INCLUDE_DIR.'qa-app-users.php';
		$permitoptions=custom_user_level_options(QA_USER_LEVEL_BASIC, QA_USER_LEVEL_SUPER);
		$permitoptions_mod=custom_user_level_options(QA_USER_LEVEL_EXPERT, QA_USER_LEVEL_SUPER);

		if ($saved) {
			qa_opt('editreview_enabled', (bool)qa_post_text('editreview_enabled_field')); // empty or 1
			qa_opt('editreview_exclude_css', (bool)qa_post_text('editreview_exclude_css_field')); // empty or 1
			qa_opt('editreview_userlevel', qa_post_text('editreview_userlevel_field'));		
			qa_opt('editreview_modlevel', qa_post_text('editreview_modlevel_field'));				
		}
		
		// form fields to display frontend for admin
		$fields = array();
		
		$fields[] = array(
			'type' => 'checkbox',
			'tags' => 'name="editreview_enabled_field" id="editreview_enabled_field"',
			'label' => qa_lang('editreview/enable_plugin'),
			'value' => qa_opt('editreview_enabled'),
		);
		
		$fields[] = array(
			'type' => 'checkbox',
			'tags' => 'name="editreview_exclude_css_field" id="editreview_exclude_css_field"',
			'label' => qa_lang('editreview/editreview_exclude_css'),
			'value' => qa_opt('editreview_exclude_css'),
			'note' => qa_lang('editreview/editreview_exclude_css_note'),
		);
				
		$fields[] = array(
			'type' => 'select',
			'label' => qa_lang('editreview/user_level'),
			'tags' => 'name="editreview_userlevel_field" id="editreview_userlevel_field"',
			'options' => $permitoptions,
			'value' => @$permitoptions[qa_opt('editreview_userlevel')],
			'note' => qa_lang('editreview/user_level_note'),
		);
		
		$fields[] = array(
			'type' => 'select',
			'label' => qa_lang('editreview/moderator_level'),
			'tags' => 'name="editreview_modlevel_field" id="editreview_modlevel_field"',
			'options' => $permitoptions_mod,
			'value' => @$permitoptions_mod[qa_opt('editreview_modlevel')],
			'note' => qa_lang('editreview/moderator_level_note'),
		);
		
		return array(
			'ok' => $saved ? 'Settings saved' : null,
			
			'fields' => $fields,
			
			'buttons' => array(
				array(
					'label' => qa_lang_html('main/save_button'),
					'tags' => 'name="editreview_save"',
				),
			),
		);
	}
	
} // END editreview_admin

