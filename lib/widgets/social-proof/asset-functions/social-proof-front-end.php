<?php
	wp_enqueue_style( 'widget-social-proof' );

	foreach( rbd_core_social_proof_options() as $var => $default_value)
		${$var} = isset( $instance[$var] ) ? $instance[$var] : $default_value;

	$url            = 'https://'. str_replace( ['http://', 'https://'], '', $url );
	$api_url        = RBD_Core::rbd_core_url( true, $url );
	$transient_name = RBD_Core::rbd_transient_salt( $args['widget_id'], 'rbd-social-proof' );

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

		# Define API Information from Review Engine and initial load information
		$review_percents = $urls = [];

		$offset  = 0;
		//$data    = $json->reviews;
		$company = $json->meta;

		$urls['all-reviews'] = $json->link;
		$urls['write-a-review'] = $json->link.'?review-us'; 

		if( $json->reputation->total_reviews > 0 ){
			$review_percents['5'] = ['percent' => round( ( $json->reputation->five_star_reviews  / $json->reputation->total_reviews ) * 100 ), 'count' => $json->reputation->five_star_reviews];
			$review_percents['4'] = ['percent' => round( ( $json->reputation->four_star_reviews  / $json->reputation->total_reviews ) * 100 ), 'count' => $json->reputation->four_star_reviews];
			$review_percents['3'] = ['percent' => round( ( $json->reputation->three_star_reviews / $json->reputation->total_reviews ) * 100 ), 'count' => $json->reputation->three_star_reviews];
			$review_percents['2'] = ['percent' => round( ( $json->reputation->two_star_reviews   / $json->reputation->total_reviews ) * 100 ), 'count' => $json->reputation->two_star_reviews];
			$review_percents['1'] = ['percent' => round( ( $json->reputation->one_star_reviews   / $json->reputation->total_reviews ) * 100 ), 'count' => $json->reputation->one_star_reviews];
		} else {
			$review_percents = [
				5 => ['percent' => 0, 'count' => 0],
				4 => ['percent' => 0, 'count' => 0],
				3 => ['percent' => 0, 'count' => 0],
				2 => ['percent' => 0, 'count' => 0],
				1 => ['percent' => 0, 'count' => 0],
			];
		}

		$aggregate_percent = $json->reputation->aggregate * 20;

		$data = new stdClass();
		$data->aggregate = $json->reputation->aggregate;
		$data->total_reviews = $json->reputation->total_reviews;
	} else {
		$json = json_decode( get_transient( $transient_name ) );
		$data = $json->data[0];

		$urls['all-reviews']    = $url.'/all-reviews/';
		$urls['write-a-review'] = $data->review_funnels->advanced_review_funnels == true ? $data->review_funnels->review_funnel_url : $url;

		if( $data->total_reviews > 0 ){
			$review_percents['5'] = ['percent' => round( ( $data->five_star_reviews  / $data->total_reviews ) * 100 ), 'count' => $data->five_star_reviews];
			$review_percents['4'] = ['percent' => round( ( $data->four_star_reviews  / $data->total_reviews ) * 100 ), 'count' => $data->four_star_reviews];
			$review_percents['3'] = ['percent' => round( ( $data->three_star_reviews / $data->total_reviews ) * 100 ), 'count' => $data->three_star_reviews];
			$review_percents['2'] = ['percent' => round( ( $data->two_star_reviews   / $data->total_reviews ) * 100 ), 'count' => $data->two_star_reviews];
			$review_percents['1'] = ['percent' => round( ( $data->one_star_reviews   / $data->total_reviews ) * 100 ), 'count' => $data->one_star_reviews];
		} else {
			$review_percents = [
				5 => ['percent' => 0, 'count' => 0],
				4 => ['percent' => 0, 'count' => 0],
				3 => ['percent' => 0, 'count' => 0],
				2 => ['percent' => 0, 'count' => 0],
				1 => ['percent' => 0, 'count' => 0],
			];
		}

		$aggregate_percent = $data->aggregate * 20;
	}

	# Before/After Widget Defined By Theme
	echo $args['before_widget']; ?>
		<div class="rbd-core-ui rbd-social-proof">
			<h3 class="rbd-header">
				<span class="rbd-aggregate"><?= $data->aggregate; ?></span>
				<span class="rbd-aggregate-container">
					<span class="rbd-earned" style="width: <?= $aggregate_percent; ?>%;">
						<i class="rbd-score renderSVG" data-icon="star" data-repeat="5"></i>
					</span>
					<span>
						<i class="rbd-score renderSVG" data-icon="star" data-repeat="5"></i>
					</span>
				</span>
				<span class="rbd-normal rbd-review-count">(<?= $data->total_reviews; ?>)</span>
				<div class="rbd-breakdown-container">
					<div class="rbd-bar-container">
						<?php foreach( $review_percents as $rating => $percent ){ ?>
							<div><i class="rbd-score renderSVG" data-icon="star" data-repeat="5" data-score="<?= $rating; ?>"></i><div class="rbd-bar" style="--width: <?= $percent['percent']; ?>%;"></div><span class="rbd-percent"><?= $percent['percent']; ?>%</span> <span class="rbd-percent rbd-count">(<?= $percent['count']; ?>)</span></div>
						<?php } ?>
					</div>
					<div class="rbd-links-container">
						<div class="rbd-center">
							<a target="_blank" href="<?= $urls['all-reviews']; ?>" class="rbd-button rbd-small rbd-secondary">Read All Reviews</a>
						</div>
					</div>
				</div>
			</h3>
		</div>
	<?php echo $args['after_widget'];
?>