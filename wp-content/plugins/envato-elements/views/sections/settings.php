<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div>

	<p>
		Thank you for registering this product.
	</p>

	<p>
		<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?action=envato_elements_deactivate' ), 'deactivate' ) ); ?>">Deactivate Website</a>
	</p>

</div>
