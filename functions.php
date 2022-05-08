<?php
/**
 * @神秘布偶猫
 * @https://www.ifalse.cn
 * @2022.05.02
 */
// ---------------------------------------------------------------------
//修改登录注册logo
function custom_loginlogo_url($url) {
  return home_url();
  }
  add_filter( 'login_headerurl', 'custom_loginlogo_url' );

function custom_loginlogo() {
  echo '<style type="text/css">
  h1 a {background-image: url('.get_bloginfo('template_directory').'/static/img/avatar.png) !important;background-size: 91px !important;}
  .login form {border-radius:8px;}
  </style>';
  }
  add_action('login_head', 'custom_loginlogo');
  
// ---------------------------------------------------------------------
// 评论中高亮站长
function filter_get_comment_author( $author, $comment_comment_id, $comment ) {
	//遍历用户
	$blogusers = get_users([ 'role__in' => [ 'administrator' ] ]);
	foreach ( $blogusers as $user ){
		if( $author == $user->display_name ){
			$webMaster = '<span class="master">'.$author.'</span>';
			return $webMaster;
		}
	};
	return $author;
};  
add_filter( 'get_comment_author', 'filter_get_comment_author', 10, 4);

// 留言时过滤站长
function filter_pre_comment_author_name( $cookie_comment_author_cookiehash ) {
	//遍历用户
	$blogusers = get_users([ 'role__in' => [ 'administrator' ] ]);
	foreach ( $blogusers as $user ){
		if( $cookie_comment_author_cookiehash == $user->display_name ){
			return $cookie_comment_author_cookiehash.'的崇拜者';
		}
	};
	return $cookie_comment_author_cookiehash;
    
};
//判断当前用户登录状态
if( !is_user_logged_in() ){
	add_filter( 'pre_comment_author_name', 'filter_pre_comment_author_name', 10, 1 ); 
}

// ---------------------------------------------------------------------
// 用户自定义头像
function get_ssl_avatar($avatar) {
  $i_logo = get_option("i_avatar_v");
  $i_logo_bak = get_template_directory_uri() . '/static/img/avatar.png';
  if(get_option("i_avatar_v")) {
    $avatar = preg_replace('/.*\/avatar\/(.*)\?s=([\d]+)&.*/','<img src="' . $i_logo . '" class="avatar avatar-$2" height="32" width="32">',$avatar);
  } else{
    $avatar = preg_replace('/.*\/avatar\/(.*)\?s=([\d]+)&.*/','<img src="' . $i_logo_bak . '" class="avatar avatar-$2" height="32" width="32">',$avatar);
  }
  return $avatar;
}
add_filter('get_avatar', 'get_ssl_avatar');

include (TEMPLATEPATH . '/admin/i_avatar.php');
// ---------------------------------------------------------------------
//默认封面图
function i_cover_pic() {
  echo get_template_directory_uri() . '/static/img/thumbnail.png';
}
function i_loading_pic() {
  echo get_template_directory_uri() . '/static/img/loading.gif';
}

