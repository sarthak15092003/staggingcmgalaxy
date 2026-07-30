<?php
/**
 * The template for displaying header.
 *
 * @package HelloElementor
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$site_name = get_bloginfo( 'name' );
$tagline   = get_bloginfo( 'description', 'display' );
$header_nav_menu = wp_nav_menu( [
	'theme_location' => 'menu-1',
	'fallback_cb' => false,
	'container' => false,
	'echo' => false,
] );
?>

<header id="site-header" class="site-header">

	<div class="site-branding">
		<?php
		if ( has_custom_logo() ) {
			the_custom_logo();
		} elseif ( $site_name ) {
			?>
			<div class="site-title">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr__( 'Home', 'hello-elementor' ); ?>" rel="home">
					<?php echo esc_html( $site_name ); ?>
				</a>
			</div>
			<?php if ( $tagline ) : ?>
			<p class="site-description">
				<?php echo esc_html( $tagline ); ?>
			</p>
			<?php endif; ?>
		<?php } ?>
	</div>

	<?php if ( $header_nav_menu ) : ?>
		<nav class="site-navigation" aria-label="<?php echo esc_attr__( 'Main menu', 'hello-elementor' ); ?>">
			<?php
			// PHPCS - escaped by WordPress with "wp_nav_menu"
			echo $header_nav_menu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</nav>
	<?php endif; ?>

	<div class="nav-rating">
		<div class="nav-rating-avatars">
			<img src="https://i.pravatar.cc/100?img=12" alt="User 1" class="nav-rating-avatar">
			<img src="https://i.pravatar.cc/100?img=5" alt="User 2" class="nav-rating-avatar">
			<img src="https://i.pravatar.cc/100?img=9" alt="User 3" class="nav-rating-avatar">
			<img src="https://i.pravatar.cc/100?img=11" alt="User 4" class="nav-rating-avatar">
		</div>
		<div class="nav-rating-content">
			<span class="nav-rating-star">★</span> 
			<span>5.0 Rated by Users</span>
		</div>
	</div>

</header>
