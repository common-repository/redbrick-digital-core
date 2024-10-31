<?php
	wp_enqueue_style( 'shortcode-review-engine-display' );
	wp_enqueue_script( 'shortcode-review-engine-display' );

	add_action( 'wp_footer', function(){
		RBD_core::display_popup();
	});

	foreach( rbd_core_review_engine_display_widget_options() as $var => $default_value )
		${$var} = apply_filters( 'widget_'.$var, $instance[$var] );

	# Backwards Compatibility
	$characters = !empty( $character_count ) ? $character_count : $characters;

	#Query Parameters
	$query_params	= array(
		# Nomenclature is messed up service is actually category.
		'service'          => 'category',
		'employee'         => 'employee',
		'location'         => 'location',
		'threshold'        => 'threshold',
		'reviews_per_page' => 'reviews_per_page',
		'perpage'          => 'reviews_per_page',
	);

	# Turn API Query from shortcode into a transient saved object
	$url            = 'https://'. str_replace( ['http://', 'https://'], '', $url );
	$api_url        = RBD_Core::rbd_core_url( true, $url );
	$transient_name = RBD_Core::rbd_transient_salt( $args['widget_id'], 'rbd-review-engine-display' );

	foreach( $query_params as $key => $val )
		$api_url .= ( empty( ${$key} ) || ${$key} == 'all' ) ? '' : "&$val=". urldecode( ${$key} );

	# Check Transient and make sure it's a valid request
	$transient = get_transient( $transient_name );
	if( false === $transient || strlen( $transient ) < 69 )
		set_transient( $transient_name, wp_remote_retrieve_body( wp_remote_get( $api_url ) ), 86400 );

	if( $wlio_api_url = get_option( 'wlio_api_url' ) ){
		// OVERRIDE WITH WLIO
		$api_url = $wlio_api_url;
		delete_transient( $transient_name );
		set_transient( $transient_name, wp_remote_retrieve_body( wp_remote_get( $api_url ) ), 86400 );
	} else {
		$json  = json_decode( get_transient( $transient_name ) );
		$data  = $json->data[0];
		$count = 0;
	}

	# Before/After Widget Defined By Theme
	echo $args['before_widget'];
		echo !empty( $title ) ? $args['before_title'] . $title . $args['after_title'] : '';

		$shortcode = '[rbd_review_engine';
		foreach( $instance as $key => $val ){
			if( $key == 'service' || $key == 'employee' || $key == 'location' ){
				if( $val == '' ){
					$val = 'all';
				}
			} else {
				if( $val == '' ){
					continue;
				}
			}

			$shortcode .= " $key='$val'";
		}
		$shortcode .= ' transient_id="'.$args['widget_id'].'"'; // Appending Custom ID
		$shortcode .= ']';
		
		echo do_shortcode( $shortcode );

	echo $args['after_widget'];
?>
