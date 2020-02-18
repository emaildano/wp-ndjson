<?php

/**
 * Create Options Page
 */

function wp_ndjson_options() { ?>

  <?php

  $ndjson = wp_get_ndjson();

  // Check permissions
  if (!current_user_can('manage_options'))  {
    wp_die( __('You do not have sufficient pilchards to access this page.')    );
  }
  
  // Check whether the button has been pressed AND also check the nonce
  if (isset($_POST['create_index']) && check_admin_referer('create_index_nonce')) {
    create_index();
  } ?>

  <div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <div class="card">
      <h2 class="title">Create index</h2>
      <form action="options-general.php?page=wp-ndjson" method="post">
        <?php wp_nonce_field('create_index_nonce'); ?>
        <input type="hidden" value="true" name="create_index" />
        <?php submit_button('Create index'); ?>
      </form>

      <?php
      // Show link to index if it exists
      if (file_exists($ndjson['file'])) { ?>
        <hr>
        <p>View index</p>
        <a rel="nofollow noopener" target="_blank" href=<?php echo $ndjson['url']; ?>><?php echo $ndjson['url']; ?></a>
      <?php } ?>
    </div>
  </div>
<?php }