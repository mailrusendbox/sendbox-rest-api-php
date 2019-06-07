<?php

/**
 *
 *
 * Class Sendbox_Newsletter_Forms
 */
class Sendbox_Newsletter_Forms {

	private $post_type = 'sendbox_form';

	public function __construct() {


		add_action( 'init', array( $this, 'register_forms_post' ) );


		add_action( 'add_meta_boxes_sendbox_form', array( $this, 'meta_box' ) );

		add_action( 'save_post', array( $this, 'save_meta' ) );

		add_filter( "manage_{$this->post_type}_posts_columns", array( $this, 'get_columns' ) );
		add_action( "manage_{$this->post_type}_posts_custom_column", array( $this, 'render_column' ), 10, 2 );

		add_filter( 'post_updated_messages', array( $this, 'change_form_updated_messages' ) );
		add_filter( 'post_date_column_status', array( $this, 'change_date_column_status' ), 10, 2 );


	}

	public function register_forms_post() {
		$labels = array(
			'name'               => _x( 'Sendbox Forms', 'Post type general name', 'sendbox-email-marketing-newsletter' ),
			'singular_name'      => _x( 'Sendbox Form', 'Post type singular name', 'sendbox-email-marketing-newsletter' ),
			'menu_name'          => _x( 'Sendbox', 'Admin Menu text', 'sendbox-email-marketing-newsletter' ),
			'name_admin_bar'     => _x( 'Sendbox Form', 'Add New on Toolbar', 'sendbox-email-marketing-newsletter' ),
			'add_new'            => _x( 'Add Form', 'Add New SP form', 'sendbox-email-marketing-newsletter' ),
			'add_new_item'       => __( 'Add New Sendbox Form', 'sendbox-email-marketing-newsletter' ),
			'new_item'           => __( 'New Sendbox Form', 'sendbox-email-marketing-newsletter' ),
			'edit_item'          => __( 'Edit Sendbox Form', 'sendbox-email-marketing-newsletter' ),
			'view_item'          => __( 'View Sendbox Form', 'sendbox-email-marketing-newsletter' ),
			'all_items'          => __( 'Sendbox Forms', 'sendbox-email-marketing-newsletter' ),
			'search_items'       => __( 'Search Sendbox Forms', 'sendbox-email-marketing-newsletter' ),
			'parent_item_colon'  => __( 'Parent Sendbox Forms:', 'sendbox-email-marketing-newsletter' ),
			'not_found'          => __( 'No Sendbox Forms found.', 'sendbox-email-marketing-newsletter' ),
			'not_found_in_trash' => __( 'No Sendbox Forms found in Trash.', 'sendbox-email-marketing-newsletter' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'menu_icon'          => '',
			'query_var'          => false,
			'has_archive'        => false,
			'supports'           => array( 'title' ),
		);

		register_post_type( 'sendbox_form', $args );
	}

	public function meta_box() {
		add_meta_box(
			'sendbox_form_code',           // Unique ID
			__( 'Constructor Form Code (paste code Sendbox form)', 'sendbox-email-marketing-newsletter' ),  // Box title
			array( $this, 'code_metabox_output' ),  // Content callback, must be of type callable
			'sendbox_form'                   // Post type
		);

		add_meta_box( 'sendbox_form_shortcode', __( 'Shortcode', 'sendbox-email-marketing-newsletter' ), array(
			$this,
			'shortcode_metabox_output'
		), 'sendbox_form', 'side', 'core', null );

		$this->remove_built_in_metaboxes();


	}

	public function code_metabox_output( $post ) {
		$code = get_post_meta( $post->ID, '_sp_form_code', true );
		?>
        <textarea rows="20" cols="40" name="sp_form_code" id="sp_form_code"
                  placeholder="<?php _e( 'Paste code here', 'sendbox-email-marketing-newsletter' ); ?>"><?php echo $code; ?></textarea>
        <p><?php echo sprintf( __( 'Code from <a href="%s">Constructor Form</a> (<a class="%s" href="%s" title="Open help page in new tab" target="_blank">Need help?</a>)', 'sendbox-email-marketing-newsletter' ),
				'https://mailer.i.bizml.ru/emailservice/forms/constructor/',
				'h-help',
				'https://help.mail.ru/biz/sendbox/email/ab/constructor' ); ?></p>
		<?php
	}

	public function save_meta( $post_id ) {
		if ( array_key_exists( 'sp_form_code', $_POST ) ) {
			update_post_meta(
				$post_id,
				'_sp_form_code',
				$_POST['sp_form_code']
			);
		}
	}

	public function shortcode_metabox_output( $post ) {
		$this->shortcode_text( $post->ID );
		$desc = __( 'You should paste this shortcode in themes files', 'sendbox-email-marketing-newsletter' );
		?>

        <p><?php echo $desc; ?></p>
		<?php

		$this->post_submit_meta_box( $post );

	}

	public function remove_built_in_metaboxes() {

		remove_meta_box( 'submitdiv', null, 'side' );

	}

	public function post_submit_meta_box( $post ) {
		?>

        <div class="submitbox" id="submitpost">
            <div style="display:none;">
				<?php submit_button( __( 'Save' ), '', 'save' ); ?>
            </div>
            <div id="major-publishing-actions">
                <div id="delete-action">
					<?php
					if ( current_user_can( "delete_post", $post->ID ) ) {
						if ( ! EMPTY_TRASH_DAYS ) {
							$delete_text = __( 'Delete Permanently' );
						} else {
							$delete_text = __( 'Move to Trash' );
						}
						?>
                        <a class="submitdelete deletion"
                           href="<?php echo get_delete_post_link( $post->ID ); ?>"><?php echo $delete_text; ?></a><?php
					} ?>
                </div>
                <div id="publishing-action">
                    <span class="spinner"></span>
                    <input name="original_publish" type="hidden" id="original_publish"
                           value="<?php esc_attr_e( 'Save' ) ?>"/>
					<?php submit_button( __( 'Save' ), 'primary large', 'publish', false ); ?>
                </div>
                <div class="clear"></div>
            </div>

        </div>

	<?php }

	public function get_columns( $columns ) {

		$first_array = array_splice( $columns, 0, 2 );
		$columns     = array_merge( $first_array, array( 'sp_shortcode' => __( 'Shortcode', 'sendbox-email-marketing-newsletter' ) ), $columns );


		return $columns;

	}

	public function render_column( $column_name, $post_id ) {

		if ( 'sp_shortcode' == $column_name ) {
			$this->shortcode_text( $post_id );
		}

	}

	protected function shortcode_text( $post_id ) {
		$shortcode = sprintf( '[sendbox-form id="%s"]', esc_attr( $post_id ) );
		$desc      = __( 'You should paste this shortcode in themes files', 'sendbox-email-marketing-newsletter' ); ?>

        <input type="text" value="<?php echo esc_attr( $shortcode ); ?>" title="<?php echo $desc; ?>"
               readonly="readonly">
		<?php
	}

	public function change_form_updated_messages( $messages ) {
		global $post_type;

		if ( 'sendbox_form' == $post_type ) {
			$messages['post'][1] =
			$messages['post'][4] =
			$messages['post'][6] = __( 'Saved', 'sendbox-email-marketing-newsletter' );
		}

		return $messages;
	}

	/**
	 * @param $status string
	 * @param $post \WP_Post
	 *
	 * @return string
	 *
	 */
	public function change_date_column_status( $status, $post ) {
		if ( 'sendbox_form' == $post->post_type ) {
			$status = __( 'Saved', 'sendbox-email-marketing-newsletter' );
		}

		return $status;
	}


}

new Sendbox_Newsletter_Forms();