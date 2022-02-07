<?php
/**
 * Gravity Forms MailerLite Add-On.
 *
 * @since     1.0
 * @package   GravityForms
 * @author    Closemarketing
 * @copyright Copyright (c) 2017, Rocketgenius
 */

GFForms::include_feed_addon_framework();

/**
 * Class for Adding functionality to GravityForms
 */
class GF_CGFM extends GFFeedAddOn {

	/**
	 * Contains an instance of this class, if available.
	 *
	 * @since  Unknown
	 * @access private
	 * @var    object $_instance If available, contains an instance of this class.
	 */
	private static $_instance = null;

	/**
	 * Defines the version of the MailerLite Add-On.
	 *
	 * @since  Unknown
	 * @access protected
	 * @var    string $_version Contains the version, defined from mailerlite.php
	 */
	protected $_version = GF_CGFM_VERSION;

	/**
	 * Defines the minimum Gravity Forms version required.
	 *
	 * @since  Unknown
	 * @access protected
	 * @var    string $_min_gravityforms_version The minimum version required.
	 */
	protected $_min_gravityforms_version = '1.9.3';

	/**
	 * Defines the plugin slug.
	 *
	 * @since  Unknown
	 * @access protected
	 * @var    string $_slug The slug used for this plugin.
	 */
	protected $_slug = 'connector-gravityforms-mailerlite';

	/**
	 * Defines the main plugin file.
	 *
	 * @since  Unknown
	 * @access protected
	 * @var    string $_path The path to the main plugin file, relative to the plugins folder.
	 */
	protected $_path = 'connector-gravityforms-mailerlite/mailerlite.php';

	/**
	 * Defines the full path to this class file.
	 *
	 * @since  Unknown
	 * @access protected
	 * @var    string $_full_path The full path.
	 */
	protected $_full_path = __FILE__;

	/**
	 * Defines the URL where this Add-On can be found.
	 *
	 * @since  Unknown
	 * @access protected
	 * @var    string The URL of the Add-On.
	 */
	protected $_url = 'https://wordpress.org/plugins/connector-gravityforms-mailerlite/';

	/**
	 * Defines the title of this Add-On.
	 *
	 * @since  Unknown
	 * @access protected
	 * @var    string $_title The title of the Add-On.
	 */
	protected $_title = 'Connector for Mailerlite Add-On';

	/**
	 * Defines the short title of the Add-On.
	 *
	 * @since  Unknown
	 * @access protected
	 * @var    string $_short_title The short title.
	 */
	protected $_short_title = 'MailerLite';

	/**
	 * Defines if Add-On should use Gravity Forms servers for update data.
	 *
	 * @since  Unknown
	 * @access protected
	 * @var    bool
	 */
	protected $_enable_rg_autoupgrade = true;

	/**
	 * Defines the capability needed to access the Add-On settings page.
	 *
	 * @since  Unknown
	 * @access protected
	 * @var    string $_capabilities_settings_page The capability needed to access the Add-On settings page.
	 */
	protected $_capabilities_settings_page = 'gravityforms_mailerlite';

	/**
	 * Defines the capability needed to access the Add-On form settings page.
	 *
	 * @since  Unknown
	 * @access protected
	 * @var    string $_capabilities_form_settings The capability needed to access the Add-On form settings page.
	 */
	protected $_capabilities_form_settings = 'gravityforms_mailerlite';

	/**
	 * Defines the capability needed to uninstall the Add-On.
	 *
	 * @since  Unknown
	 * @access protected
	 * @var    string $_capabilities_uninstall The capability needed to uninstall the Add-On.
	 */
	protected $_capabilities_uninstall = 'gravityforms_mailerlite_uninstall';

	/**
	 * Defines the capabilities needed for the MailerLite Add-On
	 *
	 * @since  Unknown
	 * @access protected
	 * @var    array $_capabilities The capabilities needed for the Add-On
	 */
	protected $_capabilities = array( 'gravityforms_mailerlite', 'gravityforms_mailerlite_uninstall' );

	/**
	 * Contains an instance of the MailerLite API library, if available.
	 *
	 * @since  3.5
	 * @access protected
	 * @var    object $api If available, contains an instance of the MailerLite API library.
	 */
	public $api = null;

	/**
	 * Get instance of this class.
	 *
	 * @since  Unknown
	 * @access public
	 * @static
	 *
	 * @return $_instance
	 */
	public static function get_instance() {

		if ( null === self::$_instance ) {
			self::$_instance = new self;
		}

		return self::$_instance;

	}

