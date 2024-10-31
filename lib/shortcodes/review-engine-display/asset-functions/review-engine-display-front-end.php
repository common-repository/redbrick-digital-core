<?php
	add_shortcode( 'rbd_review_engine', function( $atts ){
		extract( shortcode_atts( array(
			'placeholder'   => '',
			'threshold'	    => 4,
			'service'		=> 'all',
			'employee'		=> 'all',
			'location'		=> 'all',
			'characters'	=> 135,
			'perpage'		=> 8,
			'columns'		=> 2,
			'hide_reviewer' => false,
			'hide_gravatar'	=> false,
			'hide_date'		=> false,
			'hide_overview' => false,
		), $atts ) );

		add_action( 'wp_footer', function(){
			$this->display_popup( null, ['rbd-review-engine-display'] );
		});

		# Scripts and Styles are registered in the main plugin file
		# but don't need to be enqueued unless the shortcode is run.
		wp_enqueue_style( 'shortcode-review-engine-display' );
		wp_enqueue_script( 'shortcode-review-engine-display' );

		# Define vars so we can use $key instead of $atts['key']
		foreach( $atts as $key => $val ) ${str_replace('-', '_', $key)} = $val;

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
		$api_url        = $this->rbd_core_url( true, $url );

		$transient_id   = isset( $transient_id ) ? $transient_id : get_the_ID();
		$transient_name = $this->rbd_transient_salt( $transient_id, 'rbd-review-engine-display' );

		foreach( $query_params as $key => $val )
			$api_url .= ( empty( ${$key} ) || ${$key} == 'all' ) ? '' : "&$val=". urldecode( ${$key} );

		# Check Transient and make sure it's a valid request
		$transient = get_transient( $transient_name );
		if( false === $transient || strlen( $transient ) < 125 )
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

			$review_percents['5'] = ['percent' => round( ( $json->reputation->five_star_reviews  / $json->reputation->total_reviews ) * 100 ), 'count' => $json->reputation->five_star_reviews];
			$review_percents['4'] = ['percent' => round( ( $json->reputation->four_star_reviews  / $json->reputation->total_reviews ) * 100 ), 'count' => $json->reputation->four_star_reviews];
			$review_percents['3'] = ['percent' => round( ( $json->reputation->three_star_reviews / $json->reputation->total_reviews ) * 100 ), 'count' => $json->reputation->three_star_reviews];
			$review_percents['2'] = ['percent' => round( ( $json->reputation->two_star_reviews   / $json->reputation->total_reviews ) * 100 ), 'count' => $json->reputation->two_star_reviews];
			$review_percents['1'] = ['percent' => round( ( $json->reputation->one_star_reviews   / $json->reputation->total_reviews ) * 100 ), 'count' => $json->reputation->one_star_reviews];

			$aggregate_percent = $json->reputation->aggregate * 20;

			$data = new stdClass();
			$data->aggregate = $json->reputation->aggregate;
			$data->total_reviews = $json->reputation->total_reviews;
		} else {
			$json = json_decode( get_transient( $transient_name ) );

			# Define API Information from Review Engine and initial load information
			$review_percents = $urls = [];
			
			$offset  = ( $perpage ) ? $perpage : $reviews_per_page;
			$data    = $json->data[0];
			$company = $json->company[0];
		
			$urls['all-reviews']    = $url.'/all-reviews/';
			$urls['write-a-review'] = $data->review_funnels->advanced_review_funnels == true ? $data->review_funnels->review_funnel_url : $url;

			$review_percents['5'] = ['percent' => round( ( $data->five_star_reviews  / $data->total_reviews ) * 100 ), 'count' => $data->five_star_reviews];
			$review_percents['4'] = ['percent' => round( ( $data->four_star_reviews  / $data->total_reviews ) * 100 ), 'count' => $data->four_star_reviews];
			$review_percents['3'] = ['percent' => round( ( $data->three_star_reviews / $data->total_reviews ) * 100 ), 'count' => $data->three_star_reviews];
			$review_percents['2'] = ['percent' => round( ( $data->two_star_reviews   / $data->total_reviews ) * 100 ), 'count' => $data->two_star_reviews];
			$review_percents['1'] = ['percent' => round( ( $data->one_star_reviews   / $data->total_reviews ) * 100 ), 'count' => $data->one_star_reviews];

			$aggregate_percent = $data->aggregate * 20;
		}


		ob_start(); ?>

		<div class="rbd-core-ui rbd-review-engine-display <?= $disable_3d ? '' : 'rbd-3d-effects'; ?>" data-review-engine-url="<?= $url; ?>">
		    <?php if( $hide_overview != true ){ ?>
			    <h2 class="rbd-header">
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
					<button class="rbd-view-breakdown">View Rating Breakdown</button>
				</h2>
				<div class="rbd-breakdown-container">
					<div class="rbd-bar-container">
						<?php foreach( $review_percents as $rating => $percent ){ ?>
							<div><i class="rbd-score renderSVG" data-icon="star" data-repeat="5" data-score="<?= $rating; ?>"></i><div class="rbd-bar" style="--width: <?= $percent['percent']; ?>%;"></div><span class="rbd-percent"><?= $percent['percent']; ?>%</span> <span class="rbd-percent rbd-count">(<?= $percent['count']; ?>)</span></div>
						<?php } ?>
					</div>
					<div class="rbd-links-container" data-grid="grid" data-columns="2">
						<div class="rbd-center">
							<a target="_blank" href="<?= $urls['write-a-review']; ?>" class="rbd-button rbd-small">Write a Review</a>
						</div>
						<div class="rbd-center">
							<a target="_blank" href="<?= $urls['all-reviews']; ?>" class="rbd-button rbd-small rbd-secondary">Read All Reviews</a>
						</div>
					</div>
				</div>
			<?php } ?>
			<section class="rbd-review-grid" data-grid="grid" data-columns="<?= $columns; ?>" data-max="<?= $data->total_reviews; ?>">
				<?php if( $wlio_api_url ){ ?>
					<?php foreach( $json->reviews as $review ){ ?>
						<?php
							$meta['reviewer'] = ( $hide_reviewer == true || defined( 'RBD_HIPAA_COMPLIANCE' ) ) ? 'by Anonymous' : "by {$review->review_reviewer->display_name}";
							$meta['date']     = ( $hide_date == true ) ? '' : "on {$review->date->string}";
						?>
						<div class="rbd-review" data-meta="<?= !empty( $meta ) ? 'Written '. join( ' ', $meta ) : ''; ?>" data-permalink="<?= $review->url; ?>">
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
							<p class="rbd-content">
								<?php if( !defined( 'RBD_HIPAA_COMPLIANCE' ) && $hide_gravatar != true ){
									//echo ( $review->review_meta->reviewer->gravatar != null ) ? '<img class="rbd-gravatar" src="'. $review->review_meta->reviewer->gravatar .'" />' : '';
								} ?>
								<?= // Echo Review Content. Trimmed string is shortened by the word
									strlen( strip_tags( $review->review_content ) ) > $characters ? // if longer than defined
									'<span class="rbd-content-limit">'. substr( $review->review_content, 0, strpos( $review->review_content, ' ', $characters ) ) .'…</span><a href="#" data-more="'.str_replace( substr( $review->review_content, 0, strpos( $review->review_content, ' ', $characters ) ), '', $review->review_content ).'">Read More</a>' : // Trim and display Read More
									'<span class="rbd-content-limit">'. $review->review_content .'</span>'; // Otherwise show full review content
								?>
							</p>
							</div>
					<?php } ?>
				<?php } else { ?>
					<?php foreach( $json->reviews as $review ){ ?>
						<?php
							$meta['reviewer'] = ( $hide_reviewer == true || defined( 'RBD_HIPAA_COMPLIANCE' ) ) ? 'by Anonymous' : "by {$review->review_meta->reviewer->display_name}";
							$meta['date']     = ( $hide_date == true ) ? '' : "on {$review->review_meta->review_date->date}";
						?>
						<div class="rbd-review" data-meta="<?= !empty( $meta ) ? 'Written '. join( ' ', $meta ) : ''; ?>" data-permalink="<?= $review->url; ?>">
							<h3 class="rbd-heading"><?= $review->title; ?></h3>
							<i class="rbd-score renderSVG" data-icon="star" data-repeat="5" data-score="<?= $review->rating; ?>"></i>
							<p class="rbd-content">
								<?php if( !defined( 'RBD_HIPAA_COMPLIANCE' ) && $hide_gravatar != true ){
									echo ( $review->review_meta->reviewer->gravatar != null ) ? '<img class="rbd-gravatar" src="'. $review->review_meta->reviewer->gravatar .'" />' : '';
								} ?>
								<?= // Echo Review Content. Trimmed string is shortened by the word
									strlen( strip_tags( $review->content ) ) > $characters ? // if longer than defined
									'<span class="rbd-content-limit">'. substr( $review->content, 0, strpos( $review->content, ' ', $characters ) ) .'…</span><a href="#" data-more="'.str_replace( substr( $review->content, 0, strpos( $review->content, ' ', $characters ) ), '', $review->content ).'">Read More</a>' : // Trim and display Read More
									'<span class="rbd-content-limit">'. $review->content .'</span>'; // Otherwise show full review content
								?>
							</p>
							</div>
					<?php } ?>
				<?php } ?>
			</section>
			<?php if( $wlio_api_url ) { ?>
				<br><br>
				<a href="<?php echo $json->link; ?>" target="_blank" style="background: #0095ee;color: #fff;border: 1px solid #0073cc;border-radius: 3px;box-shadow: inset 0 1px 0 rgba(255,255,255,.25), 0 4px 12px -5px rgba(0,0,0,.5);padding: 12px 30px;cursor: pointer;outline: none;transition: .1s all ease-out;vertical-align: top;text-decoration: none;height: auto;line-height: 1;">Read More Reviews</a>
			<?php } else { ?>
				<?php if( $data->total_reviews > $offset ) { ?>
					<button class="rbd-load-more" data-hide-gravatars="<?= $hide_gravatar || defined( 'RBD_HIPAA_COMPLIANCE' ) ? 'true' : 'false'; ?>" <?php foreach( $atts as $key => $val ) echo "data-$key=\"$val\""; ?> data-offset="<?= $offset; ?>">Load More Reviews</button>
				<?php } ?>
			<?php } ?>
		<div>

		<?php $ob = ob_get_contents();
		ob_end_clean();

		return $ob;
	});
?>
