<?php
	wp_enqueue_style( 'widget-review-slider' );
	wp_enqueue_script( 'widget-review-slider' );

	/*add_action( 'wp_footer', function(){
		RBD_core::display_popup();
	});*/

	foreach( rbd_core_review_slider_options() as $var => $default_value )
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
	$transient_name = RBD_Core::rbd_transient_salt( $args['widget_id'], 'rbd-review-slider' );

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

		$json = json_decode( get_transient( $transient_name ) );

		$data = new stdClass();
		$data->returned_reviews = (empty($json->reviews)) ? 0 : count( $json->reviews );
		$count = 0;
		echo $args['before_widget']; ?>
			<?php echo !empty( $title ) ? $args['before_title'] . $title . $args['after_title'] : ''; ?>
			<?php if( $data->returned_reviews > 0 ){ ?>
				<div class="rbd-core-ui">
					<div class="rbd-review-slider" data-speed="<?= $slider_speed * 1000; ?>">
						<?php foreach( $json->reviews as $review ){ ?>
							<?php
								if( $hide_meta != true ){
									$meta['reviewer'] = ( $hide_reviewer == true || defined( 'RBD_HIPAA_COMPLIANCE' ) ) ? '' : "by {$review->review_reviewer->display_name}";
									$meta['date']     = ( $hide_date == true ) ? '' : "on {$review->date->string}";
								}
								$count++;

								if( $count == 1 ) $start_class = 'rbd-curr';
								else if( $count == 2 ) $start_class = 'rbd-next';
								else $start_class = '';
							?>
							<div class="rbd-review <?= $start_class ?> ">
								<?php
									//var srcId = (typeof review.review_source === 'undefined' || typeof review.review_source === null) ? null : review.review_source.sourceId;
									//var reviewSrc = (srcId === null)
									//	? '<span class="review-source x"><svg class="whirlocal-pin whirlocal-pin-2" viewBox="0 0 300 400"><defs><style>.shape{fill:#0095ee}.shape{fill-rule:evenodd}</style><linearGradient id="linear-gradient-61f052e83fc00" x1="187.984" x2="187.984" y2="201" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#0350b9"></stop><stop offset="1" stop-color="#0095ee"></stop></linearGradient></defs><path id="shape" data-name="Outer Pin" class="shape" style="fill:#0095ee;" d="M149.95,400C119.012,320.886,76.928,285.143,45.216,250.9,30.746,235.281,18.435,219.97,10.329,201,3.795,185.7-.008,168.023-0.008,145.882-0.008,65.314,67.131,0,149.95,0S299.908,65.314,299.908,145.882C299.908,255.686,206.386,255.686,149.95,400ZM102.314,98.408c27.056-27.056,70.339-27.64,96.675-1.3s25.752,69.619-1.3,96.675c-24.845,24.845-63.373,27.368-89.9,7.221a66.106,66.106,0,0,1-6.778-5.917C74.675,168.747,75.259,125.464,102.314,98.408Z"></path><path id="swipe-right" data-name="Outer Pin copy" style="fill:url(#linear-gradient-61f052e83fc00);" class="swipe-right" d="M101.053,195.083C74.706,168.747,45.219,96.8,150.013,0H149.95c82.82,0,149.958,65.314,149.958,145.882,0,22.088-3.4,39.848-9.908,55.118l-100.734-.009a71.69,71.69,0,0,0,8.42-7.212c27.055-27.056,27.639-70.339,1.3-96.674s-69.619-25.752-96.675,1.3-27.639,70.339-1.3,96.675c2.152,2.152,2.2,2.152.042,0"></path></svg></span>'
									//	: '<span class="review-source sourceId-'+srcId+'"></span>';
									if( isset($review->review_source) ){
										$reviewSrc = '<span style="margin: 7px 0;" class="review-source sourceId-'.$review->review_source->sourceId.'"></span>';
									} else {
										$reviewSrc = '<span style="margin: 8px ​3px;" class="review-source x"><svg class="whirlocal-pin whirlocal-pin-2" viewBox="0 0 300 400"><defs><style>.shape{fill:#0095ee}.shape{fill-rule:evenodd}</style><linearGradient id="linear-gradient-61f052e83fc00" x1="187.984" x2="187.984" y2="201" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#0350b9"></stop><stop offset="1" stop-color="#0095ee"></stop></linearGradient></defs><path id="shape" data-name="Outer Pin" class="shape" style="fill:#0095ee;" d="M149.95,400C119.012,320.886,76.928,285.143,45.216,250.9,30.746,235.281,18.435,219.97,10.329,201,3.795,185.7-.008,168.023-0.008,145.882-0.008,65.314,67.131,0,149.95,0S299.908,65.314,299.908,145.882C299.908,255.686,206.386,255.686,149.95,400ZM102.314,98.408c27.056-27.056,70.339-27.64,96.675-1.3s25.752,69.619-1.3,96.675c-24.845,24.845-63.373,27.368-89.9,7.221a66.106,66.106,0,0,1-6.778-5.917C74.675,168.747,75.259,125.464,102.314,98.408Z"></path><path id="swipe-right" data-name="Outer Pin copy" style="fill:url(#linear-gradient-61f052e83fc00);" class="swipe-right" d="M101.053,195.083C74.706,168.747,45.219,96.8,150.013,0H149.95c82.82,0,149.958,65.314,149.958,145.882,0,22.088-3.4,39.848-9.908,55.118l-100.734-.009a71.69,71.69,0,0,0,8.42-7.212c27.055-27.056,27.639-70.339,1.3-96.674s-69.619-25.752-96.675,1.3-27.639,70.339-1.3,96.675c2.152,2.152,2.2,2.152.042,0"></path></svg></span>';
									}
								?>
								<h3 class="rbd-title"><?php echo $reviewSrc; ?><span><?= $review->title; ?></span></h3>
								<i class="rbd-score renderSVG" data-icon="star" data-repeat="5" data-score="<?= $review->review_rating; ?>"></i>
								<div class="rbd-content">
									<?php if( !defined( 'RBD_HIPAA_COMPLIANCE' ) && $hide_gravatar != true && $hide_reviewer != true ){
										//echo ( $review->review_reviewer->gravatar != null ) ? '<img class="rbd-gravatar" src="'. $review->review_meta->reviewer->gravatar .'" />' : '';
									} ?>
									<?= // Echo Review Content. Trimmed string is shortened by the word
										strlen( strip_tags( $review->review_content ) ) > $characters ? // if longer than defined
										'<span class="rbd-content-limit">'. substr( $review->review_content, 0, strpos( $review->review_content, ' ', $characters ) ) .'…</span>' : // Trim and display Read More
										'<span class="rbd-content-limit">'. $review->review_content .'</span>'; // Otherwise show full review content
									?>
								</div>
								<div class="rbd-footing">
									<a class="rbd-button rbd-small" href="<?= $review->url; ?>" target="_blank">Read More</a>
								</div>
								<div class="rbd-review-meta"><?= !empty( $meta ) ? 'Written '. join( ' ', $meta ) : ''; ?></div>
							</div>
						<?php } ?>
					</div>
				</div>
			<?php } else { ?>
				<div class="rbd-core-ui">
					<div class="rbd-review-slider">
						<div class="rbd-review rbd-curr">
							<h3 class="rbd-heading">No Reviews Found</h3>
							<div class="rbd-content">Please adjust your review query.</div>
						</div>
					</div>
				</div>
			<?php }?>
		<?php echo $args['after_widget'];
	} else {
		$json  = json_decode( get_transient( $transient_name ) );
		$data  = $json->data[0];
		$count = 0;

		# Before/After Widget Defined By Theme
		echo $args['before_widget']; ?>
			<?php echo !empty( $title ) ? $args['before_title'] . $title . $args['after_title'] : ''; ?>
			<?php if( $data->returned_reviews > 0 ){ ?>
				<div class="rbd-core-ui">
					<div class="rbd-review-slider" data-speed="<?= $slider_speed * 1000; ?>">
						<?php foreach( $json->reviews as $review ){ ?>
							<?php
								if( $hide_meta != true ){
									$meta['reviewer'] = ( $hide_reviewer == true || defined( 'RBD_HIPAA_COMPLIANCE' ) ) ? '' : "by {$review->review_meta->reviewer->display_name}";
									$meta['date']     = ( $hide_date == true ) ? '' : "on {$review->review_meta->review_date->date}";
								}
								$count++;

								if( $count == 1 ) $start_class = 'rbd-curr';
								else if( $count == 2 ) $start_class = 'rbd-next';
								else $start_class = '';
							?>
							<div class="rbd-review <?= $start_class ?> ">
								<h3 class="rbd-title"><?= $review->title; ?></h3>
								<i class="rbd-score renderSVG" data-icon="star" data-repeat="5" data-score="<?= $review->rating; ?>"></i>
								<div class="rbd-content">
									<?php if( !defined( 'RBD_HIPAA_COMPLIANCE' ) && $hide_gravatar != true && $hide_reviewer != true ){
										echo ( $review->review_meta->reviewer->gravatar != null ) ? '<img class="rbd-gravatar" src="'. $review->review_meta->reviewer->gravatar .'" />' : '';
									} ?>
									<?= // Echo Review Content. Trimmed string is shortened by the word
										strlen( strip_tags( $review->content ) ) > $characters ? // if longer than defined
										'<span class="rbd-content-limit">'. substr( $review->content, 0, strpos( $review->content, ' ', $characters ) ) .'…</span>' : // Trim and display Read More
										'<span class="rbd-content-limit">'. $review->content .'</span>'; // Otherwise show full review content
									?>
								</div>
								<div class="rbd-footing">
									<a class="rbd-button rbd-small" href="<?= $review->url; ?>" target="_blank">Read More</a>
									<!--<a href="#" class="rbd-button rbd-small" data-more="<?= str_replace( substr( $review->content, 0, strpos( $review->content, ' ', $characters ) ), '', $review->content ); ?>">Read More</a>-->
								</div>
								<div class="rbd-review-meta"><?= !empty( $meta ) ? 'Written '. join( ' ', $meta ) : ''; ?></div>
							</div>
						<?php } ?>
					</div>
				</div>
			<?php } else { ?>
				<div class="rbd-core-ui">
					<div class="rbd-review-slider">
						<div class="rbd-review rbd-curr">
							<h3 class="rbd-heading">No Reviews Found</h3>
							<div class="rbd-content">Please adjust your review query.</div>
						</div>
					</div>
				</div>
			<?php }?>
		<?php echo $args['after_widget'];
	} ?>