	/**
	 * Register needed hooks for Add-On.
	 *
	 * @since  Unknown
	 * @access public
	 */
	public function init() {

		parent::init();

		$this->add_delayed_payment_support(
			array(
				'option_label' => esc_html__( 'Subscribe user to MailerLite only when payment is received.', 'connector-gravityforms-mailerlite' )
			)
		);

	}

	// # PLUGIN SETTINGS -----------------------------------------------------------------------------------------------

	/**
	 * Prepare plugin settings fields.
	 *
	 * @since  Unknown
	 * @access public
	 *
	 * @uses GFCampaignMonitor::get_clients_as_choices()
	 *
	 * @return array
	 */
	public function plugin_settings_fields() {

		return array(
			array(
				'title'       => esc_html__( 'MailerLite Account Information', 'connector-gravityforms-mailerlite' ),
				'description' => sprintf(
					'<p>%s</p>',
					sprintf(
						esc_html__( 'MailerLite is an email marketing software for designers and their clients. Use Gravity Forms to collect customer information and automatically add it to your client\'s MailerLite subscription list. If you don\'t have a MailerLite account, you can %1$ssign up for one here.%2$s', 'connector-gravityforms-mailerlite' ),
						'<a href="https://close.marketing/likes/mailerlite/" target="_blank">',
						'</a>'
					)
				),
				'fields'      => array(
					array(
						'name'              => 'apiKey',
						'label'             => esc_html__( 'API Key', 'connector-gravityforms-mailerlite' ),
						'type'              => 'text',
						'class'             => 'medium',
						'feedback_callback' => array( $this, 'initialize_api' ),
						'description'       => sprintf( wp_kses( __( 'You can find your Developer API key <a href="%s" target="_blank">here</a>.', 'connector-gravityforms-mailerlite' ), array(  'a' => array( 'href' => array(), 'target' => array() ) ) ), esc_url( 'https://app.mailerlite.com/integrations/api/' )
						),
					),
				),
			),
		);

	}

	// # FEED SETTINGS -------------------------------------------------------------------------------------------------

	/**
	 * Configures the settings which should be rendered on the feed edit page.
	 *
	 * @since  Unknown
	 * @access public
	 *
	 * @uses GFCampaignMonitor::get_custom_fields_as_field_map()
	 * @uses GFCampaignMonitor::get_lists_as_choices()
	 *
	 * @return array The feed settings.
	 */
	public function feed_settings_fields() {

		return array(
			array(
				'title'  => esc_html__( 'MailerLite Feed', 'connector-gravityforms-mailerlite' ),
				'fields' => array(
					array(
						'name'     => 'feedName',
						'label'    => esc_html__( 'Name', 'connector-gravityforms-mailerlite' ),
						'type'     => 'text',
						'required' => true,
						'class'    => 'medium',
						'tooltip'  => sprintf(
							'<h6>%s</h6>%s',
							esc_html__( 'Name', 'connector-gravityforms-mailerlite' ),
							esc_html__( 'Enter a feed name to uniquely identify this setup.', 'connector-gravityforms-mailerlite' )
						),
					),
					array(
						'name'       => 'groupList',
						'label'      => esc_html__( 'Group List', 'connector-gravityforms-mailerlite' ),
						'type'       => 'select',
						'required'   => true,
						'choices'    => $this->get_lists_as_choices(),
						'onchange'   => 'jQuery(this).parents("form").submit();',
						'no_choices' => sprintf(
							esc_html__( 'No clients found. Please configure one or more clients in your %1$sMailerLite%2$s account.', 'connector-gravityforms-mailerlite' ),
							'<a href="http://www.mailerlite.com" target="_blank">',
							'</a>'
						),
						'tooltip'    => sprintf(
							'<h6>%s</h6>%s',
							esc_html__( 'Contact List', 'connector-gravityforms-mailerlite' ),
							esc_html__( 'Select the MailerLite list you would like to add your contacts to.', 'connector-gravityforms-mailerlite' )
						),
					),
					array(
						'name'       => 'listFields',
						'label'      => esc_html__( 'Map Fields', 'connector-gravityforms-mailerlite' ),
						'type'       => 'field_map',
						'dependency' => 'groupList',
						'field_map'  => $this->get_custom_fields_as_field_map(),
						'tooltip'    => sprintf(
							'<h6>%s</h6>%s',
							esc_html__( 'Map Fields', 'connector-gravityforms-mailerlite' ),
							esc_html__( 'Associate your MailerLite custom fields to the appropriate Gravity Form fields by selecting the appropriate form field from the list.', 'connector-gravityforms-mailerlite' )
						),
					),
					array(
						'name'       => 'optin',
						'label'      => esc_html__( 'Conditional Logic', 'connector-gravityforms-mailerlite' ),
						'type'       => 'feed_condition',
						'dependency' => 'groupList',
						'tooltip'    => sprintf(
							'<h6>%s</h6>%s',
							esc_html__( 'Conditional Logic', 'connector-gravityforms-mailerlite' ),
							esc_html__( 'When conditional logic is enabled, form submissions will only be exported to MailerLite when the condition is met. When disabled all form submissions will be exported.', 'connector-gravityforms-mailerlite' )
						),
					),
					array(
						'name'       => 'resubscribe',
						'label'      => esc_html__( 'Options', 'connector-gravityforms-mailerlite' ),
						'type'       => 'option_resubscribe',
						'dependency' => 'groupList',
						'onclick'    => "if(this.checked){jQuery('#mailerlite_resubscribe_warning').slideDown();} else{jQuery('#mailerlite_resubscribe_warning').slideUp();}",
					),
					array(
						'type'       => 'save',
						'dependency' => 'groupList',
					),
				)
			),
		);

	}

