<?php
/*
Plugin Name: WP Audio Player
Plugin URI: http://tommcfarlin.com/wp-audio-player/
Description: An easy way to embed an audio file in your posts using the responsive and touch-friendly audio player by Codrops.
Version: 1.3
Author: Tom McFarlin
Author URI: http://tommcfarlin.com/
Author Email: tom@tommcfarlin.com
License:

	Copyright 2013 Tom McFarlin (tom@tommcfarlin.com)

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

class WP_Audio_Player {

	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/

	/**
	 * Initializes the plugin by setting localization, filters, and administration functions.
	 */
	public function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'plugin_textdomain' ) );

		// Register site styles and scripts
		add_action( 'admin_print_styles', array( $this, 'register_admin_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_scripts' ) );

		// Setup the metaboxes and the save post functionality
		add_action( 'add_meta_boxes', array( $this, 'display_audio_url' ) );
		add_action( 'save_post', array( $this, 'save_audio_url' ) );

		// Append the player to the end of the post
		add_filter( 'the_content', array( $this, 'display_audio_content' ) );

	} // end constructor

	/**
	 * Loads the plugin text domain for translation
	 */
	public function plugin_textdomain() {
		load_plugin_textdomain( 'wp-audio-player', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
	} // end plugin_textdomain

	/**
	 * Registers and enqueues plugin-specific styles.
	 */
	public function register_plugin_styles() {
		wp_enqueue_style( 'wp-audio-player', plugins_url( 'wp-audio-player/css/audioplayer.css' ) );
	} // end register_plugin_styles

	/**
	 * Registers and enqueues admin-specific styles.
	 */
	public function register_admin_styles() {
		wp_enqueue_style( 'wp-audio-player-meta', plugins_url( 'wp-audio-player/css/admin.css' ) );
	} // end register_admin_styles

	/**
	 * Registers and enqueues plugin-specific scripts.
	 */
	public function register_plugin_scripts() {
	
		wp_enqueue_script( 'wp-audio-player', plugins_url( 'wp-audio-player/js/audioplayer.min.js' ) );
		wp_enqueue_script( 'wp-audio-player-plugin', plugins_url( 'wp-audio-player/js/plugin.min.js' ) );
		
	} // end register_plugin_scripts

	/*--------------------------------------------*
	 * Core Functions
	 *--------------------------------------------*/

	/**
	 * Adds a meta box to the post edit screen.
	 */
	public function display_audio_url() {

		// First, get all of the post types in the theme
		$args = array(
			'public'              => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'show_in_nav_menus'   => true
		);

		// Next, build up the string used to represent the post types
		foreach( get_post_types( $args ) as $post_type ) {
			$this->add_meta_box( $post_type );
		} // end foreach

		// And finally add support for pages
		$this->add_meta_box( 'page' );

	} // end display_audio_url

	/**
	 * Renders the input field in which to paste the URL of the audio file.
	 *
	 * @param   object   $post   The post to which this meta box is associated.
	 */
	public function display_audio_url_input( $post ) {

		wp_nonce_field( plugin_basename( __FILE__ ), 'wp_audio_player_nonce' );

		$html  = '<span class="description">';
			$html .= __( 'Place the URL to your audio file here.', 'wp-audio-player' );
		$html .= '</span>';
		$html .= '<input type="text" id="wp_audio_url" name="wp_audio_url" value="' . esc_url( get_post_meta( $post->ID, 'wp_audio_url', true ) ) . '" />';

		echo $html;

	} // display_audio_url

	/**
	 * Saves the post data for the Audio URL to post defined by the incoming ID.
	 *
	 * @param   string   $post_id   The ID of the post to which we're saving the post data.
	 */
	public function save_audio_url( $post_id ) {

		if( isset( $_POST['wp_audio_player_nonce'] ) && isset( $_POST['post_type'] ) ) {

			// Don't save if the user hasn't submitted the changes
			if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			} // end if

			// Verify that the input is coming from the proper form
			if( ! wp_verify_nonce( $_POST['wp_audio_player_nonce'], plugin_basename( __FILE__ ) ) ) {
				return;
			} // end if

			// Make sure the user has permissions to post
			if( 'post' == $_POST['post_type'] ) {
				if( ! current_user_can( 'edit_post', $post_id ) ) {
					return;
				} // end if
			} // end if/else

			// Read the post URL
			$wp_audio_url = '';
			if( isset( $_POST['wp_audio_url'] ) ) {
				$wp_audio_url = strip_tags( stripslashes( $_POST['wp_audio_url'] ) );
			} // end if

			// If the value exists, delete it first. I don't want to write extra rows into the table.
			if ( 0 == count( get_post_meta( $post_id, 'wp_audio_url' ) ) ) {
				delete_post_meta( $post_id, 'wp_audio_url' );
			} // end if

			// Update it for this post.
			update_post_meta( $post_id, 'wp_audio_url', $wp_audio_url );

		} // end if

	} // end save_audio_url

	/**
	 * Appends the audio player to the end of the content, if it's been defined for the given post.
	 *
	 * @param   string   $content   The post content to which we're appending the player.
	 * @return  string              The content with the player at the bottom of the content.
	 */
	public function display_audio_content( $content ) {

		// We really only want to do this if we're on the single post page
		if( is_single() ) {

			// Append the audio URL ot the content, if it's defined.
			$audio_url = get_post_meta( get_the_ID(), 'wp_audio_url', true );
			if( 0 != strlen( $audio_url ) ) {

				$audio_html = '<audio src="' . esc_url ( $audio_url ) . '" preload="auto" controls class="wp-audio-player"></audio>';
				$content .= $audio_html;

			} // end if

		} // end if

		return $content;

	} // end display_audio_content

	/*--------------------------------------------*
	 * Private Functions
	 *--------------------------------------------*/

	/**
	 * Adds a 'Feature Audio' meta box to the specified post type.
	 *
	 * @param   string   $post_type   The post type to which we're adding the meta box.
	 */
	private function add_meta_box( $post_type ) {

		add_meta_box(
			'wp_audio_url',
			__( 'Featured Audio', 'wp-audio-player' ),
			array( $this, 'display_audio_url_input' ),
			$post_type,
			'side',
			'low'
		);

	} // end add_meta_box

} // end class

new WP_Audio_Player();