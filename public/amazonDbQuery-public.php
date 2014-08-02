<?php


class amazonDbQuery {


	const VERSION = '0.0.1';

	var $AmazonKeyPub = 'ENTER PUBLIC KEY';
	var $AmazonKeySec = 'ENTER SECRET';


	protected $plugin_slug = 'amazonDbQuery';

	protected static $instance = null;

	private function __construct() {

		include_once( plugin_dir_path( __FILE__ ) . '/includes/simpleAmazonSTS.php' );
		include_once( plugin_dir_path( __FILE__ ) . '/includes/simpleAmazonDB.php' );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_shortcode( 'amazon_table', array( $this, 'add_amazon_shortcode' ) );

	}


	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();

					restore_current_blog();
				}

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

					restore_current_blog();

				}

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	private static function single_activate() {
		// @TODO: Define activation functionality here
	}

	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
	}

	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
	}


	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
	}


	public function action_method_name() {
		// @TODO: Define your action hook callback here
	}

	public function filter_method_name() {
		// @TODO: Define your filter hook callback here
	}

	public function getPublicKey() {
		return $this->AmazonKeyPub;
	}


	public function getPrivateKey() {
		return $this->AmazonKeySec;
	}


	public function add_amazon_shortcode( $args ) {

		$sts = new SimpleAmazonSTS( $this->getPublicKey(), $this->getPrivateKey() );

		$db = $sts->call( 'GetSessionToken', array( 'DurationSeconds' => 3600 ) );

		$db = new SimpleAmazonDynamoDB(
			$db['GetSessionTokenResult']['Credentials']['AccessKeyId'],
			$db['GetSessionTokenResult']['Credentials']['SecretAccessKey'],
			$db['GetSessionTokenResult']['Credentials']['SessionToken']
		);

		$r = $db->call( 'Scan', $args = array(
				'TableName' => 'ElectricalDevices',
			)
		);

		$output = '<input type="search" class="light-table-filter" data-table="order-table" placeholder="Filter">';

		$output .= '<table class="order-table"><thead><tr>';

		$output .= '<th>test1</th>';
		$output .= '<th>test2</th>';
		$output .= '<th>test3</th>';
		$output .= '<th>test4</th>';
		$output .= '<th>test5</th>';
		$output .= '<th>test6</th>';
		$output .= '<th>test7</th>';

		$output .= '</thead><tbody>';

		foreach ( $r['Items'] as $item ) {
			$output .= '<tr>';

			$tmp = null;
			$sortArr = null;

			foreach ( $item as $key => $valueArr ) {

				if($key == 'test1') {
					foreach ( $valueArr as $value ) {
						$sortArr[0] = '<td>' . $value . '</td>';
					}
				} elseif ($key == 'test2') {
					foreach ( $valueArr as $value ) {
						$sortArr[1] = '<td>' . $value . '</td>';
					}
				} elseif ($key == 'test3') {
					foreach ( $valueArr as $value ) {
						$sortArr[2] = '<td>' . $value . '</td>';
					}
				} elseif ($key == 'test4') {
					foreach ( $valueArr as $value ) {
						$sortArr[3] = '<td>' . $value . '</td>';
					}
				} elseif ($key == 'test5') {
					foreach ( $valueArr as $value ) {
						$sortArr[4] = '<td>' . $value . '</td>';
					}
				} elseif ($key == 'test6') {
					foreach ( $valueArr as $value ) {
						$sortArr[5] = '<td>' . $value . '</td>';
					}
				} elseif ($key == 'test7') {
					foreach ( $valueArr as $value ) {
						$sortArr[6] = '<td>' . $value . '</td>';
					}
				}

			}

			ksort($sortArr);
			$output .= join('',$sortArr);
			$output .= '</tr>';
		}


		$output .= '</tbody></table>';

		return $output;
	}


}
