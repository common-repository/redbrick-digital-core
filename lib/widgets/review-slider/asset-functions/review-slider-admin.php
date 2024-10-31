<?php
	foreach( rbd_core_review_slider_options() as $var => $default_value)
		${$var} = isset( $instance[$var] ) ? $instance[$var] : $default_value;

	$api = RBD_Core::rbd_core_api_call();
?>
<div class="rbd-core-ui">
	<?php RBD_Core::rbd_core_hipaa_warning(); ?>
	<div data-grid="grid" data-columns="2">
		<label>
			<span>Title:</span>
			<input class="widefat" id="<?= $this->get_field_id('title'); ?>" name="<?= $this->get_field_name('title'); ?>" type="text" value="<?= esc_attr( $title ); ?>" />
		</label>
		<label>
			<span>Review Engine URL:</span>
			<input class="widefat" id="<?= $this->get_field_id( 'url' ); ?>" name="<?= $this->get_field_name( 'url' ); ?>" type="text" value="<?= esc_attr( $url ); ?>" />
		</label>
	</div>
	<div data-grid="grid" data-columns="3">
		<label>
			<span>Threshold:</span>
			<select class="widefat" id="<?= $this->get_field_id( 'threshold' ); ?>" name="<?= $this->get_field_name( 'threshold' ); ?>">
				<option <?php if( $threshold == '3'){ echo 'selected'; } ?> value="3">★★★☆☆</option>
				<option <?php if( $threshold == '4'){ echo 'selected'; } ?> value="4">★★★★☆</option>
				<option <?php if( $threshold == '5'){ echo 'selected'; } ?> value="5">★★★★★</option>
			</select>
		</label>
		<label>
			<span>Review Count:</span>
			<select class="widefat" id="<?= $this->get_field_id( 'perpage' ); ?>" name="<?= $this->get_field_name( 'perpage' ); ?>">
				<?php
					for( $i = 1; $i <= 20; ++$i ){
						$selected = ( $perpage == $i ) ? 'selected' : '';
						$label = $i > 1 ? 'Reviews' : 'Review';
						echo '<option '. $selected .' value="'. $i .'">'. $i .' '. $label .'</option>';
					}
				?>
			</select>
		</label>
		<label>
			<span>Character Count:</span>
			<input class="widefat" id="<?= $this->get_field_id( 'character_count' ); ?>" name="<?= $this->get_field_name( 'character_count' ); ?>" type="number" value="<?= esc_attr( $character_count ); ?>" />
		</label>
		<?php
			if( !empty( $api->company[0]->taxonomies[0]->service[0] ) ){ ?>
				<label>
					<span>Service:</span>
					<select class="widefat" id="<?= $this->get_field_id( 'service' ); ?>" name="<?= $this->get_field_name( 'service' ); ?>">
						<option value="">All</option>
						<?php foreach( $api->company[0]->taxonomies[0]->service[0] as $services ){
							$selected = ( $service == $services->slug ) ? 'selected' : '';
							echo "<option $selected value='{$services->slug}'>{$services->name}</option>";
						} ?>
					</select>
				</label>
			<?php }

			if( !empty( $api->company[0]->taxonomies[0]->employee[0] ) ){ ?>
				<label>
					<span>Staff:</span>
					<select class="widefat" id="<?= $this->get_field_id( 'employee' ); ?>" name="<?= $this->get_field_name( 'employee' ); ?>">
						<option value="">All</option>
						<?php foreach( $api->company[0]->taxonomies[0]->employee[0] as $employees ){
							$selected = ( $employee == $employees->slug ) ? 'selected' : '';
							echo "<option $selected value='{$employees->slug}'>{$employees->name}</option>";
						} ?>
					</select>
				</label>
			<?php }

			if( !empty( $api->company[0]->taxonomies[0]->location[0] ) ){ ?>
				<label>
					<span>Location:</span>
					<select class="widefat" id="<?= $this->get_field_id( 'location' ); ?>" name="<?= $this->get_field_name( 'location' ); ?>">
						<option value="">All</option>
						<?php foreach( $api->company[0]->taxonomies[0]->location[0] as $locations ){
							$selected = ( $location == $locations->slug ) ? 'selected' : '';
							echo "<option $selected value='{$locations->slug}'>{$locations->name}</option>";
						} ?>
					</select>
				</label>
			<?php }
		?>
	</div>
	<div data-grid="grid" data-columns="2">
		<div>
			<input style="position:relative;top:3px;left:5px;" type="checkbox" id="<?= $this->get_field_id( 'hide_reviewer' ); ?>" name="<?= $this->get_field_name( 'hide_reviewer' ); ?>" value="true" <?php if( $hide_reviewer == true ){ echo 'checked'; } ?> />
			<label for="<?= $this->get_field_id( 'hide_reviewer' ); ?>"><span>Hide Reviewer</span></label>
		</div>
		<div>
			<input style="position:relative;top:3px;left:5px;" type="checkbox" id="<?= $this->get_field_id( 'hide_date' ); ?>" name="<?= $this->get_field_name( 'hide_date' ); ?>" value="true" <?php if( $hide_date == true ){ echo 'checked'; } ?> />
			<label for="<?= $this->get_field_id( 'hide_date' ); ?>"><span>Hide Date</span></label>
		</div>
		<div>
			<input style="position:relative;top:3px;left:5px;" type="checkbox" id="<?= $this->get_field_id( 'hide_meta' ); ?>" name="<?= $this->get_field_name( 'hide_meta' ); ?>" value="true" <?php if( $hide_meta == true ){ echo 'checked'; } ?> />
			<label for="<?= $this->get_field_id( 'hide_meta' ); ?>"><span>Hide Meta</span></label>
		</div>
		<div>
			<input style="position:relative;top:3px;left:5px;" type="checkbox" id="<?= $this->get_field_id( 'hide_gravatar' ); ?>" name="<?= $this->get_field_name( 'hide_gravatar' ); ?>" value="true" <?php if( $hide_gravatar == true ){ echo 'checked'; } ?> />
			<label for="<?= $this->get_field_id( 'hide_gravatar' ); ?>"><span>Hide Gravatar</span></label>
		</div>
	</div>
	<div data-grid="grid" data-columns="2">
		<label>
			<span>Slider Speed (in Seconds):</span>
			<select class="widefat" id="<?= $this->get_field_id( 'slider_speed' ); ?>" name="<?= $this->get_field_name( 'slider_speed' ); ?>">
				<?php
					for( $i = 1; $i <= 20; ++$i ){
						$selected = ( $slider_speed == $i ) ? 'selected' : '';
						$label = $i > 1 ? 'Seconds' : 'Second';
						echo '<option '. $selected .' value="'. $i .'">'. $i .' '. $label .'</option>';
					}
				?>
			</select>
		</label>
	</div>
	<div style="display: none;">
		<input type="hidden" id="<?= $this->get_field_id( 'widget_instance_id' ); ?>" name="<?= $this->get_field_name( 'widget_instance_id' ); ?>" value="<?= esc_attr( $this->id ); ?>" />
	</div>
</div>
