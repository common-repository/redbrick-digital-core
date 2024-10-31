<?php
	add_action( 'admin_footer', function(){
		//if( get_current_screen()->base == 'post' ){
			function review_engine_display_admin_popup_html(){
				ob_start(); ?>
				<h2>Insert Review Engine Shortcode</h2>
				<form id="review-engine-display">
					<div data-grid="grid" data-columns="2">
						<label style="position: relative;">
							<span>Review Engine URL:</span>
							<input type="text" name="url" class="wp-core-ui widefat" value="<?= RBD_Core::rbd_core_url(); ?>" /><i class="rbd-sync renderSVG" data-icon="sync"></i>
						</label>
					</div>
					<div data-grid="grid" data-columns="3">
						<label>
							<span>Limit Characters:</span>
							<input type="number" name="characters" id="characters" class="wp-core-ui widefat" value="135" />
						</label>
						<label>
							<span>Reviews Per Page:</span>
							<select name="perpage" id="perpage" class="wp-core-ui widefat">
								<?php
									for( $i = 1; $i <= 20; ++$i ){
										$selected = ($i == 6) ? 'selected' : '';
										echo "<option value='$i' $selected>$i</option>";
									}
								?>
							</select>
						</label>
						<label>
							<span>Max Columns:</span>
							<select name="columns" class="wp-core-ui widefat">
								<?php
									for( $i = 1; $i <= 4; ++$i ){
										$selected = ($i == 3) ? 'selected' : '';
										echo "<option value='$i' $selected>$i</option>";
									}
								?>
							</select>
						</label>
					</div>
					<div data-grid="grid" data-columns="4">
						<label>
							<span>Threshold:</span>
							<select name="threshold" id="threshold" class="wp-core-ui widefat">
								<option value="3" style="font-size: 24px;">★★★☆☆</option>
								<option value="4" style="font-size: 24px;" selected="selected">★★★★☆</option>
								<option value="5" style="font-size: 24px;">★★★★★</option>
							</select>
						</label>
						<label id="rbd-service">
							<span>Service:</span>
							<?php if( empty( RBD_Core::rbd_core_api_call()->company[0]->taxonomies[0]->service[0] ) ) {
								$disabled = 'disabled';
								$label = 'N/A';
							} else {
								$disabled = '';
								$label = 'All';
							} ?>
							<select id="service" name="service" class="wp-core-ui widefat" <?= $disabled; ?>>
								<option value="all"><?= $label; ?></option>
								<?php foreach( RBD_Core::rbd_core_api_call()->company[0]->taxonomies[0]->service[0] as $services ){
									$selected = ( $service == $services->slug ) ? 'selected="selected"' : '';
									echo "<option $selected value='$services->slug'>$services->name</option>";
								} ?>
							</select>
						</label>
						<label id="rbd-employee">
							<span>Staff:</span>
							<?php if( empty( RBD_Core::rbd_core_api_call()->company[0]->taxonomies[0]->employee[0] ) ) {
								$disabled = 'disabled';
								$label = 'N/A';
							} else {
								$disabled = '';
								$label = 'All';
							} ?>
							<select id="employee" name="employee" class="wp-core-ui widefat" <?= $disabled; ?>>
								<option value="all"><?= $label; ?></option>
								<?php foreach( RBD_Core::rbd_core_api_call()->company[0]->taxonomies[0]->employee[0] as $employees ){
									$selected = ( $employee == $employees->slug ) ? 'selected="selected"' : '';
									echo "<option $selected value='$employees->slug'>$employees->name</option>";
								} ?>
							</select>
						</label>
						<label id="rbd-location">
							<span>Location:</span>
							<?php if( empty( RBD_Core::rbd_core_api_call()->company[0]->taxonomies[0]->location[0] ) ) {
								$disabled = 'disabled';
								$label = 'N/A';
							} else {
								$disabled = '';
								$label = 'All';
							} ?>
							<select id="location" name="location" class="wp-core-ui widefat" <?= $disabled; ?>>
								<option value="all"><?= $label; ?></option>
								<?php foreach( RBD_Core::rbd_core_api_call()->company[0]->taxonomies[0]->location[0] as $locations ){
									$selected = ( $location == $locations->slug ) ? 'selected="selected"' : '';
									echo "<option $selected value='$locations->slug'>$locations->name</option>";
								} ?>
							</select>
						</label>
						<?php foreach( ['Hide Reviewer', 'Hide Gravatar', 'Hide Date', 'Disable 3D'] as $option ){ ?>
							<label style="margin-top: 10px;">
								<input type="checkbox" name="<?= sanitize_title( $option ); ?>" id="<?= sanitize_title( $option ); ?>" class="wp-core-ui widefat" />
								<span><?= $option; ?>?</span>
							</label>
						<?php } ?>
					</div>
					<div style="text-align:right;padding:1px 0">
						<button type="submit" class="wp-core-ui button-primary" id="submit">Insert Shortcode</button>
					</div>
				</form>
				<?php
					$ob = ob_get_contents();
					ob_end_clean();

					return $ob;
			}

			RBD_Core::display_popup( review_engine_display_admin_popup_html() );
		// }
	});
?>