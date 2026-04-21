<?php
/**
 * Front page: one-pager mirroring rgeometry.lovable.app.
 *
 * Order matches the Lovable reference's src/pages/Index.tsx.
 *
 * @package RGeometry
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<div class="rg-page">
	<?php get_template_part( 'template-parts/section', 'navbar' ); ?>
	<?php get_template_part( 'template-parts/section', 'hero' ); ?>
	<?php get_template_part( 'template-parts/section', 'services' ); ?>
	<?php get_template_part( 'template-parts/section', 'projects' ); ?>
	<?php get_template_part( 'template-parts/section', 'about' ); ?>
	<?php get_template_part( 'template-parts/section', 'process' ); ?>
	<?php get_template_part( 'template-parts/section', 'testimonials' ); ?>
	<?php get_template_part( 'template-parts/section', 'contact' ); ?>
</div>
<?php
get_footer();
