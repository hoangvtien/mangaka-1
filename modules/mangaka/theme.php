<?php

/**
 * @Project MANGA ON NUKEVIET 4.x
 * @Author KENNYNGUYEN (nguyentiendat713@gmail.com)
 * @License GNU/GPL version 2 or any later version
 * @Createdate 15/07/2015 10:51
 */

if( ! defined( 'NV_IS_MOD_MANGAKA' ) ) die( 'Stop!!!' );

function viewcat_list( $array_cat_block, $catid, $viewcat_img, $viewcat_rating, $content_comment)
{
	global $module_name, $module_file, $lang_module, $module_config, $module_info, $global_array_cat, $my_head, $client_info;
	
	$xtpl = new XTemplate( 'viewcat_list.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'IMGWIDTH1', $module_config[$module_name]['homewidth'] );
	$xtpl->assign( 'SELFURL', $client_info['selfurl'] );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'MODULE_THEME', $module_info['template'] );
	$xtpl->assign( 'CATID', $catid );
	$xtpl->assign( 'CHECKSS', $viewcat_rating['checkss'] );

	// Show Category Infomation
	if( $viewcat_img ) // Home image
	{
		$xtpl->assign( 'HOMEIMG1', $viewcat_img );
		$xtpl->parse( 'main.viewdescription.image' );
	}
	if( !empty($global_array_cat[$catid]['last_update'] ))
	{
		$xtpl->assign( 'LAST_UPDATE', nv_date( 'd/m/Y H:i:s', $global_array_cat[$catid]['last_update'] ));
		$xtpl->parse( 'main.viewdescription.last_update' );
	}
	if(!empty( $global_array_cat[$catid]['authors'] ))
	{
		$xtpl->assign( 'AUTHOR',$global_array_cat[$catid]['authors'] );
		
	} else {
		$global_array_cat[$catid]['authors'] = $lang_module['updating'];
		$xtpl->assign( 'AUTHOR',$global_array_cat[$catid]['authors'] );
	}
	$xtpl->parse( 'main.viewdescription.authors' );
	
	if(!empty( $global_array_cat[$catid]['translators'] ))
	{
		$xtpl->assign( 'TRANSLATOR',$global_array_cat[$catid]['translators'] );
		
	} else {
		$global_array_cat[$catid]['translators'] = $lang_module['updating'];
		$xtpl->assign( 'TRANSLATOR',$global_array_cat[$catid]['translators'] );
	}
	$xtpl->parse( 'main.viewdescription.translators' );	
	
	if(!empty( $global_array_cat[$catid]['titlesite'] ))
	{
		$xtpl->assign( 'TITLESITE',$global_array_cat[$catid]['titlesite'] );
		
	} else {
		$global_array_cat[$catid]['titlesite'] = $lang_module['updating'];
		$xtpl->assign( 'TITLESITE',$global_array_cat[$catid]['titlesite'] );
	}
	
	$xtpl->parse( 'main.viewdescription.titlesite' );
	if( !empty($array_cat_block) )
	{
		foreach( $array_cat_block as $array_cat_block_i )
		{
			$xtpl->assign( 'GENRE', $array_cat_block_i );
			$xtpl->parse( 'main.viewdescription.genre.genre_loop' );
		}
		$xtpl->parse( 'main.viewdescription.genre' );
	}

	if( $global_array_cat[$catid]['allowed_rating'] == 1 ) // Rating mode
	{
		$xtpl->assign( 'LANGSTAR', $viewcat_rating['langstar'] );
		$xtpl->assign( 'STRINGRATING', $viewcat_rating['stringrating'] );
		$xtpl->assign( 'NUMBERRATING', $viewcat_rating['numberrating'] );
		$xtpl->assign( 'CLICK_RATING', $global_array_cat[$catid]['click_rating'] );

		if( $viewcat_rating['disablerating'] == 1 )
		{
			$xtpl->parse( 'main.viewdescription.allowed_rating.disablerating' );
		}

		if( $viewcat_rating['numberrating'] >= $module_config[$module_name]['allowed_rating_point'] )
		{
			$xtpl->parse( 'main.viewdescription.allowed_rating.data_rating' );
		}
		$xtpl->parse( 'main.viewdescription.allowed_rating' );
	}
	
	$xtpl->assign( 'CONTENT', $global_array_cat[$catid] );
	$xtpl->parse( 'main.viewdescription' );
	

	if( !empty( $content_comment ) )
	{
		$xtpl->assign( 'CONTENT_COMMENT', $content_comment );
		$xtpl->parse( 'main.comment' );
		$xtpl->parse( 'main.comment_tab' );
	}
	//Comment system
	if( ! defined( 'FACEBOOK_JSSDK' ) )
	{
		global $meta_property;
		$lang = ( NV_LANG_DATA == 'vi' ) ? 'vi_VN' : 'en_US';
		$facebookappid = $module_config[$module_name]['facebookappid'];
		$facebookadminid = $module_config[$module_name]['facebookadminid'];
		$facebookcomment = $module_config[$module_name]['facebookcomment'];
		$xtpl->assign( 'FACEBOOK_LANG', $lang );
		$xtpl->assign( 'FACEBOOK_APPID', $facebookappid );

		if( ! empty( $facebookappid ) && ! empty( $facebookcomment ) ) // Neu co ca FB ID va cho phep comment FB
		{
			$meta_property['fb:app_id'] = $facebookappid; // MetaData cho FB ID
			$xtpl->parse( 'main.facebookjssdk' ); // Xuat SDK dung FB ID
			$xtpl->parse( 'main.fb_comment' );
			$xtpl->parse( 'main.fb_comment_tab' ); // Xuat Comment cua ID tuong ung
		} 
		else if(! empty( $facebookcomment )){   // Neu KHONG co FB ID va Cho phep comment FB
			$xtpl->parse( 'main.facebook_pubsdk' ); // Xuat SDK Public Facebook
			$xtpl->parse( 'main.fb_comment' );
			$xtpl->parse( 'main.fb_comment_tab' ); // Xuat FB Comment
		}
		if( ! empty( $facebookadminid ) ) // MetaData cho FB admin - quan ly comment
		{
			$meta_property['fb:admin_id'] = $facebookadminid;
		}
		define( 'FACEBOOK_JSSDK', true );
	}
	if( $module_config[$module_name]['socialbutton'] ) // Neu su dung cac nut like, share MXH
	{
		$xtpl->parse( 'main.socialbutton' );
	}
	if( $module_config[$module_name]['disqus_shortname'] ) // Neu su dung binh luan Disqus
	{
		$disqus_shortname = $module_config[$module_name]['disqus_shortname'];
		$xtpl->assign( 'DISQUS_SHORTNAME', $disqus_shortname );
		$xtpl->parse( 'main.disqus' );
		$xtpl->parse( 'main.disqus_tab' );
	}
	
	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}
