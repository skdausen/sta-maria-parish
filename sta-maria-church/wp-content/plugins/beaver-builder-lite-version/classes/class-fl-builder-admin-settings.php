<?php

/**
 * Handles logic for the admin settings page.
 *
 * @since 1.0
 */
final class FLBuilderAdminSettings {

	/**
	 * Holds any errors that may arise from
	 * saving admin settings.
	 *
	 * @since 1.0
	 * @var array $errors
	 */
	static public $errors = array();

	private static $registered_settings = array();

	private static $global_settings = array(
		'_fl_builder_post_types',
		'_fl_builder_enabled_modules',
		'_fl_builder_enabled_blocks',
		'_fl_builder_enabled_templates',
		'_fl_builder_enabled_icons',
		'_fl_builder_user_access',
		'_fl_builder_enable_fa_pro',
		'_fl_builder_kit_fa_pro',
		'_fl_builder_cache_plugins',
		'_fl_builder_branding',
		'_fl_builder_branding_icon',
		'_fl_builder_theme_branding',
		'_fl_builder_help_button',
	);

	/**
	 * Initializes the admin settings.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function init() {
		add_action( 'init', __CLASS__ . '::init_hooks', 11 );
		add_action( 'wp_ajax_fl_welcome_submit', array( 'FLBuilderAdminSettings', 'welcome_submit' ) );
		// register global settings
		self::register_settings();
	}

	/**
	 * @since 2.6
	 */
	private static function register_settings() {
		foreach ( self::$global_settings as $setting ) {
			self::register_setting( $setting );
		}
	}

	/**
	 * @since 2.6
	 */
	public static function register_setting( $key ) {
		self::$registered_settings[] = $key;
	}

	/**
	 * @since 2.6
	 */
	public static function registered_settings() {
		return self::$registered_settings;
	}

	/**
	 * AJAX callback for welcome email subscription form.
	 * @since 2.2.2
	 */
	static public function welcome_submit() {

		if ( ! empty( $_POST ) && wp_verify_nonce( $_POST['_wpnonce'], 'welcome_submit' ) ) {

			$url = 'http://services.wpbeaverbuilder.com/drip/subscribe.php';

			$url = add_query_arg( array(
				'name'  => $_POST['name'],
				'email' => $_POST['email'],
			), $url );

			$response = wp_remote_get( $url );
			$body     = $response['body'];
			if ( '1' === $body ) {
				$args = array(
					'message' => __( 'Thank you!', 'fl-builder' ),
				);
				update_user_meta( get_current_user_id(), '_fl_welcome_subscribed', '1' );
				wp_send_json_success( $args );
			}
		} else {
			$args = array(
				'message' => __( 'Error submitting.', 'fl-builder' ),
			);
			wp_send_json_error( $args );
		}
	}

