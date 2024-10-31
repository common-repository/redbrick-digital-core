<?php
	foreach( rbd_core_social_proof_options() as $var => $default_value)
		${$var} = isset( $instance[$var] ) ? $instance[$var] : $default_value;
	
	?>
	<div class="rbd-core-ui-admin">
		<p>
			<strong><label for="<?php echo $this->get_field_id( 'url' ); ?>"><?php _e( 'Review Engine URL:' ); ?></label></strong>
			<input class="widefat" id="<?php echo $this->get_field_id( 'url' ); ?>" name="<?php echo $this->get_field_name( 'url' ); ?>" type="text" value="<?php echo esc_attr( $url ); ?>" />
		</p>
	</div>
