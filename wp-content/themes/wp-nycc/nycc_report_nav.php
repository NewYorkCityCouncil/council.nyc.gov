<?php
$pagelist = get_pages( 'post_type=nycc_report&sort_column=menu_order&sort_order=asc&child_of=' . $post->post_parent );
$pages = array();
foreach ( $pagelist as $page ) {
   $pages[] += $page->ID;
}
$current = array_search(get_the_ID(), $pages);
$prevID = $pages[$current-1];
$nextID = $pages[$current+1];
?><div class="toc-nav row medium-collapse"><?php

    echo '<div class="columns medium-8 medium-push-2 large-10 large-push-1"><a class="button secondary small expanded" href="' . get_permalink($post->post_parent) . '" title="' . get_the_title($post->post_parent) . '">' . get_the_title($post->post_parent) . '</a></div>';

    ?><div class="columns small-6 medium-2 medium-pull-8 large-1 large-pull-10"><?php
    if (!empty($prevID)) {
        echo '<a class="button secondary small expanded" href="' . get_permalink($prevID) . '" title="' . get_the_title($prevID) . '">Previous</a>';
    }
    ?></div><?php

    ?><div class="columns small-6 medium-2 large-1"><?php
    if (!empty($nextID)) {
        echo '<a class="button secondary small expanded" href="' . get_permalink($nextID) . '" title="' . get_the_title($nextID) . '">Next</a>';
    }
    ?></div><?php

?></div><?php
