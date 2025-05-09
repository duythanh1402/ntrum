<?php
/**
 * Template name: Portfolio
 *
 * @package          Flatsome\Templates
 * @flatsome-version 3.19.9
 */

get_header(); ?>

<div class="portfolio-page-wrapper portfolio-archive page-featured-item">
	<?php get_template_part( 'template-parts/portfolio/archive-portfolio' ); ?>

	<?php while ( have_posts() ) : the_post(); ?>
		<div class="container">
			<?php the_content(); ?>
		</div>
	<?php endwhile; // end of the loop. ?>
</div>

<?php get_footer(); ?>
