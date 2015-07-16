<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if(!class_exists('Post_Type_JTC_Slide_Shows'))
{
	/**
	 * A PostTypeTemplate class that provides 3 additional meta fields
	 */
	class Post_Type_JTC_Slide_Shows
	{
		const POST_TYPE	= "JTC_Slide_Shows";
		
		
    	/**
    	 * The Constructor
    	 */
    	public function __construct()
    	{
    		// register actions
			add_action( 'init', array(&$this,'add_custom_taxonomies') );
			add_action( 'jtc_slide_show_add_form_fields', array(&$this,'taxonomy_add_new_meta_field'), 10, 2 );
			add_action( 'jtc_slide_show_edit_form_fields', array(&$this,'taxonomy_edit_meta_field'), 10, 2 );
			add_action( 'edited_jtc_slide_show', array(&$this,'save_taxonomy_custom_meta'), 10, 2 );  
			add_action( 'create_jtc_slide_show', array(&$this,'save_taxonomy_custom_meta'), 10, 2 );
			add_action( 'admin_head', array(&$this,'hide_description') );
			add_filter('manage_edit-jtc_slide_show_columns', array(&$this,'jtc_slide_show_columns'), 5);
			add_action('manage_jtc_slide_show_custom_column', array(&$this,'jtc_slide_show_custom_columns'), 5, 3);
			add_action( 'admin_enqueue_scripts', array(&$this,'mw_enqueue_color_picker') );

    	} // END public function __construct()

//add color picker script
function mw_enqueue_color_picker( $hook_suffix ) {
    // first check that $hook_suffix is appropriate for your admin page
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'my-script-handle', plugins_url('my-script.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
}

/**
 * Add custom taxonomies
 *
 * Additional custom taxonomies can be defined here
 * http://codex.wordpress.org/Function_Reference/register_taxonomy
 */
function add_custom_taxonomies() {
  // Add new "Locations" taxonomy to Posts
  register_taxonomy('jtc_slide_show', 'jtc_slides', array(
    // Hierarchical taxonomy (like categories)
    'hierarchical' => true,
    'description' => false,
    // This array of options controls the labels displayed in the WordPress Admin UI
    'labels' => array(
      'name' => _x( 'JTC Slide Shows', 'taxonomy general name' ),
      'singular_name' => _x( 'JTC Slide Shows', 'taxonomy singular name' ),
      'search_items' =>  __( 'Search JTC Slide Shows' ),
      'all_items' => __( 'All JTC Slide Shows' ),
      'edit_item' => __( 'Edit JTC Slide Show' ),
      'update_item' => __( 'Update JTC Slide Show' ),
      'add_new_item' => __( 'Add New JTC Slide Show' ),
      'new_item_name' => __( 'New JTC Slide Shows Name' ),
      'menu_name' => __( 'JTC Slide Shows' ),
    ),
    // Control the slugs used for this taxonomy
    'rewrite' => array(
      'slug' => 'jtc_slide_shows', // This controls the base slug that will display before each term
      'with_front' => false, // Don't display the category base before "/locations/"
      'hierarchical' => false // This will allow URL's like "/locations/boston/cambridge/"
    ),
  ));
}

function hide_description() { 
    global $current_screen;
    if ( $current_screen->id == 'edit-jtc_slide_show' ) { ?>
        <style>
        .term-description-wrap, .term-parent-wrap {display:none;}
        </style>
        <script>
			jQuery(document).ready(function($){
    			$('.jtc-color-field').wpColorPicker();
			});        
        </script>
<?php }
}

// Add custom fields

function taxonomy_add_new_meta_field() {
	// this will add the custom meta field to the add new term page
	?>
	
	<div class="form-field">
		<label for="term_meta[jtc_slide_show_box_color]"><?php _e( 'Box Color', 'jtc_slide_show' ); ?></label>
		<input class="jtc-color-field" type="text" name="term_meta[jtc_slide_show_box_color]" id="term_meta[jtc_slide_show_box_color]" value="#007AA3">
		<p class="description"><?php _e( 'Enter a hex color value. Color for boxes.','jtc_slide_show' ); ?></p><br><br>
		<label for="term_meta[jtc_slide_show_boxt_color]"><?php _e( 'Box Text Color', 'jtc_slide_show' ); ?></label>
		<input class="jtc-color-field" type="text" name="term_meta[jtc_slide_show_boxt_color]" id="term_meta[jtc_slide_show_boxt_color]" value="#FFFFFF">
		<p class="description"><?php _e( 'Enter a hex color value. Color for text in boxes.','jtc_slide_show' ); ?></p><br><br>
		<label for="term_meta[jtc_slide_show_max_width]"><?php _e( 'Max Width', 'jtc_slide_show' ); ?></label>
		<input style="width:100px;" type="text" name="term_meta[jtc_slide_show_max_width]" id="term_meta[jtc_slide_show_max_width]" value="800"> px
		<p class="description"><?php _e( 'Enter the max width you want the slide show.','jtc_slide_show' ); ?></p><br><br>
		<label for="term_meta[jtc_slide_show_max_width]"><?php _e( 'Max Image Width', 'jtc_slide_show' ); ?></label>
		<input style="width:100px;" type="text" name="term_meta[jtc_slide_show_image_max_width]" id="term_meta[jtc_slide_show_max_image_width]" value="600"> px
		<p class="description"><?php _e( 'Enter the max width you want the image box.','jtc_slide_show' ); ?></p><br><br>
		<label for="term_meta[jtc_slide_show_max-height]"><?php _e( 'Max Height', 'jtc_slide_show' ); ?></label>
		<input style="width:100px;" type="text" name="term_meta[jtc_slide_show_max_height]" id="term_meta[jtc_slide_show_max_height]" value="300"> px
		<p class="description"><?php _e( 'Enter the max height you want the slide show.','jtc_slide_show' ); ?></p><br><br>
		<label for="term_meta[jtc_slide_show_auto]"><?php _e( 'Auto Start', 'jtc_slide_show' ); ?></label>
		<input type="checkbox" name="term_meta[jtc_slide_show_auto]" id="term_meta[jtc_slide_show_auto]"/>
		<p class="description"><?php _e( 'Starts the slideshow automatically','jtc_slide_show' ); ?></p><br>
		<label for="term_meta[jtc_slide_show_speed]"><?php _e( 'Speed', 'jtc_slide_show' ); ?></label>
		<input style="width:100px;" type="text" name="term_meta[jtc_slide_show_speed]" id="term_meta[jtc_slide_show_speed]" value="800"> ms
		<p class="description"><?php _e( 'Enter the speed of the slide show in miliseconds','jtc_slide_show' ); ?></p><br><br>
		<label for="term_meta[jtc_slide_show_css]"><?php _e( 'Custom CSS', 'jtc_slide_show' ); ?></label>
		<textarea name="term_meta[jtc_slide_show_css]" id="term_meta[jtc_slide_show_css]"></textarea>
		<p class="description"><?php _e( 'Enter custom CSS for the slide show.','jtc_slide_show' ); ?></p><br><br>
	</div>
<?php
}

function taxonomy_edit_meta_field($term) {
 
	// put the term ID into a variable
	$t_id = $term->term_id;
 
	// retrieve the existing value(s) for this meta field. This returns an array
	$term_meta = get_option( "taxonomy_$t_id" ); ?>
	<tr class="form-field">
	<th scope="row" valign="top"><label for="term_meta[jtc_slide_show_box_color]"><?php _e( 'Box Color', 'jtc_slide_show' ); ?></label></th>
		<td>
			<input class="jtc-color-field" type="text" name="term_meta[jtc_slide_show_box_color]" id="term_meta[jtc_slide_show_box_color]" value="<?php echo esc_attr( $term_meta['jtc_slide_show_box_color'] ) ? esc_attr( $term_meta['jtc_slide_show_box_color'] ) : ''; ?>">
			<p class="description"><?php _e( 'Enter a hex color value. Color for title boxes on left of slide show.' ); ?></p>
		</td>
	</tr>
	<tr class="form-field">
	<th scope="row" valign="top"><label for="term_meta[jtc_slide_show_boxt_color]"><?php _e( 'Box Text Color', 'jtc_slide_show' ); ?></label></th>
		<td>
			<input class="jtc-color-field" type="text" name="term_meta[jtc_slide_show_boxt_color]" id="term_meta[jtc_slide_show_boxt_color]" value="<?php echo esc_attr( $term_meta['jtc_slide_show_boxt_color'] ) ? esc_attr( $term_meta['jtc_slide_show_boxt_color'] ) : ''; ?>">
			<p class="description"><?php _e( 'Enter a hex color value. Color for title of slide in boxes.','jtc_slide_show' ); ?></p>
		</td>
	</tr>
	<tr class="form-field">
	<th scope="row" valign="top"><label for="term_meta[jtc_slide_show_max_width]"><?php _e( 'Max Width', 'jtc_slide_show' ); ?></label></th>
		<td>
			<input style="width:100px;" type="text" name="term_meta[jtc_slide_show_max_width]" id="term_meta[jtc_slide_show_max_width]" value="<?php echo esc_attr( $term_meta['jtc_slide_show_max_width'] ) ? esc_attr( $term_meta['jtc_slide_show_max_width'] ) : ''; ?>"> px
			<p class="description"><?php _e( 'Enter the max width you want the slide show.','jtc_slide_show' ); ?></p>
		</td>
	</tr>
	<tr class="form-field">
	<th scope="row" valign="top"><label for="term_meta[jtc_slide_show_image_max_width]"><?php _e( 'Max Image Width', 'jtc_slide_show' ); ?></label></th>
		<td>
			<input style="width:100px;" type="text" name="term_meta[jtc_slide_show_image_max_width]" id="term_meta[jtc_slide_show_image_max_width]" value="<?php echo esc_attr( $term_meta['jtc_slide_show_image_max_width'] ) ? esc_attr( $term_meta['jtc_slide_show_image_max_width'] ) : ''; ?>"> px
			<p class="description"><?php _e( 'Enter the max width you want the image box.','jtc_slide_show' ); ?></p>
		</td>
	</tr>
	<tr class="form-field">
	<th scope="row" valign="top"><label for="term_meta[jtc_slide_show_max_height]"><?php _e( 'Max Height', 'jtc_slide_show' ); ?></label></th>
		<td>
			<input style="width:100px;" type="text" name="term_meta[jtc_slide_show_max_height]" id="term_meta[jtc_slide_show_max_height]" value="<?php echo esc_attr( $term_meta['jtc_slide_show_max_height'] ) ? esc_attr( $term_meta['jtc_slide_show_max_height'] ) : ''; ?>"> px
			<p class="description"><?php _e( 'Enter the max height you want the slide show.','jtc_slide_show' ); ?></p>
		</td>
	</tr>
	<tr class="form-field">
	<th scope="row" valign="top"><label for="term_meta[jtc_slide_show_auto]"><?php _e( 'Auto Start', 'jtc_slide_show' ); ?></label></th>
		<td>			
		<input <?php if(esc_attr( $term_meta['jtc_slide_show_auto'] ) == "on"){ echo 'checked="checked"'; }; ?> type="checkbox" name="term_meta[jtc_slide_show_auto]" id="term_meta[jtc_slide_show_auto]"/>
		<p class="description"><?php _e( 'Starts the slideshow automatically','jtc_slide_show' ); ?></p>
		<br><br>
		</td>
	</tr>
	<tr class="form-field">
	<th scope="row" valign="top"><label for="term_meta[jtc_slide_show_speed]"><?php _e( 'Speed', 'jtc_slide_show' ); ?></label></th>
		<td>
			<input style="width:100px;" type="text" name="term_meta[jtc_slide_show_speed]" id="term_meta[jtc_slide_show_speed]" value="<?php echo esc_attr( $term_meta['jtc_slide_show_speed'] ) ? esc_attr( $term_meta['jtc_slide_show_speed'] ) : ''; ?>"> ms
			<p class="description"><?php _e( 'Enter the speed of the slide show in miliseconds','jtc_slide_show' ); ?></p>
		</td>
	</tr>
	<tr class="form-field">
	<th scope="row" valign="top"><label for="term_meta[jtc_slide_show_css]"><?php _e( 'Custom CSS', 'jtc_slide_show' ); ?></label></th>
		<td>
			<textarea name="term_meta[jtc_slide_show_css]" id="term_meta[jtc_slide_show_css]" ><?php echo esc_attr( $term_meta['jtc_slide_show_css'] ) ? esc_attr( $term_meta['jtc_slide_show_css'] ) : ''; ?></textarea>
			<p class="description"><?php _e( 'Enter custom CSS for the slide show.','jtc_slide_show' ); ?></p>
		</td>
	</tr>
<?php
}


// Save extra taxonomy fields callback function.
function save_taxonomy_custom_meta( $term_id ) {
	if ( isset( $_POST['term_meta'] ) ) {
		$t_id = $term_id;
		$term_meta = get_option( "taxonomy_$t_id" );
		$cat_keys = array_keys( $_POST['term_meta'] );
		//print_r($cat_keys);
		foreach ( $cat_keys as $key ) {
			if ( isset ( $_POST['term_meta'][$key] ) ) {
				$term_meta[$key] = $_POST['term_meta'][$key];
			} 
			//echo $key;
		}
		if( $_POST['term_meta']['jtc_slide_show_auto'] == "" ) {
			$term_meta['jtc_slide_show_auto'] = "off";
		}
		// Save the option array.
		update_option( "taxonomy_$t_id", $term_meta );
	}
}  


function jtc_slide_show_columns($defaults) {
	$defaults['jtc_slide_show_short_code'] = __('Short Code');
	return $defaults;
}

function jtc_slide_show_custom_columns($value, $column_name, $id) {
	if( $column_name == 'jtc_slide_show_short_code' ) {
		$term = get_term( $id, 'jtc_slide_show' );
		$slug = $term->slug; 
		return "[jtcslideshow slug='".$slug."']";
	}
}

	} // END class Post_Type_Template
} // END if(!class_exists('Post_Type_Template'))