	/**
	 * Prepare MailerLite custom fields as a field map.
	 *
	 * @since  3.5
	 * @access public
	 *
	 * @uses GFAddOn::get_setting()
	 * @uses GFAddOn::log_error()
	 * @uses GFCampaignMonitor::initialize_api()
	 * @uses GF_CampaignMonitor_API::get_custom_fields()
	 *
	 * @return array
	 */
	public function get_custom_fields_as_field_map() {

		// Initialize field map.
		$field_map = array();

		// If API is not initialized, return.
		if ( ! $this->initialize_api() ) {
			return $field_map;
		}

		// Get the list ID.
		$list_id = $this->get_setting( 'groupList' );

		try {
			$custom_fields = $this->mailerlite_api( 'GET', 'fields' );

		} catch ( \Exception $e ) {

			// Log that we could not retrieve custom fields.
			$this->log_error( __METHOD__ . '(): Unable to retrieve custom fields; ' . $e->getMessage() );

			return $field_map;

		}

		// Loop through custom fields.
		foreach ( $custom_fields as $custom_field ) {

			// Add custom field to field map.
			$field_map[] = array(
				'name'  => $custom_field['key'],
				'label' => $custom_field['title'],
			);

		}
		return $field_map;

	}

	/**
	 * Define the markup for the option_resubscribe type field.
	 *
	 * @since  Unknown
	 * @access public
	 *
	 * @param array     $field The field properties.
	 * @param bool|true $echo Should the setting markup be echoed.
	 *
	 * @uses GFAddOn::get_setting()
	 * @uses GFAddOn::settings_checkbox()
	 *
	 * @return string|void
	 */
	public function settings_option_resubscribe( $field, $echo = true ) {

		// Define field type.
		$field['type'] = 'checkbox';

		// Prepare field choices.
		$field['choices'] = array(
			array(
				'label' => esc_html__( 'Resubscribe', 'connector-gravityforms-mailerlite' ),
				'name'  => 'resubscribe',
			),
		);

		// Display checkbox field.
		$html = $this->settings_checkbox( $field, false );

		// Prepare field tooltip.
		$tooltip_content = sprintf(
			'<h6>%s</h6>%s',
			esc_html__( 'Resubscribe', 'connector-gravityforms-mailerlite' ),
			esc_html__( 'When this option is enabled, if the subscriber is in an inactive state or has previously been unsubscribed, they will be re-added to the active list. Therefore, this option should be used with caution and only when appropriate.', 'connector-gravityforms-mailerlite' )
		);

		// Display tooltip.
		$html = str_replace( '</div>', '&nbsp' . gform_tooltip( $tooltip_content, '', true ) . '</div>', $html );

		// Display warning.
		$html .= sprintf(
			'<small><span id="mailerlite_resubscribe_warning" style="%s">%s</span></small>',
			$this->get_setting( 'resubscribe' ) ? '' : 'display:none',
			esc_html__( 'This option will re-subscribe users that have been unsubscribed. Use with caution and only when appropriate.', 'connector-gravityforms-mailerlite' )
		);

		if ( $echo ) {
			echo $html;
		}

		return $html;

	}

