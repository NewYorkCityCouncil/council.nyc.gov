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
            <div class="row">
                <div class="columns small-12">
                    <button id="search-submit" class="button">Apply Filters</button>
                    <button id="search-clear" class="button">Clear Filters</button>
                </div>
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
            <small><em id="num-of-results">Showing <strong><?php echo count($active_job_postings); ?></strong> total results</em></small>
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
                    </tbody>
                </table>
            </div>
            <script>
                <?php
                    $jobAdArray = json_encode($active_job_postings);
                    echo "let jobAdArray = ". $jobAdArray . ";\n";
                ?>
                function viewJob (job) {
                    window.open(`https://council.nyc.gov/jobs/${job}`)
                };
                function resetJobTable(listOfJobs=jobAdArray){
                    jQuery("#job-table tbody").empty();
                    jQuery("#num-of-results").html(`Showing <strong>${listOfJobs.length}</strong> total results`)
                    for (let job of listOfJobs){
                        jQuery("#job-table tbody").append(`
                            <tr class='job-row' onclick='viewJob("${job.post_name}")'>
                                <td>${job.post_title}</td>
                                <td>${job.post_title}</td>
                                <td>${job.post_title}</td>
                            </tr>
                        `);
                    };
                };
                jQuery("#search-submit").on("click", () => {
                    let searchTitle = jQuery("#office-title-search").val().toLowerCase();
                    let searchDept = jQuery("#department-search").val().toLowerCase();
                    let searchSalary = jQuery("#salary-search").val().toLowerCase();
                    let jobAdArrayCopy = [...jobAdArray];
                    if (searchTitle) jobAdArrayCopy = jobAdArrayCopy.filter((job) => job.post_title.toLowerCase().includes(searchTitle));
                    if (searchDept) jobAdArrayCopy = jobAdArrayCopy.filter((job) => job.post_title.toLowerCase().includes(searchDept));
                    if (searchSalary) jobAdArrayCopy = jobAdArrayCopy.filter((job) => job.post_title.toLowerCase().includes(searchSalary));
                    resetJobTable(jobAdArrayCopy);
                });
                jQuery("#search-clear").on("click", () => {
                    jQuery("#office-title-search").val('');
                    jQuery("#department-search").val('');
                    jQuery("#salary-search").val('');
                    resetJobTable();
                })
                resetJobTable();
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
