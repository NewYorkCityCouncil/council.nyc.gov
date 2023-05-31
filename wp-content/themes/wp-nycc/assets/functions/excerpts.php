<?php

function nycc_excerpt_allowedtags() {
  return '<p>,<br>,<a>,<strong>,<em>,<small>,<ul>,<ol>,<li>';
}

if ( ! function_exists( 'nycc_excerpt_custom_wp_trim_excerpt' ) ) :

    function nycc_excerpt_custom_wp_trim_excerpt($nycc_excerpt) {

        global $post;
        $raw_excerpt = $nycc_excerpt;

        if ( '' == $nycc_excerpt ) {

            $nycc_excerpt = get_the_content('');
            $nycc_excerpt = strip_shortcodes( $nycc_excerpt );
            $nycc_excerpt = apply_filters('the_content', $nycc_excerpt);
            $nycc_excerpt = str_replace(']]>', ']]&gt;', $nycc_excerpt);
            $nycc_excerpt = strip_tags($nycc_excerpt, nycc_excerpt_allowedtags());
            $nycc_excerpt = preg_replace('/class=".*?"/', '', $nycc_excerpt);
            $nycc_excerpt = preg_replace('/style=".*?"/', '', $nycc_excerpt);

            //Set the excerpt word count and only break after sentence is complete.
            if ( is_page_template( 'page-district.php' ) || is_page_template( 'page-speakerdistrict.php' ) || is_page() ) {
              $excerpt_word_count = 200;
            } else {
              $excerpt_word_count = 60;
            }
            $excerpt_length = apply_filters('excerpt_length', $excerpt_word_count); // TODO: Can this line be nixed?
            $tokens = array();
            $excerptOutput = '';
            $count = 0;

            // Divide the string into tokens; HTML tags, or words, followed by any whitespace
            preg_match_all('/(<[^>]+>|[^<>\s]+)\s*/u', $nycc_excerpt, $tokens);

            foreach ($tokens[0] as $token) {

                if ($count >= $excerpt_word_count && preg_match('/[\;\?\.\!]\s*$/uS', $token)) {
                    // Limit reached, continue until ; ? . or ! occur at the end
                    $excerptOutput .= trim($token);
                    $excerpt_is_full_length = false; // TODO: This is wrongly set if the max is hit in the last sentence.
                    break;
                }

                // Add words to complete sentence
                $count++;

                // Append what's left of the token
                $excerptOutput .= $token;

                $excerpt_is_full_length = true;
            }

            $nycc_excerpt = trim(force_balance_tags($excerptOutput));

            $excerpt_end = '<br><br>...<br><br><strong><small><a href="'. esc_url( get_permalink() ) . '">READ MORE</a></small></strong>';
            $excerpt_more = apply_filters('excerpt_more', ' ' . $excerpt_end); // TODO: Can this line be nixed?

            $pos = strrpos($nycc_excerpt, '</');
            if ( $excerpt_is_full_length ) {
              // Don't add the read more link
            } else {
              if ($pos !== false) {
                  // Inside last HTML tag
                  $nycc_excerpt = substr_replace($nycc_excerpt, $excerpt_end, $pos, 0); /* Add read more next to last word */
              } else {
                  // After the content
                  $nycc_excerpt .= $excerpt_end; /* Add read more in new paragraph */
              }
            }

            return $nycc_excerpt;

        }

        return apply_filters('nycc_excerpt_custom_wp_trim_excerpt', $nycc_excerpt, $raw_excerpt);

    }

endif;

remove_filter('get_the_excerpt', 'wp_trim_excerpt');
add_filter('get_the_excerpt', 'nycc_excerpt_custom_wp_trim_excerpt');
