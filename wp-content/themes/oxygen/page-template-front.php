<?php
/**
 * Template Name: Front Page
 *
 * Magazine layout with several loop areas and a featured content area (slider). This template must be manually set for a page. Then the same page can be set as a Front page from Settings -> Reading -> Front page displays -> A static page.
 *
 * @package Oxygen
 * @subpackage Template
 */

get_header(); // Loads the header.php template. ?>

	<?php get_template_part( 'featured-content' ); // Loads the featured-content.php template. ?>
	
	<div class="aside">
	
		<?php get_template_part( 'menu', 'secondary' ); // Loads the menu-secondary.php template.  ?>
		
		<?php get_sidebar( 'primary' ); // Loads the sidebar-primary.php template. ?>
	
	</div>

	<?php do_atomic( 'before_content' ); // oxygen_before_content ?>
	
	<div class="content-wrap">

		<div id="content">		
	
			<?php do_atomic( 'open_content' ); // oxygen_open_content ?>
	
			<div class="hfeed">
				
				<h4 class="section-title"><?php _e( 'Recent Articles', 'oxygen' ); ?></h4>
				
				<?php $args = array( 'post__not_in' => get_option( 'sticky_posts' ), 'posts_per_page' => 3, 'meta_key' => '_oxygen_post_location', 'meta_value' => 'primary' ); ?>
				
				<?php $loop = new WP_Query( $args ); ?>
				
				<?php if ( $loop->have_posts() ) : ?>
				
					<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
	
						<?php do_atomic( 'before_entry' ); // oxygen_before_entry ?>
	
							<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">
	
								<?php do_atomic( 'open_entry' ); // oxygen_open_entry ?>
	
								<?php if ( current_theme_supports( 'get-the-image' ) ) {
											
									get_the_image( array( 'meta_key' => 'Thumbnail', 'size' => 'archive-thumbnail', 'image_class' => 'featured', 'width' => 470, 'height' => 140, 'default_image' => get_template_directory_uri() . '/images/archive-thumbnail-placeholder.gif' ) );							
									
								} ?>
								
								<div class="entry-header">
										
									<h2 class="post-title entry-title">
										<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
									</h2>
									
									<?php echo apply_atomic_shortcode( 'byline_date', '<div class="byline byline-date">' . __( '[entry-published]', 'oxygen' ) . '</div>' ); ?>
				
									<?php echo apply_atomic_shortcode( 'byline_author', '<div class="byline byline-author">' . __( 'by [entry-author]', 'oxygen' ) . '</div>' ); ?>
				
									<?php echo apply_atomic_shortcode( 'byline_edit', '<div class="byline byline-edit">' . __( '[entry-edit-link]', 'oxygen' ) . '</div>' ); ?>
			
								</div>
								
								<?php echo apply_atomic_shortcode( 'byline_category', '<div class="byline byline-cat">' . __( '[entry-terms taxonomy="category" before=""]', 'oxygen' ) . '</div>' ); ?>
								
								<div class="entry-summary">
									
									<?php the_excerpt(); ?>
									
									<?php wp_link_pages( array( 'before' => '<p class="page-links">' . __( 'Pages:', 'oxygen' ), 'after' => '</p>' ) ); ?>
									
								</div>
									
								<a class="read-more" href="<?php the_permalink(); ?>"><?php _e( 'Read Article', 'oxygen' ); ?> &rarr;</a>
	
								<?php do_atomic( 'close_entry' ); // oxygen_close_entry ?>
	
							</div><!-- .hentry -->
	
						<?php do_atomic( 'after_entry' ); // oxygen_after_entry ?>
	
					<?php endwhile; ?>			
	
				<?php else : ?>
	
					<?php get_template_part( 'loop-error' ); // Loads the loop-error.php template. ?>
	
				<?php endif; ?>
				
				<h4 class="section-title"><?php _e( 'More Articles', 'oxygen' ); ?></h4>
				
				<div class="hfeed-more">				
					
					<?php $args = array( 'post__not_in' => get_option( 'sticky_posts' ), 'posts_per_page' => 12, 'meta_key' => '_oxygen_post_location', 'meta_value' => 'secondary' ); ?>
					
					<?php $loop = new WP_Query( $args ); ?>
					
					<?php if ( $loop->have_posts() ) : ?>
					
						<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
		
							<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">
										
								<h2 class="post-title entry-title">
									<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
								</h2>
									
								<?php echo apply_atomic_shortcode( 'byline', '<div class="byline">' . __( '[entry-published] / by [entry-author] / in [entry-terms taxonomy="category"] [entry-edit-link before=" / "]', 'oxygen' ) . '</div>' ); ?>
	
							</div><!-- .hentry -->
		
						<?php endwhile; ?>			
		
					<?php else : ?>
		
						<?php get_template_part( 'loop-error' ); // Loads the loop-error.php template. ?>
		
					<?php endif; ?>				
		
				</div><!-- .hfeed-more -->
			
			</div><!-- .hfeed -->
	
			<?php do_atomic( 'close_content' ); // oxygen_close_content ?>
	
		</div><!-- #content -->
	
		<?php do_atomic( 'after_content' ); // oxygen_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>