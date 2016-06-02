<?php

// Add tags to media attachments
function nycc_add_tags_to_attachments() {
    register_taxonomy_for_object_type( 'post_tag', 'attachment' );
}
add_action( 'init' , 'nycc_add_tags_to_attachments' );

// Add tagged attachments to queries
function nycc_add_tagged_attachments_to_queries() {
    global $wp_query;
    if ( is_tag() || is_search() ) {
        $wp_query->query_vars['post_type'] =  array( 'post', 'attachment' );
        $wp_query->query_vars['post_status'] =  array( null );
        return $wp_query;
    }
}
add_action('parse_query', 'nycc_add_tagged_attachments_to_queries');
