<?php
/*
Plugin Name: JTC Slides
Plugin URI: http://www.jtc-art.com/code-blog
Description: A simple wordpress customizable responsive slide show
Version: 1.0
Author: Jason Campbell
Author URI: http://www.jtc-art.com
License: GPLv2 or later.
*/
/*
Copyright 2015  Jason Campbell  (email : jtcampbellart@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if(!class_exists('JTC_Slides'))
{
	class JTC_Slides
	{
		/**
		 * Construct the plugin object
		 */
		public function __construct()
		{
			// Initialize Settings
			//require_once(sprintf("%s/settings.php", dirname(__FILE__)));
			//$JTC_Slides_Settings = new JTC_Slides_Settings();

			// Register custom post types
			require_once(sprintf("%s/post-types/post_type_jtc_slides.php", dirname(__FILE__)));
			$Post_Type_JTC_Slides = new Post_Type_JTC_Slides();
			
			// Register custom post types
			require_once(sprintf("%s/post-types/post_type_jtc_slide_shows.php", dirname(__FILE__)));
			$Post_Type_JTC_Slide_Shows = new Post_Type_JTC_Slide_Shows();
			
			$plugin = plugin_basename(__FILE__);
			//add_filter("plugin_action_links_$plugin", array( $this, 'plugin_settings_link' ));

			add_shortcode( 'jtcslideshow', array( $this, 'jtcslideshow_func' ));
			add_action('init', array( $this, 'register_jtc_script'));
			add_action('wp_footer', array( $this, 'print_jtc_script'));

		} // END public function __construct
		


		function register_jtc_script() {
			wp_register_script('jtc-script', plugins_url('js/jtc-slide-show.js', __FILE__), array('jquery'), '1.0', true);
		}

		function print_jtc_script() {

			global $add_jtc_script;

			if ( ! $add_jtc_script )
			return;

			wp_print_scripts('jtc-script');
		}

		/**
		 * Activate the plugin
		 */
		public static function activate()
		{
			// Do nothing
		} // END public static function activate

		/**
		 * Deactivate the plugin
		 */
		public static function deactivate()
		{
			// Do nothing
		} // END public static function deactivate

		// Add the settings link to the plugins page
		function plugin_settings_link($links)
		{
			$settings_link = '<a href="options-general.php?page=jtc-slides">Settings</a>';
			array_unshift($links, $settings_link);
			return $links;
		}
		
		// Short Code Function
		function jtcslideshow_func( $atts ) {
			global $add_jtc_script;
			$add_jtc_script = true;
			
    		$a = shortcode_atts( array(
        		'id' => 'none',
        		'slug' => 'none',
        		'mobilewidth' => '700',
        		'bgcolor' => '777777',
    		), $atts );

			// get short code prefs
			$mobilewidth = str_replace("px","",$a['mobilewidth']);
			$bgcolor = str_replace("#","",$a['bgcolor']);

			
			//calculate title box width
			$titleboxwidth = (100-$imageboxwidth);
			
			// get slide show prefs
			$term = get_term_by( 'slug', $a['slug'], 'jtc_slide_show' );
			$termID = $term->term_id;
			$term_meta = get_option( "taxonomy_".$termID);
			$jtcSlideShowBoxColor = esc_attr( $term_meta['jtc_slide_show_box_color'] );
			$jtcSlideShowBoxTextColor = esc_attr( $term_meta['jtc_slide_show_boxt_color'] );
			$jtcSlideShowMaxWidth = esc_attr( $term_meta['jtc_slide_show_max_width'] );
			$jtcSlideShowImageMaxWidth = esc_attr( $term_meta['jtc_slide_show_image_max_width'] );
			$jtcSlideShowMaxHeight = esc_attr( $term_meta['jtc_slide_show_max_height'] );
			$jtcSlideShowAuto = esc_attr( $term_meta['jtc_slide_show_auto'] );
			$jtcSlideShowSpeed = esc_attr( $term_meta['jtc_slide_show_speed'] );
			$jtcSlideShowCss = esc_attr( $term_meta['jtc_slide_show_css'] );

			if($jtcSlideShowBoxColor == ""){ $jtcSlideShowBoxColor = "#555555"; }
			if($jtcSlideShowBoxTextColor == ""){ $jtcSlideShowBoxTextColor = "#FFFFFF"; }
			if($jtcSlideShowMaxWidth == ""){ $jtcSlideShowMaxWidth = "1200"; }
			if($jtcSlideShowImageMaxWidth == ""){ $jtcSlideShowImageMaxWidth = "800"; }
			if($jtcSlideShowMaxHeight == ""){ $jtcSlideShowMaxHeight = "600"; }
			if($jtcSlideShowSpeed == ""){ $jtcSlideShowSpeed = "800"; }
			
			// calculate image box ratio
			$jtcSlideShowImageRatio = ($jtcSlideShowMaxHeight/$jtcSlideShowImageMaxWidth);
			
			// calculate column width percentages
			$jtcSlideShowTitleBoxPercent = (($jtcSlideShowMaxWidth-$jtcSlideShowImageMaxWidth)/$jtcSlideShowMaxWidth)*100;
			$jtcSlideShowImageBoxPercent = ($jtcSlideShowImageMaxWidth/$jtcSlideShowMaxWidth)*100;

			// collect css styling
			$collect1 .= '
			<div id="jtcSlideShowWrapper'.$termID.'">

				<style scoped>
					#jtcSlideShowWrapper'.$termID.' {position:relative;width:100%;display:table;max-width:'.$jtcSlideShowMaxWidth.'px;max-height:'.$jtcSlideShowMaxHeight.'px;}
					#jtcSlideShowWrapper'.$termID.' .jtcSlideShowWrapperInner { position:relative;display:none;max-height:'.$jtcSlideShowMaxHeight.'px;}
					#jtcSlideShowWrapper'.$termID.' .jtcSlideShowBoxes {display:table-cell;width:'.$jtcSlideShowTitleBoxPercent.'%;vertical-align:top;height:100%;max-height:'.$jtcSlideShowMaxHeight.'px;}
					#jtcSlideShowWrapper'.$termID.' .jtcSlideShowBoxesInner {width:'.$jtcSlideShowTitleBoxPercent.'%;display:block;position:absolute;top:0;bottom:0;left:0;right:0;max-height:'.$jtcSlideShowMaxHeight.'px;background:#'.$bgcolor.';}
					#jtcSlideShowWrapper'.$termID.' .jtcSlideShowBoxes .jtcSlideShowBox {display:block;width:100%;vertical-align:top;overflow:hidden;}
					#jtcSlideShowWrapper'.$termID.' .jtcSlideShowBoxes .jtcSlideShowBox .jtcSlideShowBoxInner {display:table;width:100%;vertical-align:middle;height:100%;background-color:'.$jtcSlideShowBoxColor.';}
					#jtcSlideShowWrapper'.$termID.' .jtcSlideShowBoxes .jtcSlideShowBox .jtcSlideShowBoxInner a {border-bottom:none;font-weight:bold;text-transform:uppercase;font-size:14px;line-height: 1.4;display:table-cell;vertical-align:middle;height:100%;color:'.$jtcSlideShowBoxTextColor.';padding: 0 20px;border-top:solid 1px #'.$bgcolor.';}
					#jtcSlideShowWrapper'.$termID.' .jtcSlideShowBoxes .jtcSlideShowBox:first-child .jtcSlideShowBoxInner a, #jtcSlideShowWrapper'.$termID.' .jtcSlideShowBoxes .jtcSlideShowBox:first-child .jtcSlideShowBoxInner a:hover, #jtcSlideShowWrapper'.$termID.' .jtcSlideShowBoxes .jtcSlideShowBox:first-child .jtcSlideShowBoxInner a.selected {border-top:none;}
					#jtcSlideShowWrapper'.$termID.' .jtcSlideShowBoxes .jtcSlideShowBox .jtcSlideShowBoxInner a:hover, #jtcSlideShowWrapper'.$termID.' .jtcSlideShowBoxes .jtcSlideShowBox .jtcSlideShowBoxInner a.selected, #jtcSlideShowWrapper'.$termID.' .jtcSlideButton:hover {color:'.$jtcSlideShowBoxColor.';background:'.$jtcSlideShowBoxTextColor.';border-top:solid 1px #'.$bgcolor.';}
					#jtcSlideShowWrapper'.$termID.' .jtcSlides {display:table-cell;max-width:'.$jtcSlideShowMaxWidth.'px;width:'.$jtcSlideShowImageBoxPercent.'%;max-height:'.$jtcSlideShowMaxHeight.'px;vertical-align:top;position:relative;overflow:hidden;text-align:center;background:#'.$bgcolor.';}
					#jtcSlideShowWrapper'.$termID.' .jtcSlides .jtcSlidesInner {position:absolute;top:0;left:0;display:block;width:100%;height:100%;}
					#jtcSlideShowWrapper'.$termID.' .jtcSlide {height: 100%;display:none; position:  relative;max-height:'.$jtcSlideShowMaxHeight.'px;overflow:hidden;}
					#jtcSlideShowWrapper'.$termID.' .jtcSlide a {position: absolute;top: 0;left: 0;width: 100%;height: 100%;border:none;z-index:10;background:url("'.plugins_url('clear.gif', __FILE__).'");}
					#jtcSlideShowWrapper'.$termID.' #jtcSlide-'.$termID.'-1 {display:block;}
					#jtcSlideShowWrapper'.$termID.' .jtcSlide img {max-height: 100%;max-width: 100%;}
					#jtcSlideShowWrapper'.$termID.' .jtcSlideExcerpt {line-height:1.7;position:absolute;font-size:14px;text-align:left;bottom:0;left:0;width:100%;padding:5px;background:#333333;background:rgba(20,20,20,0.8);color:#FFFFFF;}
					#jtcSlideShowWrapper'.$termID.' a.jtcSlideButton {display:inline;width:auto;height:auto;position:relative;padding:2px 5px;font-size:12px;font-weight:bold;text-transform:uppercase;float:right;margin-left:5px;background:'.$jtcSlideShowBoxColor.';color:'.$jtcSlideShowBoxTextColor.';}

					@media screen and (max-width:'.$mobilewidth.'px){
						#jtcSlideShowWrapper'.$termID.' .jtcSlideShowWrapperInner {display:block;}
						#jtcSlideShowWrapper'.$termID.' .jtcSlideShowBoxes {display:block;width:auto;height:auto !important;}	
						#jtcSlideShowWrapper'.$termID.' .jtcSlides {display:block;width:auto;}
						#jtcSlideShowWrapper'.$termID.' .jtcSlideShowBoxesInner {display:block;position:relative;width:auto;}	
						#jtcSlideShowWrapper'.$termID.' .jtcSlideShowBoxes {display:block;width:auto;}
						#jtcSlideShowWrapper'.$termID.' .jtcSlideShowBoxes .jtcSlideShowBox .jtcSlideShowBoxInner a {display:block;width:auto;padding:10px;}				
					}
			';
			
			// add custom css styling pref if any
			if($jtcSlideShowCss != ""){
				$collect1 .= $jtcSlideShowCss;
			}

  			// query slides with this slide show selected            
 			$args2 = array(
 				'post_type'   => 'jtc_slides',
 				'jtc_slide_show' => $a['slug'],
 				'meta_key' => '_order_meta_value_key',
				'orderby' => 'meta_value_num',
 				'order' => 'ASC',
 				'meta_query' => array(
  					array(
     					'key' => '_order_meta_value_key',
     					//'value' => '',
     					//'compare' => '>=',
     					//'type' => 'NUMERIC'
  					)
 				)
			);

			$the_query = new WP_Query( $args2 );

            // loop through slides and add them to collection
			if ( $the_query->have_posts() ) {
				$collect2 .= '
				<div class="jtcSlideShowBoxes">
				<div class="jtcSlideShowBoxesInner">'
				;
				$collect3 .= '
				<div class="jtcSlides">
				<div class="jtcSlidesInner">
				';
				$i = 1;
				while ( $the_query->have_posts() ) {
					$the_query->the_post();
					$postID = get_the_ID();
					$jtcSlideExcerpt = get_post_meta( $postID, '_excerpt_meta_value_key', true );
					$jtcSlideLink = get_post_meta( $postID, '_link_meta_value_key', true );
					$jtcSlideLinkNW = get_post_meta( $postID, '_link_nw_meta_value_key', true );
					$jtcSlideButton = get_post_meta( $postID, '_button_meta_value_key', true );
		
					$collect2 .= '<div class="jtcSlideShowBox" id="jtcSlideShowBox-'.$termID.'-'.$i.'"><div class="jtcSlideShowBoxInner"><a ';
					if($i == 1){ $collect2 .= 'class="selected" '; }
					$collect2 .= 'data-jtc-slide="'.$termID.'-'.$i.'" data-jtc-slide-show-id="'.$termID.'" href="javascript:;" >' . get_the_title() . '</a></div></div>';
					$collect3 .= '<div class="jtcSlide" id="jtcSlide-'.$termID.'-'.$i.'">';
					if($jtcSlideLink != ""){
						$collect3 .= '<a href="'.$jtcSlideLink.'" ';
						if($jtcSlideLinkNW == "on"){ $collect3 .= 'target="_blank" '; }
						$collect3 .= '></a>';
						
					}
					$collect3 .= '<img ';
					if($i == 1){ $collect3 .= 'class="jtcFirstSlide" '; }
					$collect3 .= 'src="'.wp_get_attachment_url( get_post_thumbnail_id( $postID )).'" alt="' . get_the_title() . '" />';
		
					if($jtcSlideExcerpt != ""){
						$collect3 .= '<div class="jtcSlideExcerpt">'. $jtcSlideExcerpt;
						if($jtcSlideButton != ""){ 
							$collect3 .= '<a class="jtcSlideButton" href="'.$jtcSlideLink.'" '; 
								if($jtcSlideLinkNW == "on"){ $collect3 .= 'target="_blank" '; }
								$collect3 .= '>'. $jtcSlideButton .'</a>';
			
						}
						$collect3 .= '</div>
						</div>';	
					}
					$i++;
		
				}
				$collect1 .= '
					#jtcSlideShowWrapper'.$termID.' .jtcSlideShowBoxes .jtcSlideShowBox{height:'.(100/($i-1)).'%;}
				</style>
				<div class="jtcSlidesLoading"><img src="'.plugins_url('loading.gif', __FILE__).'" alt="Loading..."></div>
				<div class="jtcSlideShowWrapperInner">
	
				';
				$collect2 .= '</div></div>';
				$collect3 .= '</div></div>';
					
				// combine collections
				$collect = $collect1.$collect2.$collect3.'</div></div>';
					
				// add auto play script if selected. Add loading bar and window resize function to adjust height either way.
				if($jtcSlideShowAuto == "on"){
			
						$collect .= '
						<script> 
						var jtcAutoInterval'.$termID.';
						var jtcCurSlide'.$termID.' = 1;
						var jtcTotalSlides'.$termID.' = '.($i-1).';

						function changeJtcSlide'.$termID.'() {
							var nextJtcSlide;
							if(jtcCurSlide'.$termID.' < jtcTotalSlides'.$termID.') {
								nextJtcSlide = jtcCurSlide'.$termID.' + 1;
							} else {
								nextJtcSlide = 1;
							
							}
    						var jtcSlideId = '.$termID.';
							var jtcSlideNum = jtcSlideId + "-" + nextJtcSlide;
							jQuery("#jtcSlideShowWrapper'.$termID.' .jtcSlide").hide();
							jQuery("#jtcSlideShowWrapper'.$termID.' #jtcSlide-"+jtcSlideNum).fadeIn();
							jQuery("#jtcSlideShowWrapper'.$termID.' .jtcSlideShowBox .jtcSlideShowBoxInner a").removeClass("selected");
							jQuery("#jtcSlideShowWrapper'.$termID.' #jtcSlideShowBox-"+jtcSlideNum+" .jtcSlideShowBoxInner a").addClass("selected");
							jtcCurSlide'.$termID.' = nextJtcSlide;

						}
	 
	 					function autoJtcSlideShow'.$termID.'(){
	 				
    						jtcAutoInterval'.$termID.' = setInterval( function() { changeJtcSlide'.$termID.'() }, 5000);	 	
	 	
	 					}
	 					
						 function adjustWrapperHeight'.$termID.'(){
	 						var wrapperWidth = jQuery("#jtcSlideShowWrapper'.$termID.' .jtcSlides").width();
	 						var slideShowRatio = '.$jtcSlideShowImageRatio.';
	 						jQuery("#jtcSlideShowWrapper'.$termID.'").height((wrapperWidth*slideShowRatio) + "px");
	 						jQuery("#jtcSlideShowWrapper'.$termID.' .jtcSlides").height((wrapperWidth*slideShowRatio) + "px");
	 						jQuery("#jtcSlideShowWrapper'.$termID.' .jtcSlides .jtcSlidesInner").height((wrapperWidth * slideShowRatio) + "px");
	 					}
						
						jQuery("document").ready(function(){						
				 			jQuery(window).resize(function(){adjustWrapperHeight'.$termID.'();}); 
						}); 
						
						jQuery("#jtcSlide-'.$termID.'-1 img.jtcFirstSlide").load(function() {
				 			jQuery("#jtcSlideShowWrapper'.$termID.' .jtcSlidesLoading").hide();
				 			jQuery("#jtcSlideShowWrapper'.$termID.' .jtcSlideShowWrapperInner").css( "display", "table-row" );
				 			adjustWrapperHeight'.$termID.'();
				 			autoJtcSlideShow'.$termID.'();
				 			jQuery("#jtcSlideShowWrapper'.$termID.'").hover(function(){clearInterval(jtcAutoInterval'.$termID.');});
				 			jQuery("#jtcSlideShowWrapper'.$termID.'").mouseleave(function(){autoJtcSlideShow'.$termID.'();});
						});
						
						</script>';
							
				} else {
				
					$collect .= '
					<script> 
					
						 function adjustWrapperHeight'.$termID.'() {
	 						var wrapperWidth = jQuery("#jtcSlideShowWrapper'.$termID.' .jtcSlides").width();
	 						var slideShowRatio = '.$jtcSlideShowImageRatio.';
	 						jQuery("#jtcSlideShowWrapper'.$termID.'").height((wrapperWidth*slideShowRatio)+"px");
	 						jQuery("#jtcSlideShowWrapper'.$termID.' .jtcSlides").height((wrapperWidth*slideShowRatio)+"px");
	 						jQuery("#jtcSlideShowWrapper'.$termID.' .jtcSlides .jtcSlidesInner").height((wrapperWidth*slideShowRatio)+"px");
	 					}
	 			
						jQuery("document").ready(function(){
				 			jQuery(window).resize(function() {adjustWrapperHeight'.$termID.'();});
						}); 
						
						jQuery("#jtcSlide-'.$termID.'-1 img.jtcFirstSlide").load(function(){
				 			jQuery("#jtcSlideShowWrapper'.$termID.' .jtcSlidesLoading").hide();
				 			jQuery("#jtcSlideShowWrapper'.$termID.' .jtcSlideShowWrapperInner").css( "display", "table-row" );
				 			adjustWrapperHeight'.$termID.'();
						});
						 
					</script>
					';
			
				}
			} else {
				// no posts found error message
				return '<p style="font-weight:bold;font-color:red;">Error: No posts found for this slide show '.$a['slug'].'.</p>';
			}
			/* Restore original Post Data */
			wp_reset_postdata();

			// return all of that awesome stuff!
			return $collect;
		}

	} // END class WP_Plugin_Template
} // END if(!class_exists('WP_Plugin_Template'))

if(class_exists('JTC_Slides'))
{
	// Installation and uninstallation hooks
	register_activation_hook(__FILE__, array('JTC_Slides', 'activate'));
	register_deactivation_hook(__FILE__, array('JTC_Slides', 'deactivate'));

	// instantiate the plugin class
	$JTC_Slides = new JTC_Slides();

}