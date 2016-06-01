<?php

// Archive Titles
add_filter( 'get_the_archive_title', function ($title) {
  if ( is_category() ) {
    $cat_title = single_cat_title( '', false );
    $title = '<small>Tag: </small><em>' . $cat_title . '</em>';
  } elseif ( is_tag() ) {
    $tag_title = single_tag_title( '', false );
    $title = '<small>Tag: </small><em>' . $tag_title . '</em>';
  } elseif ( is_day() ) {
    $day_title = get_the_time('F d, Y');
    $title = '<small>Day: </small><em>' . $day_title . '</em>';
  } elseif ( is_month() ) {
    $month_title = single_month_title( ' ', false );
    $title = '<small>Month: </small><em>' . $month_title . '</em>';
  } elseif ( is_year() ) {
    $year_title = get_the_time('Y');
    $title = '<small>Year: </small><em>' . $year_title . '</em>';
  // } elseif ( is_author() ) {
  //   $title = '<span class="vcard">' . get_the_author() . '</span>' ;
  // } elseif ( is_taxonomy() ) {
  //   $title = wp_title('',false) ;
  }
  return $title;
});
