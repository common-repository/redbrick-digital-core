<?php
	function rbd_core_social_proof_options(){
		return [
			'url'			=> RBD_Core::rbd_core_url(),
			'text_align'	=> 'left',
		];
	}

	class rbd_core_widget_social_proof extends WP_Widget {
		function __construct() {
			parent::__construct(
				'rbd_core_widget_social_proof',
				__('RBD Core ★ Social Proof', 'rbd_core_widget_social_proof_domain'),
				['description' => __( 'Display your social proof.', 'rbd_core_widget_social_proof_domain' )]
			);
		}

		public function widget( $args, $instance ) {
			include plugin_dir_path( __FILE__ ) . 'asset-functions/social-proof-front-end.php';
		}

		public function form( $instance ) {
			include plugin_dir_path( __FILE__ ) . 'asset-functions/social-proof-admin.php';
		}

		public function update( $new_instance, $old_instance ) {
			delete_transient( RBD_Core::rbd_transient_salt( $new_instance['widget_instance_id'], 'rbd-social-proof' ) );
			$instance = [];

			foreach( rbd_core_social_proof_options() as $var => $default_value)
				$instance[$var] = ( !empty( $new_instance[$var] ) ) ? sanitize_text_field( $new_instance[$var] ) : '';

			return $instance;
		}
	}

	add_action( 'widgets_init', function(){
		register_widget( 'rbd_core_widget_social_proof' );
	});
?>
