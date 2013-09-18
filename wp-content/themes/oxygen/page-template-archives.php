<?php
/**
 * Template Name: Archives
 *
 * A custom page template for displaying blog archives.
 *
 * @package Oxygen
 * @subpackage Template
 */

get_header(); // Loads the header.php template. ?>

	<div class="aside">
	
		<?php get_template_part( 'menu', 'secondary' ); // Loads the menu-secondary.php template.  ?>
		
		<?php get_sidebar( 'primary' ); // Loads the sidebar-primary.php template. ?>
	
	</div>

	<?php do_atomic( 'before_content' ); // oxygen_before_content ?>
	
	<div class="content-wrap">

		<div id="content">	

			<?php do_atomic( 'open_content' ); // oxygen_open_content ?>
	
			<div class="hfeed">
	
				<?php if ( have_posts() ) : ?>
	
					<?php while ( have_posts() ) : the_post(); ?>
	
						<?php do_atomic( 'before_entry' ); // oxygen_before_entry ?>
	
						<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">
	
							<?php do_atomic( 'open_entry' ); // oxygen_open_entry ?>
	
							<?php echo apply_atomic_shortcode( 'entry_title', '[entry-title permalink="0"]' ); ?>
	
							<div class="entry-content">
								
								<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'oxygen' ) ); ?>
	
								<h2><?php _e( 'Archives by category', 'oxygen' ); ?></h2>
	
								<ul class="xoxo category-archives">
									
									<?php wp_list_categories( array( 'feed' => __( 'RSS', 'oxygen' ), 'show_count' => true, 'use_desc_for_title' => false, 'title_li' => false ) ); ?>
									
								</ul><!-- .xoxo .category-archives -->
	
								<h2><?php _e( 'Archives by month', 'oxygen' ); ?></h2>
	
								<ul class="xoxo monthly-archives">
									
									<?php wp_get_archives( array( 'show_post_count' => true, 'type' => 'monthly' ) ); ?>
									
								</ul><!-- .xoxo .monthly-archives -->
	
								<?php wp_link_pages( array( 'before' => '<p class="page-links">' . __( 'Pages:', 'oxygen' ), 'after' => '</p>' ) ); ?>
								
							</div><!-- .entry-content -->
	
							<?php echo apply_atomic_shortcode( 'entry_meta', '<div class="entry-meta">[entry-edit-link]</div>' ); ?>
	
							<?php do_atomic( 'close_entry' ); // oxygen_close_entry ?>
	
						</div><!-- .hentry -->
	
						<?php do_atomic( 'after_entry' ); // oxygen_after_entry ?>
	
						<?php do_atomic( 'after_singular' ); // oxygen_after_singular ?>
	
					<?php endwhile; ?>
	
				<?php endif; ?>
	
			</div><!-- .hfeed -->
	
			<?php do_atomic( 'close_content' ); // oxygen_close_content ?>
	
		</div><!-- #content -->
	
		<?php do_atomic( 'after_content' ); // oxygen_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>