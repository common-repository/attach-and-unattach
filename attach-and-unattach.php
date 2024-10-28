<?php
/*
Plugin Name: Attach and Unattach
Plugin URI: http://weposolutions.de/attach-and-unattach
Description: Unattach, re-attach and attach media files from/to posts
Version: 0.4
Author: Lukas Werner
Author URI: http://weposolutions.de
License: GPLv2 or later
Text Domain: attach-and-unattach
Domain Path: /lang/
*/

// Add Attach Button to media library
function add_attach_button($actions, $post, $detached) {
  if(current_user_can('edit_post', $post->ID)) {
      $actions['attach'] = '<a href="#the-list" onclick="findPosts.open( \'media[]\',\''.$post->ID.'\' );return false;" class="hide-if-no-js">' . __('Attach', 'attach-and-unattach') . '</a>';
  }
  return $actions;
}

// Add Unattach Button to media library
function add_unattach_button($actions, $post) {
    if ($post->post_parent) {
        $url = admin_url('tools.php?page=unattach&noheader=true&id=' . $post->ID);
        $actions['unattach'] = '<a href="' . esc_url($url) . '">' . __('Unattach', 'attach-and-unattach') . '</a>';
    }
    return $actions;
}

// Function to set parent post to 0
function do_unattach() {
    global $wpdb;
    if (!empty($_REQUEST['id'])) {
        $wpdb->update($wpdb->posts, array('post_parent' => 0), array('id' => intval($_REQUEST['id']), 'post_type' => 'attachment'));
    }
    wp_redirect(admin_url('upload.php'));
    exit;
}

// Hook for unattach files
function load_attach_and_unattach_module() {
    if (current_user_can('upload_files')) {
        add_filter('media_row_actions', 'add_unattach_button', 11, 2);
        add_filter('media_row_actions', 'add_attach_button', 10, 3);
        add_submenu_page('tools.php', 'Unattach Media', 'Unattach', 'upload_files', 'unattach', 'do_unattach');
        remove_submenu_page('tools.php', 'unattach');
    }
}

// Load translation files
function attach_and_unattach_load_translation_files() {
	load_plugin_textdomain('attach-and-unattach', false, dirname(plugin_basename(__FILE__)) . '/lang/');
}

// Add hooks
add_action('plugins_loaded', 'attach_and_unattach_load_translation_files'); // Language files hook
add_action('admin_menu', 'load_attach_and_unattach_module'); // General hook to load plugin
