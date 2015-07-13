<?php get_header(); ?>

<main>

	<?php get_template_part('parts/headers'); ?>

	<?php if ( spine_has_featured_image() ) {
		$featured_image_src = spine_get_featured_image_src(); ?>
		<figure class="featured-image" style="background-image: url('<?php echo $featured_image_src; ?>');">
		<?php spine_the_featured_image(); ?>
		</figure>
	<?php } ?>

	<section class="row side-right gutter pad-ends">

		<div class="column one">

			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

					<header class="article-header">
						<hgroup>
							<!--<h1 class="article-title"><?php the_title(); ?></h1>-->
							<?php
								$subtitle = get_post_meta( get_the_ID(), '_ir_subtitle', true );
      					if ( $subtitle ) {
        					echo '<h2 class="article-subtitle">' . esc_html( $subtitle ) . '</h2>';
								}
							?>
						</hgroup>
						<hgroup class="source">
							<time class="article-date" datetime="<?php echo get_the_date( 'c' ); ?>"><?php echo get_the_date(); ?></time>
						</hgroup>
					</header>

					<!--<p class="quicklinks">Jump to: <a href="#issue">Issue</a> | <a href="#response">Response</a> | <a href="#impacts">Impacts</a></p>-->

					<div class="article-body">

						<?php 
        			$headline = get_post_meta( get_the_ID(), '_ir_headline', true );
        			if ( $headline ) {
          			$long = ( strlen( $headline ) > 111 ) ? ' class="long"' : '';
            			echo '<h2' . $long . '>' . esc_html( $headline ) . '</h2>';
        			}
      			?>

      			<h4 id="issue">Issue</h4>
      			<?php
        			$issue = get_post_meta( get_the_ID(), '_ir_issue', true );
        			if ( $issue ) {
          			echo wpautop( wp_kses_post( $issue ) );
							}
      			?>

						<h4 id="response">Response</h4>
						<?php
							$response = get_post_meta( get_the_ID(), '_ir_response', true );
							if ( $response ) {
								echo wpautop( wp_kses_post( str_replace( '<!--more-->', '', $response ) ) );
							}
						?>

						<h4 id="impacts">Impacts</h4>
      			<?php
							$impacts = get_post_meta( get_the_ID(), '_ir_impacts', true );
							if ( $impacts ) {
								echo wpautop( wp_kses_post( str_replace( '<!--more-->', '', $impacts ) ) );
							}
						?>

					</div>

				</article>

			<?php endwhile; ?>

		</div><!--/column-->

		<div class="column two">

			<?php 
				$pdf_meta = get_post_meta( get_the_ID(), '_impact_report_pdfs', true );
				if ( $pdf_meta ) {
					foreach ( $pdf_meta as $year => $file ) {
						if ( date('Y') == (int) $year ) {
							echo '<h2 id="impact-report-pdf"><a href="' . $file . '">Print Version (PDF) &raquo;</a></h2>';
							unset( $pdf_meta[date('Y')] );
						}
					}
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

			<h4 id="numbers">By the numbers</h4>
      <?php
        $numbers = get_post_meta( get_the_ID(), '_ir_numbers', true );
        if ( $numbers ) {
          echo wpautop( wp_kses_post( $numbers ) );
				}
      ?>

			<?php
				$back_page_left = get_post_meta( $post->ID, '_ir_image_2', true );
				if ( $back_page_left ) {
					$img_array = explode( '$S$', $back_page_left );
					$image = wp_get_attachment_image_src( $img_array[0], 'medium' );
					echo '<img src="' . $image[0] . '" alt="" height="182" width="219" />';
				}
				
				$back_page_right_one = get_post_meta( $post->ID, '_ir_image_3', true );
				if ( $back_page_right_one ) {
					$img_array = explode( '$S$', $back_page_right_one );
					$image = wp_get_attachment_image_src( $img_array[0], 'medium' );
					echo '<img src="' . $image[0] . '" alt="" height="147" width="219" />';
				}

				$back_page_right_two = get_post_meta( $post->ID, '_ir_image_4', true );
				if ( $back_page_right_two ) {
					$img_array = explode( '$S$', $back_page_right_two );
					$image = wp_get_attachment_image_src( $img_array[0], 'medium' );
					echo '<img src="' . $image[0] . '" alt="" height="147" width="219" />';
				}
			?>

      <h4 id="quotes">Quotes</h4>
      <?php
        $quotes = get_post_meta( get_the_ID(), '_ir_quotes', true );
        if ( $quotes ) {
          echo wpautop( wp_kses_post( $quotes ) );
				}
      ?>

      <?php
        $additional_title = get_post_meta( get_the_ID(), '_ir_additional_title', true );
        $additional = get_post_meta( get_the_ID(), '_ir_additional', true );
        if ( $additional_title && $additional ) {
          echo '<h4>' . esc_html( $additional_title ) . '</h4>';
          echo wpautop( wp_kses_post( $additional ) );
        }
      ?>

		</div><!--/column two-->

	</section>

	<section class="row single gutter pad-ends">

		<div class="column one">

			<?php
				$footer_front = get_post_meta( get_the_ID(), '_ir_footer_front', true );
				if ( $footer_front ) {
					echo wpautop( wp_kses_post ( $footer_front ) );
				}
			?>
	
			<?php
				$footer_back = get_post_meta( get_the_ID(), '_ir_footer_back', true );
				if ( $footer_back ) {
					echo wpautop( wp_kses_post( $footer_back ) );
				}
			?>

		</div>

	</section>

</main>

<?php get_footer(); ?>