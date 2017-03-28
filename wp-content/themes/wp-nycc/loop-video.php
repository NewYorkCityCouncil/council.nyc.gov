<?php

$grid_columns = 2;

if ( 0 === ( $wp_query->current_post  )  % $grid_columns ) { ?>
<div class="row">
<?php } ?>

  <article style="margin:0 0 2rem;padding-top:0;border:0;" id="post-<?php the_ID(); ?>" <?php post_class('columns large-6'); ?>>
    <?php
    $content = apply_filters('the_content', get_post_field('post_content', $post->ID));
    $iframes = get_media_embedded_in_content( $content, 'iframe' );
    echo $iframes[0];
    ?>
    <p class="byline" style="margin:-0.5rem 0 0;"><?php the_time('F j, Y') ?></p>
    <h2 class="header-tiny"><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
  </article>



<?php if ( 0 === ( $wp_query->current_post + 1 )  % $grid_columns ||  ( $wp_query->current_post + 1 ) ===  $wp_query->post_count ) { ?>
</div>
<?php } ?>
