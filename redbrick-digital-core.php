<?php
/**
	* Plugin Name: Redbrick Digital Core
	* Description: This plugin enables Redbrick Digital Core usage, including the Review Engine Display shortcode, Review Slider widget, and Social Proof widget.
	* Version:     1.2.3
	* Author:      RedbrickDigital.net
	* Text Domain: rbd-core
	* License:     GPL-2.0+
	* License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

class RBD_Core {
	public static $wlioURL = 'https://whirlocal.io/wp-json/wp/v2/company/438211';
	private static $instance;
	protected static $rbd_popup_displayed = false;

	public static function get_instance() {
		if( null == self::$instance ) self::$instance = new RBD_Core();
		return self::$instance;
	}

	public static function rbd_core_vars(){
		$base_path = plugin_dir_path( __FILE__ );

		return (object)[
			'shortcodes' => [
				'review-engine-display'
			],
			'widgets' => [
				'review-engine-display',
				'review-slider',
				'social-proof'
			],
			'uris' => [
				'dirs' => [],
				'paths' => [
					'plugin'     => $base_path,
					'admin'      => $base_path . 'admin',
					'widgets'    => $base_path .'lib/widgets',
					'shortcodes' => $base_path .'lib/shortcodes',
				]
			]
		];
	}

	public function __construct(){
		add_action( 'save_post',             [$this, 'reset_transients'] );
		add_action( 'after_setup_theme',     [$this, 'register_shortcodes'] );
		add_action( 'after_setup_theme',     [$this, 'register_widgets'] );
		add_action( 'after_setup_theme',     [$this, 'register_admin_page'] );
		add_action( 'after_setup_theme',     [$this, 'register_scripts'] );
		add_action( 'wp_enqueue_scripts',    [$this, 'enqueue_scripts'] );
		add_action( 'admin_enqueue_scripts', [$this, 'enqueue_scripts'] );
		add_action( 'media_buttons',         [$this, 'admin_enqueue_shortcode_scripts'] );

		add_filter( 'script_loader_tag', function( $tag, $handle ){
			$deferred = ['rbd-core', 'shortcode-review-engine-display', 'widget-review-slider'];
			if( in_array( $handle, $deferred ) )
				$tag = str_replace( ' src', ' defer src', $tag );

			return $tag;
		}, 10, 2 );
	}

	function register_admin_page(){
		require_once plugin_dir_path( __FILE__ ) . 'admin/admin-core.php';
	}

	function enqueue_scripts(){
		// Files that are always necessary are enqueued
		$required_styles = ['400', '400i', '700'];
		wp_enqueue_style( 'montserrat', 'https://fonts.googleapis.com/css?family=Montserrat:'. implode( ',', $required_styles ) );
		wp_enqueue_style( 'rbd-core', plugins_url( '/assets/css/core.css', __FILE__ ), [], filemtime( plugin_dir_path( __FILE__ ) . 'assets/css/core.css') );
		wp_enqueue_script( 'rbd-core', plugins_url( '/assets/js/core.js', __FILE__ ), [], filemtime( plugin_dir_path( __FILE__ ) . 'assets/js/core.js' ), true );
	}

	static function rbd_popup_displayed(){
		RBD_Core::$rbd_popup_displayed = true;
	}

	function admin_enqueue_shortcode_scripts(){
		wp_enqueue_style( 'shortcode-review-engine-display-admin' );
		wp_enqueue_script( 'shortcode-review-engine-display-admin' );
	}

	function register_scripts(){
		// Files that are conditionally necessary (for instance, shortcode specific CSS/JS) are registered, and then enqueued in the files directly.
		$dynamic_styles = ['shortcode-review-engine-display', 'shortcode-review-engine-display-admin', 'widget-review-slider', 'widget-social-proof'];
		foreach( $dynamic_styles as $style )
			wp_register_style( $style, plugins_url( "/assets/css/$style.css", __FILE__ ), ['rbd-core'], filemtime( plugin_dir_path( __FILE__ ) . "assets/css/$style.css" ) );

		$dynamic_scripts = ['shortcode-review-engine-display', 'shortcode-review-engine-display-admin', 'widget-review-slider'];
		foreach( $dynamic_scripts as $script )
			wp_register_script( $script, plugins_url( "/assets/js/$script.js", __FILE__ ), ['rbd-core'], filemtime( plugin_dir_path( __FILE__ ) . "assets/js/$script.js" ), true );
	}

	function register_shortcodes(){
		foreach( $this->rbd_core_vars()->shortcodes as $shortcode ){
			add_action( 'media_buttons', function() use ( $shortcode ){
				echo '<style>#rbd-review-engine-display-button .dashicons:before{content:"\f155";position:relative;top:1px;left:-2px;font-size:17px;color:#82878c}</style>';
				echo '<button type="button" id="rbd-'. $shortcode .'-button" class="button rbd-shortcode-button '. $shortcode .'" data-editor="content"><span class="dashicons dashicons-star-filled"></span>'. ucwords( str_replace( '-', ' ', $shortcode ) ) .'</button>';
			});

			require_once plugin_dir_path( __FILE__ ) . "lib/shortcodes/$shortcode/shortcode-$shortcode.php";
		}
	}

	function register_widgets(){
		foreach( $this->rbd_core_vars()->widgets as $widget ){
			require_once plugin_dir_path( __FILE__ ) . "lib/widgets/$widget/widget-$widget.php";
		}
	}

	function reset_transients( $post_id ){
		foreach( $this->rbd_core_vars()->shortcodes as $shortcode ){
			delete_transient( $this->rbd_transient_salt( $post_id, "rbd-$shortcode" ) );
		}
	}

	public static function display_popup( $html = '', $classes = [] ){ ?>
		<?php if( RBD_Core::$rbd_popup_displayed == false ){ ?>
			<div class="rbd-core-ui">
				<div id="rbd-popup-container">
					<div class="rbd-popup-content <?= join(' ', $classes); ?>">
						<div><?= $html; ?></div>
						<i class="renderSVG rbd-popup-close" data-icon="close"></i>
					</div>
				</div>
			</div>
			<?php RBD_Core::rbd_popup_displayed(); ?>
		<?php } ?>
	<?php }

	public static function rbd_core_url( $api_url = false, $alternate_url = null ){
		$url = ( $alternate_url == null ) ? get_option('rbd_core_review_engine_url') : $alternate_url;

		//$url = current( explode( '/', str_replace( array( 'https://', 'http://' ), '', $url ) ) );
		$url = str_replace( array( 'https://', 'http://' ), '', $url );

		if( substr( $url, -1 ) == '/' ){
			$url = substr( $url, 0, -1 );
		}
		
		return ( $api_url == true ) ? 'https://'. $url .'/reviews-api-v2/?query_v2=true&user=RedBrickDigital&key=cebb.4268eddefa' : $url;
	}

	public static function rbd_core_api_call(){
		$api_url        = RBD_Core::rbd_core_url( true ) .'&reviews_per_page=1';
		$transient_name = 'rbd_core_api_call';

		$transient = get_transient( $transient_name );
		if( false === $transient || strlen( $transient ) < 69 )
			set_transient( $transient_name, wp_remote_retrieve_body( wp_remote_get( $api_url ) ), 86400 );

		return json_decode( get_transient( $transient_name ) );
	}

	public static function rbd_core_hipaa_warning(){
		if( defined( 'RBD_HIPAA_COMPLIANCE' ) ) { ?>
			<div style="background: #f8f8f8; padding: 1px 30px; border-left: 4px solid #dc3232; box-shadow: 0 2px 2px rgba(0,0,0,.15); margin-bottom: 15px;">
				<p><strong>HIPAA Compliance Enabled:</strong> Personally identifying information will be removed from reviews. To change this setting, go <a href="'. admin_url() .'admin.php?page=redbrick-digital-core/admin/admin-core.php">here</a>.</p>
			</div>
		<?php }
	}

	public static function rbd_transient_salt( $id, $string ){
		$salt_site = str_replace( array( '/', '.' ), '-', str_replace( array( 'https://', 'http://' ), '', site_url() ) );
		return "$string-$id-$salt_site";
	}

	public static function issetor(&$var, $default = false) {
	    return isset( $var ) ? $var : $default;
	}
}

add_action( 'plugins_loaded', ['RBD_Core', 'get_instance'] );