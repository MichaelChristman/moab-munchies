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
 *      ##Meta Box Contents
 *      ##Save Meta Boxes
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
        
        //Order Options
        add_meta_box( 
                'moab_munchies_order_options_meta',       // HTML 'id' attribute of the edit screen section
                'Order Options',                   // Title of the edit screen section, visible to user.
                'moab_munchies_order_options_meta_box' ,   // Function that prints out the HTML for the edit screen section
                $post_type,                         // The type of Write screen on which to show the edit screen section
                'top',                              // The part of the page where the edit screen section should be shown.
                'high'                              // The priority within the context where the boxes should show
        );
        
        //Hours
        add_meta_box( 
                'moab_munchies_hours_meta',       // HTML 'id' attribute of the edit screen section
                'Hours of Operation',                   // Title of the edit screen section, visible to user.
                'moab_munchies_hours_meta_box' ,   // Function that prints out the HTML for the edit screen section
                $post_type,                         // The type of Write screen on which to show the edit screen section
                'top',                              // The part of the page where the edit screen section should be shown.
                'high'                              // The priority within the context where the boxes should show
        );
        
        //Address
        add_meta_box( 
                'moab_munchies_address_meta',       // HTML 'id' attribute of the edit screen section
                'Address',                   // Title of the edit screen section, visible to user.
                'moab_munchies_address_meta_box' ,   // Function that prints out the HTML for the edit screen section
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
 * ##Meta Box Contents
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

function moab_munchies_hours_meta_box($post){
    //call in existing post meta
    $moab_munchies_monday_open = get_post_meta( $post->ID, '_moab_munchies_monday_open', true);
    $moab_munchies_monday_closed = get_post_meta( $post->ID, '_moab_munchies_monday_closed', true);
    $moab_munchies_tuesday_open = get_post_meta( $post->ID, '_moab_munchies_tuesday_open', true);
    $moab_munchies_tuesday_closed = get_post_meta( $post->ID, '_moab_munchies_tuesday_closed', true);
    $moab_munchies_wednesday_open = get_post_meta( $post->ID, '_moab_munchies_wednesday_open', true);
    $moab_munchies_wednesday_closed = get_post_meta( $post->ID, '_moab_munchies_wednesday_closed', true);
    $moab_munchies_thursday_open = get_post_meta( $post->ID, '_moab_munchies_thursday_open', true);
    $moab_munchies_thursday_closed = get_post_meta( $post->ID, '_moab_munchies_thursday_closed', true);
    $moab_munchies_friday_open = get_post_meta( $post->ID, '_moab_munchies_friday_open', true);
    $moab_munchies_friday_closed = get_post_meta( $post->ID, '_moab_munchies_friday_closed', true);
    $moab_munchies_saturday_open = get_post_meta( $post->ID, '_moab_munchies_saturday_open', true);
    $moab_munchies_saturday_closed = get_post_meta( $post->ID, '_moab_munchies_saturday_closed', true);
    $moab_munchies_sunday_open = get_post_meta( $post->ID, '_moab_munchies_sunday_open', true);
    $moab_munchies_sunday_closed = get_post_meta( $post->ID, '_moab_munchies_sunday_closed', true);
    
    //nonce for security
    wp_nonce_field( plugin_basename(__FILE__), 'moab_munchies_save_meta_boxes');
    
    ?>
    
    <table>
        <tr>
          <th>Day</th>
          <th>Open</th> 
          <th>Close</th>
        </tr>
        <tr>
          <td>Monday</td>
          <td>
              <?php 
//                echo "<script type='text/javascript'>alert('$moab_munchies_monday_open');</script>";
                ?> 
                <select name="moab_munchies_monday_open">
                <?php for($i = 0; $i < 24; $i++): ?>
                  <?php if($i == $moab_munchies_monday_open):?>
                  <option value="<?= $i; ?>" selected="selected"><?= $i % 12 ? $i % 12 : 12 ?>:00 <?= $i >= 12 ? 'pm' : 'am' ?></option>
                  <?php else: ?>
                  <option value="<?= $i; ?>"><?= $i % 12 ? $i % 12 : 12 ?>:00 <?= $i >= 12 ? 'pm' : 'am' ?></option>
                  <?php endif ?>
                <?php endfor ?>
                </select>
          </td>
          <td>
                <select name="moab_munchies_monday_closed">
                <?php for($i = 0; $i < 24; $i++): ?>
                  <?php if($i == $moab_munchies_monday_closed):?>
                  <option value="<?= $i; ?>" selected="selected"><?= $i % 12 ? $i % 12 : 12 ?>:00 <?= $i >= 12 ? 'pm' : 'am' ?></option>
                  <?php else: ?>
                  <option value="<?= $i; ?>"><?= $i % 12 ? $i % 12 : 12 ?>:00 <?= $i >= 12 ? 'pm' : 'am' ?></option>
                  <?php endif ?>
                <?php endfor ?>
                </select>
          </td>
        </tr>
        <tr>
          <td>Tuesday</td>
          <td>
                <select name="moab_munchies_tuesday_open">
                <?php for($i = 0; $i < 24; $i++): ?>
                  <?php if($i == $moab_munchies_tuesday_open):?>
                  <option value="<?= $i; ?>" selected="selected"><?= $i % 12 ? $i % 12 : 12 ?>:00 <?= $i >= 12 ? 'pm' : 'am' ?></option>
                  <?php else: ?>
                  <option value="<?= $i; ?>"><?= $i % 12 ? $i % 12 : 12 ?>:00 <?= $i >= 12 ? 'pm' : 'am' ?></option>
                  <?php endif ?>
                <?php endfor ?>
                </select>
          </td>
          <td>
                <select name="moab_munchies_tuesday_closed">
                <?php for($i = 0; $i < 24; $i++): ?>
                  <?php if($i == $moab_munchies_tuesday_closed):?>
                  <option value="<?= $i; ?>" selected="selected"><?= $i % 12 ? $i % 12 : 12 ?>:00 <?= $i >= 12 ? 'pm' : 'am' ?></option>
                  <?php else: ?>
                  <option value="<?= $i; ?>"><?= $i % 12 ? $i % 12 : 12 ?>:00 <?= $i >= 12 ? 'pm' : 'am' ?></option>
                  <?php endif ?>
                <?php endfor ?>
                </select>
          </td>
        </tr>
        <tr>
          <td>Wednesday</td>
           <td>
                <select name="moab_munchies_wednesday_open">
                <?php for($i = 0; $i < 24; $i++): ?>
                  <?php if($i == $moab_munchies_wednesday_open):?>
                  <option value="<?= $i; ?>" selected="selected"><?= $i % 12 ? $i % 12 : 12 ?>:00 <?= $i >= 12 ? 'pm' : 'am' ?></option>
                  <?php else: ?>
                  <option value="<?= $i; ?>"><?= $i % 12 ? $i % 12 : 12 ?>:00 <?= $i >= 12 ? 'pm' : 'am' ?></option>
                  <?php endif ?>
                <?php endfor ?>
                </select>
          </td>
          <td>
                <select name="moab_munchies_wednesday_closed">
                <?php for($i = 0; $i < 24; $i++): ?>
                  <?php if($i == $moab_munchies_wednesday_closed):?>
                  <option value="<?= $i; ?>" selected="selected"><?= $i % 12 ? $i % 12 : 12 ?>:00 <?= $i >= 12 ? 'pm' : 'am' ?></option>
                  <?php else: ?>
                  <option value="<?= $i; ?>"><?= $i % 12 ? $i % 12 : 12 ?>:00 <?= $i >= 12 ? 'pm' : 'am' ?></option>
                  <?php endif ?>
                <?php endfor ?>
                </select>
          </td>
        </tr>
        <tr>
          <td>Thursday</td>
          <td>
                <select name="moab_munchies_thursday_open">
                <?php for($i = 0; $i < 24; $i++): ?>
                  <?php if($i == $moab_munchies_thursday_open):?>
                  <option value="<?= $i; ?>" selected="selected"><?= $i % 12 ? $i % 12 : 12 ?>:00 <?= $i >= 12 ? 'pm' : 'am' ?></option>
                  <?php else: ?>
                  <option value="<?= $i; ?>"><?= $i % 12 ? $i % 12 : 12 ?>:00 <?= $i >= 12 ? 'pm' : 'am' ?></option>
                  <?php endif ?>
                <?php endfor ?>
                </select>
          </td>
          <td>
                <select name="moab_munchies_thursday_closed">
                <?php for($i = 0; $i < 24; $i++): ?>
                  <?php if($i == $moab_munchies_thursday_closed):?>
                  <option value="<?= $i; ?>" selected="selected"><?= $i % 12 ? $i % 12 : 12 ?>:00 <?= $i >= 12 ? 'pm' : 'am' ?></option>
                  <?php else: ?>
                  <option value="<?= $i; ?>"><?= $i % 12 ? $i % 12 : 12 ?>:00 <?= $i >= 12 ? 'pm' : 'am' ?></option>
                  <?php endif ?>
                <?php endfor ?>
                </select>
          </td>
        </tr>
        <tr>
          <td>Friday</td>
          <td>
                <select name="moab_munchies_friday_open">
                <?php for($i = 0; $i < 24; $i++): ?>
                  <?php if($i == $moab_munchies_friday_open):?>
                  <option value="<?= $i; ?>" selected="selected"><?= $i % 12 ? $i % 12 : 12 ?>:00 <?= $i >= 12 ? 'pm' : 'am' ?></option>
                  <?php else: ?>
                  <option value="<?= $i; ?>"><?= $i % 12 ? $i % 12 : 12 ?>:00 <?= $i >= 12 ? 'pm' : 'am' ?></option>
                  <?php endif ?>
                <?php endfor ?>
                </select>
          </td>
          <td>
                <select name="moab_munchies_friday_closed">
                <?php for($i = 0; $i < 24; $i++): ?>
                  <?php if($i == $moab_munchies_friday_closed):?>
                  <option value="<?= $i; ?>" selected="selected"><?= $i % 12 ? $i % 12 : 12 ?>:00 <?= $i >= 12 ? 'pm' : 'am' ?></option>
                  <?php else: ?>
                  <option value="<?= $i; ?>"><?= $i % 12 ? $i % 12 : 12 ?>:00 <?= $i >= 12 ? 'pm' : 'am' ?></option>
                  <?php endif ?>
                <?php endfor ?>
                </select>
          </td>
        </tr>
        <tr>
          <td>Saturday</td>
          <td>
                <select name="moab_munchies_saturday_open">
                <?php for($i = 0; $i < 24; $i++): ?>
                  <?php if($i == $moab_munchies_saturday_open):?>
                  <option value="<?= $i; ?>" selected="selected"><?= $i % 12 ? $i % 12 : 12 ?>:00 <?= $i >= 12 ? 'pm' : 'am' ?></option>
                  <?php else: ?>
                  <option value="<?= $i; ?>"><?= $i % 12 ? $i % 12 : 12 ?>:00 <?= $i >= 12 ? 'pm' : 'am' ?></option>
                  <?php endif ?>
                <?php endfor ?>
                </select>
          </td>
          <td>
                <select name="moab_munchies_saturday_closed">
                <?php for($i = 0; $i < 24; $i++): ?>
                  <?php if($i == $moab_munchies_saturday_closed):?>
                  <option value="<?= $i; ?>" selected="selected"><?= $i % 12 ? $i % 12 : 12 ?>:00 <?= $i >= 12 ? 'pm' : 'am' ?></option>
                  <?php else: ?>
                  <option value="<?= $i; ?>"><?= $i % 12 ? $i % 12 : 12 ?>:00 <?= $i >= 12 ? 'pm' : 'am' ?></option>
                  <?php endif ?>
                <?php endfor ?>
                </select>
          </td>
        </tr>
        <tr>
          <td>Sunday</td>
          <td>
                <select name="moab_munchies_sunday_open">
                <?php for($i = 0; $i < 24; $i++): ?>
                  <?php if($i == $moab_munchies_sunday_open):?>
                  <option value="<?= $i; ?>" selected="selected"><?= $i % 12 ? $i % 12 : 12 ?>:00 <?= $i >= 12 ? 'pm' : 'am' ?></option>
                  <?php else: ?>
                  <option value="<?= $i; ?>"><?= $i % 12 ? $i % 12 : 12 ?>:00 <?= $i >= 12 ? 'pm' : 'am' ?></option>
                  <?php endif ?>
                <?php endfor ?>
                </select>
          </td>
          <td>
                <select name="moab_munchies_sunday_closed">
                <?php for($i = 0; $i < 24; $i++): ?>
                  <?php if($i == $moab_munchies_sunday_closed):?>
                  <option value="<?= $i; ?>" selected="selected"><?= $i % 12 ? $i % 12 : 12 ?>:00 <?= $i >= 12 ? 'pm' : 'am' ?></option>
                  <?php else: ?>
                  <option value="<?= $i; ?>"><?= $i % 12 ? $i % 12 : 12 ?>:00 <?= $i >= 12 ? 'pm' : 'am' ?></option>
                  <?php endif ?>
                <?php endfor ?>
                </select>
          </td>
        </tr>
        
      </table>
    
    
    <?php
}

function moab_munchies_address_meta_box($post){
    $moab_munchies_street_address = get_post_meta( $post->ID, '_moab_munchies_street_address', true);
    $moab_munchies_city_address = get_post_meta( $post->ID, '_moab_munchies_city_address', true);
    $moab_munchies_state_address = get_post_meta( $post->ID, '_moab_munchies_state_address', true);
    $moab_munchies_zip_code_address = get_post_meta( $post->ID, '_moab_munchies_zip_code_address', true);
    
//    var_dump($moab_munchies_zip_code_address);
    //nonce for security
    wp_nonce_field( plugin_basename(__FILE__), 'moab_munchies_save_meta_boxes');
    
    echo '<div class="address-meta-field">';
    
    echo '<label for ="moab_munchies_street_address" style="display:block">'. __( 'Street Address', 'moab-munchies-custom-post-types' ) .'</label>';
    if($moab_munchies_street_address){
        echo '<input type="text" name="moab_munchies_street_address" placeholder="'. $moab_munchies_street_address .'" value="'. $moab_munchies_street_address .'"></br>';
    }else{
         echo '<input type="text" name="moab_munchies_street_address" placeholder="'.__( 'Street Address', 'moab-munchies-custom-post-types' ).'"></br>';
    }
    
    echo '<label for ="city-address" style="display:block">'. __( 'City', 'moab-munchies-custom-post-types' ) .'</label>';
    if($moab_munchies_city_address){
        echo '<input type="text" name="moab_munchies_city_address" placeholder="'. $moab_munchies_city_address .'" value="'. $moab_munchies_city_address .'"></br>';
    }else{
         echo '<input type="text" name="moab_munchies_city_address" placeholder="'. __( 'City', 'moab-munchies-custom-post-types' ) .'"></br>';
    }
    

    echo '<label for ="moab_munchies_state_address" style="display:block">State</label>';
    echo '<select name = "moab_munchies_state_address">'
                ?>
                <option value="AL" <?php echo ($moab_munchies_state_address == 'AL')? 'selected="selected"':''; ?>>Alabama</option>
                <option value="AK" <?php echo ($moab_munchies_state_address == 'AK')? 'selected="selected"':''; ?>>Alaska</option>
                <option value="AZ" <?php echo ($moab_munchies_state_address == 'AZ')? 'selected="selected"':''; ?>>Arizona</option>
                <option value="AR" <?php echo ($moab_munchies_state_address == 'AR')? 'selected="selected"':''; ?>>Arkansas</option>
                <option value="CA" <?php echo ($moab_munchies_state_address == 'CA')? 'selected="selected"':''; ?>>California</option>
                <option value="CO" <?php echo ($moab_munchies_state_address == 'CO')? 'selected="selected"':''; ?>>Colorado</option>
                <option value="CT" <?php echo ($moab_munchies_state_address == 'CT')? 'selected="selected"':''; ?>>Connecticut</option>
                <option value="DE" <?php echo ($moab_munchies_state_address == 'DE')? 'selected="selected"':''; ?>>Delaware</option>
                <option value="DC" <?php echo ($moab_munchies_state_address == 'DC')? 'selected="selected"':''; ?>>District Of Columbia</option>
                <option value="FL" <?php echo ($moab_munchies_state_address == 'FL')? 'selected="selected"':''; ?>>Florida</option>
                <option value="GA" <?php echo ($moab_munchies_state_address == 'GA')? 'selected="selected"':''; ?>>Georgia</option>
                <option value="HI" <?php echo ($moab_munchies_state_address == 'HI')? 'selected="selected"':''; ?>>Hawaii</option>
                <option value="ID" <?php echo ($moab_munchies_state_address == 'ID')? 'selected="selected"':''; ?>>Idaho</option>
                <option value="IL" <?php echo ($moab_munchies_state_address == 'IL')? 'selected="selected"':''; ?>>Illinois</option>
                <option value="IN" <?php echo ($moab_munchies_state_address == 'IN')? 'selected="selected"':''; ?>>Indiana</option>
                <option value="IA" <?php echo ($moab_munchies_state_address == 'IA')? 'selected="selected"':''; ?>>Iowa</option>
                <option value="KS" <?php echo ($moab_munchies_state_address == 'KS')? 'selected="selected"':''; ?>>Kansas</option> 
                <option value="KY" <?php echo ($moab_munchies_state_address == 'KY')? 'selected="selected"':''; ?>>Kentucky</option>
                <option value="LA" <?php echo ($moab_munchies_state_address == 'LA')? 'selected="selected"':''; ?>>Louisiana</option>
                <option value="ME" <?php echo ($moab_munchies_state_address == 'ME')? 'selected="selected"':''; ?>>Maine</option>
                <option value="MD" <?php echo ($moab_munchies_state_address == 'MD')? 'selected="selected"':''; ?>>Maryland</option>
                <option value="MA" <?php echo ($moab_munchies_state_address == 'MA')? 'selected="selected"':''; ?>>Massachusetts</option>
                <option value="MI" <?php echo ($moab_munchies_state_address == 'MI')? 'selected="selected"':''; ?>>Michigan</option>
                <option value="MN" <?php echo ($moab_munchies_state_address == 'MN')? 'selected="selected"':''; ?>>Minnesota</option>
                <option value="MS" <?php echo ($moab_munchies_state_address == 'MS')? 'selected="selected"':''; ?>>Mississippi</option>
                <option value="MO" <?php echo ($moab_munchies_state_address == 'MO')? 'selected="selected"':''; ?>>Missouri</option>
                <option value="MT" <?php echo ($moab_munchies_state_address == 'MT')? 'selected="selected"':''; ?>>Montana</option>
                <option value="NE" <?php echo ($moab_munchies_state_address == 'NE')? 'selected="selected"':''; ?>>Nebraska</option>
                <option value="NV" <?php echo ($moab_munchies_state_address == 'NV')? 'selected="selected"':''; ?>>Nevada</option>
                <option value="NH" <?php echo ($moab_munchies_state_address == 'NH')? 'selected="selected"':''; ?>>New Hampshire</option>
                <option value="NJ" <?php echo ($moab_munchies_state_address == 'NJ')? 'selected="selected"':''; ?>>New Jersey</option>
                <option value="NM" <?php echo ($moab_munchies_state_address == 'NM')? 'selected="selected"':''; ?>>New Mexico</option>
                <option value="NY" <?php echo ($moab_munchies_state_address == 'NY')? 'selected="selected"':''; ?>>New York</option>
                <option value="NC" <?php echo ($moab_munchies_state_address == 'NC')? 'selected="selected"':''; ?>>North Carolina</option>
                <option value="ND" <?php echo ($moab_munchies_state_address == 'ND')? 'selected="selected"':''; ?>>North Dakota</option>
                <option value="OH" <?php echo ($moab_munchies_state_address == 'OH')? 'selected="selected"':''; ?>>Ohio</option>
                <option value="OK" <?php echo ($moab_munchies_state_address == 'OK')? 'selected="selected"':''; ?>>Oklahoma</option>
                <option value="OR" <?php echo ($moab_munchies_state_address == 'OR')? 'selected="selected"':''; ?>>Oregon</option>
                <option value="PA" <?php echo ($moab_munchies_state_address == 'PA')? 'selected="selected"':''; ?>>Pennsylvania</option>
                <option value="RI" <?php echo ($moab_munchies_state_address == 'RI')? 'selected="selected"':''; ?>>Rhode Island</option>
                <option value="SC" <?php echo ($moab_munchies_state_address == 'SC')? 'selected="selected"':''; ?>>South Carolina</option>
                <option value="SD" <?php echo ($moab_munchies_state_address == 'SD')? 'selected="selected"':''; ?>>South Dakota</option>
                <option value="TN" <?php echo ($moab_munchies_state_address == 'TN')? 'selected="selected"':''; ?>>Tennessee</option>
                <option value="TX" <?php echo ($moab_munchies_state_address == 'TX')? 'selected="selected"':''; ?>>Texas</option>
                <option value="UT" <?php echo ($moab_munchies_state_address == 'UT')? 'selected="selected"':''; ?>>Utah</option>
                <option value="VT" <?php echo ($moab_munchies_state_address == 'VT')? 'selected="selected"':''; ?>>Vermont</option>
                <option value="VA" <?php echo ($moab_munchies_state_address == 'VA')? 'selected="selected"':''; ?>>Virginia</option>
                <option value="WA" <?php echo ($moab_munchies_state_address == 'WA')? 'selected="selected"':''; ?>>Washington</option>
                <option value="WV" <?php echo ($moab_munchies_state_address == 'WV')? 'selected="selected"':''; ?>>West Virginia</option>
                <option value="WI" <?php echo ($moab_munchies_state_address == 'WI')? 'selected="selected"':''; ?>>Wisconsin</option>
                <option value="WY" <?php echo ($moab_munchies_state_address == 'WY')? 'selected="selected"':''; ?>>Wyoming</option>
                <?php
    echo  '</select></br>';
    echo '<label for ="moab_munchies_zip_code_address" style="display:block">Zip Code</label>';
    
    if($moab_munchies_zip_code_address){
         echo '<input type="text" name="moab_munchies_zip_code_address" placeholder="'. $moab_munchies_zip_code_address .'" value="'. $moab_munchies_zip_code_address .'"></br>';
     }else{
         echo '<input type="text" name="moab_munchies_zip_code_address" placeholder="'. __( 'Zip Code', 'moab-munchies-custom-post-types' ) .'"></br>';
     }
    echo '</div>';
    
}


/**********************************************************************
 *
 * ##Save Meta Boxes
 * 
 ***********************************************************************/
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
     *Hours of Operation Meta Info
     ******************************************/
    
    //accepted values whitelist
    $hours_allowed = [];
    
    foreach (range(0, 24) as $number) {
        array_push($hours_allowed, $number);
    }
   
    if( isset( $_POST['moab_munchies_monday_open'] )  && in_array($_POST['moab_munchies_monday_open'], $hours_allowed)){

        update_post_meta( $post_id, '_moab_munchies_monday_open',  $_POST['moab_munchies_monday_open'] );

    }
    if( isset( $_POST['moab_munchies_monday_closed'] )  && in_array($_POST['moab_munchies_monday_closed'], $hours_allowed)){

        update_post_meta( $post_id, '_moab_munchies_monday_closed',  $_POST['moab_munchies_monday_closed'] );

    }
    if( isset( $_POST['moab_munchies_tuesday_open'] )  && in_array($_POST['moab_munchies_tueday_open'], $hours_allowed)){

        update_post_meta( $post_id, '_moab_munchies_tuesday_open',  $_POST['moab_munchies_tuesday_open'] );

    }
    if( isset( $_POST['moab_munchies_tuesday_closed'] )  && in_array($_POST['moab_munchies_tuesday_closed'], $hours_allowed)){

        update_post_meta( $post_id, '_moab_munchies_tuesday_closed',  $_POST['moab_munchies_tuesday_closed'] );

    }
    if( isset( $_POST['moab_munchies_wednesday_open'] )  && in_array($_POST['moab_munchies_wednesday_open'], $hours_allowed)){

        update_post_meta( $post_id, '_moab_munchies_wednesday_open',  $_POST['moab_munchies_wednesday_open'] );

    }
    if( isset( $_POST['moab_munchies_wednesday_closed'] )  && in_array($_POST['moab_munchies_wednesday_closed'], $hours_allowed)){

        update_post_meta( $post_id, '_moab_munchies_wednesday_closed',  $_POST['moab_munchies_wednesday_closed'] );

    }
    if( isset( $_POST['moab_munchies_thursday_open'] )  && in_array($_POST['moab_munchies_thursday_open'], $hours_allowed)){

        update_post_meta( $post_id, '_moab_munchies_thursday_open',  $_POST['moab_munchies_thursday_open'] );

    }
    if( isset( $_POST['moab_munchies_thursday_closed'] )  && in_array($_POST['moab_munchies_thursday_closed'], $hours_allowed)){

        update_post_meta( $post_id, '_moab_munchies_thursday_closed',  $_POST['moab_munchies_thursday_closed'] );

    }
    if( isset( $_POST['moab_munchies_friday_open'] )  && in_array($_POST['moab_munchies_friday_open'], $hours_allowed)){

        update_post_meta( $post_id, '_moab_munchies_friday_open',  $_POST['moab_munchies_friday_open'] );

    }
    if( isset( $_POST['moab_munchies_friday_closed'] )  && in_array($_POST['moab_munchies_friday_closed'], $hours_allowed)){

        update_post_meta( $post_id, '_moab_munchies_friday_closed',  $_POST['moab_munchies_friday_closed'] );

    }
    if( isset( $_POST['moab_munchies_saturday_open'] )  && in_array($_POST['moab_munchies_saturday_open'], $hours_allowed)){

        update_post_meta( $post_id, '_moab_munchies_saturday_open',  $_POST['moab_munchies_saturday_open'] );

    }
    if( isset( $_POST['moab_munchies_saturday_closed'] )  && in_array($_POST['moab_munchies_saturday_closed'], $hours_allowed)){

        update_post_meta( $post_id, '_moab_munchies_saturday_closed',  $_POST['moab_munchies_saturday_closed'] );

    }
    if( isset( $_POST['moab_munchies_sunday_open'] )  && in_array($_POST['moab_munchies_sunday_open'], $hours_allowed)){

        update_post_meta( $post_id, '_moab_munchies_sunday_open',  $_POST['moab_munchies_sunday_open'] );

    }
    if( isset( $_POST['moab_munchies_sunday_closed'] )  && in_array($_POST['moab_munchies_sunday_closed'], $hours_allowed)){

        update_post_meta( $post_id, '_moab_munchies_sunday_closed',  $_POST['moab_munchies_sunday_closed'] );

    }
    /*****************************************
     *Location Meta Info
     ******************************************/
    if(isset($_POST['moab_munchies_street_address'] ) ){
        
        //save the meta box data as post meta using the post ID as a unique prefix
        update_post_meta($post_id,'_moab_munchies_street_address', sanitize_text_field( $_POST['moab_munchies_street_address'] ) );

    }
    if(isset($_POST['moab_munchies_city_address'] ) ){
        
        //save the meta box data as post meta using the post ID as a unique prefix
        update_post_meta($post_id,'_moab_munchies_city_address', sanitize_text_field( $_POST['moab_munchies_city_address'] ) );

    }
    
    //accepted values whitelist
    $states_allowed = ['AL','AK','AZ','AR','CA','CO','CT','DE','DC','FL','GA','HI','ID','IL','IN','IA','KS','KY','LA','ME','MD','MA','MI','MN','MS','MO','MT','NE','NV','NH','NJ','NM','NY','NC','ND','OH','OK','OR','PA','RI','SC','SD','TN','TX','UT','VA','WA','WV','WI','WY'];
    
    if( isset( $_POST['moab_munchies_state_address'] )  && in_array($_POST['moab_munchies_state_address'], $states_allowed)){

        update_post_meta( $post_id, '_moab_munchies_state_address',  $_POST['moab_munchies_state_address'] );

    }
    
    if( isset( $_POST['moab_munchies_zip_code_address'] ) ){

        update_post_meta($post_id,'_moab_munchies_zip_code_address', sanitize_text_field( intval( $_POST['moab_munchies_zip_code_address'] ) ) );

    }
    
    
    
}
//hook to save our meta box data when the post is saved
add_action('save_post', 'moab_munchies_save_meta_boxes');