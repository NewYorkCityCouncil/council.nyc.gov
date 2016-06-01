<?php

// Archive Titles
add_filter( 'get_the_archive_title', function ($title) {
  if ( is_category() ) {
    $tag_title = single_cat_title( '', false );
    $title = '<small>Tag: </small><em>' . $tag_title . '</em>';
  } elseif ( is_tag() ) {
    $tag_title = single_tag_title( '', false );
    $title = '<small>Tag: </small><em>' . $tag_title . '</em>';
  // } elseif ( is_author() ) {
  //   $title = '<span class="vcard">' . get_the_author() . '</span>' ;
  }
  return $title;
});