	// # FEED LIST -------------------------------------------------------------------------------------------------

	/**
	 * Prevent feeds being listed or created if the api key or client id aren't valid.
	 *
	 * @since  Unknown
	 * @access public
	 *
	 * @uses GFCampaignMonitor::initialize_api()
	 *
	 * @return bool
	 */
	public function can_create_feed() {

		return $this->initialize_api();

	}


	/**
	 * Configures which columns should be displayed on the feed list page.
	 *
	 * @since  Unknown
	 * @access public
	 *
	 * @return array
	 */
	public function feed_list_columns() {

		return array(
			'feedName'  => esc_html__( 'Name', 'connector-gravityforms-mailerlite' ),
			'groupList' => esc_html__( 'MailerLite Group', 'connector-gravityforms-mailerlite' )
		);

	}

	/**
	 * Returns the value to be displayed in the groupList column.
	 *
	 * @since  Unknown
	 * @access public
	 *
	 * @param array $feed The feed being included in the feed list.
	 *
	 * @uses GFAddOn::log_error()
	 * @uses GFCampaignMonitor::initialize_api()
	 * @uses GF_CampaignMonitor_API::get_list()
	 *
	 * @return string
	 */
	public function get_column_value_groupList( $feed ) {

		// If we cannot initialize the API, return list ID.
		if ( ! $this->initialize_api() || ! rgars( $feed, 'meta/groupList' ) ) {
			return rgars( $feed, 'meta/groupList' );
		}

		try {
			// Get group.
			$results = $this->mailerlite_api( 'GET', 'groups' );

			foreach ( $results as $group ) {
				if ( isset( $group['id'] ) && $group['id'] === (int) $feed['meta']['groupList'] ) {
					$group_name = $group['name'] ?? '';
					break;
				} else {
					$group_name = __( 'None', 'connector-gravityforms-mailerlite' );
				}
			}

			return esc_html( $group_name );

		} catch ( \Exception $e ) {

			// Log that we could not get the list.
			$this->log_error( __METHOD__ . '(): Unable to get list; ' . $e->getMessage() );

			return sprintf(
				'<strong>%s</strong>',
				esc_html__( 'List could not be found.', 'connector-gravityforms-mailerlite' )
			);

		}

	}

	// # FEED PROCESSING -----------------------------------------------------------------------------------------------

