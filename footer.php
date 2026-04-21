<?php
/**
 * Site footer template.
 *
 * Renders the footer section only on the front page (one-pager). On any other
 * template the footer is just the closing tags, keeping sitewide output valid.
 *
 * @package RGeometry
 */

if ( is_front_page() ) {
	get_template_part( 'template-parts/section', 'footer' );
}
?>
<?php wp_footer(); ?>
</body>
</html>
