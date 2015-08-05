<?php get_header(); ?>

<main class="spine-archive-index">

	<?php get_template_part('parts/headers'); ?>

	<section class="row single gutter pad-ends">

		<div class="column one">

			<?php echo wpautop( wp_kses_post( get_option( 'impact_report_archive_text' ) ) ); ?>

		</div><!--/column-->

	</section>

	<section class="row side-right gutter pad-ends full-bleed gray-darker-back">

		<div class="column one">

			<div id="impact-reports" class="clearfix">

			<?php
				// Modify loop. Probably not the best way to do it... maybe a wp_query instead
				global $query_string;
				query_posts( $query_string . '&posts_per_page=9&meta_key=_impact_report_visibility&meta_value=display' );
				
				$count = 0;

				while ( have_posts() ) : the_post();
				
					$count++;
					if ( $count == 1 ) {
						$column = 'one';
					} else if ( $count == 2 ) {
						$column = 'two';
					} else if ( $count == 3 ) {
						$column = 'three';
						$count = 0;
					}

				?>

				<article id="post-<?php the_ID(); ?>" <?php post_class( $column ); ?>>

					<figure class="article-thumbnail"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'thumbnail' ); ?></a></figure>

					<div>

						<header class="article-header">
							<hgroup>
								<h3 class="article-title">
									<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
								</h3>
							</hgroup>
						</header>

						<?php
							$summary = get_post_meta( get_the_ID(), '_impact_report_summary', true );
							if ( $summary ) {
								?><div class="article-summary"><?php echo wpautop( wp_kses_post( $summary ) ); ?></div><?php
							}
						?>

					</div>

				</article>

			<?php endwhile; // end of the loop. ?>

			</div>

			<?php
				$max = $wp_query->max_num_pages;
 				$paged = ( get_query_var('paged') > 1 ) ? get_query_var('paged') : 1;
			?>

			<p class="more-button center" id="load-more-impact-reports"><a href="#" data-page="<?php echo $paged; ?>" data-max="<?php echo $max; ?>">More</a></p>

		</div><div class="column two">

			<form role="search" method="get" class="cahnrs-search" action="<?php echo home_url( '/' ); ?>">
				<input type="hidden" name="post_type" value="impact">
				<label>
					<span class="screen-reader-text">Search Impact Reports for:</span>
					<input type="search" class="search-field" placeholder="Search Impact Reports" value="<?php echo get_search_query(); ?>" name="s" title="Search Impact Reports for:" />
				</label>
				<input type="submit" class="search-submit" value="$" />
			</form>

			<h2>Topics</h2>
			<?php
				$terms = get_terms( 'topic', array( 'parent' => 0 ) );
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
					echo '<ul class="browse-topics">';
					foreach ( $terms as $term ) {
						echo '<li class="topic-' . $term->slug . '"><a href="' . get_term_link( $term ) . '">' . $term->name . '</a></li>';
					}
					echo '</ul>';
 				}
			?>

			<h2>Locations</h2>
			<?php
			
			?>

		</div>

	</section>

</main>

<?php

get_footer();