	/**
	 * Adds the admin menu and enqueues CSS/JS if we are on
	 * the builder admin settings page.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function init_hooks() {
		if ( ! is_admin() ) {
			return;
		}

		add_action( 'admin_menu', __CLASS__ . '::menu' );

		if ( isset( $_REQUEST['page'] ) && 'fl-builder-settings' == $_REQUEST['page'] ) {
			add_action( 'admin_enqueue_scripts', __CLASS__ . '::styles_scripts' );
			add_filter( 'admin_footer_text', array( __CLASS__, '_filter_admin_footer_text' ) );
			self::save();
		}
	}

	/**
	 * Enqueues the needed CSS/JS for the builder's admin settings page.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function styles_scripts() {
		// Styles
		wp_enqueue_style( 'fl-builder-admin-settings', FLBuilder::plugin_url() . 'css/fl-builder-admin-settings.css', array(), FL_BUILDER_VERSION );
		wp_enqueue_style( 'jquery-multiselect', FLBuilder::plugin_url() . 'css/jquery.multiselect.css', array(), FL_BUILDER_VERSION );
		wp_enqueue_style( 'fl-jquery-tiptip', FLBuilder::plugin_url() . 'css/jquery.tiptip.css', array(), FL_BUILDER_VERSION );
		wp_enqueue_style( 'fl-admin-notify', FLBuilder::plugin_url() . 'css/simple-notify.min.css', array(), FL_BUILDER_VERSION );

		if ( FLBuilder::fa5_pro_enabled() ) {
			if ( '' !== get_option( '_fl_builder_kit_fa_pro' ) ) {
				wp_enqueue_script( 'fa5-kit', get_option( '_fl_builder_kit_fa_pro' ) );
			} else {
				wp_register_style( 'font-awesome-5', FLBuilder::get_fa5_url() );
				wp_enqueue_style( 'font-awesome-5' );
			}
		}
		// Scripts
		wp_enqueue_script( 'fl-builder-admin-settings', FLBuilder::plugin_url() . 'js/fl-builder-admin-settings.js', array( 'fl-jquery-tiptip' ), FL_BUILDER_VERSION );
		wp_enqueue_script( 'jquery-actual', FLBuilder::plugin_url() . 'js/jquery.actual.min.js', array( 'jquery' ), FL_BUILDER_VERSION );
		wp_enqueue_script( 'jquery-multiselect', FLBuilder::plugin_url() . 'js/jquery.multiselect.js', array( 'jquery' ), FL_BUILDER_VERSION );
		wp_enqueue_script( 'fl-jquery-tiptip', FLBuilder::plugin_url() . 'js/jquery.tiptip.min.js', array( 'jquery' ), FL_BUILDER_VERSION, true );
		wp_enqueue_script( 'fl-admin-notify', FLBuilder::plugin_url() . 'js/simple-notify.min.js', array(), FL_BUILDER_VERSION );
		// Media Uploader
		wp_enqueue_media();
	}

	/**
	 * Renders the admin settings menu.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function menu() {
		if ( FLBuilderAdmin::current_user_can_access_settings() ) {

			$title = FLBuilderModel::get_branding();
			$cap   = FLBuilderAdmin::admin_settings_capability();
			$slug  = 'fl-builder-settings';
			$func  = __CLASS__ . '::render';

			add_submenu_page( 'options-general.php', $title, $title, $cap, $slug, $func );
		}
	}

	/**
	 * Renders the admin settings.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function render() {
		include FL_BUILDER_DIR . 'includes/admin-settings-js-config.php';
		include FL_BUILDER_DIR . 'includes/admin-settings.php';
	}

	/**
	 * Renders the page class for network installs and single site installs.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function render_page_class() {
		if ( self::multisite_support() ) {
			echo 'fl-settings-network-admin';
		} else {
			echo 'fl-settings-single-install';
		}
	}

	/**
	 * Renders the admin settings page heading.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function render_page_heading() {
		$icon = FLBuilderModel::get_branding_icon();
		$name = FLBuilderModel::get_branding();

		if ( ! empty( $icon ) ) {
			echo '<img role="presentation" src="' . $icon . '" />';
		}
		/* translators: %s: builder branded name */
		echo '<span>' . sprintf( _x( '%s Settings', '%s stands for custom branded "Page Builder" name.', 'fl-builder' ), FLBuilderModel::get_branding() ) . '</span>';
	}

	/**
	 * Renders the update message.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function render_update_message() {
		if ( ! empty( self::$errors ) ) {
			foreach ( self::$errors as $message ) {
				echo '<div class="error"><p>' . $message . '</p></div>';
			}
		} elseif ( ! empty( $_POST ) && ! isset( $_POST['email'] ) ) {
			echo '<div class="updated"><p>' . __( 'Settings updated!', 'fl-builder' ) . '</p></div>';
		}
	}

	/**
	 * Renders the nav items for the admin settings menu.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function render_nav_items() {
		/**
		 * Builder admin nav items
		 * @see fl_builder_admin_settings_nav_items
		 */
		$item_data = apply_filters( 'fl_builder_admin_settings_nav_items', array(
			'welcome'       => array(
				'title'    => __( 'Welcome', 'fl-builder' ),
				'show'     => ! FLBuilderModel::is_white_labeled() && ( is_network_admin() || ! self::multisite_support() ),
				'priority' => 50,
			),
			'license'       => array(
				'title'    => __( 'License', 'fl-builder' ),
				'show'     => ( is_network_admin() || ! self::multisite_support() ),
				'priority' => 100,
			),
			'upgrade'       => array(
				'title'    => __( 'Upgrade', 'fl-builder' ),
				'show'     => FL_BUILDER_LITE === true,
				'priority' => 200,
			),
			'modules'       => array(
				'title'    => __( 'Modules', 'fl-builder' ),
				'show'     => true,
				'priority' => 300,
			),
			'blocks'        => array(
				'title'    => __( 'Blocks', 'fl-builder' ),
				'show'     => true,
				'priority' => 310,
			),
			'post-types'    => array(
				'title'    => __( 'Post Types', 'fl-builder' ),
				'show'     => true,
				'priority' => 400,
			),
			'user-access'   => array(
				'title'    => __( 'User Access', 'fl-builder' ),
				'show'     => true,
				'priority' => 500,
			),
			'icons'         => array(
				'title'    => __( 'Icons', 'fl-builder' ),
				'show'     => FL_BUILDER_LITE !== true,
				'priority' => 600,
			),
			'tools'         => array(
				'title'    => __( 'Tools', 'fl-builder' ),
				'show'     => true,
				'priority' => 700,
			),
			'advanced'      => array(
				'title'    => __( 'Advanced', 'fl-builder' ),
				'show'     => true,
				'priority' => 750,
			),
			'import-export' => array(
				'title'    => __( 'Import / Export', 'fl-builder' ),
				'show'     => true,
				'priority' => 800,
			),
		) );

		$sorted_data = array();

		if ( is_network_admin() ) {
			unset( $item_data['advanced'] );
		}

		foreach ( $item_data as $key => $data ) {
			$priority                 = self::check_priority( $data['priority'], $sorted_data );
			$data['key']              = $key;
			$sorted_data[ $priority ] = $data;
		}

		ksort( $sorted_data );

		foreach ( $sorted_data as $data ) {
			if ( $data['show'] ) {
				echo '<li><a href="#' . $data['key'] . '">' . $data['title'] . '</a></li>';
			}
		}
	}

	/**
	 * @since 2.7
	 */
	static public function check_priority( $priority, $sorted_data ) {
		if ( isset( $sorted_data[ $priority ] ) ) {
			$priority = self::check_priority( $priority + 1, $sorted_data );
		}
		return $priority;
	}

	/**
	 * Renders the admin settings forms.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function render_forms() {
		// Welcome
		if ( ! FLBuilderModel::is_white_labeled() && ( is_network_admin() || ! self::multisite_support() ) ) {
			self::render_form( 'welcome' );
		}

		// License
		if ( is_network_admin() || ! self::multisite_support() ) {
			self::render_form( 'license' );
		}

		// Upgrade
		if ( FL_BUILDER_LITE === true ) {
			self::render_form( 'upgrade' );
		}

		// Modules
		self::render_form( 'modules' );

		// Blocks
		self::render_form( 'blocks' );

		// Post Types
		self::render_form( 'post-types' );

		// Icons
		self::render_form( 'icons' );

		// User Access
		self::render_form( 'user-access' );

		// Tools
		self::render_form( 'tools' );

		self::render_form( 'advanced' );

		self::render_form( 'import-export' );

		/**
		 * Let extensions hook into form rendering.
		 * @see fl_builder_admin_settings_render_forms
		 */
		do_action( 'fl_builder_admin_settings_render_forms' );
	}

	/**
	 * Renders an admin settings form based on the type specified.
	 *
	 * @since 1.0
	 * @param string $type The type of form to render.
	 * @return void
	 */
	static public function render_form( $type ) {
		if ( self::has_support( $type ) ) {
			include FL_BUILDER_DIR . 'includes/admin-settings-' . $type . '.php';
		}
	}

	/**
	 * Renders the action for a form.
	 *
	 * @since 1.0
	 * @param string $type The type of form being rendered.
	 * @return void
	 */
	static public function render_form_action( $type = '' ) {
		if ( is_network_admin() ) {
			echo network_admin_url( '/settings.php?page=fl-builder-multisite-settings#' . $type );
		} else {
			echo admin_url( '/options-general.php?page=fl-builder-settings#' . $type );
		}
	}

	/**
	 * Returns the action for a form.
	 *
	 * @since 1.0
	 * @param string $type The type of form being rendered.
	 * @return string The URL for the form action.
	 */
	static public function get_form_action( $type = '' ) {
		if ( is_network_admin() ) {
			return network_admin_url( '/settings.php?page=fl-builder-multisite-settings#' . $type );
		} else {
			return admin_url( '/options-general.php?page=fl-builder-settings#' . $type );
		}
	}

	/**
	 * Checks to see if a settings form is supported.
	 *
	 * @since 1.0
	 * @param string $type The type of form to check.
	 * @return bool
	 */
	static public function has_support( $type ) {
		return file_exists( FL_BUILDER_DIR . 'includes/admin-settings-' . $type . '.php' );
	}

	/**
	 * Checks to see if multisite is supported.
	 *
	 * @since 1.0
	 * @return bool
	 */
	static public function multisite_support() {
		return is_multisite() && class_exists( 'FLBuilderMultisiteSettings' );
	}

	/**
	 * Adds an error message to be rendered.
	 *
	 * @since 1.0
	 * @param string $message The error message to add.
	 * @return void
	 */
	static public function add_error( $message ) {
		self::$errors[] = $message;
	}

	/**
	 * Saves the admin settings.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function save() {
		// Only admins can save settings.
		if ( ! FLBuilderAdmin::current_user_can_access_settings() ) {
			return;
		}

		self::save_enabled_modules();
		self::save_enabled_blocks();
		self::save_enabled_post_types();
		self::save_enabled_icons();
		self::save_user_access();
		self::clear_cache();
		self::debug();
		self::global_edit();
		self::theme_code_edit();
		self::beta();
		self::uninstall();

		/**
		 * Let extensions hook into saving.
		 * @see fl_builder_admin_settings_save
		 */
		do_action( 'fl_builder_admin_settings_save' );
	}

	/**
	 * Saves the enabled modules.
	 *
	 * @since 1.0
	 * @access private
	 * @return void
	 */
	static private function save_enabled_modules() {
		if ( isset( $_POST['fl-modules-nonce'] ) && wp_verify_nonce( $_POST['fl-modules-nonce'], 'modules' ) ) {

			$modules = array();

			if ( isset( $_POST['fl-modules'] ) && is_array( $_POST['fl-modules'] ) ) {
				$modules = array_map( 'sanitize_text_field', $_POST['fl-modules'] );
			}

			if ( empty( $modules ) ) {
				self::add_error( __( 'Error! You must have at least one module enabled.', 'fl-builder' ) );
				return;
			}

			FLBuilderModel::update_admin_settings_option( '_fl_builder_enabled_modules', $modules, true );
		}
	}

	/**
	 * Saves the enabled blocks.
	 *
	 * @since 2.9
	 * @access private
	 * @return void
	 */
	static private function save_enabled_blocks() {
		if ( isset( $_POST['fl-blocks-nonce'] ) && wp_verify_nonce( $_POST['fl-blocks-nonce'], 'blocks' ) ) {

			$blocks = array();

			if ( isset( $_POST['fl-blocks'] ) && is_array( $_POST['fl-blocks'] ) ) {
				$blocks = array_map( 'sanitize_text_field', $_POST['fl-blocks'] );
			}

			FLBuilderModel::update_admin_settings_option( '_fl_builder_enabled_blocks', $blocks, true );
		}
	}

	/**
	 * Saves the enabled post types.
	 *
	 * @since 1.0
	 * @access private
	 * @return void
	 */
	static private function save_enabled_post_types() {
		if ( isset( $_POST['fl-post-types-nonce'] ) && wp_verify_nonce( $_POST['fl-post-types-nonce'], 'post-types' ) ) {

			if ( is_network_admin() ) {
				$post_types = sanitize_text_field( $_POST['fl-post-types'] );
				$post_types = str_replace( ' ', '', $post_types );
				$post_types = explode( ',', $post_types );
			} else {

				$post_types = array();

				if ( isset( $_POST['fl-post-types'] ) && is_array( $_POST['fl-post-types'] ) ) {
					$post_types = array_map( 'sanitize_text_field', $_POST['fl-post-types'] );
				}
			}

			FLBuilderModel::update_admin_settings_option( '_fl_builder_post_types', $post_types, true, true );
		}
	}

	/**
	 * Saves the enabled icons.
	 *
	 * @since 1.0
	 * @access private
	 * @return void
	 */
	static private function save_enabled_icons() {
		if ( isset( $_POST['fl-icons-nonce'] ) && wp_verify_nonce( $_POST['fl-icons-nonce'], 'icons' ) ) {

			// Make sure we have at least one enabled icon set.
			if ( ! isset( $_POST['fl-enabled-icons'] ) && empty( $_POST['fl-new-icon-set'] ) ) {
				self::add_error( __( 'Error! You must have at least one icon set enabled.', 'fl-builder' ) );
				return;
			}

			$enabled_icons = array();

			// Sanitize the enabled icons.
			if ( isset( $_POST['fl-enabled-icons'] ) && is_array( $_POST['fl-enabled-icons'] ) ) {
				$enabled_icons = array_map( 'sanitize_text_field', $_POST['fl-enabled-icons'] );
			}

			// Update the enabled sets.
			self::update_enabled_icons( $enabled_icons );

			// Enable pro?
			$enable_fa_pro = isset( $_POST['fl-enable-fa-pro'] ) ? true : false;
			FLBuilderUtils::update_option( '_fl_builder_enable_fa_pro', $enable_fa_pro, true );
			do_action( 'fl_builder_fa_pro_save', $enable_fa_pro );
			// Update KIT url
			$kit_url = isset( $_POST['fl-fa-pro-kit'] ) ? $_POST['fl-fa-pro-kit'] : '';

			preg_match( '#https:\/\/.+\.js#', $kit_url, $match );

			if ( $kit_url && isset( $match[0] ) ) {
				FLBuilderUtils::update_option( '_fl_builder_kit_fa_pro', $match[0], true );
			} else {
				if ( ! $kit_url ) {
					delete_option( '_fl_builder_kit_fa_pro' );
					update_option( '_fl_builder_kit_fa_pro', '', true );
				} else {
					/* translators: %s: KIT url */
					self::add_error( sprintf( __( 'Invalid Kit Url: we were unable to determine the URL, code entered was %s', 'fl-builder' ), '<code>' . esc_html( $kit_url ) . '</code>' ) );
				}
			}

			// Delete a set?
			if ( ! empty( $_POST['fl-delete-icon-set'] ) ) {

				$sets  = FLBuilderIcons::get_sets();
				$key   = sanitize_text_field( $_POST['fl-delete-icon-set'] );
				$index = array_search( $key, $enabled_icons );

				if ( false !== $index ) {
					unset( $enabled_icons[ $index ] );
				}
				if ( isset( $sets[ $key ] ) ) {
					fl_builder_filesystem()->rmdir( $sets[ $key ]['path'], true );
					FLBuilderIcons::remove_set( $key );
				}
				/**
				 * After set is deleted.
				 * @see fl_builder_admin_settings_remove_icon_set
				 */
				do_action( 'fl_builder_admin_settings_remove_icon_set', $key );
			}

			// Upload a new set?
			if ( ! empty( $_POST['fl-new-icon-set'] ) ) {

				$dir = FLBuilderModel::get_cache_dir( 'icons' );
				$id  = (int) $_POST['fl-new-icon-set'];
				/**
				 * Icon upload path
				 * @see fl_builder_icon_set_upload_path
				 */
				$path = apply_filters( 'fl_builder_icon_set_upload_path', get_attached_file( $id ) );
				/**
				 * @see fl_builder_icon_set_new_path
				 */
				$new_path = apply_filters( 'fl_builder_icon_set_new_path', $dir['path'] . 'icon-' . time() . '/' );

				fl_builder_filesystem()->get_filesystem();

				/**
				 * Before set is unzipped.
				 * @see fl_builder_before_unzip_icon_set
				 */
				do_action( 'fl_builder_before_unzip_icon_set', $id, $path, $new_path );

				$unzipped = unzip_file( $path, $new_path );

				// unzip returned a WP_Error
				if ( is_wp_error( $unzipped ) ) {
					/* translators: %s: unzip error message */
					self::add_error( sprintf( __( 'Unzip Error: %s', 'fl-builder' ), $unzipped->get_error_message() ) );
					return;
				}

				// Unzip failed.
				if ( ! $unzipped ) {
					self::add_error( __( 'Error! Could not unzip file.', 'fl-builder' ) );
					return;
				}

				// Move files if unzipped into a subfolder.
				$files = fl_builder_filesystem()->dirlist( $new_path );

				if ( 1 == count( $files ) ) {

					$values         = array_values( $files );
					$subfolder_info = array_shift( $values );
					$subfolder      = $new_path . $subfolder_info['name'] . '/';

					if ( fl_builder_filesystem()->file_exists( $subfolder ) && fl_builder_filesystem()->is_dir( $subfolder ) ) {

						$files = fl_builder_filesystem()->dirlist( $subfolder );

						if ( $files ) {
							foreach ( $files as $file ) {
								fl_builder_filesystem()->move( $subfolder . $file['name'], $new_path . $file['name'] );
							}
						}

						fl_builder_filesystem()->rmdir( $subfolder );
					}
				}

				/**
				 * After set is unzipped.
				 * @see fl_builder_after_unzip_icon_set
				 */
				do_action( 'fl_builder_after_unzip_icon_set', $new_path );

				/**
				 * Delete any files you accidentally added to the zipfile
				 */
				$it   = new RecursiveDirectoryIterator( $new_path );
				$good = array( 'json', 'css', 'scss', 'eot', 'svg', 'ttf', 'woff', 'woff2' );
				foreach ( new RecursiveIteratorIterator( $it ) as $file ) {
					$ext = pathinfo( $file, PATHINFO_EXTENSION );
					if ( $ext && ! in_array( $ext, $good ) ) {
						fl_builder_filesystem()->unlink( $file );
					}
				}

				/**
				 * @see fl_builder_icon_set_check_path
				 */
				$check_path = apply_filters( 'fl_builder_icon_set_check_path', $new_path );

				// Check for supported sets.
				$is_icomoon  = fl_builder_filesystem()->file_exists( $check_path . 'selection.json' );
				$is_fontello = fl_builder_filesystem()->file_exists( $check_path . 'config.json' );
				$is_awesome  = fl_builder_filesystem()->file_exists( $check_path . '/metadata/icons.json' );

				// Show an error if we don't have a supported icon set.
				if ( ! $is_icomoon && ! $is_fontello && ! $is_awesome ) {
					fl_builder_filesystem()->rmdir( $new_path, true );
					self::add_error( __( 'Error! Please upload an icon set from either Icomoon, Fontello or Font Awesome Pro Subset.', 'fl-builder' ) );
					return;
				}

				// check for valid Icomoon
				if ( $is_icomoon ) {
					$data = json_decode( fl_builder_filesystem()->file_get_contents( $check_path . 'selection.json' ) );
					if ( ! isset( $data->metadata ) ) {
						fl_builder_filesystem()->rmdir( $new_path, true );
						self::add_error( __( 'Error! When downloading from Icomoon, be sure to click the Download Font button and not Generate SVG.', 'fl-builder' ) );
						return;
					}
				}

				// we need to patch the all.css file because _reasons_
				if ( $is_awesome ) {
					$search  = array( '.fa,.fas{font-family:', '.fad{', '.fal,.far{font-family' );
					$replace = array( '.subset.fa,.subset.fas{font-family:', '.subset.fad{', '.subset.fal,.subset.far{font-family' );
					$css     = str_replace( $search, $replace, fl_builder_filesystem()->file_get_contents( $check_path . 'css/all.min.css' ) );
					fl_builder_filesystem()->file_put_contents( $check_path . 'css/all.min.css', $css );
				}

				// Enable the new set.
				if ( is_array( $enabled_icons ) ) {
					$key             = FLBuilderIcons::get_key_from_path( $check_path );
					$enabled_icons[] = $key;
				}
			}

			// Update the enabled sets again in case they have changed.
			self::update_enabled_icons( $enabled_icons );
		}
	}

	/**
	 * Updates the enabled icons in the database.
	 *
	 * @since 1.0
	 * @access private
	 * @return void
	 */
	static private function update_enabled_icons( $enabled_icons = array() ) {
		FLBuilderModel::update_admin_settings_option( '_fl_builder_enabled_icons', $enabled_icons, true, true );
	}

	/**
	 * Saves the user access settings
	 *
	 * @since 1.10
	 * @access private
	 * @return void
	 */
	static private function save_user_access() {
		if ( isset( $_POST['fl-user-access-nonce'] ) && wp_verify_nonce( $_POST['fl-user-access-nonce'], 'user-access' ) ) {
			FLBuilderUserAccess::save_settings( isset( $_POST['fl_user_access'] ) ? $_POST['fl_user_access'] : array() );
		}
	}

	/**
	 * Clears the builder cache.
	 *
	 * @since 1.5.3
	 * @access private
	 * @return void
	 */
	static private function clear_cache() {
		if ( ! FLBuilderAdmin::current_user_can_access_settings() ) {
			return;
		} elseif ( isset( $_POST['fl-cache-nonce'] ) && wp_verify_nonce( $_POST['fl-cache-nonce'], 'cache' ) ) {
			if ( is_network_admin() ) {
				self::clear_cache_for_all_sites();
			} else {

				// Clear builder cache.
				FLBuilderModel::delete_asset_cache_for_all_posts();

				// Clear theme cache.
				if ( class_exists( 'FLCustomizer' ) && method_exists( 'FLCustomizer', 'clear_all_css_cache' ) ) {
					FLCustomizer::clear_all_css_cache();
				}
			}
			/**
			 * Fires after cache is cleared.
			 * @see fl_builder_cache_cleared
			 */
			do_action( 'fl_builder_cache_cleared' );
		}
	}

	/**
	 * Enable/disable debug
	 *
	 * @since 1.10.7
	 * @access private
	 * @return void
	 */
	static private function debug() {
		if ( ! FLBuilderAdmin::current_user_can_access_settings() ) {
			return;
		} elseif ( isset( $_POST['fl-debug-nonce'] ) && wp_verify_nonce( $_POST['fl-debug-nonce'], 'debug' ) ) {
			$debugmode = get_transient( 'fl_debug_mode' );

			if ( ! $debugmode ) {
				set_transient( 'fl_debug_mode', md5( rand() ), 172800 ); // 48 hours 172800
				update_option( 'fl_debug_mode', true );
			} else {
				delete_transient( 'fl_debug_mode' );
				update_option( 'fl_debug_mode', false );
			}
		}
	}

	/**
	 * Update global js/css
	 *
	 * @since 2.4
	 * @access private
	 * @return void
	 */
	static private function global_edit() {
		if ( ! FLBuilderAdmin::current_user_can_access_settings() ) {
			return;
		} elseif ( isset( $_POST['fl-css-js-nonce'] ) && wp_verify_nonce( $_POST['fl-css-js-nonce'], 'debug' ) ) {
			if ( get_transient( 'fl_debug_mode' ) || ( defined( 'FL_ENABLE_META_CSS_EDIT' ) && FL_ENABLE_META_CSS_EDIT ) ) {
				$css          = stripslashes( $_POST['css'] );
				$js           = stripslashes( $_POST['js'] );
				$options      = get_option( '_fl_builder_settings', (object) array() );
				$options->css = $css;
				$options->js  = $js;
				FLBuilderUtils::update_option( '_fl_builder_settings', $options, true );
			}
		}
	}

	/**
	 * Update Theme Code
	 *
	 * @since 2.6
	 * @access private
	 * @return void
	 */
	static private function theme_code_edit() {
		if ( ! FLBuilderAdmin::current_user_can_access_settings() ) {
			return;
		} elseif ( isset( $_POST['fl-theme-opts-nonce'] ) && wp_verify_nonce( $_POST['fl-theme-opts-nonce'], 'debug' ) ) {
			if ( get_transient( 'fl_debug_mode' ) || ( defined( 'FL_ENABLE_META_CSS_EDIT' ) && FL_ENABLE_META_CSS_EDIT ) ) {
				$theme_codes = array(
					'fl-js-code',
					'fl-head-code',
					'fl-header-code',
					'fl-footer-code',
				);
				foreach ( $theme_codes as $key ) {
					set_theme_mod( $key, stripslashes( $_POST[ $key ] ) );
				}
			}
		}
	}

	/**
	 * Clears the builder cache for all sites on a network.
	 *
	 * @since 1.5.3
	 * @access private
	 * @return void
	 */
	static private function clear_cache_for_all_sites() {
		global $blog_id;
		global $wpdb;

		// Save the original blog id.
		$original_blog_id = $blog_id;

		// Get all blog ids.
		$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

		// Loop through the blog ids and clear the cache.
		foreach ( $blog_ids as $id ) {

			// Switch to the blog.
			switch_to_blog( $id );

			// Clear builder cache.
			FLBuilderModel::delete_asset_cache_for_all_posts();

			// Clear theme cache.
			if ( class_exists( 'FLCustomizer' ) && method_exists( 'FLCustomizer', 'clear_all_css_cache' ) ) {
				FLCustomizer::clear_all_css_cache();
			}
		}

		// Revert to the original blog.
		switch_to_blog( $original_blog_id );
	}

	/**
	 * Uninstalls the builder and all of its data.
	 *
	 * @since 1.0
	 * @access private
	 * @return void
	 */
	static private function uninstall() {
		if ( ! current_user_can( 'delete_plugins' ) ) {
			return;
		} elseif ( isset( $_POST['fl-uninstall'] ) && wp_verify_nonce( $_POST['fl-uninstall'], 'uninstall' ) ) {

			/**
			 * Disable Uninstall ( default true )
			 * @see fl_builder_uninstall
			 */
			$uninstall = apply_filters( 'fl_builder_uninstall', true );

			if ( $uninstall ) {
				FLBuilderAdmin::uninstall();
			}
		}
	}

	/**
	 * Enable/disable beta updates
	 *
	 * @since 2.4
	 * @access private
	 * @return void
	 */
	static private function beta() {

		if ( ! current_user_can( 'delete_users' ) ) {
			return;
		} elseif ( isset( $_POST['fl-beta-nonce'] ) && wp_verify_nonce( $_POST['fl-beta-nonce'], 'beta' ) ) {

			if ( isset( $_POST['beta-checkbox'] ) ) {
				FLBuilderUtils::update_option( 'fl_beta_updates', true, true );
			} else {
				FLBuilderUtils::update_option( 'fl_beta_updates', false, true );
			}

			if ( isset( $_POST['alpha-checkbox'] ) ) {
				FLBuilderUtils::update_option( 'fl_alpha_updates', true, true );
			} else {
				FLBuilderUtils::update_option( 'fl_alpha_updates', false, true );
			}
		}
	}


	/**
	 * @since 1.0
	 * @deprecated 1.8
	 */
	static private function save_help_button() {
		_deprecated_function( __METHOD__, '1.8', 'FLBuilderWhiteLabel::save_help_button_settings()' );
	}

	/**
	 * @since 1.0
	 * @deprecated 1.8
	 */
	static private function save_branding() {
		_deprecated_function( __METHOD__, '1.8', 'FLBuilderWhiteLabel::save_branding_settings()' );
	}

	/**
	 * @since 1.0
	 * @deprecated 1.8
	 */
	static private function save_enabled_templates() {
		_deprecated_function( __METHOD__, '1.8', 'FLBuilderUserTemplatesAdmin::save_settings()' );
	}

	/**
	 * @since 1.10.6
	 */
	public static function _filter_admin_footer_text( $text ) {

		$stars = '<a target="_blank" href="https://wordpress.org/support/plugin/beaver-builder-lite-version/reviews/#new-post" >&#9733;&#9733;&#9733;&#9733;&#9733;</a>';

		$wporg = '<a target="_blank" href="https://wordpress.org/plugins/beaver-builder-lite-version/">wordpress.org</a>';

		/* translators: 1: stars link: 2: link to wporg page */
		return sprintf( __( 'Add your %1$s on %2$s to spread the love.', 'fl-builder' ), $stars, $wporg );
	}
}

FLBuilderAdminSettings::init();
