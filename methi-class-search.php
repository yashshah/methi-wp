<?php

class MethiSearch {

    private static $pluginurl = "";
    private static $plugindir = "";
	private static $notauthenticate = 0;
	
	private static $METHI_SECRETKEY_ARRAY = "methi-secretkey-array";


    public function __construct() {

        global $pluginUrl, $pluginDir, $post, $wpdb;
		
        self::$pluginurl = $pluginUrl;

        self::$plugindir = $pluginDir;

        add_action("admin_init", array($this, "count_total_post"));

        add_action('admin_enqueue_scripts', array($this, 'addscriptAdmin'));

        add_action('wp_footer', array($this, 'addsearchscriptfooter'));

        add_action("save_post", array($this, "add_wpdata_to_appbase"));
		
        add_action('admin_menu', array($this, "my_plugin_menu"));
		
		add_action( 'wp_ajax_reindex_all_data', array($this, "reindex_all_data"));
		add_action( 'wp_ajax_nopriv_reindex_all_data', array($this, "reindex_all_data"));
		
		//add_action('methiAuth', array($this, "methiAuth"));
		add_action( 'wp_ajax_methiAuth', array($this, "methiAuth"));
		add_action( 'wp_ajax_nopriv_methiAuth', array($this, "methiAuth"));
		
		//add_action('home_page_import_all_data', array($this, 'home_page_import_all_data'), 8);
		
		add_action('wp_head', array($this, "pluginname_ajaxurl"));
    }
	
	public function count_total_post()
	{
		$count_posts = wp_count_posts()->publish;
	}
	
	function my_plugin_menu() {
		
        add_menu_page('Methi Search', 'Methi Search', 8, 'methi_search', array($this, 'methiAuth'));

        add_submenu_page('methi_search', 'Methi Search', 'Settings', 8, 'settings', array($this, 'reindex_all_data'));

    }
	
	function pluginname_ajaxurl() {
	?>
	<script type="text/javascript">
		var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';	
	</script>
	<?php
	}

	function methiauth() {
	
	$my_simple_links = get_option(self::$METHI_SECRETKEY_ARRAY);
		
	if(empty($my_simple_links) || $my_simple_links  == "" || $my_simple_links == NULL)
	{	
	
		if($_POST && isset($_POST["submitallpost"]) && $_POST["submitallpost"] == 1)
		{
			$APPBASE_SECRET_KEY = $_REQUEST['appbasesecretkey'];
			$result = explode (":", $APPBASE_SECRET_KEY);
			$keyarray = array("app_name", "write_access_username", "write_access_password", "read_access_username", "read_access_password");
			
			$finalsecretarray = array_combine($keyarray, $result);
			
			if(!empty($finalsecretarray))
			{
				update_option(self::$METHI_SECRETKEY_ARRAY, $finalsecretarray );
				$this->indexallpost();
			}
		}
        include 'methiauthentication.php';
	}
	else
	{
		do_action("wp_ajax_reindex_all_data");
	}
    }
	
