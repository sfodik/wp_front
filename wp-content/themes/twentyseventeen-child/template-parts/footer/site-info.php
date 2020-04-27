<?php
/**
 * Displays footer site info
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

?>
<div class="site-info">
<?php if( !wp_is_mobile() ) : ?>
	<a href="<?php echo esc_url( __( 'https://wordpress.org/', 'twentyseventeen' ) ); ?>"><?php printf( __( 'Proudly powered by %s', 'twentyseventeen' ), 'WordPress' ); ?></a>
<?php endif ;?>
</div><!-- .site-info -->
<div class="site-contact">
	<?php if( wp_is_mobile() ) : ?>
		<button class="site-call_us mob"><a href="tel:099-876-54-321"><?php echo __( 'Call Us', 'twentyseventeen' ) ; ?></a></button>
		<button class="site-email mob">
			<a href="mailto:<?php echo __( 'testdomain@mail.to', 'twentyseventeen' ) ; ?>"><?php echo __( 'Email Us', 'twentyseventeen' ) ; ?></a>
		</button>
		<div class="site-contact_us mob">
			<button class="triger-call"><?php echo __( 'Contact Us', 'twentyseventeen' ) ; ?></button>
		</div>
		<a href="<?php echo esc_url( __( 'https://wordpress.org/', 'twentyseventeen' ) ); ?>"><?php printf( __( 'Proudly powered by %s', 'twentyseventeen' ), 'WordPress' ); ?></a>
	<?php else: ?>
		<div class="site-call_us"><?php echo __( 'Call Us: 09987654321', 'twentyseventeen' ) ; ?></div>
		<div class="site-email"><a href="mailto:<?php echo __( 'testdomain@mail.to', 'twentyseventeen' ) ; ?>"><?php echo __( 'Email: testdomain@mail.to', 'twentyseventeen' ) ; ?></a></div>
		<div class="site-contact_us"><button class="triger-call"><?php echo __( 'Contact Us', 'twentyseventeen' ) ; ?></button></div>
	<?php endif ;?>

</div>