// ---------------------------------------------------------------------
// 文章目录
// declare a function and pass the $content as an argument
function insert_table_of_contents($content) {
	// used to determine the location of the
	// table of contents when $fixed_location is set to false
	$html_comment = "<!--insert-toc-->";
	// checks if $html_comment exists in $content
	$comment_found = strpos($content, $html_comment) ? true : false;
	// set to true to insert the table of contents in a fixed location
	// set to false to replace $html_comment with $table_of_contents
	$fixed_location = true;
 
	// return the $content if
	// $comment_found and $fixed_location are false
	if (!$fixed_location && !$comment_found) {
		return $content;
	}
 
	// 设置排除，默认页面文章不生成目录
	// exclude the table of contents from all pages
	// other exclusion options include:
	// in_category($id)
	// has_term($term_name)
	// is_single($array)
	// is_author($id)
    if (is_page()) {
        return $content;
    }
		
	// regex to match all HTML heading elements 2-6
	$regex = "~(<h([1-6]))(.*?>(.*)<\/h[1-6]>)~";
 
	// preg_match_all() searches the $content using $regex patterns and
	// returns the results to $heading_results[]
	//
	// $heading_results[0][] contains all matches in full
	// $heading_results[1][] contains '<h2-6'
	// $heading_results[2][] contains '2-6'
	// $heading_results[3][] contains '>heading title</h2-6>
	// $heading_results[4][] contains the title text
	preg_match_all($regex, $content, $heading_results);
 
	// 默认小于3个段落标题不生成目录
	// return $content if less than 3 heading exist in the $content
	$num_match = count($heading_results[0]);
	if($num_match < 1) {
		return $content;
	}
 
	// declare local variable
	$link_list = "";
	// loop through $heading_results
	for ($i = 0; $i < $num_match; ++ $i) {
 
	    // rebuild heading elements to have anchors
	    $new_heading = $heading_results[1][$i] . " id='$i' " . $heading_results[3][$i];
 
	    // find original heading elements that don't have anchors
	    $old_heading = $heading_results[0][$i];
 
	    // search the $content for $old_heading and replace with $new_heading
		$content = str_replace($old_heading, $new_heading, $content);
 
	    // generate links for each heading element
	    // each link points to an anchor
	    $link_list .= "<li class='heading-level-" . $heading_results[2][$i] .
	    	"'><a href='#$i'>" . $heading_results[4][$i] . "</a></li>";
	}
	    
	// opening nav tag
	$start_nav = "<div id='article-toc'>";
	// closing nav tag
	$end_nav = "</div>";
	// title
	$title = "<div id=\"article-toc-title\">文章目录</div>";
 
	// wrap links in '<ul>' element
	$link_list = "<ul id=\"article-toc-ul\">" . $link_list . "</ul>";
 
	// piece together the table of contents
	$table_of_contents = $start_nav . $title . $link_list . $end_nav;
 
	// if $fixed_location is true and
	// $comment_found is false
	// insert the table of contents at a fixed location
	if($fixed_location && !$comment_found) {
		// location of first paragraph
		$first_paragraph = strpos($content, '</p>', 0) + 4;
		// location of second paragraph
		$second_paragraph = strpos($content, '</p>', $first_p_pos);
		// insert $table_of_contents after $second_paragraph
		return substr_replace($content, $table_of_contents, $second_paragraph + 4 , 0);
	}
	// if $fixed_location is false and
	// $comment_found is true
	else {
		// replace $html_comment with the $table_of_contents
		return str_replace($html_comment, $table_of_contents, $content);
	}
}
// pass the function to the content add_filter hook
add_filter('the_content', 'insert_table_of_contents');

// ---------------------------------------------------------------------
// 移动端菜单
function insert_table_of_contents_mb($content) {

	$html_comment = "<!--insert-toc-->";
	$comment_found = strpos($content, $html_comment) ? true : false;
	$fixed_location = true;
	if (!$fixed_location && !$comment_found) {
		return $content;
	}
    if (is_page()) {
        return $content;
    }
	$regex = "~(<h([1-6]))(.*?>(.*)<\/h[1-6]>)~";
	preg_match_all($regex, $content, $heading_results);
	$num_match = count($heading_results[0]);
	if($num_match < 1) {
		return $content;
	}
	$link_list = "";
	for ($i = 0; $i < $num_match; ++ $i) {
	    $new_heading = $heading_results[1][$i] . " id='$i' " . $heading_results[3][$i];
	    $old_heading = $heading_results[0][$i];
		$content = str_replace($old_heading, $new_heading, $content);
	    $link_list .= "<li class='heading-level-" . $heading_results[2][$i] .
	    	"'><a href='#$i'>" . $heading_results[4][$i] . "</a></li>";
	}
	$start_nav = "<div id='article-toc-mb'>";
	$end_nav = "</div>";
	$title = "<div id=\"article-toc-title-mb\">文章目录</div>";
	$link_list = "<ul id=\"article-toc-ul-mb\">" . $link_list . "</ul>";
	$table_of_contents = $start_nav . $title . $link_list . $end_nav;
	if($fixed_location && !$comment_found) {
		$first_paragraph = strpos($content, '</p>', 0) + 4;
		$second_paragraph = strpos($content, '</p>', $first_p_pos);
		return substr_replace($content, $table_of_contents, $second_paragraph + 4 , 0);
	}
	else {
		return str_replace($html_comment, $table_of_contents, $content);
	}
}
add_filter('the_content', 'insert_table_of_contents_mb');