	public function redirect($url)
	{
		if(!headers_sent())
		{
			wp_redirect($url);
		}
		else
		{
		?>
		<script type="text/javascript">
			window.location.href = "<?php echo $url; ?>";
		</script>
		<?php
		}
		exit();
	}
	
	
	public function indexallpost(){
	
	$my_simple_links = get_option(self::$METHI_SECRETKEY_ARRAY);
		
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
	$totalpostcount = count($posts);
	
	/*echo "<pre>";
	print_r($posts);
	die;*/
	
	/* Close ES start*/
	$closeesargs = array(
	    'method' => 'POST',
        'headers' => array(
          "Authorization" => "Basic ".base64_encode($my_simple_links['write_access_username'].":".$my_simple_links['write_access_password'])
        ),
		'body' => ''
	); 
	
		$closeesresponse = wp_remote_post("http://".$my_simple_links['write_access_username'].":".$my_simple_links['write_access_password']."@scalr.api.appbase.io/".$my_simple_links['app_name']."/_close", $closeesargs);
	/* Close ES end*/
	
	/* Update Settings start */
	 $updatesettingsargs = array(
	    'method' => 'PUT',
        'headers' => array(
          "Authorization" => "Basic ".base64_encode($my_simple_links['write_access_username'].":".$my_simple_links['write_access_password'])
        ),
		'body' => '{ "analysis": { "filter": { "nGram_filter": { "type": "nGram", "min_gram": 2, "max_gram": 20, "token_chars": [ "letter", "digit", "punctuation", "symbol" ] } }, "analyzer": { "nGram_analyzer": { "type": "custom", "tokenizer": "whitespace", "filter": [ "lowercase", "asciifolding", "nGram_filter" ] }, "body_analyzer": { "type": "custom", "tokenizer": "standard", "filter": [ "lowercase", "asciifolding", "stop", "snowball", "word_delimiter" ] }, "title_default_analyzer": { "type": "custom", "tokenizer": "standard", "filter": [ "lowercase", "asciifolding" ] }, "whitespace_analyzer": { "type": "whitespace", "tokenizer": "standard", "filter": [ "lowercase", "asciifolding" ] } } } }'
      ); 
	$updatesettingresponse = wp_remote_post("http://".$my_simple_links['write_access_username'].":".$my_simple_links['write_access_password']."@scalr.api.appbase.io/".$my_simple_links['app_name']."/_settings", $updatesettingsargs);
	/* Update Settings end */
	
	/* Open ES start*/
	$openesargs = array(
	    'method' => 'POST',
        'headers' => array(
          "Authorization" => "Basic ".base64_encode($my_simple_links['write_access_username'].":".$my_simple_links['write_access_password'])
        ),
		'body' => ''
	); 
		$openesresponse = wp_remote_post("http://".$my_simple_links['write_access_username'].":".$my_simple_links['write_access_password']."@scalr.api.appbase.io/".$my_simple_links['app_name']."/_open", $openesargs);
	/* Open ES end*/
	
	
	/* Set Mapping start */
	$setmappingargs = array(
	    'method' => 'PUT',
        'headers' => array(
          "Authorization" => "Basic ".base64_encode($my_simple_links['write_access_username'].":".$my_simple_links['write_access_password'])
        ),
        'body' => '{ "article": { "properties": { "title": { "type": "multi_field", "fields": { "title_simple": { "type": "string", "analyzer": "title_default_analyzer" }, "title_ngrams": { "type": "string", "index_analyzer": "nGram_analyzer", "search_analyzer": "whitespace_analyzer" } } }, "meta_description": { "type": "string", "analyzer": "body_analyzer" }, "tags": { "type": "string", "index": "not_analyzed" }, "keywords": { "type": "string", "index": "not_analyzed" }, "link": { "type": "string", "index": "not_analyzed" }, "image_url": { "type": "string", "index": "not_analyzed" }, "videos_url": { "type": "string", "index": "not_analyzed" }, "body": { "type": "string", "analyzer": "body_analyzer" }, "updated_at": { "type": "date", "format" : "yyyy-MM-dd HH:mm:ss" }, "created_at": { "type": "date", "format" : "yyyy-MM-dd HH:mm:ss" } } } }'
      );
	$setmappingresponse = wp_remote_post("http://".$my_simple_links['write_access_username'].":".$my_simple_links['write_access_password']."@scalr.api.appbase.io/".$my_simple_links['app_name']."/_mapping/article", $setmappingargs);
	
	/* Set Mapping end */
	
	$postcount = 0; 
    foreach($posts as $post) {
	
	$postimage = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
	$alltags = wp_get_post_tags($post->ID);
	
	$tagarray = array();
	if($alltags != '' && $alltags != null && !empty($alltags))
	{
		
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
          "Authorization" => "Basic ".base64_encode($my_simple_links['write_access_username'].":".$my_simple_links['write_access_password'])
        ),
        'body' => json_encode(array(
          "title" => $post->post_title,
		  "body" => strip_tags($post->post_content),
		  "link" => get_permalink($post->ID),
		  "image_url" => $postimage,
		  "tags" => json_encode($tagarray),
          "created_at" => $post->post_date,
		  "updated_at" => $post->post_modified,
          "author" => $post->post_author,
          
        ))
      );
	  
      $response = wp_remote_post("https://".$my_simple_links['write_access_username'].":".$my_simple_links['write_access_password']."@scalr.api.appbase.io/".$my_simple_links['app_name']."/article/$post->ID", $args);
      $postcount++;
		if ( is_wp_error( $response ) ) {
		   $error_message = $response->get_error_message();
		   //echo "Something went wrong: $error_message";
		   echo "error";
		} 
		else {
		   /* echo 'Response:<pre>';
		   print_r( $response['response']['message'] );
		   echo '</pre>'; */
		   //echo "success";
		   echo $totalpostcount."/".$postcount."_";
			//just a usual sleep
			sleep(1);
			ob_flush();
			flush();

		}
    }
	wp_die();
	}
	
	
	function reindex_all_data() {
	
	$my_simple_links = get_option(self::$METHI_SECRETKEY_ARRAY);
		
	if(empty($my_simple_links) || $my_simple_links  == "" || $my_simple_links == NULL)
	{
		$this->redirect(admin_url("/admin.php?page=methi_search"));
	}
	
	if($_POST && isset($_POST["reindexallpost"]) && $_POST["reindexallpost"] == 1)
	{
		$this->indexallpost();
		wp_die();
	}
   
	include "settings.php";
  }
  
    public function addscriptAdmin() {
	?>
	<script type="text/javascript">
		var adminurl = '<?php echo admin_url('admin.php'); ?>';	
	</script>
	<?php
        wp_enqueue_script("methi-serach-admin", self::$pluginurl . "lib/js/search.js");
    }
	
	public function addsearchscriptfooter() {
			$my_simple_links = get_option(self::$METHI_SECRETKEY_ARRAY);
            ?>
			<script type="text/javascript">
			var appbase_variables={app_name:"<?php echo $my_simple_links['app_name']; ?>",credentials:"<?php echo $my_simple_links['write_access_username'].":".$my_simple_links['write_access_password']; ?>",index_document_type:"article"},src="https://s3-us-west-1.amazonaws.com/methi/methi/js/app.js",s=document.createElement("script");s.type="text/javascript",s.async=!0,s.src=src,s.addEventListener("load",function(){},!1);var head=document.getElementsByTagName("head")[0];head.appendChild(s);
			</script>
			<?php
			wp_enqueue_script("methi-serach-fromt", self::$pluginurl . "lib/js/search-front.js");
    }


   function add_wpdata_to_appbase($post_id) {
	
		$post = wp_get_single_post( $post_id );
		
		/* echo "<pre>";
		print_r($post);
		die(); */

		if($post->post_status == 'publish')
		{
			$my_simple_links = get_option(self::$METHI_SECRETKEY_ARRAY);
			
			//print_r($my_simple_links); die();
		if(isset($my_simple_links) && !empty($my_simple_links))
		{
				$postimage = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
				$alltags = wp_get_post_tags($post->ID);
				
				$tagarray = array();
				if($alltags != '' && $alltags != null && !empty($alltags))
				{	
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
					  "Authorization" => "Basic ".base64_encode($my_simple_links['write_access_username'].":".$my_simple_links['write_access_password'])
					),
					'body' => json_encode(array(
					  "title" => $post->post_title,
					  "body" => strip_tags($post->post_content),
					  "link" => get_permalink($post->ID),
					  "image_url" => $postimage,
					  "tags" => json_encode($tagarray),
					  "created_at" => $post->post_date,
					  "updated_at" => $post->post_modified,
					  "author" => $post->post_author,  
					))
				  );
				  $response = wp_remote_post("https://".$my_simple_links['write_access_username'].":".$my_simple_links['write_access_password']."@scalr.api.appbase.io/".$my_simple_links['app_name']."/article/$post->ID", $args);
				  
					if ( is_wp_error( $response ) ) {
					   $error_message = $response->get_error_message();
					   echo "Something went wrong: $error_message";
					} 
					/*else {
					   echo 'Response:<pre>';
					   print_r( $response['response']['message'] );
					}*/
		}
	}
  }
}
new MethiSearch();
