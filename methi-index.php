<?php

  $APPBASE_APPNAME = "faltu";
  $APPBASE_USERNAME = "dAIbIAm5W";
  $APPBASE_PASSWORD = "d91257af-1fe4-44ab-84cb-e93cb3768b92";

  function add_wpdata_to_appbase() {
    global $wpdb;
    $posts = $wpdb->get_results("SELECT * from $wpdb->posts")
    foreach($posts as $post) {
      echo $post->post_title;
      var_dump($post)
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, "https://".$APPBASE_USERNAME.":".$APPBASE_PASSWORD.
                      "@scalr.api.appbase.io/".$APPBASE_APPNAME."/posts/");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_POST, TRUE);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
      $response = curl_exec($ch);
      curl_close($ch);
      var_dump($response);
    }
  }
?>
