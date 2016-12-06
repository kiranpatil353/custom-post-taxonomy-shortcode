<!DOCTYPE html>
<?php
/**
* Plugin Name:   Custom Post taxonomy Metabox shortcode 
* Description:   Register custom post type and taxonomy with repeater meta fields
* Version:       1.0
* Author:        Kiran Patil
* Author URI:    https://kiranpatil353.wordpress.com/about/
*
*
*/

/*************Create Custom Post type *************/
function cptms_create_post_type() {
	$labels = array( 
		'name' => __( 'Vendor', 'cptms' ),
		'singular_name' => __( 'Vendor', 'cptms' ),
		'add_new' => __( 'New Vendor', 'cptms' ),
		'add_new_item' => __( 'Add New Vendor', 'cptms' ),
		'edit_item' => __( 'Edit Vendor', 'cptms' ),
		'new_item' => __( 'New Vendor', 'cptms' ),
		'view_item' => __( 'View Vendor', 'cptms' ),
		'search_items' => __( 'Search Vendors', 'cptms' ),
		'not_found' =>  __( 'No Vendors Found', 'cptms' ),
		'not_found_in_trash' => __( 'No Vendors found in Trash', 'cptms' ),
	);
	$args = array(
		'labels' => $labels,
		'has_archive' => true,
		'public' => true,
		'hierarchical' => false,
		'rewrite' => array( 'slug' => 'vendors' ),
		'supports' => array(
			'title', 
			'editor', 
			'excerpt', 
			'thumbnail',
			'page-attributes'
		),
		'taxonomies' => array('city'), 
	
	);
	register_post_type( 'vendor', $args );
} 
add_action( 'init', 'cptms_create_post_type' );


/*******************Add vendor metaboxes *********************/

function cptms_vendor_metaboxes(){
	add_meta_box('cptms_vendor_name', 'Vendor Name', 'cptms_vendor_name', 'vendor', 'normal', 'default');
	add_meta_box('cptms_vendor_establishment_year', 'Vendor Establishment Year', 'cptms_vendor_establishment_year', 'vendor', 'normal', 'default');
}


// The Name Metabox
add_action('add_meta_boxes', 'cptms_vendor_metaboxes');
function cptms_vendor_name() {
	global $post;
	
	// Noncename needed to verify where the data originated
	echo '<input type="hidden" name="vendormeta_noncename" id="vendormeta_noncename" value="' . 
	wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
	
	// Get the name data if its already been entered
	$vendorname = get_post_meta($post->ID, '_vendorname', true);
	
	// Echo out the field
	echo '<input required type="text" name="_vendorname" value="' . $vendorname  . '" class="widefat" />';

}
// The Name Metabox

