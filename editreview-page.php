<?php

class editreview_page
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
				'title' => qa_lang('editreview/pending_edits_title'), // title of page
				'request' => 'see-edit', // request name
				'nav' => 'null', // 'M'=main, 'F'=footer, 'B'=before main, 'O'=opposite main, null=none
			),
		);
	}
	
	// for url query
	function match_request($request)
	{
		if ($request=='see-edit') 
		{
			return true;
		}
		return false;
	}
	function process_request($request)
	{	
		/* start */
		$qa_content=qa_content_prepare();
		
		// only allowed in users
		if( qa_opt('editreview_enabled') && qa_is_logged_in() && qa_get_logged_in_level() >= qa_opt('editreview_modlevel') ){				
			$qa_content['head_lines'][]= '<style>.qa-dr-list{margin:0px -20px}.qa-dr-list .qa-q-list-item{margin-bottom:5px;padding:20px;width:100%}.qa-dr-list .qa-q-item-tag-item{display:inline;margin-right:5px}.qa-part-custom{background:unset;padding:0 20px}.edit-changes span{display:block;font-size:12px} span.change_0{background:#f3f3f3}span.change_1{background:#d1ff9c}a.see-edit{background:#FF9800;color:#fff;	padding:2px 6px;text-decoration:none;}a.see-edit:hover{background:#fba606;}.qa-dr-list .pe_mod_buttons{float: right}.qa-dr-list .pe_mod_buttons button{font-size:12px;padding:5px 10px}.qa-dr-list .pe_mod_buttons button#pe_reject{background:#F44336}.qa-dr-list .pe_mod_buttons button#pe_accept{background:#1ecc25}</style>';
		
			$postid	= qa_get('postid');
			

			if( isset($postid) && $postid > 0 ){
				$edit_data = custom_get_editreview($postid);
				$post_data = custom_get_orgpost($postid);
				
				$qa_content['title'] = qa_lang('editreview/edit_version_title'); // title
				
				$fields = '';				
				$fields .= '<div class="qa-dr-list"><div class="qa-q-list-item">';
				
				if(isset($edit_data)){
					$edit_category=custom_get_categoryname($edit_data['categoryid']);
					$edit_handle=qa_userid_to_handle($edit_data['userid']);
					$tags_array=explode(',', $edit_data['tags']);
					
					$org_title = $post_data['title'];
					$edit_title = $edit_data['title'];
					$changes_title = show_changes($org_title, $edit_title);
					
					$org_content = strip_tags($post_data['content']);
					$edit_content = strip_tags($edit_data['content']);					
					$changes_content = show_changes($org_content, $edit_content);

					$titlechanged = strcmp($org_title, $edit_title) !== 0;
					$contentchanged = strcmp($org_content, $edit_content) !== 0;
					$tagschanged = strcmp($post_data['tags'], $edit_data['tags']) !== 0;				
									
					$fields .= '<span class="pe_mod_buttons">
									<form method="post" action="'.qa_self_html().'" name="peform_accept">
										<input type="hidden" name="pe_accept_id" value="'.$edit_data['editid'].'" />
										<input type="hidden" name="pe_postid" value="'.$edit_data['postid'].'" />
										<button id="pe_accept" name="pe_accept" type="submit" class="qa-form-tall-button qa-form-tall-button-peaccept">'.qa_lang('editreview/accept_edit').'</button>
									</form>
									<form method="post" action="'.qa_self_html().'" name="peform_reject">
										<input type="hidden" name="pe_reject_id" value="'.$edit_data['editid'].'" />
										<input type="hidden" name="pe_postid" value="'.$edit_data['postid'].'" />
										<button id="pe_reject" name="pe_reject" type="submit" class="qa-form-tall-button qa-form-tall-button-pereject">'.qa_lang('editreview/reject_edit').'</button>
									</form>
								</span>
					
								<div class="qa-q-item-title">
									<a href="./'.$postid.'">'.$changes_title['edit'].'</a>
								</div>
								<div class="qa-q-item-content">'.$changes_content['edit'].'</div>
								<span class="qa-q-item-avatar-meta">
									<span class="qa-q-item-meta">
										<span class="qa-q-item-what">edited on</span>
										<span class="qa-q-item-when">
											<span class="qa-q-item-when-data">'.$edit_data['edittime'].'</span>
										</span>
										<span class="qa-q-item-where">
											<span class="qa-q-item-where-pad">in </span>
											<span class="qa-q-item-where-data"><a href="./questions/'.$edit_category['backpath'].'" class="qa-category-link">'.$edit_category['title'].'</a></span>
										</span>
										<span class="qa-q-item-who">
											<span class="qa-q-item-who-pad">by </span>
											<span class="qa-q-item-who-data"><a href="./user/'.$edit_handle.'" class="qa-user-link">'.$edit_handle.'</a></span>
										</span>
									</span>
								</span>
								<div class="qa-q-item-tags">
									<ul class="qa-q-item-tag-list">';
		
						foreach ($tags_array as $tag) {
							$fields .= '<li class="qa-q-item-tag-item"><a class="qa-tag-link">'.$tag.'</a></li>';
						}				
					
						$fields .= '</ul>
								</div>
								<div class="edit-changes">
									<span class="change_'.$titlechanged.'">TITLE: '.qa_lang('editreview/has_changed_'.$titlechanged.'').'</span>
									<span class="change_'.$contentchanged.'">CONTENT: '.qa_lang('editreview/has_changed_'.$contentchanged.'').'</span>
									<span class="change_'.$tagschanged.'">TAGS: '.qa_lang('editreview/has_changed_'.$tagschanged.'').'</span>
								</div>';
				} else {
					$fields .= qa_lang('editreview/edit_version_na');
				}
				
				$fields .= '</div></div>';// closing tags for pre-edit version content
					
				$post_category=custom_get_categoryname($post_data['categoryid']);
				$post_handle=qa_userid_to_handle($post_data['userid']);
				$post_tags_array=explode(',', $post_data['tags']);
				
				$post_org_title = isset($org_title) ? $org_title : $post_data['title'];
				$post_org_content = isset($org_content) ? $org_content : strip_tags($post_data['content']);
				
				$fields .= '<div class="qa-dr-list"><div class="qa-main-heading"><h1>'.qa_lang('editreview/current_version_title').'</h1></div>';		
					$fields .= '<div class="qa-q-list-item">
									<div class="qa-q-item-title"><a href="./'.$postid.'">'.$post_org_title.'</a></div>
									<div class="qa-q-item-content">'.$post_org_content.'</div>
									<span class="qa-q-item-avatar-meta">
										<span class="qa-q-item-meta">
											<span class="qa-q-item-what">posted on</span>
											<span class="qa-q-item-when">
												<span class="qa-q-item-when-data">'.$post_data['created'].'</span>
											</span>
											<span class="qa-q-item-where">
												<span class="qa-q-item-where-pad">in </span>
												<span class="qa-q-item-where-data"><a href="./questions/'.$post_category['backpath'].'" class="qa-category-link">'.$post_category['title'].'</a></span>
											</span>
											<span class="qa-q-item-who">
												<span class="qa-q-item-who-pad">by </span>
												<span class="qa-q-item-who-data"><a href="./user/'.$post_handle.'" class="qa-user-link">'.$post_handle.'</a></span>
											</span>
										</span>
									</span>
									<div class="qa-q-item-tags">
										<ul class="qa-q-item-tag-list">';
		
							foreach ($post_tags_array as $post_tag) {
								$fields .= '<li class="qa-q-item-tag-item"><a class="qa-tag-link">'.$post_tag.'</a></li>';
							}	
					
							$fields .= '</ul>
									</div>
								</div>';	
				$fields .= '</div>';// closing tags for dr-list	
				$qa_content['custom'] = $fields;				
			} else {
				$pending_edit_ids = custom_get_editreview_postids();
				$qa_content['title'] = qa_lang('editreview/pending_edits_title'); // title
				
				$ofields = '';
				$ofields .= '<div class="qa-dr-list" style="margin:-20px;background:#ecf0f1">';	
				
				foreach($pending_edit_ids as $pe_edit_id) {
					$pe_post_data = custom_get_orgpost($pe_edit_id);					
					$pe_category=custom_get_categoryname($pe_post_data['categoryid']);
					$pe_handle=qa_userid_to_handle($pe_post_data['userid']);
					$pe_tags_array=explode(',', $pe_post_data['tags']);
					
					$pe_url = qa_path_html('see-edit', array('postid'=> $pe_post_data['postid']));
					
					$ofields .= '<div class="qa-q-list-item">
									<div class="qa-q-item-title"><a href="./'.$pe_post_data['postid'].'">'.$pe_post_data['title'].'</a></div>
									<div class="qa-q-item-content">'.$pe_post_data['content'].'</div>
									<span class="qa-q-item-avatar-meta">
										<span class="qa-q-item-meta">
											<span class="qa-q-item-what">posted on</span>
											<span class="qa-q-item-when">
												<span class="qa-q-item-when-data">'.$pe_post_data['created'].'</span>
											</span>
											<span class="qa-q-item-where">
												<span class="qa-q-item-where-pad">in </span>
												<span class="qa-q-item-where-data"><a href="./questions/'.$pe_category['backpath'].'" class="qa-category-link">'.$pe_category['title'].'</a></span>
											</span>
											<span class="qa-q-item-who">
												<span class="qa-q-item-who-pad">by </span>
												<span class="qa-q-item-who-data"><a href="./user/'.$pe_handle.'" class="qa-user-link">'.$pe_handle.'</a></span>
												<span class="qa-q-item-who-pad"><a href="'.$pe_url.'" class="see-edit">'.qa_lang('editreview/see_edit').'</a></span>
											</span>
										</span>
									</span>
									<div class="qa-q-item-tags">
										<ul class="qa-q-item-tag-list">';		
										foreach ($pe_tags_array as $tag) {
											$ofields .= '<li class="qa-q-item-tag-item"><a class="qa-tag-link">'.$tag.'</a></li>';
										}											
										$ofields .= '</ul>
									</div>
								</div>';
				}
			
				$ofields .= '</div>';
			
				$qa_content['custom_1'] = $ofields;
			}		
		} else {
			$qa_content['error'] = qa_lang('editreview/you_dont_have_permission_view'); // error
		}
		
		if (qa_clicked('pe_accept')){
			$pe_accept_id = qa_post_text('pe_accept_id');
			$pe_postid = qa_post_text('pe_postid');
			custom_accept_editreview($pe_accept_id);//validate edit
			custom_delete_editreview($pe_accept_id);//after publishing edit version, delete related edit row in custom database table
			//header("Refresh:0");
			$post_url = qa_path_html($pe_postid);
			qa_redirect_raw($post_url);	
		} elseif (qa_clicked('pe_reject')){
			$pe_reject_id = qa_post_text('pe_reject_id');
			$pe_postid = qa_post_text('pe_postid');
			custom_delete_editreview($pe_reject_id);//after rejection of edit version, delete related edit row in custom database table
			//header("Refresh:0");
			$post_url = qa_path_html($pe_postid);
			qa_redirect_raw($post_url);	
		}
		
		return $qa_content;
	} // end process_request
	
}; // END