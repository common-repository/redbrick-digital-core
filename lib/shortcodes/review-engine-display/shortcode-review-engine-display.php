<?php
	foreach( ['admin', 'front-end'] as $include ){
		include( plugin_dir_path( __FILE__ ) . "asset-functions/review-engine-display-$include.php" );
	}
?>
