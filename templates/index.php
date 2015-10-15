<?php get_header(); ?>

<main class="spine-archive-index">

	<?php get_template_part('parts/headers'); ?>

	<section class="row single gutter pad-ends">

		<div class="column one">

			<?php echo wpautop( wp_kses_post( get_option( 'impact_report_archive_text' ) ) ); ?>

		</div><!--/column-->

	</section>

	<section class="row side-right gutter pad-ends full-bleed gray-darker-back gray-er-text">

		<div class="column one">

			<div id="impact-reports" class="clearfix">

			<?php
				// Modify loop. Probably not the best way to do it... maybe a wp_query instead
				// check https://codex.wordpress.org/Function_Reference/get_next_posts_link if doing so
				global $query_string;
				query_posts( $query_string . '&posts_per_page=12&meta_key=_impact_report_visibility&meta_value=display' );
				while ( have_posts() ) : the_post();
					load_template( dirname( __FILE__ ) . '/post.php', false );
				endwhile;
			?>

			</div>

			<?php
				$max = $wp_query->max_num_pages;
 				$paged = ( get_query_var('paged') > 1 ) ? get_query_var('paged') : 1;
			?>

			<?php if ( get_next_posts_link() ) : ?> 
				<div class="more-button center white" id="load-more-impact-reports">
      		<a href="<?php next_posts(); ?>" data-page="<?php echo $paged; ?>" data-max="<?php echo $max; ?>" data-loaded="1">More</a>
				</div>
			<?php endif; ?>

		</div>
    
    <div class="column two">

			<form role="search" method="get" class="cahnrs-search" action="<?php echo home_url( '/' ); ?>">
				<input type="hidden" name="post_type" value="impact">
				<label>
					<span class="screen-reader-text">Search Impact Reports for:</span>
					<input type="search" class="cahnrs-search-field" placeholder="Search Impact Reports" value="<?php echo get_search_query(); ?>" name="s" title="Search Impact Reports for:" />
				</label>
				<input type="submit" class="cahnrs-search-submit" value="$" />
			</form>

			<h2>Topics</h2>
			<?php
				$topics = get_terms( 'topic', array( 'parent' => 0 ) );
				if ( ! empty( $topics ) && ! is_wp_error( $topics ) ) {
					echo '<ul class="browse-terms topics">';
					foreach ( $topics as $topic ) {
						echo '<li class="topic-' . $topic->slug . '">';
						echo '<a href="' . get_term_link( $topic ) . '" data-type="topic" data-slug="' . $topic->slug . '" data-name="' . $topic->name . '">' . $topic->name . '</a>';
						echo '</li>';
					}
					echo '</ul>';
 				}
			?>

			<h2>Locations</h2>
			<div class="locations-container">
				<?php
					$extension = get_term_by( 'name', 'WSU Extension', 'wsuwp_university_location' );
					$locations = get_terms( 'wsuwp_university_location', array( 'parent' => (int) $extension->term_id ) );
					if ( ! empty( $locations ) && ! is_wp_error( $locations ) ) {
						echo '<ul class="browse-terms locations">';
						foreach ( $locations as $location ) {
							echo '<li class="wsuwp_university_location-' . $location->slug . '">';
							echo '<a href="' . get_term_link( $location ) . '" data-type="wsuwp_university_location" data-slug="' . $location->slug . '" data-name="' . $location->name . '">' . $location->name . '</a>';
							echo '</li>';
						}
						echo '</ul>';
					}
				?>
			</div>

		</div>

	</section>

</main>

<?php

get_footer();