// ---------------------------------------------------------------------
// 文章目录-bak
// function article_index($content) {
//   $matches = array();
//   $ul_li = '';
//   $r = '/<h([1-6]).*?\>(.*?)<\/h[1-6]>/is';
//   if(is_single() && preg_match_all($r, $content, $matches)) {
//   foreach($matches[1] as $key => $value) {
//   $title = trim(strip_tags($matches[2][$key]));
//   $content = str_replace($matches[0][$key], '<h' . $value . ' id="title-' . $key . '">'.$title.'</h1>', $content);
//   $ul_li .= '<li><a href="#title-'.$key.'" title="'.$title.'">'.$title."</a></li>\n";
//   }
//   $content = "\n<div id=\"article-toc-mb\">
//   <div id=\"article-toc-title-mb\">文章目录</div>
//   <ul id=\"article-toc-ul-mb\">\n" . $ul_li . "</ul>
//   </div>\n" . $content;
//   }
//   return $content;
//   }
//   add_filter( 'the_content', 'article_index' );

// ---------------------------------------------------------------------
// 自定义引入文件
function i_frame() {
  require('inc/frame.php');
}
function i_frame_js() {
  require('inc/frame-js.php');
}
function i_home() {
  require('inc/home.php');
}
function i_article() {
  require('inc/article.php');
}
function i_page() {
  require('inc/page-template.php');
}
function i_search() {
  require('inc/page-search.php');
}
function i_archive() {
  require('inc/page-archive.php');
}
function i_header_mb() {
  require('inc/header-mobile.php');
}
function i_searchform_mb() {
  require('inc/searchform-mobile.php');
}

// ---------------------------------------------------------------------
// 说明：获取完整URL 
// function curPageURL() { 
//   $pageURL = 'http'; 
// if ($_SERVER["HTTPS"] == "on") { 
//   $pageURL .= "s"; 
// } 
//   $pageURL .= "://"; 
// if ($_SERVER["SERVER_PORT"] != "80") { 
//   $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"]; 
// } else { 
//   $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]; 
// } 
// return $pageURL; 
// } 
// $i_home_url = bloginfo('url');
// $i_cur_url = curPageURL();
// $i_page1_url = bloginfo('url') . '/page/1';
// if($i_page1_url == $i_cur_url){
//     wp_redirect($i_home_url ,301);
//     exit;
// }

// ---------------------------------------------------------------------
// 菜单
register_nav_menus( array(        
    'menu' => '顶部菜单'
) );

// ---------------------------------------------------------------------
//侧边栏
register_sidebar( array(
  'name'          => __( '首页侧边栏'),
  'id'            => 'sidebar-1',
  'description'   => '将显示在首页',
  'class'         => '',
  'before_widget' => '<li id="%1$s" class="widget %2$s">',
  'after_widget'  => '</li>',
  'before_title'  => '<h2 class="widgettitle">',
  'after_title'   => '</h2>' 
) );

register_sidebar( array(
  'name'          => __( '文章侧边栏'),
  'id'            => 'sidebar-2',
  'description'   => '将显示在文章页',
  'class'         => '',
  'before_widget' => '<li id="%1$s" class="widget %2$s">',
  'after_widget'  => '</li>',
  'before_title'  => '<h2 class="widgettitle">',
  'after_title'   => '</h2>' 
) );

// ---------------------------------------------------------------------
// 特色图
if ( function_exists( 'add_theme_support' ) ) {
    add_theme_support( 'post-thumbnails', array( 'post', 'page' ) );
    set_post_thumbnail_size( 1920, 1080, true );
}
// ---------------------------------------------------------------------
// 设定摘要的长度
function new_excerpt_length($length) {
    return 100;
}
add_filter('excerpt_length', 'new_excerpt_length');

// ---------------------------------------------------------------------
// Gravatar缓存头像 blog.guluqiu.cc
function baolog_get_avatar($avatar)
{
    $avatar = str_replace(array("www.gravatar.com", "0.gravatar.com", "1.gravatar.com", "2.gravatar.com","secure.gravatar.com"),
        "gravatar.wp-china-yes.net", $avatar);
    return $avatar;
}
add_filter('get_avatar', 'baolog_get_avatar', 10, 3);

// ---------------------------------------------------------------------
// 获取用户头像
function get_user_avatar(){
    global $current_user;
    get_currentuserinfo();
    return get_avatar($current_user -> user_email, 400);
}

// ---------------------------------------------------------------------
// 获取用户信息
function get_user_role($id)
{
$user = new WP_User($id);
return $user -> data;
}

// ---------------------------------------------------------------------
// 显示文章浏览次数
function getPostViews($postID) {
    $count_key = 'post_views_count';
    $count = get_post_meta ( $postID, $count_key, true );
    if ($count == '') {
        delete_post_meta ( $postID, $count_key );
        add_post_meta ( $postID, $count_key, '0' );
        return "0";
    }
    return $count . '';
}
function setPostViews($postID) {
    $count_key = 'post_views_count';
    $count = get_post_meta ( $postID, $count_key, true );
    if ($count == '') {
        $count = 0;
        delete_post_meta ( $postID, $count_key );
        add_post_meta ( $postID, $count_key, '0' );
    } else {
        $count ++;
        update_post_meta ( $postID, $count_key, $count );
    }
}

