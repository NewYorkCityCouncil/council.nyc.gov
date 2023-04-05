<div id="featured-section">
    <div class="row">
        <div class="column">
            <h2>
                NEWS &amp; UPDATES
                <hr style="border-bottom: 1px solid #58595B; margin: 30px 0;"/>
            </h2>
        </div>
    </div>
    <div class="row small-up-1 medium-up-2 large-up-3">
        <?php 
            $args=array(
                'post_type'     => 'nycc_feature',
                'post_status'   => 'publish',
                'numberposts'   => 6,
                'orderby'       => "publish_date",
                'order'         => 'DESC'
            );
            $posts = new WP_Query($args);
            if ( $posts->have_posts() ) : while ( $posts->have_posts() ) : $posts->the_post();
        ?>
                <div class="column column-block">
                    <a href="<?php the_permalink(); ?>">
                        <div class="featured-content-img" style="background-image: url('<?php the_post_thumbnail_url(); ?>')"></div>
                    </a>
                    <h3 class="featured-content-header"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <p class="featured-content-text">
                        <small><strong><?php the_date(); ?></strong></small>
                        <?php 
                            the_excerpt();
                            if( has_excerpt() ){
                        ?>
                            <p><strong><small><a href="<?php the_permalink(); ?>">READ MORE</a></small></strong></p>
                        <?php
                           }
                        ?>
                    </p>
                </div>
        <?php 
            endwhile; wp_reset_postdata(); else :
        ?>
            <!-- What to do if there a no feature content -->
            <div class="column column-block">Oops</div>
        <?php
            endif; 
        ?>
    </div>
    <div class="row" id="past-featured-container">
        <div class="columns">
            <a href="/past-featured-content/" rel="noopener noreferrer" style="display:block;">
                <strong>
                    <i class="fa fa-play hearing-links-arrow"></i> View past featured content
                </strong>
            </a>
        </div>
    </div>
</div>