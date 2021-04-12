<?php get_header(); ?>

    <?php query_posts('posts_per_page=3'); ?>
    <?php if (have_posts()) : ?>
        <div class="section-inner">
            <div class="posts" id="posts">
                <ul>
                    <li><h2 class="page-subtitle">Recent Articles</h2></li>
                    <?php while (have_posts()) : the_post(); ?>
                        <?php $isLink = has_post_format('link'); ?>
                        <li <?php post_class('post-preview'); ?> id="post-<?php the_ID(); ?>">
                            <a href="<?= $isLink ? mcluhan_get_first_url() : get_the_permalink(); ?>" title="<?php the_title_attribute($title_args); ?>">
                                <?php if ($isLink) : ?>
                                    <img src="<?= get_stylesheet_directory_uri().'/assets/images/external.svg' ?>" alt="External link" title="External link" />
                                <?php endif; ?>
                                <?php the_title('<h2 class="title"><span>', '</span></h2>'); ?>
                            </a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
            <div class="archive-pagination section-inner group">
                <div class="next-posts-link">
                    <h4 class="title"><a href="<?= get_permalink(get_option('page_for_posts')); ?>">All Articles</a></h4>
                </div>
            </div>
            <hr />
        </div>
    <?php endif; ?>
    <?php wp_reset_query(); ?>

    <article <?php post_class(); ?>>
        <header class="entry-header section-inner">
            <?php the_title( '<h1 class="entry-title">', '</h2>' ); ?>
        </header><!-- .entry-header -->
        <div class="entry-content section-inner">
            <?php the_content(); ?>
        </div> <!-- .content -->
    </article>
<?php get_footer(); ?>
