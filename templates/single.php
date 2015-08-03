<?php get_header(); ?>

<main>

	<?php get_template_part('parts/headers'); ?>

	<?php while ( have_posts() ) : the_post(); ?>

	<?php if ( spine_has_featured_image() ) {
		$featured_image_src = spine_get_featured_image_src(); ?>
		<figure class="featured-image" style="background-image: url('<?php echo $featured_image_src; ?>');">
		<?php spine_the_featured_image(); ?>
		</figure>
	<?php } ?>

	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<section class="row side-right gutter pad-top">

			<div class="column one">
    		<header class="article-header">
					<hgroup>
						<?php
							$subtitle = get_post_meta( get_the_ID(), '_impact_report_subtitle', true );
      				if ( $subtitle ) {
       					echo '<h2 class="article-subtitle">' . esc_html( $subtitle ) . '</h2>';
							}
						?>
					</hgroup>
					<hgroup class="source">
						<time class="article-date" datetime="<?php echo get_the_date( 'c' ); ?>"><?php echo get_the_date(); ?></time>
					</hgroup>
				</header>
    	</div>

			<div class="column two">
    		<?php 
				$pdf_meta = get_post_meta( get_the_ID(), '_impact_report_pdfs', true );
				if ( $pdf_meta ) {
					$most_recent = max( array_keys( $pdf_meta ) );
					echo '<h2 id="impact-report-pdf"><a href="' . $pdf_meta[ $most_recent ] . '">Print Version (PDF) &raquo;</a></h2>';
					unset( $pdf_meta[$most_recent] );
				}
				if ( $pdf_meta ) {
					?>
					<dl id="impact-report-pdf-archive">
						<dt>
							<h2>PDF Archive</h2>
						</dt>
						<dd>
							<ul>
							<?php
								foreach ( $pdf_meta as $year => $file ) {
									echo '<li><a href="' . $file . '">' . $year . '</a></li>';
								}
							?>
							</ul>
						</dd>
					</dl>
					<?php
				}
			?>
	    </div>

		</section>

		<section class="row side-right gutter pad-bottom">

			<div class="column one">

				<?php 
					// Headline.
					$headline = get_post_meta( get_the_ID(), '_impact_report_headline', true );
					if ( $headline ) {
						echo '<h2>' . esc_html( $headline ) . '</h2>';
					}

					// Issue.
					$issue = get_post_meta( get_the_ID(), '_impact_report_issue', true );
					if ( $issue ) {
						echo '<h4 id="issue">Issue</h4>';
						echo wpautop( wp_kses_post( $issue ) );
					}

					// Response.
					$response = get_post_meta( get_the_ID(), '_impact_report_response', true );
					if ( $response ) {
						echo '<h4 id="response">Response</h4>';
						echo wpautop( wp_kses_post( str_replace( '<!--more-->', '', $response ) ) );
					}
				?>

			</div><!--/column-->

			<div class="column two">

    	  <?php
					// By the Numbers.
        	$numbers = get_post_meta( get_the_ID(), '_impact_report_numbers', true );
					echo '<h4 id="numbers">By the numbers</h4>';
      	  if ( $numbers ) {
        	  echo wpautop( wp_kses_post( $numbers ) );
					}

					// Front bottom left image.
					$front_bottom_left = get_post_meta( $post->ID, '_impact_report_image_1', true );
					if ( $front_bottom_left ) {
						$img_array = explode( '$S$', $front_bottom_left );
						$image = wp_get_attachment_image_src( $img_array[0], 'medium' );
						echo '<img src="' . $image[0] . '" width="' . $image[1] . '" height="' . $image[2] . '" />';
					}
				?>

			</div>

		</section>

		<section class="row thirds gutter">

			<?php
				// Back page top left image.
				$back_top_left = get_post_meta( $post->ID, '_impact_report_image_2', true );
				if ( $back_top_left ) {
					$img_array = explode( '$S$', $back_top_left );
					$image = wp_get_attachment_image_src( $img_array[0], 'thumbnail' );
					echo '<div class="column one"><img src="' . $image[0] . '" width="' . $image[1] . '" height="' . $image[2] . '" /></div>';
				}

				// Back page top center image.		
				$back_top_center = get_post_meta( $post->ID, '_impact_report_image_3', true );
				if ( $back_top_center ) {
					$img_array = explode( '$S$', $back_top_center );
					$image = wp_get_attachment_image_src( $img_array[0], 'thumbnail' );
					echo '<div class="column two"><img src="' . $image[0] . '" width="' . $image[1] . '" height="' . $image[2] . '" /></div>';
				}

				// Back page top right image.
				$back_top_right = get_post_meta( $post->ID, '_impact_report_image_4', true );
				if ( $back_top_right ) {
					$img_array = explode( '$S$', $back_top_right );
					$image = wp_get_attachment_image_src( $img_array[0], 'thumbnail' );
					echo '<div class="column three"><img src="' . $image[0] . '" width="' . $image[1] . '" height="' . $image[2] . '" /></div>';
				}
			?>

		</section>

		<section class="row side-right gutter pad-ends">

			<div class="column one">

				<?php
					// Impacts.
					$impacts = get_post_meta( get_the_ID(), '_impact_report_impacts', true );
					if ( $impacts ) {
						echo '<h4 id="impacts">Impacts</h4>';
						echo wpautop( wp_kses_post( str_replace( '<!--more-->', '', $impacts ) ) );
					}
				?>

			</div>

			<div class="column two">

      	<?php
					// Quotes.
					$quotes = get_post_meta( get_the_ID(), '_impact_report_quotes', true );
					if ( $quotes ) {
						echo '<h4 id="quotes">Quotes</h4>';
						echo wpautop( wp_kses_post( $quotes ) );
					}

					// Additional content.
					$additional_title = get_post_meta( get_the_ID(), '_impact_report_additional_title', true );
					$additional = get_post_meta( get_the_ID(), '_impact_report_additional', true );
					if ( $additional_title && $additional ) {
						echo '<h4>' . esc_html( $additional_title ) . '</h4>';
						echo wpautop( wp_kses_post( $additional ) );
					}

					// Back page bottom left image.
					$back_bottom_left = get_post_meta( $post->ID, '_impact_report_image_5', true );
					if ( $back_bottom_left ) {
						$img_array = explode( '$S$', $back_bottom_left );
						$image = wp_get_attachment_image_src( $img_array[0], 'medium' );
						echo '<img src="' . $image[0] . '" width="' . $image[1] . '" height="' . $image[2] . '" />';
					}
				?>

		</div><!--/column two-->

		</section>

		<section class="row single gutter pad-ends">

			<div class="column one">

				<?php
					// Front footer.
					$footer_front = get_post_meta( get_the_ID(), '_impact_report_footer_front', true );
					if ( $footer_front ) {
						echo wpautop( wp_kses_post ( $footer_front ) );
					}

					// Back footer.
					$footer_back = get_post_meta( get_the_ID(), '_impact_report_footer_back', true );
					if ( $footer_back ) {
						echo wpautop( wp_kses_post( $footer_back ) );
					}
				?>

			</div>

		</section>

	</div>

<?php endwhile; ?>

</main>

<?php get_footer(); ?>