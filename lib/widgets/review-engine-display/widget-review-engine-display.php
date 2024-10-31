<?php
	function rbd_core_review_engine_display_widget_options(){
		return [
			'url'             => get_option( 'rbd_core_review_engine_url' ),
			'title'           => 'Featured Reviews',
			'columns'         => 1,
			'perpage'         => 5,
			'service'         => '',
			'employee'        => '',
			'location'        => '',
			'threshold'       => 4,
			'hide_meta'       => false,
			'hide_date'       => false,
			'characters'      => 255,
			'disable_3d'      => false,
			'hide_gravatar'   => false,
			'hide_reviewer'   => false,
			//'character_count' => 255,
		];
	}

	class rbd_core_widget_review_engine_display_widget extends WP_Widget {
		function __construct() {
			parent::__construct(
				'rbd_core_widget_review_engine_display_widget',
				__('RBD Core ★ Review Engine Display', 'rbd_core_widget_review_engine_display_widget_domain'),
				['description' => __( 'Display your reviews in a grid.', 'rbd_core_widget_review_engine_display_widget_domain' )]
			);
		}

		public function widget( $args, $instance ){
			include plugin_dir_path( __FILE__ ) . 'asset-functions/review-engine-display-front-end.php';
		}

		public function form( $instance ){
			include plugin_dir_path( __FILE__ ) . 'asset-functions/review-engine-display-admin.php';
		}

		public function update( $new_instance, $old_instance ) {
			delete_transient( RBD_Core::rbd_transient_salt( $new_instance['widget_instance_id'], 'rbd-review-engine-display' ) );
			$instance = [];

			foreach( rbd_core_review_engine_display_widget_options() as $var => $default_value)
				$instance[$var] = ( !empty( $new_instance[$var] ) ) ? sanitize_text_field( $new_instance[$var] ) : '';

			return $instance;
		}
	}

	add_action( 'widgets_init', function(){
		register_widget( 'rbd_core_widget_review_engine_display_widget' );
	});
?>
