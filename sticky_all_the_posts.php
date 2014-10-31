<?php

// add sticky posts support to all the things!
function sticky_all_posts( $posts ) {

    // apply the magic on post archive only
  if ( !is_home() && !is_main_query() ) {
      global $wp_query;

        $sticky_posts = get_option( 'sticky_posts' );
        $num_posts = count( $posts );
        $sticky_offset = 0;

        // loop through the post array and find the sticky post
        for ($i = 0; $i < $num_posts; $i++) {
            // Put sticky posts at the top of the posts array
            if ( in_array( $posts[$i]->ID, $sticky_posts ) ) {
                $sticky_post = $posts[$i];

                // Remove sticky from current position
                array_splice( $posts, $i, 1 );

                // Move to front, after other stickies
                array_splice( $posts, $sticky_offset, 0, array($sticky_post) );
                $sticky_offset++;

            // Remove post from sticky posts array
                $offset = array_search($sticky_post->ID, $sticky_posts);
                unset( $sticky_posts[$offset] );
            }
        }

        // Fetch sticky posts that weren't in the query results
        if ( !empty( $sticky_posts) ) {
            $stickies = get_posts( array(
                'post__in' => $sticky_posts,
                'post_type' => $wp_query->query_vars['post_type'],
                'post_status' => 'publish',
                'nopaging' => true
            ) );
            foreach ( $stickies as $sticky_post ) {
                array_splice( $posts, $sticky_offset, 0, array( $sticky_post ) );
                $sticky_offset++;
            }
        }

        // remove duplicates
        $posts = array_unique($posts, SORT_REGULAR);
  }
  return $posts;
}
add_filter( 'the_posts', 'sticky_all_posts' );

?>