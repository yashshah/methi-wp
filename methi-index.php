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
	
	$postimage = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
	$alltags = wp_get_post_tags($post->ID);
	
	if($alltags != '' && $alltags != null && !empty($alltags))
	{
		$tagarray = array();
		foreach($alltags as $tag)
		{
			array_push($tagarray, $tag->name);
		}
		$tagstring = implode(',',$tagarray); 
	}
	
	//echo $tagstring; die; 
	
      $args = array(
	    'method' => 'POST',
        'headers' => array(
          "Authorization" => "Basic ".base64_encode($APPBASE_USERNAME.":".$APPBASE_PASSWORD)
        ),
        'body' => json_encode(array(
          "title" => $post->post_title,
		  "body" => $post->post_content,
		  "link" => get_permalink($post->ID),
		  "image_url" => $postimage,
		  "tags" => $tagstring,
          "created_at" => $post->post_date,
		  "updated_at" => $post->post_modified,
          "author" => $post->post_author,
          
        ))
      );
      $response = wp_remote_post("https://".$APPBASE_USERNAME.":".$APPBASE_PASSWORD."@scalr.api.appbase.io/".$APPBASE_APPNAME."/article/$post->ID", $args);
      
		if ( is_wp_error( $response ) ) {
		   $error_message = $response->get_error_message();
		   echo "Something went wrong: $error_message";
		} 
		/*else {
		   echo 'Response:<pre>';
		   print_r( $response['response']['message'] );
		   echo '</pre>';
		}*/
    }
  }
?>
