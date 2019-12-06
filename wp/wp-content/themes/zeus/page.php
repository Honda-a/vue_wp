<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Plate_theme
 */

get_header();
?>

		<?php
		while ( have_posts() ) :
			the_post();?>
					  <div class="content">
            <div class="title">
              <p class="sub"><?php the_field("sub_title");?></p>
              <h2><?php the_title();?></h2>
            </div>
			<?php the_content();?>

			
			</div>
		<?php endwhile; // End of the loop.
		?>

<?php
get_footer();
