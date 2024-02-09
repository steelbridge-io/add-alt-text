<?php

/*
Plugin Name: Add Alt Text
Plugin URI: http://parsonshosting.com/add-alt-text-wp-plugin
Description: A WordPress plugin. This plugin will add default alt text to all images without alt text.
Version: 1.0
Author: chris j. parsons
Author URI: http://steelbridge.io
License: A "add-alt-text" license name e.g. GPL2
*/


/**
 * Create a new admin menu item with the title "Manually Add Alt Tags"
 * and the menu title "Add Alt Tags".
 *
 * This menu item is only visible to users with the capability "manage_options".
 *
 * The menu item will take the user to the callback function "alttext_admin_page"
 * when clicked.
 *
 * The menu item will have the icon "dashicons-admin-media" and will be placed
 * at position 20 in the admin menu.
 *
 * @return void
 */
function alt_text_admin_menu() {
    add_menu_page('Manually Add Alt Tags', 'Add Alt Tags', 'manage_options', 'add-alt-tags', 'alttext_admin_page', 'dashicons-admin-media', 20);
}
add_action('admin_menu', 'alt_text_admin_menu');

/**
 * Displays the admin page for adding alt text to images.
 *
 * This method renders the HTML markup for the admin page. It includes a form
 * with a button to manually add alt text to 100 images at a time. The form
 * includes a nonce field for security purposes.
 *
 * If the form is submitted and the nonce is verified, the method calls the
 * add_alt_tags() function to add alt text to the images. It then displays a
 * success message.
 *
 * @return void
 */
function alttext_admin_page(){
    ?>
    <div class="wrap">
        <h2>Add Alt Text to Images</h2>
        <form method="post" action="">
            <?php
            wp_nonce_field( 'alttext_add_alt_tags_action', 'alttext_add_alt_tags_field' );
            ?>
            <p>Press the button below to manually add alt text "Matt Dover Guided Fly Fishing" to 100 images at a time.</p>
            <p><input type="submit" value="Add Alt Text Now" class="button button-primary"></p>
        </form>
    </div>
    <?php

    if (($_SERVER['REQUEST_METHOD'] == 'POST') && wp_verify_nonce($_POST['alttext_add_alt_tags_field'], 'alttext_add_alt_tags_action' )) {
        add_alt_tags();
        echo '<div id="message" class="updated fade"><p><strong>Added Alt Text to all images</strong></p></div>';
    }
}

/**
 * Adds alt text to images that don't have it.
 *
 * This function retrieves all image attachments and checks if they have
 * alt text. If an image does not have alt text, it adds the alt text
 * "Matt Dover Guided Fly Fishing". After adding the alt text, it displays
 * a success or failure message for each image.
 *
 * @return void
 */

function add_alt_tags()
{
    $args = array(
        'post_type' => 'attachment',
        'numberposts' => -1, // Get all images
        'post_status' => 'any',
        'post_mime_type' => 'image',
    );

    $images = get_posts($args);

    $without_alt = [];

    foreach($images as $image) {
        $alt = get_post_meta($image->ID, '_wp_attachment_image_alt', true);
        if (empty($alt)) {
            array_push($without_alt, $image->ID);
        }
    }

    if (!empty($without_alt)) {
        echo '<p>Found ' . count($without_alt) . ' Images without alt texts.</p>';

        $alt_text = 'Matt Dover Guided Fly Fishing';

        foreach ($without_alt as $image_id) {
            if (update_post_meta($image_id, '_wp_attachment_image_alt', $alt_text)) {
                echo '<p>Alt text added for image ID: ' . $image_id . '<p>';
            } else {
                echo '<p>Failed to add alt text for image ID: ' . $image_id . '</p>';
            }
        }
    } else {
        echo '<p>No images without alt texts found.</p>';
    }
}

