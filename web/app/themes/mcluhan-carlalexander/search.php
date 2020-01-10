<?php get_header(); ?>

<div class="section-inner">

    <?php if ( ! have_posts() ) : ?>

        <div class="section-inner">

            <header class="page-header">
                <h4 class="page-subtitle"><?php _e( 'Search Results', 'mcluhan' ); ?></h4>
                <h2 class="page-title"><?php echo '&ldquo;' . get_search_query() . '&rdquo;'; ?></h2>
                <?php /* Translators: %s = the search query */ ?>
                <p><?php printf( _x( 'We could not find any results for the search query "%s". You can try again through the form below.', 'Translators: %s = the search query', 'mcluhan' ), get_search_query() ); ?></p>
            </header>

            <?php get_search_form(); ?>

        </div>

    <?php else: ?>

        <header class="page-header">
            <div>
                <h4 class="page-subtitle"><?php _e( 'Search Results', 'mcluhan' ); ?></h4>
                <h2 class="page-title"><?php echo '&ldquo;' . get_search_query() . '&rdquo;'; ?></h2>
                <?php /* Translators: %s = the number of search results */ ?>
                <p><?php printf( _x( 'We found %s matching your search query.', 'Translators: %s = the number of search results', 'mcluhan' ), $wp_query->found_posts . ' ' . ( 1 == $wp_query->found_posts ? __( 'result', 'mcluhan' ) : __( 'results', 'mcluhan' ) ) ); ?></p>
            </div>
        </header>

        <div class="posts" id="posts">
            <ul>
            <?php
            while ( have_posts() ) : the_post();
                get_template_part( 'content', get_post_type() );
            endwhile;
            ?>
            </ul>
        </div><!-- .posts -->

    <?php endif; ?>

</div><!-- .section-inner -->

<?php

get_template_part( 'pagination' );

get_footer(); ?>