	/**
	 * Initiate processing the feed.
	 *
	 * @since  Unknown
	 * @access public
	 *
	 * @param  array $feed  The feed object to be processed.
	 * @param  array $entry The entry object currently being processed.
	 * @param  array $form  The form object currently being processed.
	 *
	 * @uses GFAddOn::get_field_map_fields()
	 * @uses GFAddOn::get_field_value()
	 * @uses GFAddOn::log_debug()
	 * @uses GFCampaignMonitor::initialize_api()
	 * @uses GF_CampaignMonitor_API::add_subscriber()
	 * @uses GFCommon::is_invalid_or_empty_email()
	 * @uses GFFeedAddOn::add_feed_error()
	 */
	public function process_feed( $feed, $entry, $form ) {

		// If API cannot be initialized, exit.
		if ( ! $this->initialize_api() ) {

			// Log that API could not be initialized.
			$this->add_feed_error( esc_html__( 'User could not be subscribed because API could not be initialized.', 'connector-gravityforms-mailerlite' ), $feed, $entry, $form );

			return;

		}

		$subscriber = array();

		// Initialize subscriber object.
		$subscriber = array(
			'email'        => $this->get_field_value( $form, $entry, $feed['meta']['listFields_email'] ),
			'name'         => $this->get_field_value( $form, $entry, rgars( $feed, 'meta/listFields_fullname' ) ),
			'resubscribe'  => rgars( $feed, 'meta/resubscribe' ) ? true : false,
		);

		// Get field map.
		$field_map = $this->get_field_map_fields( $feed, 'listFields' );

		/**
		 * Modify how MailerLite Add-On handles blank custom fields.
		 * The default behaviour is to remove custom fields which don't have a value from the CustomFields array so they aren't sent to MailerLite.
		 *
		 * @since  Unknown
		 *
		 * @param bool  $override The default is false.
		 * @param array $entry    The Entry which is currently being processed.
		 * @param array $form     The Form which is currently being processed.
		 * @param array $feed     The Feed which is currently being processed.
		 */
		$override_custom_fields = gf_apply_filters( array( 'gform_mailerlite_override_blank_custom_fields', $form['id'], $feed['id'] ), false, $entry, $form, $feed );

		// Loop through field map.
		foreach ( $field_map as $key => $field_id ) {

			// If this is an email or name field, skip it.
			if ( in_array( $key, array( 'email', 'fullname' ) ) ) {
				continue;
			}

			// Get field value.
			$field_values = $this->get_field_value( $form, $entry, $field_id );

			// Convert field value to array.
			if ( ! is_array( $field_values ) ) {
				$field_values = array( $field_values );
			}

			// Loop through values and add to subscriber custom fields.
			foreach ( $field_values as $field_value ) {

				// If we are not overriding custom fields and the field value is blank, skip it.
				if ( ! $override_custom_fields && rgblank( $field_value ) ) {
					continue;
				}

				if ( 'name' === $key || 'email' === $key ) {
					// Add normal values.
					$subscriber[ $key ] = $field_value;
				} else {
					// Add custom values.
					$subscriber['fields'][ $key ] = $field_value;
				}
			}
		}

		try {
			// Subscribe user.
			$added_subscriber = $this->mailerlite_api( 'POST', 'groups/' . rgars( $feed, 'meta/groupList' ) . '/subscribers', $subscriber );
			// returns added subscriber.
			if ( isset( $added_subscriber['id'] ) ) {
				return $added_subscriber['id'];
			} else {
				return false;
			}

			// Log that user was subscribed.
			$this->log_debug( __METHOD__ . '(): User was subscribed to list.' );

			return;

		} catch ( \Exception $e ) {

			// Log that user could not be subscribed.
			$this->add_feed_error( sprintf( esc_html__( 'User could not be subscribed: %s', 'connector-gravityforms-mailerlite' ), $e->getMessage() ), $feed, $entry, $form );

			return;

		}

	}

	/**
	 * Returns the value of the selected field.
	 *
	 * @param array $form The form object currently being processed.
	 * @param array $entry The entry object currently being processed.
	 * @param string $field_id The ID of the field being processed.
	 *
	 * @return array
	 */
	public function get_field_value( $form, $entry, $field_id ) {

		$field_value = '';

		switch ( strtolower( $field_id ) ) {

			case 'form_title':
				$field_value = rgar( $form, 'title' );
				break;

			case 'date_created':
				$date_created = rgar( $entry, strtolower( $field_id ) );
				if ( empty( $date_created ) ) {
					//the date created may not yet be populated if this function is called during the validation phase and the entry is not yet created
					$field_value = gmdate( 'Y-m-d H:i:s' );
				} else {
					$field_value = $date_created;
				}
				break;

			case 'ip':
			case 'source_url':
				$field_value = rgar( $entry, strtolower( $field_id ) );
				break;

			default:

				$field = GFFormsModel::get_field( $form, $field_id );

				if ( is_object( $field ) ) {

					$is_integer = $field_id == intval( $field_id );
					$input_type = RGFormsModel::get_input_type( $field );

					if ( $is_integer && $input_type == 'address' ) {

						$field_value = $this->get_full_address( $entry, $field_id );

					} elseif ( $is_integer && $input_type == 'name' ) {

						$field_value = $this->get_full_name( $entry, $field_id );

					} elseif ( $is_integer && $input_type == 'checkbox' ) {

						$field_value = array();
						foreach ( $field->inputs as $input ) {
							$index         = (string) $input['id'];
							$field_value[] = $this->maybe_override_field_value( rgar( $entry, $index ), $form, $entry, $index );
						}

					} elseif ( $input_type == 'multiselect' ) {

						$value = $this->maybe_override_field_value( rgar( $entry, $field_id ), $form, $entry, $field_id );
						if ( ! empty( $value ) ) {
							$field_value = explode( ',', $value );
						}

					} elseif ( GFCommon::is_product_field( $field->type ) && $field->enablePrice ) {

						$ary         = explode( '|', rgar( $entry, $field_id ) );
						$field_value = count( $ary ) > 0 ? $ary[0] : '';

					} else {

						if ( is_callable( array( 'GF_Field', 'get_value_export' ) ) ) {
							$field_value = $field->get_value_export( $entry, $field_id );
						} else {
							$field_value = rgar( $entry, $field_id );
						}

					}

					if ( ! in_array( $input_type, array( 'checkbox', 'multiselect' ) ) ) {
						$field_value = $this->maybe_override_field_value( $field_value, $form, $entry, $field_id );
					}

				} else {

					$field_value = $this->maybe_override_field_value( rgar( $entry, $field_id ), $form, $entry, $field_id );
				}

		}

		return $field_value;

	}

