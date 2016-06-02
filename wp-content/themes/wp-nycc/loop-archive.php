<?php
if ( get_post_type() == 'attachment' ) { ?>

  <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>
    <div class="row">
      <header class="columns large-5">
        <h2 class="header-small"><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
        <?php the_excerpt(); ?>
      </header>
      <section class="post-content columns large-7">
        <img class="thumbnail" src="<?php echo wp_get_attachment_url() ?>">
      </section>
    </div>
  </article>

<?php } else { ?>

  <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>
    <header>
      <h2 class="header-large"><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
    </header>
    <section class="post-content">
      <?php the_excerpt(); ?>
    </section>
  </article>

<?php } ?>
