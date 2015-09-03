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

        //add_action("admin_init", array($this, "redirect_to_authentication"));

        add_action('admin_enqueue_scripts', array($this, 'addscriptAdmin'));

        add_action('wp_footer', array($this, 'addsearchscriptfooter'));

        add_action("save_post", array($this, "add_wpdata_to_appbase"));
		
        add_action('admin_menu', array($this, "my_plugin_menu"));
		
		add_action( 'wp_ajax_home_page_import_all_data', array($this, "home_page_import_all_data"));
		add_action( 'wp_ajax_nopriv_home_page_import_all_data', array($this, "home_page_import_all_data"));
		
		add_action('methiAuth', array($this, "methiAuth"));
		//add_action('home_page_import_all_data', array($this, 'home_page_import_all_data'), 8);
		
		add_action('wp_head', array($this, "pluginname_ajaxurl"));
    }
	
	function my_plugin_menu() {
		
        add_menu_page('Methi Search', 'Methi Search', 8, 'methi_search', array($this, 'methiauth'));

        add_submenu_page('methi_search', 'Methi Search', 'Import all data', 8, 'import_all_post', array($this, 'home_page_import_all_data'));

    }
	
	public function redirect_to_authentication()
	{
	
		if(self::$notauthenticate == 1)
		{
		echo "in if";
			wp_redirect(admin_url('/post-new.php?post_type=page', 'http'), 301);
			exit();
		}
	}
	
	function pluginname_ajaxurl() {
	?>
	<script type="text/javascript">
		var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
	</script>
	<?php
	}

	function methiauth() {
		
		if($_REQUEST && isset($_REQUEST["submit_methi_authentication"]))
		{
			$APPBASE_SECRET_KEY = $_REQUEST['appbase_secret_token'];
			$result = explode (":", $APPBASE_SECRET_KEY);
			$keyarray = array("app_name", "write_access_username", "write_access_password", "read_access_username", "read_access_password");
			
			$finalsecretarray = array_combine($keyarray, $result);
			
			if(!empty($finalsecretarray))
			{
				update_option(self::$METHI_SECRETKEY_ARRAY, $finalsecretarray );
			}
		}
		
        include 'methiauthentication.php';

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
	
	function home_page_import_all_data() {
		
		$my_simple_links = get_option(self::$METHI_SECRETKEY_ARRAY);
		
	if(empty($my_simple_links) || $my_simple_links  == "" || $my_simple_links == NULL)
	{
		$this->redirect(admin_url("/admin.php?page=methi_search"));
	}
		
	if($_POST && isset($_POST["submitallpost"]) && $_POST["submitallpost"] == 1)
	{
		
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
          "Authorization" => "Basic ".base64_encode($my_simple_links['write_access_username'].":".$my_simple_links['write_access_password'])
        ),
        'body' => json_encode(array(
          "title" => $post->post_title,
		  "body" => $post->post_content,
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
		   //echo "Something went wrong: $error_message";
		   echo "error";
		} 
		else {
		   /* echo 'Response:<pre>';
		   print_r( $response['response']['message'] );
		   echo '</pre>'; */
		   echo "success";
		}
    }
	wp_die();
   }
   
   include "importallpost.php";
  }
  
    public function addscriptAdmin() {
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
					  "body" => $post->post_content,
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
