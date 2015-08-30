<?php
  function add_wpdata_to_appbase() {
    global $wpdb;
	global $post;
    $APPBASE_APPNAME = "faltu";
    $APPBASE_USERNAME = "dAIbIAm5W";
    $APPBASE_PASSWORD = "d91257af-1fe4-44ab-84cb-e93cb3768b92";
  
	 $args = array(
		'posts_per_page'   => -1,
		'offset'           => 0,
		'category'         => '',
		'category_name'    => '',
		'orderby'          => 'date',
		'order'            => 'DESC',
		'include'          => '',
		'exclude'          => '',
		'meta_key'         => '',
		'meta_value'       => '',
		'post_type'        => 'post',
		'post_mime_type'   => '',
		'post_parent'      => '',
		'author'	   => '',
		'post_status'      => 'publish',
		'suppress_filters' => true 
	 );

	$posts = get_posts($args);
	
	/*echo "<pre>";
	print_r($posts);
	die;*/
	
    foreach($posts as $post) {
      $args = array(
        'headers' => array(
          "Authorization" => "Basic ".base64_encode($APPBASE_USERNAME.":".$APPBASE_PASSWORD)
        ),
        'body' => json_encode(array(
          "title" => $post->post_title,
		  "body" => $post->post_content,
		  "link" => get_permalink($post->ID),
          "date" => $post->post_date,
          "author" => $post->post_author,
          
        ))
      );
      $response = wp_remote_post("https://".$APPBASE_USERNAME.":".$APPBASE_PASSWORD."@scalr.api.appbase.io/".$APPBASE_APPNAME."/article/", $args);
      var_dump($response['response']);
      echo "<br>";
    }
  }
?>
