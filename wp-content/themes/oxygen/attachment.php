<?php
/**
 * Attachment Template
 *
 * This is the default attachment template.  It is used when visiting the singular view of a post attachment 
 * page (images, videos, audio, etc.).
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
	
							<div class="entry-content">
								<?php if ( wp_attachment_is_image( get_the_ID() ) ) : ?>
	
									<p class="attachment-image">
										<?php echo wp_get_attachment_image( get_the_ID(), 'full', false, array( 'class' => 'alignleft' ) ); ?>
									</p><!-- .attachment-image -->
	
								<?php else : ?>
	
									<?php hybrid_attachment(); // Function for handling non-image attachments. ?>
	
									<p class="download">
										<a href="<?php echo wp_get_attachment_url(); ?>" title="<?php the_title_attribute(); ?>" rel="enclosure" type="<?php echo get_post_mime_type(); ?>"><?php printf( __( 'Download &quot;%1$s&quot;', 'oxygen' ), the_title( '<span class="fn">', '</span>', false) ); ?></a>
									</p><!-- .download -->
	
								<?php endif; ?>
	
								<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'oxygen' ) ); ?>
								<?php wp_link_pages( array( 'before' => '<p class="page-links">' . __( 'Pages:', 'oxygen' ), 'after' => '</p>' ) ); ?>
							</div><!-- .entry-content -->
	
							<?php if ( wp_attachment_is_image( get_the_ID() ) ) echo do_shortcode( sprintf( '[gallery id="%1$s" exclude="%2$s" columns="8"]', $post->post_parent, get_the_ID() ) ); ?>
	
							<?php do_atomic( 'close_entry' ); // oxygen_close_entry ?>
	
						</div><!-- .hentry -->
	
						<?php do_atomic( 'after_entry' ); // oxygen_after_entry ?>
	
						<?php get_sidebar( 'after-singular' ); // Loads the sidebar-after-singular.php template. ?>
	
						<?php do_atomic( 'after_singular' ); // oxygen_after_singular ?>
	
						<?php comments_template( '/comments.php', true ); // Loads the comments.php template. ?>
	
					<?php endwhile; ?>
	
				<?php endif; ?>
	
			</div><!-- .hfeed -->
	
			<?php do_atomic( 'close_content' ); // oxygen_close_content ?>
	
			<?php get_template_part( 'loop-nav' ); // Loads the loop-nav.php template. ?>
	
		</div><!-- #content -->
	
		<?php do_atomic( 'after_content' ); // oxygen_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>