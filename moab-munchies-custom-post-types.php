<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**********************************************************************
 *
 * Custom Post Types
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
    register_post_type('listing',  $restaurantArgs);
    
}

add_action('init', 'moab_munchies_custom_post_types');