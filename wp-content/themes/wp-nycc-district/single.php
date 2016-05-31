<?php get_header(); ?>

      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>

          <header class="post-header">
            <h1 class="post-title single-title"><?php the_title(); ?></h1>
            <p class="byline"><img class="inline-icon large" src="<?php echo get_blog_option('','council_member_thumbnail') ?>">&nbsp;&nbsp;Council Member <?php echo get_blog_option('','council_member_short_name') ?>&nbsp;<span class="meta-separator">|</span> <?php the_time('F j, Y') ?></p>
          </header>

          <section class="post-content">
            <?php the_content(); ?>
          </section>

          <footer class="post-footer">
            <p>Posted in <?php the_category(', ') ?><?php if( has_tag() ) { ?>&nbsp;<span class="meta-separator">|</span> <?php the_tags('Tagged '); } ?></p>
          </footer>

          <?php if( comments_open() ) { ?>
          <div class="post-comments">
            <?php comments_template(); ?>
          </div>
          <?php } ?>

        </article>

      <?php endwhile; endif; ?>

    </div>

    <?php get_sidebar(); ?>

  </div>

<?php get_footer(); ?>
