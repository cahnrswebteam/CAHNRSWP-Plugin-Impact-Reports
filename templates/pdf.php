<?php
function cahnrs_impact_report_process_pdf_image( $image, $width, $height, $class, $style ) {
	$height_param = ( $height !== false ) ? '&height=' . $height : '';
	$class = ( $class !== false ) ? ' class="' . $class . '"' : '';
	$style = ( $style !== false ) ? ' style="' . $style . '"' : '';
	if ( $image[1] === $width && $image[2] === $height ) {
		echo '<img src="' . $image[0] . '"' . $class . $style . ' />';
	} else if ( $image[1] >= $width && $image[2] >= $height ) {
		echo '<img src="' . get_home_url() . '/?resized&width=' . $width . $height_param . '&img=' . $image[0] . '"' . $class . $style . ' />';
	}
}

$report_query = new WP_Query( 'p=' . $post_id . '&post_type=impact' );
if ( $report_query->have_posts() ) :
	while ( $report_query->have_posts() ) :
		$report_query->the_post();

		// Content meta
		$subtitle      = get_post_meta( $post_id, '_ir_subtitle', true );
		$numbers       = get_post_meta( $post_id, '_ir_numbers', true );
		$headline      = get_post_meta( $post_id, '_ir_headline', true );
		$issue         = get_post_meta( $post_id, '_ir_issue', true );
		$response      = get_post_meta( $post_id, '_ir_response', true );
		$impacts_start = get_post_meta( $post_id, '_ir_impacts_position', true );
		$impacts       = get_post_meta( $post_id, '_ir_impacts', true );
		$footer_one    = get_post_meta( $post_id, '_ir_footer_front', true );
		$quotes        = get_post_meta( $post_id, '_ir_quotes', true );
		$addtl_title   = get_post_meta( $post_id, '_ir_additional_title', true );
		$additional    = get_post_meta( $post_id, '_ir_additional', true );
		$footer_two    = get_post_meta( $post_id, '_ir_footer_back', true );

		// Image meta
		$pg_1_b_l = get_post_meta( $post_id, '_ir_image_1', true );
		$pg_2_t_l = get_post_meta( $post_id, '_ir_image_2', true );
		$pg_2_t_c = get_post_meta( $post_id, '_ir_image_3', true );
		$pg_2_t_r = get_post_meta( $post_id, '_ir_image_4', true );
		$pg_2_b_l = get_post_meta( $post_id, '_ir_image_5', true );

		// Taxonomy meta
		$program = wp_get_post_terms( $post_id, 'programs', array( 'fields' => 'slugs' ) );
?>
<!DOCTYPE html>
<html>
<head>
	<link type="text/css" rel="stylesheet" href="<?php echo plugin_dir_url( dirname(__FILE__) ) . 'css/pdf.css'; ?>">
