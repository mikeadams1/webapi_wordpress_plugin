<?php


if ( ! class_exists( 'WebAPI_Meta_Box' ) ) {

	/**
	 * Generated by the WordPress Meta Box Generator at http://goo.gl/8nwllb
	 */
	class WebAPI_Meta_Box {

		/**
		 * Manage the conditiional load of webapi
		 *
		 * @var short_code used for the conditional load of scripts
		 */
		private $short_code;

		private $screens = array(
			'navionics_webapi',
		);
		private $fields = array(
			array(
				'id' => 'class_name',
				'label' => 'Class Name',
				'type' => 'text',
			),
			array(
				'id' => 'style_content',
				'label' => 'Cascading Style Sheets',
				'type' => 'textarea',
				'rows' => 3,
			),
			array(
				'id' => 'code_content',
				'label' => 'JavaScript code',
				'type' => 'textarea',
				'rows' => 10,
			),
		);

		/**
		 * Class construct method. Adds actions to their respective WordPress hooks.
		 */
		public function __construct($short_code="") {
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
			add_action( 'save_post', array( $this, 'save_post' ) );
			$this->short_code=$short_code;
		}

		/**
		 * Hooks into WordPress' add_meta_boxes function.
		 * Goes through screens (post types) and adds the meta box.
		 */
		public function add_meta_boxes() {
			foreach ( $this->screens as $screen ) {
				add_meta_box(
					'navionics-webapi',
					__( 'Navionics WebAPIv2', 'nwa' ),
					array( $this, 'add_meta_box_callback' ),
					$screen,
					'normal',
					'high'
				);
			}
		}

		/**
		 * Generates the HTML for the meta box
		 *
		 * @param object $post WordPress post object
		 */
		public function add_meta_box_callback( $post ) {
			wp_nonce_field( 'navionics_webapi_data', 'navionics_webapi_nonce' );
			$this->generate_fields( $post );
			printf(
				"<hr><h2><span class=\"dashicons dashicons-lightbulb\"></span> ".__('Use the%sto add this component to your pages', 'nwa')."</h2>",
				'<code>['. $this->short_code .' item='.$post->ID .']</code>'
			);
		}

		/**
		 * Generates the field's HTML for the meta box.
		 */
		public function generate_fields( $post ) {
			$output = '';
			foreach ( $this->fields as $field ) {
				$label = '<label for="' . $field['id'] . '">' . $field['label'] . '</label>';
				$db_value = get_post_meta( $post->ID, 'navionics_webapi_' . $field['id'], true );
				switch ( $field['type'] ) {
					case 'textarea':
						$input = sprintf(
							'<textarea class="large-text" id="%s" name="%s" rows="%s">%s</textarea>',
							$field['id'],
							$field['id'],
							$field['rows'],
							$db_value
						);
						break;
					default:
						$input = sprintf(
							'<input %s id="%s" name="%s" type="%s" value="%s">',
							$field['type'] !== 'color' ? 'class="regular-text"' : '',
							$field['id'],
							$field['id'],
							$field['type'],
							$db_value
						);
				}
				$output .= $this->row_format( $label, $input );
			}
			echo '<table class="form-table"><tbody>' . $output . '</tbody></table>';
		}

		/**
		 * Generates the HTML for table rows.
		 */
		public function row_format( $label, $input ) {
			return sprintf(
				'<tr><th scope="row">%s</th><td>%s</td></tr>',
				$label,
				$input
			);
		}

		/**
		 * Hooks into WordPress' save_post function
		 */
		public function save_post( $post_id ) {
			if ( ! isset( $_POST['navionics_webapi_nonce'] ) )
				return $post_id;

			$nonce = $_POST['navionics_webapi_nonce'];
			if ( !wp_verify_nonce( $nonce, 'navionics_webapi_data' ) )
				return $post_id;

			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
				return $post_id;

			foreach ( $this->fields as $field ) {
				if ( isset( $_POST[ $field['id'] ] ) ) {
					switch ( $field['type'] ) {
						case 'email':
							$_POST[ $field['id'] ] = sanitize_email( $_POST[ $field['id'] ] );
							break;
						case 'text':
							$_POST[ $field['id'] ] = sanitize_text_field( $_POST[ $field['id'] ] );
							break;
					}
					update_post_meta( $post_id, 'navionics_webapi_' . $field['id'], $_POST[ $field['id'] ] );
				} else if ( $field['type'] === 'checkbox' ) {
					update_post_meta( $post_id, 'navionics_webapi_' . $field['id'], '0' );
				}
			}

			/*if ( ! wp_is_post_revision( $post_id ) ){

				// unhook this function so it doesn't loop infinitely
				remove_action('save_post', array($this, 'save_post' ));

				$my_args = array(
					'ID' => esc_sql($post_id),
					'post_content' => 'Pippo' . do_shortcode( '['.$this->short_code.' item='.$post_id.' ]'),
					'post_title' => wp_strip_all_tags($_POST['post_title'])
				);

				// update the post, which calls save_post again
				wp_update_post( $my_args );

				// re-hook this function
				add_action('save_post', array($this, 'save_post' ));
			}*/

		}
		
	}


}