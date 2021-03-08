<?php

class qa_html_theme_layer extends qa_html_theme_base
{	
	function head_script()
	{
		qa_html_theme_base::head_script();
			
		if( qa_opt('editreview_enabled') && qa_get_logged_in_level() >= qa_opt('editreview_userlevel') && $this->template=='question' && strpos(qa_get_state(), "edit-") !== false )
		{			
			$postid = $this->content['q_view']['raw']['postid'];
			
			$this->output('
				<script>
					var editreviewAjaxURL = "'.qa_path('ajax-editreview').'";
					var postid = "'.$postid.'";
					var editorid = "'.qa_get_logged_in_userid().'";
				</script>
			');  
			
			$this->output('<script type="text/javascript" src="'.QA_HTML_THEME_LAYER_URLTOROOT.'editreview-ajax.js"></script>');
		}		
	}
	
	function head_custom()
	{
		parent::head_custom();
		$hidecss = qa_opt('editreview_exclude_css') === '1';

		if( !$hidecss && qa_opt('editreview_enabled') && qa_is_logged_in() && qa_get_logged_in_level() >= qa_opt('editreview_modlevel') ) {
			$editreview_css1 = '
				<style>				
					.qa-dr-list {
						margin: 0px -20px;
					}
					.qa-dr-list .qa-q-list-item {
						margin-bottom:5px;
						padding:20px;
						width:100%;
					}
					.qa-dr-list .qa-q-item-tag-item {
						display:inline;
						margin-right:5px;
					}
					.qa-dr-list .pe_mod_buttons {
						float: right;
					}
					.qa-dr-list .pe_mod_buttons button {
						font-size: 12px;
						padding: 5px 10px;
					}
					.qa-dr-list .pe_mod_buttons button#pe_reject {
						background: #F44336;
					}
					.qa-dr-list .pe_mod_buttons button#pe_accept {
						background: #1ecc25;
					}
				</style>';
				
			$editreview_css2 = '
				<style>				
					a.see-edit {
						background: #FF9800;
						color: #fff;
						padding: 2px 6px;
						text-decoration: none;
					}
					a.see-edit:hover {
						background: #fba606;
					}
				</style>';
			
			if(qa_request_part(0) == 'see-edit'){//css on edit review page
				$this->output_raw( $editreview_css1 );
			}
			if( $this->template=='question' || $this->template=='qa' || $this->template=='questions' || $this->template=='unanswered' ){//css on question pages
				$this->output_raw( $editreview_css2 );
			}	
		}	
	}
		
	public function form($form)	{
		if( qa_opt('editreview_enabled') && qa_get_logged_in_level() >= qa_opt('editreview_userlevel') && $this->template=='question' && strpos(qa_get_state(), "edit-") !== false && isset($form['buttons']['save']) && qa_get_logged_in_level() < qa_opt('permit_edit_q') ){	
			if( !empty($form) && isset($form['tags']) ) {
				$form['tags'] .= ' name="q_editreview" id="q_editreview"';
			}			
			unset($form['buttons']['save']);
			$editreview_button = array('editreview' => array(
											'tags' => 'name="doeditreview" id="doeditreview" data-postid="'.$this->content['q_view']['raw']['postid'].'" data-editorid="'.qa_get_logged_in_userid().'"', 
											'label' => 'Submit Edit', 
											'id' => 'editreview')
						);

			$form['buttons'] = $editreview_button + $form['buttons'];
			$form['hidden']['q_dosave']=0;
			$form['hidden']['q_doeditreview']=1;
			$form['hidden']['postid']=$this->content['q_view']['raw']['postid'];	
			$form['hidden']['editorid']=qa_get_logged_in_userid();				
		}
		qa_html_theme_base::form($form);
	}

	
	public function post_meta($post, $class, $prefix=null, $separator='<br/>')
	{
		if( qa_opt('editreview_enabled') && qa_get_logged_in_level() >= qa_opt('editreview_modlevel') && $this->template=='question' ){
			// show link if pending edit
			$pending_editids=custom_get_editreview_postids();
			
			if(in_array($post['raw']['postid'],$pending_editids)) {
				$url = qa_path_html('see-edit', array('postid'=> $post['raw']['postid']));
				$post['who']['suffix'] .= ' <a href="'.$url.'" class="see-edit">'.qa_lang('editreview/see_edit').'</a>';
			}
		}
		parent::post_meta($post, $class, $prefix, $separator);
	}
	
	public function q_item_main($q_item)
	{
		if(qa_opt('editreview_enabled') && qa_get_logged_in_level() >= qa_opt('editreview_modlevel') &&  ($this->template=='qa' || $this->template=='questions' || $this->template=='unanswered') && isset($this->content['q_list'])) {
			$pending_editids=custom_get_editreview_postids();		
			if(in_array($q_item['raw']['postid'],$pending_editids)) {
				$url = qa_path_html('see-edit', array('postid'=> $q_item['raw']['postid']));
				$q_item['who']['suffix'] .= '<span class="see-edit"><a href="'.$url.'" class="see-edit">'.qa_lang('editreview/see_edit').'</a></span>';
			}
		}
		parent::q_item_main($q_item);
	}
}
