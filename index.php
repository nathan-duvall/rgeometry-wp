<?php
/**
 * Fallback template. The real entry point is front-page.php for the one-pager.
 *
 * @package RGeometry
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main class="rg-main">
	<div class="rg-container">
		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				the_title( '<h1>', '</h1>' );
				the_content();
			endwhile;
		else :
			echo '<p>' . esc_html__( 'Nothing here yet.', 'rgeometry' ) . '</p>';
		endif;
		?>
	</div>
</main>
<?php
get_footer();
