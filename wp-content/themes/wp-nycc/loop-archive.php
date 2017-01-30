<?php
if ( get_post_type() == 'attachment' ) {
  ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>

      <?php if ( get_post_mime_type() == 'application/pdf' ) { ?>

        <header>
          <h2 class="header-small"><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
          <p class="byline"><?php the_time('F j, Y') ?></p>
        </header>
        <a class="button small" href="<?php echo wp_get_attachment_url(); ?>" title="<?php the_title_attribute(); ?>">Download&nbsp;<small>(PDF)</small></a>

      <?php } elseif ( in_array(get_post_mime_type(),array('image/jpeg','image/png','image/gif')) ) { ?>

        <div class="row">
          <header class="columns large-5">
            <h2 class="header-small"><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
            <p class="byline"><?php the_time('F j, Y') ?></p>
            <?php the_excerpt(); ?>
          </header>
          <section class="post-content columns large-7">
            <a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>">
              <img class="thumbnail" src="<?php echo wp_get_attachment_url() ?>">
            </a>
          </section>
        </div>

      <?php } else { ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>
          <header>
            <h2 class="header-large"><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
            <p class="byline"><?php the_time('F j, Y') ?></p>
          </header>
          <section class="post-content">
            <?php the_excerpt(); ?>
          </section>
        </article>

      <?php } ?>
    </article>

  <?php

} else {

  ?>
  <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>
    <header>
      <h2 class="header-large"><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
      <p class="byline"><?php the_time('F j, Y') ?></p>
    </header>
    <?php if ( has_post_format( 'video' )) { ?>
    <div class="row">
      <div class="columns large-6 large-push-6">
        <section class="post-content text-small">
          <?php the_excerpt(); ?>
        </section>
      </div>
      <div class="columns large-6 large-pull-6">
        <?php
        $content = apply_filters('the_content', get_post_field('post_content', $post_id));
        $iframes = get_media_embedded_in_content( $content, 'iframe' );
        echo $iframes[0];
        ?>
      </div>
    </div>
    <?php } else { ?>
    <section class="post-content">
      <?php the_excerpt(); ?>
    </section>
    <?php } ?>
  </article>
  <?php
} ?>