</head>
<body>

	<div class="row header">

		<div class="column left">
			<img src="<?php echo plugin_dir_url( dirname(__FILE__) ) . 'images/extension-mark.jpg'; ?>" height="188" width="220" />
		</div>

		<div class="column right">
			<?php
      	// Featured Image
				if ( has_post_thumbnail() ) {
					cahnrs_impact_report_process_pdf_image( wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' ), 1370, 450, false, false );
				}
			?>
		</div>

	</div>
 
	<div class="row content">

		<div class="column left">
			<h1><?php the_title(); ?>&nbsp;</h1>
			<?php
      	// Subtitle
				if ( $subtitle ) {
					echo '<h3>' . esc_html( $subtitle ) . '</h3>';
				}

				// By the Numbers
				if ( $numbers ) {
					echo '<h4>By the Numbers</h4>';
					echo wpautop( wp_kses_post( $numbers ) );
				}
			?>
		</div>

		<div class="column right">
			<?php
      	// Headline
				if ( $headline ) {
					$class = ( strlen( $headline ) > 111 ) ? ' class="long"' : '';
					echo '<h2' . $class . '>' . esc_html( $headline ) . '</h2>';
				}

				// Issue
				if ( $issue ) {
					echo '<h4>Issue</h4>';
					echo wpautop( wp_kses_post( $issue ) );
				}

				// Response
				$response_split = ( $response ) ? preg_split( '/<!--more-->/', $response ) : false;
				if ( $response_split ) {
					echo '<h4>Response</h4>';
					echo wpautop( wp_kses_post( $response_split[0] ) );
				}

				// Impacts (if starting on front page)
				if ( 'front' === $impacts_position ) { // should add a check against $response_split
					if ( $impacts ) {
						echo '<h4>Impacts</h4>';
						$impacts_split = ( $impacts ) ? preg_split( '/<!--more-->/', $impacts ) : false;
						if ( $impacts_split ) { echo wpautop( wp_kses_post( $impacts_split[0] ) ); }
					}
				}
			?>
		</div>

	</div>

	<div class="page-one row footer">

		<?php
			// First page bottom left image
			if ( $pg_1_b_l ) {
				$img_array = explode( '$S$', $pg_1_b_l );
				$image = wp_get_attachment_image_src( $img_array[0], 'full' );
				if ( $image[1] >= '550' ) {
					$proportion = 222 / $image[1];
					$margin = round( ( $proportion * $image[2] ) + 7 );
				}
				cahnrs_impact_report_process_pdf_image( $image, 550, false, 'bottom-left-image', 'top: -' . $margin . 'px;' );
			}
		?>

		<div class="column left">
			<div>
				<a href="http://extension.wsu.edu/impact/">extension.wsu.edu/impact/</a>
			</div>
		</div>

		<div class="column right">
			<?php
				// First page footer
				if ( $footer_one ) {
					echo wpautop( wp_kses_post ( $footer_one ) );
				}
			?>
		</div>

	</div>

	<div class="page-two row header">

		<div class="column left">
			<?php
      	// Second page top left image
				if ( $pg_2_t_l ) {
					$image = explode( '$S$', $pg_2_t_l );
					cahnrs_impact_report_process_pdf_image( wp_get_attachment_image_src( $image[0], 'full' ), 550, 450, false, false );
				}
			?>
		</div>

		<div class="column right">
			<?php
      	// Second page top center image
				if ( $pg_2_t_c ) {
					$image = explode( '$S$', $pg_2_t_c );
					cahnrs_impact_report_process_pdf_image( wp_get_attachment_image_src( $image[0], 'full' ), 667, 450, false, false );
				}

				// Second page top right image
				if ( $pg_2_t_r ) {
					$image = explode( '$S$', $pg_2_t_r );
					cahnrs_impact_report_process_pdf_image( wp_get_attachment_image_src( $image[0], 'full' ), 667, 450, false, false );
				}
			?>
		</div>

	</div>

	<div class="row content">

		<div class="column left">
			<?php
      	// Quotes
				if ( $quotes ) {
					echo '<h4>Quotes</h4>';
					echo wpautop( wp_kses_post( $quotes ) );
				}

				// Additional
				if ( $addtl_title && $additional ) {
        	echo '<h4>' . esc_html( $additional_title ) . '</h4>';
					echo wpautop( wp_kses_post( $additional ) );
				}
			?>
		</div>

		<div class="column right">
			<?php
				// Response continued
      	if ( $response_split && $response_split[1] ) {
					echo wpautop( wp_kses_post( $response_split[1] ) );
				}

				// Impacts
      	if ( $impacts_position == 'front' ) {
					// continued
					if ( $impacts_split && $impacts_split[1] ) {
						echo wpautop( wp_kses_post( $impacts_split[1] ) );
					}
				} else {
					// start
					if ( $impacts ) {
						echo '<h4>Impacts</h4>';
						echo wpautop( wp_kses_post( $impacts ) );
					}
				}
			?>
		</div>

	</div>

	<div class="page-two row footer">
		<?php
			// Second page bottom left image
			if ( $pg_2_b_l ) {
				$img_array = explode( '$S$', $pg_2_b_l );
				$image = wp_get_attachment_image_src( $img_array[0], 'full' );
				if ( $image[1] >= '550' ) {
					$proportion = 222 / $image[1];
					$margin = round( ( $proportion * $image[2] ) + 7 );
				}
				cahnrs_impact_report_process_pdf_image( $image, 550, false, 'bottom-left-image', 'top: -' . $margin . 'px;' );
			}

			// Logo
			if ( $program ) {
				echo '<img src="' . plugin_dir_url( dirname(__FILE__) ) . 'images/' . $program[0] . '-wsu-logo.jpg" class="footer-logo" width="430" />';
			}

			// Second page footer
			if ( $footer_two ) {
				echo wpautop( wp_kses_post( $footer_two ) );
			}
		?>
	</div>

</body>

</html>
<?php
	endwhile;
endif;
?>