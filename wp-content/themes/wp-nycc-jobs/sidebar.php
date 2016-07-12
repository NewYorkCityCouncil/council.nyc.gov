<?php
// Get all the divisions
$division_terms = get_terms( 'job_division' );

// Loop through all the divisions
foreach ( $division_terms as $term ) {
    // Show a list of page links for the division
    $divisions_query = new WP_Query( array(
        'post_type' => 'page',
        'tax_query' => array(
            array(
                'taxonomy' => 'job_division',
                'field' => 'slug',
                'terms' => array( $term->slug ),
                'operator' => 'IN'
            )
        )
    ) );
    ?>
    <br>
    <h2 class="header-small"><?php echo $term->name; ?></h2>
    <ul>
      <?php
      if ( $divisions_query->have_posts() ) : while ( $divisions_query->have_posts() ) : $divisions_query->the_post(); ?>
        <li><strong><a href="<?php echo the_permalink(); ?>"><?php echo the_title(); ?></a></strong></li>
      <?php endwhile; endif; ?>
    </ul>
    <?php
    // Reset the query (for good measure)
    $divisions_query = null;
    wp_reset_postdata();
}
?>
