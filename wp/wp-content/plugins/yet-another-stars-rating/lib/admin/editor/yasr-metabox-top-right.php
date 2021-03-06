<?php

/*

Copyright 2014 Dario Curvino (email : d.curvino@tiscali.it)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>
*/

    if ( ! defined( 'ABSPATH' ) ) exit('You\'re not allowed to see this page'); // Exit if accessed directly

    $post_id=get_the_ID();

	$overall_rating=yasr_get_overall_rating($post_id);

	if (!$overall_rating) {
		$overall_rating = "0";
	}

    $ajax_nonce_overall = wp_nonce_field( "yasr_nonce_overall_rating_action", "yasr_nonce_overall_rating");
    $ajax_nonce_review_type = wp_nonce_field( "yasr_nonce_review_type_action", "yasr_nonce_review_type" );

?>


<div id="yasr-matabox-top-right">

    <input type="hidden" name="yasr_overall_rating" id="yasr-overall-rating-value" value="<?php echo $overall_rating?>">

        <div id="yasr-vote-overall-stars-container">

            <div id="yasr-vote-overall-stars">
                <span id="yasr-rateit-vote-overall-text">
                    <?php _e("Rate this article / item", 'yet-another-stars-rating'); ?>
                </span>

                <div id="yasr-rater-overall" >
                </div>

                </p>

                <div>
                    <span id="yasr_rateit_overall_value"></span>
                </div>

            </div>

        </div> <!--End stars container-->

    <hr>

    <?php

        if (YASR_AUTO_INSERT_ENABLED == 1) {

        $is_this_post_exluded = get_post_meta($post_id, 'yasr_auto_insert_disabled', TRUE);

        ?>

        <div id="yasr-toprightmetabox-disable-auto-insert">

            <?php _e('Disable auto insert for this post or page?', 'yet-another-stars-rating'); ?>

                <br />

                <div class="yasr-onoffswitch-big" id="yasr-switcher-disable-auto-insert">
                    <input type="checkbox" name="yasr_auto_insert_disabled" class="yasr-onoffswitch-checkbox" value="yes" id="yasr-auto-insert-disabled-switch" <?php if ($is_this_post_exluded === 'yes') echo " checked='checked' "; ?> >
                    <label class="yasr-onoffswitch-label" for="yasr-auto-insert-disabled-switch">
                        <span class="yasr-onoffswitch-inner"></span>
                        <span class="yasr-onoffswitch-switch"></span>
                    </label>
                </div>


        </div>

        <hr>

        <?php

    } //End if auto insert enabled

    ?>

    <div class="yasr-choose-reviews-types"><?php _e("This review is about a...", "yet-another-stars-rating"); ?>
        <br />

        <?php yasr_select_itemtype(); ?>

    </div>

    <p>

    <div>

        <?php

            //Show this message if auto insert is off or if auto insert is not set to show overall rating (so if it is set to visitor rating)
            if( YASR_AUTO_INSERT_ENABLED == 0 || (YASR_AUTO_INSERT_ENABLED == 1 && YASR_AUTO_INSERT_WHAT === 'visitor_rating') ) {

                echo "<div>";
                  _e ("Remember to insert this shortcode <strong>[yasr_overall_rating]</strong> where you want to display this rating", 'yet-another-stars-rating');
                echo "</div>";

            }

        ?>

    </div>

    <?php

        do_action( 'yasr_add_content_bottom_topright_metabox', $post_id );

    ?>

</div>

<script type="text/javascript">

    jQuery(document).ready(function() {

        var postid = <?php json_encode(the_ID()); ?>;

        var overallRating = <?php echo (json_encode($overall_rating)); ?>;

        yasrPrintMetaBoxOverall (postid, overallRating);

    }); //End document ready

</script>
