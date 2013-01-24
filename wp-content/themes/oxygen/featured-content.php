<?php
/**
 * Featured Content (slider)
 *
 */

$args = array( 'posts_per_page' => 6, 'meta_key' => '_oxygen_post_location', 'meta_value' => 'featured', 'post__not_in' => get_option( 'sticky_posts' ) );
$loop = new WP_Query( $args );
		
if ( $loop->have_posts() ) : ?>
		
	<div class="featured-wrapper">
		
		<div id="featured-content">		

			<img class="dummy <?php echo ( $loop->post_count == 1 ) ? 'hidden' : ''; ?>" src="<?php echo get_template_directory_uri() . '/images/empty.gif' ?>" alt="" width="750" height="380" />
	
			<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>

					<div class="featured-post">
	
						<?php if ( current_theme_supports( 'get-the-image' ) ) get_the_image( array( 'meta_key' => 'Thumbnail', 'size' => 'featured-thumbnail' ) ); ?>
						
						<h2 class="post-title entry-title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h2>
						
						<?php echo apply_atomic_shortcode( 'byline', '<div class="byline">' . __( '[entry-published]', 'oxygen' ) . '</div>' ); ?>
					
					</div> <!-- .featured-post -->
					
				<?php endwhile; ?>

			<span id="slider-prev" class="slider-nav">&larr;</span>
			<span id="slider-next" class="slider-nav">&rarr;</span>
			
		</div><!-- #featured-content -->
				
		<div id="slider-nav">
		
			<ul id="slide-thumbs">
				
				<?php $slidecount = 1;
				$args = array( 'meta_key' => '_oxygen_post_location', 'meta_value' => 'featured', 'post__not_in' => get_option( 'sticky_posts' ) );
				
				while ( $loop->have_posts() ) : $loop->the_post(); ?>
						   
					<li class="<?php echo ( $slidecount == 6 ) ? 'last' : ''; ?>">
					
						<?php if ( current_theme_supports( 'get-the-image' ) ) get_the_image( array( 'meta_key' => 'Thumbnail', 'size' => 'slider-nav-thumbnail' ) ); ?>
							
					</li>
				
					<?php $slidecount++; ?>
					 
				<?php endwhile; ?>
					
			</ul>
			
		</div><!-- #slider-nav-->
	   
	</div><!-- .featured-wrapper-->
	
<?php endif; ?>