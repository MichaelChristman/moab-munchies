<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**********************************************************************
 *
 * Table of contents
 * 
 * #Custom Post Types
 *      ##Default Title Text
 *      ##Custom Meta box information
 * 
 * 
 ***********************************************************************/




/**********************************************************************
 *
 * #Custom Post Types
 * 
 ***********************************************************************/
//register post types
function moab_munchies_custom_post_types() {
    
    $restaurantLabels = [
        'name'               => 'Restaurant',
        'singular_name'      => 'Restaurant',
        'menu_name'          => 'Restaurants',
        'name_admin_bar'     => 'Restaurant',
        'add_new'            => 'Add New Restaurant',
        'add_new_item'       => 'Add New Restaurant',
        'new_item'           => 'New Restaurant',
        'edit_item'          => 'Edit Restaurant',
        'view_item'          => 'View Restaurant',
        'all_items'          => 'All Restaurants',
        'search_items'       => 'Search Restaurants',
        'not_found'          => 'No Restaurants found',
        'not_found_in_trash' => 'No Restaurants found in trash'
    ];

    $restaurantArgs = [
        'labels'             => $restaurantLabels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_nav_menus'  => true,
        'menu_position'      => 4,
        'menu_icon'          => 'dashicons-store',
        'rewrite'            => array('slug'=> 'restaurant','pages'=> true),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt',
                                'page-attributes', 'post-formats')

    ];
    register_post_type('restaurant',  $restaurantArgs);
    
}

add_action('init', 'moab_munchies_custom_post_types');

/**********************************************************************
 *
 * ##Default Title Text
 * 
 ***********************************************************************/
//function to replace default title text on custom post type edit screens
function moab_munchies_change_title_text( $title ){

    $screen = get_current_screen();

    if  ( 'restaurant' == $screen->post_type ) {

        $title = 'Enter Restaurant';

     }
     
     return $title;

}
add_filter( 'enter_title_here', 'moab_munchies_change_title_text' );

/**********************************************************************
 *
 * ##Custom Meta box information
 * 
 ***********************************************************************/
//add meta box to restaurant custom post type 
function moab_munchies_meta_box_init($post_type){
    if ( $post_type === 'restaurant' ) {
        
        add_meta_box( 
                'moab_munchies_order_options_meta',       // HTML 'id' attribute of the edit screen section
                'Order Options',                   // Title of the edit screen section, visible to user.
                'moab_munchies_order_options_meta_box' ,   // Function that prints out the HTML for the edit screen section
                $post_type,                         // The type of Write screen on which to show the edit screen section
                'top',                              // The part of the page where the edit screen section should be shown.
                'high'                              // The priority within the context where the boxes should show
        );
        
    }
}
add_action( 'add_meta_boxes' , 'moab_munchies_meta_box_init' );

//function used to sort meta boxes with section set to "top" above editor
function move_to_top() {
        # Get the globals:
        global $post, $wp_meta_boxes;

        # Output the "advanced" meta boxes:
        do_meta_boxes( get_current_screen(), 'top', $post );

        # Remove the initial "advanced" meta boxes:
        unset($wp_meta_boxes['post']['top']);
    }

add_action('edit_form_after_title', 'move_to_top');

/*********************************************************************
 *  
 * Meta Box Contents
 * 
 *********************************************************************/
//create nonce and display custom meta data for restauraunt custom post type
function moab_munchies_order_options_meta_box($post){
//    echo "<p>this is the metabox</p>";
    //retrieve the custom meta box values
//    var_dump($meta['moab_munchies_order_options_take_out']);
    $meta = get_post_meta( $post->ID );
    $moab_munchies_take_out = ( isset( $meta['_moab_munchies_take_out'][0] ) &&  '1' === $meta['_moab_munchies_take_out'][0] ) ? 1 : 0;
    $moab_munchies_delivery = ( isset( $meta['_moab_munchies_delivery'][0] ) &&  '1' === $meta['_moab_munchies_delivery'][0] ) ? 1 : 0;
//    $moab_munchies_order_options = get_post_meta( $post->ID, '_moab_munchies_order_options', true);
    
   //nonce for security
    wp_nonce_field( plugin_basename(__FILE__), 'moab_munchies_save_meta_boxes');
    
    //Custom meta box form elements
    ?>
      <!--Order Options Radio Buttons--> 
    <p class = "moab_munchies_meta_option moab_munchies_meta_radio" id ="order_options_checkboxes">
       <label class="screen-reader-text" for="order_options"> Order Options:</label> </br>
        <input id="moab_munchies_take_out" type="checkbox" name = "moab_munchies_take_out" value = "1" <?php checked( $moab_munchies_take_out, 1 ); ?> /><?php esc_attr_e( 'Take Out', 'moab_munchies' ); ?>
        <input id="moab_munchies_delivery" type="checkbox" name = "moab_munchies_delivery" value = "1" <?php checked( $moab_munchies_delivery, 1 ); ?> /><?php esc_attr_e( 'Delivery', 'moab_munchies' ); ?>
    </p>
   <?php
}

function moab_munchies_save_meta_boxes($post_id){
    
    /*****************************************
     * Order Options meta info
     ******************************************/
   
    //if auto saving skip saving our meta box data
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    //check nonce for security
    wp_verify_nonce( plugin_basename(__FILE__), 'moab_munchies_save_meta_boxes');

    
    $moab_munchies_take_out = ( isset( $_POST['moab_munchies_take_out'] ) && '1' === $_POST['moab_munchies_take_out'] ) ? 1 : 0; // Input var okay.
    update_post_meta( $post_id, '_moab_munchies_take_out', esc_attr( $moab_munchies_take_out ) );

    $moab_munchies_delivery = ( isset( $_POST['moab_munchies_delivery'] ) && '1' === $_POST['moab_munchies_delivery'] ) ? 1 : 0; // Input var okay.
    update_post_meta( $post_id, '_moab_munchies_delivery', esc_attr( $moab_munchies_delivery ) );
            
    //$mytheme_checkbox_value = ( isset( $_POST['mytheme_checkbox_value'] ) && '1' === $_POST['mytheme_checkbox_value'] ) ? 1 : 0; // Input var okay.
   // update_post_meta( $post_id, 'mytheme_checkbox_value', esc_attr( $mytheme_checkbox_value ) );
     /*****************************************
     *  End Order Options meta info
     ******************************************/
    
}
//hook to save our meta box data when the post is saved
add_action('save_post', 'moab_munchies_save_meta_boxes');