// ---------------------------------------------------------------------
//登录页面
// function redirect_login_page() {
//   $login_page  = home_url( '/login/' );
//   $page_viewed = basename($_SERVER['REQUEST_URI']);
 
//   if( $page_viewed == "wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'GET') {
//     wp_redirect($login_page);
//     exit;
//   }
// }
// add_action('init','redirect_login_page');

// 登录后重定向
function soi_login_redirect($redirect_to, $request, $user) {
    return (is_array($user->roles) && in_array('administrator', $user->roles)) ? admin_url() : site_url();
}
add_filter('login_redirect', 'soi_login_redirect', 10, 3);
// 登出后重定向
function auto_redirect_after_logout(){
    wp_redirect(home_url());
        exit();
}
add_action('wp_logout','auto_redirect_after_logout');

// ---------------------------------------------------------------------
// 文章页码
function wp_pagenavi() {
    global $wp_query, $wp_rewrite;
    $wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1;  
    $pagination = array(
        'base' => @add_query_arg('paged','%#%'),
        'format' => '',
        'total' => $wp_query->max_num_pages,
        'current' => $current,
        'show_all' => false,
        'type' => 'plain',
        'end_size'=>'1',
        'mid_size'=>'1',
        'prev_text' => '<', //♂
        'next_text' => '>' //♀
    ); 
    if( $wp_rewrite->using_permalinks() )
        $pagination['base'] = user_trailingslashit( trailingslashit( remove_query_arg('s',get_pagenum_link(1) ) ) . 'page/%#%/', 'paged');
    if( !empty($wp_query->query_vars['s']) )
        $pagination['add_args'] = array('s'=>get_query_var('s'));
    echo paginate_links($pagination);
}

// ---------------------------------------------------------------------
// 自定义头像
add_filter( 'avatar_defaults', 'newgravatar' );  
function newgravatar ($avatar_defaults) {  
    $myavatar = get_bloginfo('template_directory') . '/static/img/avatar.png';  
    $avatar_defaults[$myavatar] = "默认头像";  
    return $avatar_defaults;  
}  

// ---------------------------------------------------------------------
// 后台友情链接
add_filter( 'pre_option_link_manager_enabled', '__return_true' );

// ---------------------------------------------------------------------
//搜索时排除页面
function exclude_page() {
	global $post;
	if ($post->post_type == 'page') {
		return true;
	} else {
		return false;
	}
}

// ---------------------------------------------------------------------
//禁止空搜索
add_filter( 'request', 'uctheme_redirect_blank_search' );
function uctheme_redirect_blank_search( $query_variables ) {
 if (isset($_GET['s']) && !is_admin()) {
 if (empty($_GET['s']) || ctype_space($_GET['s'])) {
 wp_redirect( home_url() );
 exit;
 }
 }
 return $query_variables;
}

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

add_action('admin_menu', 'ifasle_theme_set');
    function ifasle_theme_set() {
        add_menu_page(
            '主题设置',
            '主题设置', 
            'edit_themes',
            'i-opt',
            'i_settings' 
        );
}
function i_settings() {
    require get_template_directory()."/admin/i_opt.php";
}

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