function cptms_vendor_establishment_year() {
	global $post;
	
	// Noncename needed to verify where the data originated
	echo '<input type="hidden" name="vendorestablishmeta_noncename" id="vendorestablishmeta_noncename" value="' . 
	wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
	
	// Get the name data if its already been entered
	$vendoryear = get_post_meta($post->ID, '_vendoryear', true);
	
	// Echo out the field
	echo '<input required type="text" name="_vendoryear" pattern="[0-9]{4}" value="' . $vendoryear  . '" class="widefat" />';

}
/****************Repeater meta box****************/
add_action('admin_init', 'cptms_add_meta_boxes', 1);
function cptms_add_meta_boxes() {
	add_meta_box( 'repeatable-fields', 'Repeatable Fields', 'cptms_repeatable_meta_box_display', 'vendor', 'normal', 'default');
}
function cptms_repeatable_meta_box_display() {
	global $post;
	$repeatable_fields = get_post_meta($post->ID, 'repeatable_fields', true);

	wp_nonce_field( 'cptms_repeatable_meta_box_nonce', 'cptms_repeatable_meta_box_nonce' );
	?>
	<script type="text/javascript">
	jQuery(document).ready(function( $ ){
		$( '#add-row' ).on('click', function() {
			var row = $( '.empty-row.screen-reader-text' ).clone(true);
			row.find('input').attr('required', 'required');
			row.removeClass( 'empty-row screen-reader-text' );
			row.insertBefore( '#repeatable-fieldset-one tbody>tr:last' );
			return false;
		});
  	
		$( '.remove-row' ).on('click', function() {
			$(this).parents('tr').remove();
			return false;
		});
	});
	</script>
  
	<table id="repeatable-fieldset-one" width="100%">
	<thead>
		<tr>
			<th width="20%">Street</th>
			<th width="20%">Area</th>
			<th width="20%">City</th>
			<th width="20%">State</th>
			<th width="20%">Pin code</th>
		</tr>
	</thead>
	<tbody>
	<?php
	$repeatable_fields = json_decode($repeatable_fields,true);
	if ( $repeatable_fields ) :
	
	foreach ( $repeatable_fields as $field ) {
	?>
	<tr>
		<td><input type="text" class="widefat" required name="cptms_street[]" value="<?php if($field['cptms_area'] != '') echo esc_attr( $field['cptms_street'] ); ?>" /></td>
		<td><input type="text" class="widefat" required name="cptms_area[]" value="<?php if($field['cptms_area'] != '') echo esc_attr( $field['cptms_area'] ); ?>" /></td>
		<td><input type="text" class="widefat" required name="cptms_city[]" value="<?php if($field['cptms_city'] != '') echo esc_attr( $field['cptms_city'] ); ?>" /></td>
		<td><input type="text" class="widefat" required name="cptms_state[]" value="<?php if($field['cptms_state'] != '') echo esc_attr( $field['cptms_state'] ); ?>" /></td>
		<td><input type="text" class="widefat" pattern="[0-9]{6}" required name="cptms_pincode[]" value="<?php if($field['cptms_pincode'] != '') echo esc_attr( $field['cptms_pincode'] ); ?>" /></td>		
	
		<td><a class="button remove-row" href="#">Remove</a></td>
	</tr>
	<?php
	}
	else :
	// show a blank one
	?>
	<tr>
		<td><input required type="text" class="widefat" name="cptms_street[]" /></td>
		<td><input required type="text" class="widefat" name="cptms_area[]" /></td>
		<td><input required type="text" class="widefat" name="cptms_city[]" /></td>
		<td><input required type="text" class="widefat" name="cptms_state[]" /></td>
		<td><input required pattern="[0-9]{6}" type="text" class="widefat" name="cptms_pincode[]" /></td>
	
		<td><a class="button remove-row" href="#">Remove</a></td>
	</tr>
	<?php endif; ?>
	
	<!-- empty hidden one for jQuery -->
	<tr class="empty-row screen-reader-text">
		<td><input  type="text" class="widefat" name="cptms_street[]" /></td>
		<td><input   type="text" class="widefat" name="cptms_area[]" /></td>
		<td><input  type="text" class="widefat" name="cptms_city[]" /></td>
		<td><input   type="text" class="widefat" name="cptms_state[]" /></td>
		<td><input  pattern="[0-9]{6}" type="text" class="widefat" name="cptms_pincode[]" /></td>
	  
		<td><a class="button remove-row" href="#">Remove</a></td>
	</tr>
	</tbody>
	</table>
	
	<p><a id="add-row" class="button" href="#">Add another</a></p>
	<?php
}

// Save the Metabox Data