	/**
	 * Use the legacy gform_mailerlite_field_value filter instead of the framework gform_SLUG_field_value filter.
	 *
	 * @param string $field_value The field value.
	 * @param array $form The form object currently being processed.
	 * @param array $entry The entry object currently being processed.
	 * @param string $field_id The ID of the field being processed.
	 *
	 * @return string
	 */
	public function maybe_override_field_value( $field_value, $form, $entry, $field_id ) {

		return gf_apply_filters(
			'gform_mailerlite_field_value',
			array(
				$form['id'],
				$field_id,
			),
			$field_value,
			$form['id'],
			$field_id,
			$entry
		);

	}

	// # HELPER METHODS ------------------------------------------------------------------------------------------------

	/**
	 * Initializes MailerLite API if credentials are valid.
	 *
	 * @since  3.5
	 * @access public
	 *
	 * @uses GFAddOn::get_plugin_settings()
	 * @uses GFAddOn::log_debug()
	 * @uses GFAddOn::log_error()
	 * @uses GF_CampaignMonitor_API::auth_test()
	 *
	 * @return bool|null
	 */
	public function initialize_api() {
		// Log validation step.
		$this->log_debug( __METHOD__ . '(): Validating API Info.' );

		try {
			$results = $this->mailerlite_api( 'GET', 'groups' );

			if ( is_array( $results ) && ! isset( $results[0]->error->message ) ) {
				return true;
			}

			// Log that authentication test passed.
			$this->log_debug( __METHOD__ . '(): API credentials are valid.' );

			return false;

		} catch ( \Exception $e ) {

			// Log that authentication test failed.
			$this->log_error( __METHOD__ . '(): API credentials are invalid; '. $e->getMessage() );

			return false;

		}
	}

	/**
	 * Get available MailerLite lists as choices.
	 *
	 * @since  3.5
	 * @access public
	 *
	 * @param string $client_ID Client to get lists from.
	 *
	 * @uses GFAddOn::log_error()
	 * @uses GFAddOn::get_plugin_settings()
	 * @uses GFAddOn::get_setting()
	 * @uses GFCampaignMonitor::initialize_api()
	 * @uses GF_CampaignMonitor_API::get_lists()
	 *
	 * @return array
	 */
	public function get_lists_as_choices() {

		// If API cannot be initialized, return array.
		if ( ! $this->initialize_api() ) {
			return array();
		}

		// Initialize choices array.
		$choices = array(
			array(
				'label' => esc_html__( 'Select a Group', 'connector-gravityforms-mailerlite' ),
				'value' => '',
			)
		);

		$groups = $this->mailerlite_api( 'GET', 'groups' );

		// If no lists were found, return.
		if ( empty( $groups ) ) {
			return array();
		}

		// Loop through array.
		foreach ( $groups as $group ) {

			// Add list as choice.
			$choices[] = array(
				'label' => esc_html( $group['name'] ),
				'value' => esc_attr( $group['id'] ),
			);

		}

		return $choices;

	}

	/**
	 * Mailer Lite Connector API
	 *
	 * @param string $method Method to connect: GET, POST..
	 * @param string $module URL endpoint.
	 * @param array  $data   Body data.
	 * @return array
	 */
	private function mailerlite_api( $method, $module, $data = array() ) {
		// Get the plugin settings.
		$settings = $this->get_plugin_settings();
		$apikey   = isset( $settings['apiKey'] ) ? $settings['apiKey'] : '';

		if ( ! $apikey ) {
			return;
		}
		$args = array(
			'method' => $method,
			'headers' => array(
				'X-MailerLite-ApiKey' => $apikey,
				'Content-Type'        => 'application/json',
			),
		);
		if ( ! empty( $data ) ) {
			$args['body'] = json_encode( $data );
		}
		$url = 'https://api.mailerlite.com/api/v2/' . $module;
		$response = wp_remote_request( $url, $args );

		if ( 200 === $response['response']['code'] ) {
			$body = wp_remote_retrieve_body( $response );
			return json_decode( $body, true );
		} else {
			return false;
		}
	}

}
