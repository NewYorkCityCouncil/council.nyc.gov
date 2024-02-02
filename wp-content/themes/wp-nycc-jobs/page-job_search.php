<?php /* Template Name: Job Page w/ Search */ ?>
<?php get_header(); ?>

    <div class="row">
        <div class="columns small-12">
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(''); ?>>
                    <div class="page-header">
                        <h1 class="header-xxlarge"><?php the_title(); ?></h1>
                        <?php if ( has_excerpt( $post->ID ) ) { ?><p class="header-medium subheader sans-serif"><?php echo get_the_excerpt(); ?></p><?php } ?>
                        <hr>
                    </div>

                    <section class="page-content">
                    <?php the_content(); ?>
                    </section>
                 </article>
            <?php endwhile; endif; ?>
            <div class="row">
                <div class="columns medium-12 large-4">
                    <input placeholder="Office title" type="text" id="office-title-search">
                </div>
                <div class="columns medium-12 large-4">
                    <input placeholder="Department" type="text" id="department-search">
                </div>
                <div class="columns medium-12 large-4">
                    <input placeholder="Salary range" type="text" id="salary-search">
                </div>
            </div>
            <div class="row align-right">
                <div class="columns small-2">
                    <button>Apply Filters</button>
                </div>
            </div>
            <?php 
                $args = array(
                    'post__not_in' => array(2,859),
                    'posts_per_page' => '-1',
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'order'      => 'ASC',
                    'meta_query'     => array(
                        array(
                            'key'     => '_wp_page_template',
                            'value'   => 'front-page.php',
                            'compare' => '!=',
                        ),
                        array(
                            'key'     => '_wp_page_template',
                            'value'   => 'page-job_search.php',
                            'compare' => '!=',
                        ),
                    ),
                );
                $active_job_postings = get_pages($args);
            ?>
            <small><em>Showing <?php echo count($active_job_postings); ?> total results</em></small>
            <div id="job-table-container">
                <table id="job-table">
                    <thead>
                        <tr>
                            <th>Office Title</th>
                            <th>Department</th>
                            <th>Salary Range</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach ( $active_job_postings as $active_job_posting) {
                                $job_ad_url = get_page_link($active_job_posting->ID);
                                $job_title = $active_job_posting->post_title;
                                $job_url = esc_url($job_ad_url);
                                echo "<tr class='job-row' onclick='viewJob(`$job_url`)'>
                                    <td>$job_title</td>
                                    <td>$job_title</td>
                                    <td>$job_title</td>
                                </tr>";
                            };
                        ?>
                    </tbody>
                </table>
            </div>
            <script>
                function viewJob (job) {
                    window.open(`${job}`)
                };
            </script>
        </div>
    </div>
    <style>
        #job-table-container{ border: 1px solid grey; height: 350px; margin-bottom: 25px; overflow: auto; }
        #job-table thead{ position: sticky; top: -1px; }
        .job-row{ cursor: pointer; }
        .job-row:hover{ background-color: #23417D; color: white; }
    </style>
<?php get_footer(); ?>