// Phong cach trang chu BT.com
function viewcat_full_home( $array_catpage, $generate_page )
{
	global $global_array_cat, $module_name, $module_file, $lang_module, $module_config, $module_info, $catid, $page;

	$xtpl = new XTemplate( 'viewcat_full_home.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'IMGWIDTH1', $module_config[$module_name]['homewidth'] );
 
	foreach( $array_catpage as $array_row_i )
	{
		$array_row_i['link'] = $global_array_cat[$array_row_i['catid']]['link'];
		$xtpl->clear_autoreset();
		
		$array_row_i['descriptionhtml'] = strip_tags($array_row_i['descriptionhtml']);
		$array_row_i['descriptionhtml'] = nv_clean60($array_row_i['descriptionhtml'],450);
		if( !empty($array_row_i['last_update'] ))
		{
			$xtpl->assign( 'LAST_UPDATE', nv_date( 'd/m/Y H:i:s', $array_row_i['last_update'] ));
			$xtpl->parse( 'main.viewcatloop.last_update' );
		}
		
		if(!empty( $array_row_i['authors'] ))
		{
			$xtpl->assign( 'AUTHOR',$array_row_i['authors'] );
			
		} else {
			$array_row_i['authors'] = $lang_module['updating'];
			$xtpl->assign( 'AUTHOR',$array_row_i['authors'] );
		}
		$xtpl->parse( 'main.viewcatloop.authors' );
		
		if(!empty( $array_row_i['translators'] ))
		{
			$xtpl->assign( 'TRANSLATOR',$array_row_i['translators'] );
			
		} else {
			$array_row_i['translators'] = $lang_module['updating'];
			$xtpl->assign( 'TRANSLATOR',$array_row_i['translators'] );
		}
		$xtpl->parse( 'main.viewcatloop.translators' );
	
		$array_row_i['last_chap_update'] = "Update ".$lang_module['chapter']." ".$array_row_i['last_chapter'];
		if(!empty($array_row_i['last_update']))
		{
			$last_7days = $array_row_i['last_update'] + (86400*7);
		}

		if($last_7days >= NV_CURRENTTIME && ($array_row_i['last_chapter'] > 0))
		{
			$xtpl->assign( 'LAST_CHAP',$array_row_i['last_chap_update'] );
			$xtpl->parse( 'main.viewcatloop.last_chap' );
		}
		
		if( !empty( $array_row_i['bid'] ) )
		{
			foreach( $array_row_i['bid'] as $bid )
			{
				$xtpl->assign( 'BID', $bid );
				$xtpl->parse( 'main.viewcatloop.block_loop' );
			}
			$xtpl->parse( 'main.viewcatloop.block_icon' );
		}
		$xtpl->assign( 'CONTENT', $array_row_i );
		$xtpl->set_autoreset();
		$xtpl->parse( 'main.viewcatloop' );
	}

	if( ! empty( $generate_page ) )
	{
		$xtpl->assign( 'GENERATE_PAGE', $generate_page );
		$xtpl->parse( 'main.generate_page' );
	}

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}
// Phong cach trang chu 3T.com
function viewcat_list_home( $array_catpage, $generate_page )
{
	global $global_array_cat, $module_name, $module_file, $lang_module, $module_config, $module_info, $catid, $page;

	$xtpl = new XTemplate( 'viewcat_list_home.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'IMGWIDTH1', $module_config[$module_name]['homewidth'] );
	if( ($global_array_cat[$catid]['viewdescription'] and $page == 1) OR $global_array_cat[$catid]['viewdescription'] == 2 )
	{
		$xtpl->assign( 'CONTENT', $global_array_cat[$catid] );
		if( $global_array_cat[$catid]['image'] )
		{
			$xtpl->assign( 'HOMEIMG1', NV_BASE_SITEURL . NV_FILES_DIR . '/' . $module_name . '/' . $global_array_cat[$catid]['image'] );
			$xtpl->parse( 'main.viewdescription.image' );
		}
		$xtpl->parse( 'main.viewdescription' );
	}
	$a = 0;
 
	foreach( $array_catpage as $array_row_i )
	{
		$array_row_i['link'] = $global_array_cat[$array_row_i['catid']]['link'];
		$array_row_i['chapter'] = round($array_row_i['chapter'],1);
		$xtpl->clear_autoreset();
		$xtpl->assign( 'CONTENT', $array_row_i );

		if( $array_row_i['imghome'] != '' )
		{
			$xtpl->assign( 'HOMEIMG1', $array_row_i['imghome'] );
			$xtpl->assign( 'HOMEIMGALT1', ! empty( $array_row_i['homeimgalt'] ) ? $array_row_i['homeimgalt'] : $array_row_i['title'] );
			$xtpl->parse( 'main.viewcatloop.image' );
		}
	
		if( !empty( $array_row_i['bid'] ) )
		{
			foreach( $array_row_i['bid'] as $bid )
			{
				$xtpl->assign( 'BID', $bid );
				$xtpl->parse( 'main.viewcatloop.block' );
			}
		}
		
		$xtpl->set_autoreset();
		$xtpl->parse( 'main.viewcatloop' );
		++$a;
	}

	if( ! empty( $generate_page ) )
	{
		$xtpl->assign( 'GENERATE_PAGE', $generate_page );
		$xtpl->parse( 'main.generate_page' );
	}

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}
// Bai viet chi tiet
function detail_theme( $news_contents, $next_chapter, $previous_chapter, $list_chaps)
{
	global $global_config, $module_info, $lang_module, $module_name, $module_file, $module_config, $my_head, $lang_global, $user_info, $admin_info, $client_info, $global_array_cat, $catid;

	$xtpl = new XTemplate( 'detail.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG_GLOBAL', $lang_global );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'TEMPLATE', $global_config['module_theme'] );
	$xtpl->assign( 'LANG', $lang_module );

	$news_contents['addtime'] = nv_date( "d/m/Y H:i:s", $news_contents['addtime'] );

	$xtpl->assign( 'NEWSID', $news_contents['id'] );
	$xtpl->assign( 'NEWSCHECKSS', $news_contents['newscheckss'] );
	$xtpl->assign( 'DETAIL', $news_contents );
	$xtpl->assign( 'SELFURL', $client_info['selfurl'] );
	if (!empty($next_chapter))
	{
		$xtpl->assign( 'NEXT', $next_chapter );
		$xtpl->parse( 'main.next' );
		$xtpl->parse( 'main.next_top' );
	}
	if (!empty($previous_chapter))
	{
		$xtpl->assign( 'PREV', $previous_chapter );
		$xtpl->parse( 'main.pre' );
		$xtpl->parse( 'main.pre_top' );
	}

	if (!empty($list_chaps))
	{
		foreach ($list_chaps as $list_chap)
		{
			$list_chap['selected']=( $news_contents['id'] == $list_chap['id']?"selected":'' );
			$xtpl->assign( 'LIST_CHAP', $list_chap );
			$xtpl->parse( 'main.list_chap' );
			$xtpl->parse( 'main.list_chap_top' );
		}
	}
	
	if( $news_contents['allowed_rating'] == 1 )
	{
		$xtpl->assign( 'LANGSTAR', $news_contents['langstar'] );
		$xtpl->assign( 'STRINGRATING', $news_contents['stringrating'] );
		$xtpl->assign( 'NUMBERRATING', $news_contents['numberrating'] );

		if( $news_contents['disablerating'] == 1 )
		{
			$xtpl->parse( 'main.allowed_rating.disablerating' );
		}

		if( $news_contents['numberrating'] >= $module_config[$module_name]['allowed_rating_point'] )
		{
			$xtpl->parse( 'main.allowed_rating.data_rating' );
		}

		$xtpl->parse( 'main.allowed_rating' );
	}

	if( ! empty( $news_contents['post_name'] ) )
	{
		$xtpl->parse( 'main.post_name' );
	}

	if( ! empty( $news_contents['author'] ) )
	{
		if( ! empty( $news_contents['author'] ) )
		{
			$xtpl->parse( 'main.author.name' );
		}
		$xtpl->parse( 'main.author' );
	}
	
	$src = '';
	$array_data_content = explode('http://',$news_contents['bodyhtml']);
	foreach ($array_data_content as $body_data)
	{
		if(!empty($body_data)){
			$src = 'http://'.$body_data;
			$xtpl->assign( 'BODY_SRC', $src );
			$xtpl->parse( 'main.body' );
		}
	
	}

	if( defined( 'NV_IS_MODADMIN' ) )
	{
		$xtpl->assign( 'ADMINLINK', nv_link_edit_page( $news_contents['id'] ) . " " . nv_link_delete_page( $news_contents['id'], 1 ) );
		$xtpl->parse( 'main.adminlink' );
	}

	global $meta_property;
	if( ! defined( 'FACEBOOK_JSSDK' ) )
	{
		$lang = ( NV_LANG_DATA == 'vi' ) ? 'vi_VN' : 'en_US';
		$facebookappid = $module_config[$module_name]['facebookappid'];
		$facebookadminid = $module_config[$module_name]['facebookadminid'];
		$facebookcomment = $module_config[$module_name]['facebookcomment'];
		$xtpl->assign( 'FACEBOOK_LANG', $lang );
		$xtpl->assign( 'FACEBOOK_APPID', $facebookappid );
		
		if ( (! empty( $facebookappid ) && ! empty( $facebookcomment )) or ($module_config[$module_name]['socialbutton']) ) // Neu co ca FB ID va cho phep comment FB
		{
			$meta_property['fb:app_id'] = $facebookappid; // MetaData cho FB ID
			$xtpl->parse( 'main.facebookjssdk' ); // Xuat SDK dung FB ID
			$xtpl->parse( 'main.fb_comment' );
			$xtpl->parse( 'main.fb_comment_tab' ); // Xuat Comment cua ID tuong ung
		} 
		elseif( (!empty( $facebookcomment)) or ($module_config[$module_name]['socialbutton']) ) // Neu KHONG co FB ID va Cho phep comment FB
		{   
			$xtpl->parse( 'main.facebook_pubsdk' ); // Xuat SDK Public Facebook
			$xtpl->parse( 'main.fb_comment' );
			$xtpl->parse( 'main.fb_comment_tab' ); // Xuat FB Comment
		}
		if( ! empty( $facebookadminid ) ) // MetaData cho FB admin - quan ly comment
		{
			$meta_property['fb:admin_id'] = $facebookadminid;
		}
		define( 'FACEBOOK_JSSDK', true );
	}

	if( $module_config[$module_name]['socialbutton'] ) // Neu su dung cac cong cu MXH
	{
		$xtpl->parse( 'main.socialbutton' );
	}

	if( $news_contents['status'] != 1 )
	{
		$xtpl->parse( 'main.no_public' );
	}

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}

function no_permission()
{
	global $module_info, $module_file, $lang_module;

	$xtpl = new XTemplate( 'detail.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );

	$xtpl->assign( 'NO_PERMISSION', $lang_module['no_permission'] );
	$xtpl->parse( 'no_permission' );
	return $xtpl->text( 'no_permission' );
}
// Group (Genre cua danh muc)
function group_theme( $group_array, $generate_page, $page_title, $description, $group_image )
{
	global $lang_module, $module_info, $module_name, $module_file, $module_config;

	$xtpl = new XTemplate( 'groups.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GROUP_TITLE', $page_title );
	if( ! empty( $page_title ) )
	{
		$xtpl->assign( 'GROUP_DESCRIPTION', $description );
		if(!empty($group_image))
		{
			$xtpl->assign( 'HOMEIMG1', $group_image );
			$xtpl->parse( 'main.groupdescription.image' );
		}
		$xtpl->parse( 'main.groupdescription' );
	}
	if( ! empty( $group_array ) )
	{
		foreach( $group_array as $group_array_i )
		{
			$xtpl->assign( 'GROUP', $group_array_i );
			if (!empty($group_array_i['last_update']))
			{
				$xtpl->assign( 'TIME', date( 'H:i', $group_array_i['last_update'] ) );
				$xtpl->assign( 'DATE', date( 'd/m/Y', $group_array_i['last_update'] ) );
				$xtpl->parse( 'main.group.time' );
			}
			if( ! empty( $group_array_i['src'] ) )
			{
				$xtpl->parse( 'main.group.homethumb' );
			}
			if( ! empty( $group_array_i['letter'] ) )
			{
				$xtpl->assign( 'F_LETTER', strtoupper($group_array_i['letter']  ));
				$xtpl->parse( 'main.group.letter' );
			}
			if(  !empty($group_array_i['total_chap'] ) )
			{
				$xtpl->parse( 'main.group.total_chap' );
			}
			$xtpl->parse( 'main.group' );
		}
	}

	if( ! empty( $generate_page ) )
	{
		$xtpl->assign( 'GENERATE_PAGE', $generate_page );
		$xtpl->parse( 'main.generate_page' );
	}

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}