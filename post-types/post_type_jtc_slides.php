<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if(!class_exists('Post_Type_Jtc_Slides'))
{
	/**
	 * A PostTypeTemplate class that provides 3 additional meta fields
	 */
	class Post_Type_JTC_Slides
	{
		const POST_TYPE	= "JTC_Slides";
		
		
    	/**
    	 * The Constructor
    	 */
    	public function __construct()
    	{
    		// register actions
    		add_action('init', array(&$this, 'init'));
			add_action( 'admin_head', array(&$this,'hide_stuff') );
			add_action( 'add_meta_boxes', array(&$this,'jtc_slides_add_meta_box') );
			add_action( 'save_post', array(&$this,'jtc_slides_save_meta_box_data') );

    	} // END public function __construct()

		function hide_stuff() {
		?>
        	<style>
        		#jtc_slide_show-add-toggle {display:none;}
       	 	</style>
       	 	<script>
       	 		jQuery('document').ready(function(){
       	 			jQuery('#taxonomy-jtc_slide_show').append('<a href="edit-tags.php?taxonomy=jtc_slide_show&post_type=jtc_slides">Add a Slide Show</a>');
       	 		});
       	 	</script>	
       	<?php	
       	}

    	/**
    	 * hook into WP's init action hook
    	 */
    	public function init()
    	{
    		// Initialize Post Type
    		$this->create_post_type();
    		add_action('save_post', array(&$this, 'save_post'));
    	} // END public function init()

    	/**
    	 * Create the post type
    	 */
    	public function create_post_type()
    	{
    		register_post_type(self::POST_TYPE,
    			array(
    				'labels' => array(
    					'name' => __(sprintf('%s', ucwords(str_replace("_", " ", self::POST_TYPE)))),
    					'singular_name' => __(ucwords(str_replace("_", " ", self::POST_TYPE))),
    					'add_new' => 'Add New Slide',
    					'add_new_item' => 'Add New Slide'

    				),
    				'menu_icon' => 'dashicons-images-alt',
    				'public' => true,
    				'has_archive' => true,
    				'description' => __("JTC Slide"),
    				'supports' => array(
    					'title', 'thumbnail',
    				),
    				'taxonomies' => array('jtc_slide_show'),
    			)
    		);
    	}
	
    	/**
    	 * Save the metaboxes for this custom post type
    	 */
    	public function save_post($post_id)
    	{
            // verify if this is an auto save routine. 
            // If it is our form has not been submitted, so we dont want to do anything
            if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            {
                return;
            }
            
    		if(isset($_POST['post_type']) && $_POST['post_type'] == self::POST_TYPE && current_user_can('edit_post', $post_id))
    		{
    			foreach($this->_meta as $field_name)
    			{
    				// Update the post's meta field
    				update_post_meta($post_id, $field_name, $_POST[$field_name]);
    			}
    		}
    		else
    		{
    			return;
    		} // if($_POST['post_type'] == self::POST_TYPE && current_user_can('edit_post', $post_id))
    	} // END public function save_post($post_id)



/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function jtc_slides_add_meta_box() {

	$screens = array( 'jtc_slides' );

	foreach ( $screens as $screen ) {

		add_meta_box(
			'jtc_slides_sectionid',
			__( 'Slide Data', 'jtc_slides_textdomain' ),
			array(&$this,'jtc_slides_meta_box_callback'),
			$screen
		);
	}
}

/**
 * Prints the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function jtc_slides_meta_box_callback( $post ) {

	// Add a nonce field so we can check for it later.
	wp_nonce_field( 'jtc_slides_meta_box', 'jtc_slides_meta_box_nonce' );

	/*
	 * Use get_post_meta() to retrieve an existing value
	 * from the database and use the value for the form.
	 */
	$value0 = get_post_meta( $post->ID, '_excerpt_meta_value_key', true );
	$value = get_post_meta( $post->ID, '_link_meta_value_key', true );
	$value2 = get_post_meta( $post->ID, '_link_nw_meta_value_key', true );
	$value3 = get_post_meta( $post->ID, '_button_meta_value_key', true );
	$value4 = get_post_meta( $post->ID, '_order_meta_value_key', true );

	echo '<label for="Excerpt"><strong>';
	_e( 'Excerpt', 'jtc_slides_textdomain' );
	echo '</strong></label><br>';
	echo '<input type="text" id="jtc_slides_excerpt" name="jtc_slides_excerpt" value="' . esc_attr( $value0 ) . '" style="width:100%;" /><br><br>';
	echo '<label for="Excerpt"><strong>';
	_e( 'Order', 'jtc_slides_textdomain' );
	echo '</strong></label><br>';
	echo '<input type="text" id="jtc_slides_order" name="jtc_slides_order" value="' . esc_attr( $value4 ) . '" size="10" /><br><br>';
	echo '<label for="Link">';
	_e( 'Link', 'jtc_slides_textdomain' );
	echo '</label><br>';
	echo '<input type="text" id="jtc_slides_link" name="jtc_slides_link" value="' . esc_attr( $value ) . '" size="25" /><br>';
	echo '<label for="Link New Window">';
	_e( 'Open in new window', 'jtc_slides_textdomain' );
	echo '</label> ';
	echo '<input ';
	if(esc_attr( $value2 ) == "on"){ echo 'checked="checked" '; }
	echo 'type="checkbox" id="jtc_slides_link_nw" name="jtc_slides_link_nw" size="25" /><br><br>';
	echo '<label for="Button"><br>';
	_e( 'Button Text', 'jtc_slides_textdomain' );
	echo '</label><br>';
	echo '<input type="text" id="jtc_slides_button" name="jtc_slides_button" value="' . esc_attr( $value3 ) . '" size="25" />';
	echo '<p class="description">If left blank no button will appear on slide.</p><br><br>';
	echo '<p><strong>Note: use "Featured Image" to add your image to the slide.</strong><br><a href="http://www.jtc-art.com/code-blog/jtc-slide-show-info" target="_blank">Learn More</a></p>';
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function jtc_slides_save_meta_box_data( $post_id ) {

	/*
	 * We need to verify this came from our screen and with proper authorization,
	 * because the save_post action can be triggered at other times.
	 */

	// Check if our nonce is set.
	if ( ! isset( $_POST['jtc_slides_meta_box_nonce'] ) ) {
		return;
	}

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['jtc_slides_meta_box_nonce'], 'jtc_slides_meta_box' ) ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions.
	if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	/* OK, it's safe for us to save the data now. */
	
	// Make sure that it is set.
	//if ( ! isset( $_POST['jtc_slides_excerpt']) || ! isset( $_POST['jtc_slides_link']) || ! isset( $_POST['jtc_slides_link_nw']) || ! isset( $_POST['jtc_slides_button']) || ! isset( $_POST['jtc_slides_order'] ) ) {
		//return;
	//}

	if ( isset( $_POST['jtc_slides_excerpt'])){
		// Sanitize user input.
		$my_data = sanitize_text_field( $_POST['jtc_slides_excerpt'] );

		// Update the meta field in the database.
		update_post_meta( $post_id, '_excerpt_meta_value_key', $my_data );	
	}
	if( isset( $_POST['jtc_slides_link'])){
	
		// Sanitize user input.
		$my_data1 = sanitize_text_field( $_POST['jtc_slides_link'] );

		// Update the meta field in the database.
		update_post_meta( $post_id, '_link_meta_value_key', $my_data1 );

	}
	if( isset( $_POST['jtc_slides_link_nw'])){
	
		// Sanitize user input.
		$my_data2 = sanitize_text_field( $_POST['jtc_slides_link_nw'] );

		// Update the meta field in the database.
		update_post_meta( $post_id, '_link_nw_meta_value_key', $my_data2 );

	}
	if( isset( $_POST['jtc_slides_button'])){
	
		// Sanitize user input.
		$my_data3 = sanitize_text_field( $_POST['jtc_slides_button'] );
	
		// Update the meta field in the database.
		update_post_meta( $post_id, '_button_meta_value_key', $my_data3 );

	}
	if( isset( $_POST['jtc_slides_order'])){
		
		// Sanitize user input.
		$my_data4 = sanitize_text_field( $_POST['jtc_slides_order'] );

		// Update the meta field in the database.
		update_post_meta( $post_id, '_order_meta_value_key', $my_data4 );
		
	}
}



	} // END class Post_Type_Template
} // END if(!class_exists('Post_Type_Template'))