function cptms_save_vendors_meta($post_id, $post) {

global $post;
	echo $post_id;
	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times
	if ( !wp_verify_nonce( $_POST['vendormeta_noncename'], plugin_basename(__FILE__) )) {
	return $post->ID;
	}

	// Is the user allowed to edit the post or page?
	if ( !current_user_can( 'edit_post', $post->ID ))
		return $post->ID;

	$vendors_meta['_vendorname'] = $_POST['_vendorname'];
	$vendors_meta['_vendoryear'] = $_POST['_vendoryear'];
	
	// Add values of $vendors_meta as custom fields
	
	foreach ($vendors_meta as $key => $value) { // Cycle through the $vendors_meta array!
		if( $post->post_type == 'revision' ) return; // Don't store custom data twice
		$value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
		if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
			update_post_meta($post->ID, $key, $value);
		} else { // If the custom field doesn't have a value
			add_post_meta($post->ID, $key, $value);
		}
		if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
	}
	
	if ( ! isset( $_POST['cptms_repeatable_meta_box_nonce'] ) ||
	! wp_verify_nonce( $_POST['cptms_repeatable_meta_box_nonce'], 'cptms_repeatable_meta_box_nonce' ) )
		return; 
		
		
	$old = get_post_meta($post_id, 'repeatable_fields', true);
	$new = array();
	
	
	$cptms_street = $_POST['cptms_street'];
	$cptms_area = $_POST['cptms_area'];
	$cptms_city = $_POST['cptms_city'];
	$cptms_state = $_POST['cptms_state'];
	$cptms_pincode = $_POST['cptms_pincode'];

	$count = count( $cptms_street );
	
	for ( $i = 0; $i < $count; $i++ ) {
		
		if ( $cptms_street[$i] != '' ) :
			$new[$i]['cptms_street'] =  $cptms_street[$i]  ;
			$new[$i]['cptms_area'] =   $cptms_area[$i]  ;
			$new[$i]['cptms_city'] =   $cptms_city[$i]  ;
			$new[$i]['cptms_state'] =    $cptms_state[$i]  ;
			$new[$i]['cptms_pincode'] =   $cptms_pincode[$i]  ;			
		endif;
	}
	if ( !empty( $new ) && $new != $old )
		update_post_meta( $post_id, 'repeatable_fields', json_encode($new));
	elseif ( empty($new) && $old )
		delete_post_meta( $post_id, 'repeatable_fields', $old );

}

add_action('save_post', 'cptms_save_vendors_meta', 1, 2); // save the custom fields
/************************Register taxonomy******************/

function cptms_register_taxonomy() {

  $labels = array(
		'name'              => __( 'City', 'cptms' ),
		'singular_name'     => __( 'City', 'cptms' ),
		'search_items'      => __( 'Search City', 'cptms' ),
		'all_items'         => __( 'All City', 'cptms' ),
		'edit_item'         => __( 'Edit City', 'cptms' ),
		'update_item'       => __( 'Update City', 'cptms' ),
		'add_new_item'      => __( 'Add New City', 'cptms' ),
		'new_item_name'     => __( 'New City Name', 'cptms' ),
		'menu_name'         => __( 'City', 'cptms' ),
	);
	
	$args = array(
		'labels' => $labels,
		'hierarchical' => true,
		'sort' => true,
		'args' => array( 'orderby' => 'term_order' ),
		'rewrite' => array( 'slug' => 'city' ),
		'show_admin_column' => true
	);
	
	register_taxonomy( 'city', array('vendor') , $args);
	
}
add_action( 'init', 'cptms_register_taxonomy' );

/******************Add Shortcode for id********************/
function cptms_content_shortcode_data($atts){

extract( shortcode_atts( array(

'id' => '',

), $atts ) );

$args = array(
'id' => $id,
'post_type' => 'vendor',
'numberposts' => 1
);

$post = get_posts( $args );
$custom_fields = get_post_custom($id);
$content = $post[0]->post_content;
$title = $post[0]->post_title;

$repeatable_fields = json_decode($custom_fields['repeatable_fields'][0],true);

$html = '<div>
<h1>Vendor : '.$title.' </h1>
<h3>Description : '.$content.' </h3>
<p>Vendor Name : '.$custom_fields['_vendorname'][0].'</p>
<p>Vendor Establishment Year : '.$custom_fields['_vendoryear'][0].'</p>
<table id="repeatable-fieldset-one" width="100%">
	<thead>
		<tr>
			<th width="20%">Street</th>
			<th width="20%">Area</th>
			<th width="20%">City</th>
			<th width="20%">State</th>
			<th width="20%">Pin code</th>
		</tr>
	</thead>
	<tbody>';
	if ( $repeatable_fields ) :
	foreach ( $repeatable_fields as $field ) {
	
$html .="<tr>
		<td> ".$field['cptms_street']." </td>
		<td> ".$field['cptms_area']." </td>
		<td> ".$field['cptms_city']." </td>
		<td> ".$field['cptms_state']."</td>
		<td> ".$field['cptms_pincode']." </td>		
	</tr>";
	
	}
	endif;
$html .='</tbody></table></div>';

return $html;
}

add_shortcode('vendor','cptms_content_shortcode_data');