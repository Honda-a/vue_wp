<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Plate_theme
 */
global $post;
$extra_code = get_field("extra_code", 'theme_settings');
?>

</main>
<footer>
    <div>
    <?php if( have_rows('footer_links', $post->ID) ):?>
        <ul>
        <?php while( have_rows('footer_links', $post->ID) ):the_row(); ?>
            <li>
                <a href="<?php the_sub_field('url');?>">
                    <i class="fas fa-angle-right"></i>
                    <p><?php the_sub_field('title');?></p>
                </a>
            </li>
        <?php endwhile;?>
        </ul>
    <?php endif;?>
        <p class="text">Copyright © All right reserved.</p>
    </div>
</footer>
<?php echo $extra_code["body_end"];?>
</body>
<?php wp_footer(); ?> 

</html>

