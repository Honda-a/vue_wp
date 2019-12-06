     <?php
     /**
      * 
      * Template Name: 記事テンプレート
      */
     
     
     
     get_header();$theme_dir = get_template_directory_uri();
     if( have_posts() ):
      while( have_posts() ):
        the_post();
        $messages = get_field("message");
        $related_posts = get_field("related_post__group");
        $banner = get_field("banner");
        $writer = get_field("writer");
?>
		  <div class="content">
            <div class="title">
              <p class="sub"><?php the_field("sub_title");?></p>
              <h2><?php the_title();?></h2>
              <p class="day">
                最新記事　　
                <?php 
                switch( get_field("date_view") ){
                  case 'today':
                    echo date("Y年m月d日");
                    break;
                  case 'manual':
                    echo get_the_date("Y年m月d日");
                    break;
                  case 'none':
                    echo "";
                    break;
                }
                
                ?>
            
              </p>
            </div>
            <?php the_field("content");?>
            <p class="messe"><?php echo $messages["banner"];?></p>
          </div>
          <?php if( have_rows("comments") && get_field("comment_show") == "true" ):?>
          <div class="coment">
            <h3>この記事への皆さんのコメント</h3>
              <ul>
            <?php while( have_rows("comments") ): the_row();
            $image = get_sub_field("img");
            ?>
                <li>
                  <p class="name"><?php the_sub_field("author");?></p>
                  <p class="text"><?php the_sub_field("title");?></p>
                  <div class="img"><img src="<?php echo $image["url"];?>"></div>
                  <p class="messe"><?php the_sub_field("comment");?></p>
                  <p class="messe"><?php echo $messages["comment"];?></p>
                </li>
                <?php endwhile;?>
              </ul>
            </div>
            <?php endif;?>
          <div class="writer">
            <h3>ライター情報</h3>
            <ul>
              <li>
                <img src="<?php echo $writer["img"]["url"];?>">
                <p><?php echo $writer["name"];?></p>
                <p><?php echo $writer["description"];?></p>
              </li>
            </ul>
          </div>
          <div class="list">
            <div class="img">
              <a href="<?php echo $banner["url"];?>" <?php echo $banner["js"]?> >
                <img src="<?php echo $banner["img"]["url"];?>">
              </a>
            </div>
            <p class="messe"><?php echo $messages["content"];?></p>
            <?php $headline = ( isset($related_posts["headline"]) && !empty($related_posts["headline"]) )? $related_posts["headline"]: "";  ?>
            <h3><?php echo $headline; ?></h3>
            <?php if( isset($related_posts["related_post"]) && !empty($related_posts["related_post"]) ):?>
              <ul>
                <?php foreach( $related_posts["related_post"] as $post ):
                  $image = ( isset($post["img"]) && !empty($post["img"]) )?wp_get_attachment_image_src($post["img"]):"";
                  ?>

                  <li>
                    <a href="<?php echo $post["url"];?>" <?php echo $post["js"];?> >
                    <img src="<?php echo ( isset($image[0]) )?$image[0]:"";?>">
                      <?php echo $post["title"];?>
                    </a>
                  </li>

                <?php endforeach;?>
              </ul> 
            <?php endif;?>
          </div>
        </div>
<?php 
  endwhile;
  wp_reset_query();
endif;
get_footer();?>
