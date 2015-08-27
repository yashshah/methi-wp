<?php
  function add_wpdata_to_appbase() {
    global $wpdb;
    $APPBASE_APPNAME = "faltu";
    $APPBASE_USERNAME = "dAIbIAm5W";
    $APPBASE_PASSWORD = "d91257af-1fe4-44ab-84cb-e93cb3768b92";
    $posts = $wpdb->get_results("SELECT * from $wpdb->posts");
    foreach($posts as $post) {
      $args = array(
        'headers' => array(
          "Authorization" => "Basic ".base64_encode($APPBASE_USERNAME.":".$APPBASE_PASSWORD)
        ),
        'body' => json_encode(array(
          "title" => $post->post_title,
          "date" => $post->post_date,
          "author" => $post->post_author,
          "post_content" => $post->post_content
        ))
      );
      $response = wp_remote_post("https://".$APPBASE_USERNAME.":".$APPBASE_PASSWORD."@scalr.api.appbase.io/".$APPBASE_APPNAME."/posts/", $args);
      var_dump($response['response']);
      echo "<br>";
    }
  }
?>
