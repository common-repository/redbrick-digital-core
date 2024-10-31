<?php
	function rbd_core_review_slider_options(){
		return [
			'url'             => get_option( 'rbd_core_review_engine_url' ),
			'title'           => 'Featured Reviews',
			'perpage'         => 10,
			'service'         => '',
			'employee'        => '',
			'location'        => '',
			'threshold'       => 4,
			'hide_meta'       => false,
			'hide_date'       => false,
			'characters'      => 255,
			'hide_gravatar'   => false,
			'hide_reviewer'   => false,
			'character_count' => 255,
			'slider_speed'    => 4
		];
	}

	class rbd_core_widget_review_slider extends WP_Widget {
		function __construct() {
			parent::__construct(
				'rbd_core_widget_review_slider',
				__('RBD Core ★ Review Slider', 'rbd_core_widget_review_slider_domain'),
				['description' => __( 'Display a slider with reviews from your Review Engine.', 'rbd_core_widget_review_slider_domain' )]
			);
		}

		public function widget( $args, $instance ) {
			include plugin_dir_path( __FILE__ ) . 'asset-functions/review-slider-front-end.php';
		}

		public function form( $instance ) {
			include plugin_dir_path( __FILE__ ) . 'asset-functions/review-slider-admin.php';
		}

		public function update( $new_instance, $old_instance ) {
			delete_transient( RBD_Core::rbd_transient_salt( $new_instance['widget_instance_id'], 'rbd-review-slider' ) );
			$instance = [];

			foreach( rbd_core_review_slider_options() as $var => $default_value )
				$instance[$var] = ( !empty( $new_instance[$var] ) ) ? sanitize_text_field( $new_instance[$var] ) : '';

			return $instance;
		}
	}

	add_action( 'widgets_init', function(){
		register_widget( 'rbd_core_widget_review_slider' );
	});
?>
