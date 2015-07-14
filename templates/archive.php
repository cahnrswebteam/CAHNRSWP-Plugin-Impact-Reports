<?php get_header(); ?>

<main class="spine-archive-index">

	<?php get_template_part('parts/headers'); ?>

	<section class="row side-right gutter pad-ends">

		<div class="column one">

			<?php echo wpautop( wp_kses_post( get_option( 'impact_report_archive_blurb' ) ) ); ?>

			<?php
				// Modify loop. Probably not the best way to do it... maybe a wp_query instead
				global $query_string;
				query_posts( $query_string . '&meta_key=_impact_report_visibility&meta_value=display' );
			?>

			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

					<header class="article-header">
						<hgroup>
							<h2 class="article-title">
								<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
							</h2>
						</hgroup>
						<hgroup class="source">
							<time class="article-date" datetime="<?php echo get_the_date( 'c' ); ?>"><?php echo get_the_date(); ?></time> | <time><?php the_modified_date(); ?></time>
						</hgroup>
					</header>

					<div class="article-summary">
						<?php
							$summary = get_post_meta( get_the_ID(), '_ir_summary', true );
							if ( $summary ) {
								echo wpautop( wp_kses_post( $summary ) );
							}
							echo $post->post_name;
						?>
					</div>

				</article>

			<?php endwhile; // end of the loop. ?>

		</div><!--/column-->

		<div class="column two">

			<h4>Browse by Program</h4>

      <?php
				$programs = get_terms( 'programs' );
				$program_count = count( $programs );

				echo '<ul>';
				foreach ( $programs as $program ) {

					$program = sanitize_term( $program, 'programs' );
    			$program_link = get_term_link( $program, 'programs' );

					//echo '<li><a href="' . esc_url( $program_link ) . '">' . $program->name . '</a> (' . $program->count . ')</li>';
					echo '<li><a href="' . esc_url( $program_link ) . '">' . $program->name . '</a></li>';

				}
				echo '</ul>';
			?>
	
			<h4>Browse by Location</h4>

      <?php
				$locations = get_terms( 'locations' );
				$location_count = count( $locations );

				echo '<ul>';
				foreach ( $locations as $location ) {

					$location = sanitize_term( $location, 'locations' );
    			$location_link = get_term_link( $location, 'locations' );

					//echo '<li><a href="' . esc_url( $location_link ) . '">' . $location->name . '</a> (' . $location->count . ')</li>';
					echo '<li><a href="' . esc_url( $location_link ) . '">' . $location->name . '</a></li>';

				}
				echo '</ul>';
			?>

		</div><!--/column two-->

	</section>
 
<?php
/* @type WP_Query $wp_query */
global $wp_query;

$big = 99164;
$args = array(
	'base'         => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
	'format'       => 'page/%#%',
	'total'        => $wp_query->max_num_pages, // Provide the number of pages this query expects to fill.
	'current'      => max( 1, get_query_var('paged') ), // Provide either 1 or the page number we're on.
);
?>
	<footer class="main-footer archive-footer">
		<section class="row side-right pager prevnext gutter">
			<div class="column one">
				<?php echo paginate_links( $args ); ?>
			</div>
			<div class="column two">
				<!-- intentionally empty -->
			</div>
		</section><!--pager-->
	</footer>

	<?php get_template_part( 'parts/footers' ); ?>

</main>

<?php

get_footer();