// ---------------------------------------------------------------------
// 去除分类标志代码
add_action( 'load-themes.php',  'no_category_base_refresh_rules');
add_action('created_category', 'no_category_base_refresh_rules');
add_action('edited_category', 'no_category_base_refresh_rules');
add_action('delete_category', 'no_category_base_refresh_rules');
function no_category_base_refresh_rules() {
    global $wp_rewrite;
    $wp_rewrite -> flush_rules();
}
// Remove category base
add_action('init', 'no_category_base_permastruct');
function no_category_base_permastruct() {
    global $wp_rewrite, $wp_version;
    if (version_compare($wp_version, '3.4', '<')) {
        // For pre-3.4 support
        $wp_rewrite -> extra_permastructs['category'][0] = '%category%';
    } else {
        $wp_rewrite -> extra_permastructs['category']['struct'] = '%category%';
    }
}
// Add our custom category rewrite rules
add_filter('category_rewrite_rules', 'no_category_base_rewrite_rules');
function no_category_base_rewrite_rules($category_rewrite) {
    //var_dump($category_rewrite); // For Debugging
    $category_rewrite = array();
    $categories = get_categories(array('hide_empty' => false));
    foreach ($categories as $category) {
        $category_nicename = $category -> slug;
        if ($category -> parent == $category -> cat_ID)// recursive recursion
            $category -> parent = 0;
        elseif ($category -> parent != 0)
            $category_nicename = get_category_parents($category -> parent, false, '/', true) . $category_nicename;
        $category_rewrite['(' . $category_nicename . ')/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
        $category_rewrite['(' . $category_nicename . ')/page/?([0-9]{1,})/?$'] = 'index.php?category_name=$matches[1]&paged=$matches[2]';
        $category_rewrite['(' . $category_nicename . ')/?$'] = 'index.php?category_name=$matches[1]';
    }
    // Redirect support from Old Category Base
    global $wp_rewrite;
    $old_category_base = get_option('category_base') ? get_option('category_base') : 'category';
    $old_category_base = trim($old_category_base, '/');
    $category_rewrite[$old_category_base . '/(.*)$'] = 'index.php?category_redirect=$matches[1]';
    //var_dump($category_rewrite); // For Debugging
    return $category_rewrite;
}
// Add 'category_redirect' query variable
add_filter('query_vars', 'no_category_base_query_vars');
function no_category_base_query_vars($public_query_vars) {
    $public_query_vars[] = 'category_redirect';
    return $public_query_vars;
}
// Redirect if 'category_redirect' is set
add_filter('request', 'no_category_base_request');
function no_category_base_request($query_vars) {
    //print_r($query_vars); // For Debugging
    if (isset($query_vars['category_redirect'])) {
        $catlink = trailingslashit(get_option('home')) . user_trailingslashit($query_vars['category_redirect'], 'category');
        status_header(301);
        header("Location: $catlink");
        exit();
    }
    return $query_vars;
}

// ---------------------------------------------------------------------
//面包屑导航
// WordPress Breadcrumb Function
// Add this code into your theme function file.

function i_breadcrumb() {

    // Check if is front/home page, return
    if ( is_front_page() ) {
      return;
    }
  
    // Define
    global $post;
    $custom_taxonomy  = ''; // If you have custom taxonomy place it here
  
    $defaults = array(
      'seperator'   =>  '/',
      'id'          =>  'i-breadcrumb',
      'classes'     =>  'i-breadcrumb',
      'home_title'  =>  esc_html__( '首页', '' )
    );
    $sep  = '<li class="seperator">'. esc_html( $defaults['seperator'] ) .'</li>';
    // Start the breadcrumb with a link to your homepage
    echo '<ul id="'. esc_attr( $defaults['id'] ) .'" class="'. esc_attr( $defaults['classes'] ) .'">';
    // Creating home link
    echo '<li class="item"><a href="'. get_home_url() .'">'. esc_html( $defaults['home_title'] ) .'</a></li>' . $sep;
    if ( is_single() ) {
      // Get posts type
      $post_type = get_post_type();
      // If post type is not post
      if( $post_type != 'post' ) {
        $post_type_object   = get_post_type_object( $post_type );
        $post_type_link     = get_post_type_archive_link( $post_type );
        echo '<li class="item item-cat"><a href="'. $post_type_link .'">'. $post_type_object->labels->name .'</a></li>'. $sep;
      }
      // Get categories
      $category = get_the_category( $post->ID );
      // If category not empty
      if( !empty( $category ) ) {
        // Arrange category parent to child
        $category_values      = array_values( $category );
        $get_last_category    = end( $category_values );
        // $get_last_category    = $category[count($category) - 1];
        $get_parent_category  = rtrim( get_category_parents( $get_last_category->term_id, true, ',' ), ',' );
        $cat_parent           = explode( ',', $get_parent_category );
        // Store category in $display_category
        $display_category = '';
        foreach( $cat_parent as $p ) {
          $display_category .=  '<li class="item item-cat">'. $p .'</li>' . $sep;
        }
      }
      // If it's a custom post type within a custom taxonomy
      $taxonomy_exists = taxonomy_exists( $custom_taxonomy );
      if( empty( $get_last_category ) && !empty( $custom_taxonomy ) && $taxonomy_exists ) {
        $taxonomy_terms = get_the_terms( $post->ID, $custom_taxonomy );
        $cat_id         = $taxonomy_terms[0]->term_id;
        $cat_link       = get_term_link($taxonomy_terms[0]->term_id, $custom_taxonomy);
        $cat_name       = $taxonomy_terms[0]->name;
      }
      // Check if the post is in a category
      if( !empty( $get_last_category ) ) {
        echo $display_category;
        echo '<li class="item item-current">'. '正文' .'</li>';
      } else if( !empty( $cat_id ) ) {
        echo '<li class="item item-cat"><a href="'. $cat_link .'">'. $cat_name .'</a></li>' . $sep;
        echo '<li class="item-current item">'. '正文' .'</li>';
      } else {
        echo '<li class="item-current item">'. '正文' .'</li>';
      }
    } else if( is_archive() ) {
      if( is_tax() ) {
        // Get posts type
        $post_type = get_post_type();
        // If post type is not post
        if( $post_type != 'post' ) {
          $post_type_object   = get_post_type_object( $post_type );
          $post_type_link     = get_post_type_archive_link( $post_type );
          echo '<li class="item item-cat item-custom-post-type-' . $post_type . '"><a href="' . $post_type_link . '">' . $post_type_object->labels->name . '</a></li>' . $sep;
        }
        $custom_tax_name = get_queried_object()->name;
        echo '<li class="item item-current">'. $custom_tax_name .'</li>';
      } else if ( is_category() ) {
        $parent = get_queried_object()->category_parent;
        if ( $parent !== 0 ) {
          $parent_category = get_category( $parent );
          $category_link   = get_category_link( $parent );
          echo '<li class="item"><a href="'. esc_url( $category_link ) .'">'. $parent_category->name .'</a></li>' . $sep;
        }
        echo '<li class="item item-current">'. single_cat_title( '', false ) .'</li>';
      } else if ( is_tag() ) {
        // Get tag information
        $term_id        = get_query_var('tag_id');
        $taxonomy       = 'post_tag';
        $args           = 'include=' . $term_id;
        $terms          = get_terms( $taxonomy, $args );
        $get_term_name  = $terms[0]->name;
        // Display the tag name
        echo '<li class="item-current item">'. $get_term_name .'</li>';
      } else if( is_day() ) {
        // Day archive
        // Year link
        echo '<li class="item-year item"><a href="'. get_year_link( get_the_time('Y') ) .'">'. get_the_time('Y') . ' Archives</a></li>' . $sep;
        // Month link
        echo '<li class="item-month item"><a href="'. get_month_link( get_the_time('Y'), get_the_time('m') ) .'">'. get_the_time('M') .' Archives</a></li>' . $sep;
        // Day display
        echo '<li class="item-current item">'. get_the_time('jS') .' '. get_the_time('M'). ' Archives</li>';
      } else if( is_month() ) {
        // Month archive
        // Year link
        echo '<li class="item-year item"><a href="'. get_year_link( get_the_time('Y') ) .'">'. get_the_time('Y') . ' Archives</a></li>' . $sep;
        // Month Display
        echo '<li class="item-month item-current item">'. get_the_time('M') .' Archives</li>';
      } else if ( is_year() ) {
        // Year Display
        echo '<li class="item-year item-current item">'. get_the_time('Y') .' Archives</li>';
      } else if ( is_author() ) {
        // Auhor archive
        // Get the author information
        global $author;
        $userdata = get_userdata( $author );
        // Display author name
        echo '<li class="item-current item">'. 'Author: '. $userdata->display_name . '</li>';
      } else {
        echo '<li class="item item-current">'. post_type_archive_title() .'</li>';
      }
    } else if ( is_page() ) {
      // Standard page
      if( $post->post_parent ) {
        // If child page, get parents
        $anc = get_post_ancestors( $post->ID );
        // Get parents in the right order
        $anc = array_reverse( $anc );
        // Parent page loop
        if ( !isset( $parents ) ) $parents = null;
        foreach ( $anc as $ancestor ) {
          $parents .= '<li class="item-parent item"><a href="'. get_permalink( $ancestor ) .'">'. get_the_title( $ancestor ) .'</a></li>' . $sep;
        }
        // Display parent pages
        echo $parents;
        // Current page
        echo '<li class="item-current item">'. '正文' .'</li>';
      } else {
        // Just display current page if not parents
        echo '<li class="item-current item">'. '正文' .'</li>';
      }
    } else if ( is_search() ) {
      // Search results page
      echo '<li class="item-current item">Search results for: '. get_search_query() .'</li>';
    } else if ( is_404() ) {
      // 404 page
      echo '<li class="item-current item">' . 'Error 404' . '</li>';
    }
    // End breadcrumb
    echo '</ul>';
  }

