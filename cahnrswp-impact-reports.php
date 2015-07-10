<?php
/*
Plugin Name: CAHNRSWP Impact Reports
Description: Impact reports content type and PDF generation.
Author: CAHNRS, danialbleile, philcable
Version: 0.1.1
*/

/**
 * @todo:
 *	PDF archiving.
 *	http://codex.wordpress.org/Function_Reference/register_post_type#Flushing_Rewrite_on_Activation.
 *	Character count.
 *	Backend visual overhaul (better reflect front-end).
 *	"Send for review" function (https://wordpress.org/support/topic/email-notification-to-admin-for-pending-posts).
 *		use built-in capabilities for this, may have to reconcile contributor and author
 *	Use CAHNRS Topics instead of IR Categories?
 *  Use University Locations instead of IR locations?
 */ 

class CAHNRSWP_Impact_Reports {

	/**
	 * @var string: Content type slug.
	 */
	var $impact_report_content_type = 'impact';

	/**
	 * @var string: Custom taxonomy slugs.
	 */
	var $impact_report_locations = 'locations';
	var $impact_report_programs = 'programs';
	var $impact_report_categories = 'categories';

	/**
	 * @var array: Custom field details.
	 */
	var $impact_report_meta = array(
		'ir_image_1' => array(
			'title' => 'front page: left column bottom image',
			'desc' => 'optional; at least <strong>550</strong> pixels wide',
			'type' => 'img',
		),
		'ir_image_2' => array(
			'title' => 'back page: first banner image',
			'desc'  => 'at least <strong>550 × 450</strong> pixels [wide × tall]',
			'type'  => 'img',
		),
		'ir_image_3' => array(
			'title' => 'back page: second banner image',
			'desc' => 'at least <strong>677 × 450</strong> pixels [wide × tall]',
			'type' => 'img',
		),
		'ir_image_4' => array(
			'title' => 'back page: third banner image',
			'desc' => 'at least <strong>677 × 450</strong> pixels [wide × tall]',
			'type' => 'img',
		),
		'ir_image_5' => array(
			'title' => 'back page: left column bottom image',
			'desc' => 'optional; at least <strong>550</strong> pixels wide',
			'type' => 'img',
		),
		'ir_subtitle' => array(
			'title' => 'Subtitle',
			'desc' => 'optional',
			'type' => 'text',
		),
		'ir_headline' => array(
			'title' => 'Headline',
			'desc' => 'optional',
			'type' => 'text',
		),
		'ir_impacts_position' => array(
			'title' => 'Start "Impacts" section on front page',
			'desc' => '',
			'type' => 'checkbox',
		),
		'ir_additional_title' => array(
			'title' => 'Enter title for additional area here',
			'desc' => '',
			'type' => '',
		),
		/*'ir_author' => array(
			'title' => 'Report Author Email Address',
			'desc' => '(not displayed)',
			'type' => 'text',
		),*/
	);

	/**
	 * @var array: wp_editor details.
	 */
	var $impact_report_editors = array(
		'ir_summary' => array(
			'title' => 'Summary',
			'desc' => 'Summarize the project in 60 words or less',
			'type' => '',
		),
		'ir_issue' => array(
			'title' => 'Issue',
			'desc' => 'Why the project was initiated',
			'type' => 'main',
		),
		'ir_response' => array(
			'title' => 'Response',
			'desc' => 'The work that transpired in response to the issue',
			'type' => 'main',
		),
		'ir_impacts' => array(
			'title' => 'Impacts',
			'desc' => 'The actual or likely result of the project',
			'type' => 'main',
		),
		'ir_numbers' => array(
			'title' => 'By the Numbers',
			'desc' => 'Quantitative results of the project',
			'type' => 'front-sidebar',
		),
		'ir_quotes' => array(
			'title' => 'Quotes',
			'desc' => 'Supporting testimony of the project',
			'type' => 'back-sidebar',
		),
		'ir_additional' => array(
			'title' => 'Additional',
			'desc' => 'Optional area for any further information (e.g. Partners, Grants & Donors, etc.)',
			'type' => 'back-sidebar',
		),
		'ir_footer_front' => array(
			'title' => 'Front page footer',
			'desc' => 'Contact information for the project lead',
			'type' => '',
		),
		'ir_footer_back' => array(
			'title' => 'Back page footer',
			'desc' => 'For more information about the project',
			'type' => '',
		),
	);

	/**
	 * @var string: Impact report editor email.
	 */
	//var $impact_report_editor = get_option( 'impact_report_editor_email' );
	public $impact_report_editor;

	/**
	 * Start the plugin and apply associated hooks.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ), 11 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'edit_form_after_title', array( $this, 'edit_form_after_title' ) );
		add_action( 'edit_form_after_editor',	array( $this, 'edit_form_after_editor' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 10, 2 );
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
		//add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 9999 );
		add_filter( 'template_include', array( $this, 'template_include' ), 1 );
		$this->impact_report_editor = get_option( 'impact_report_editor_email' );
	}

	/**
	 * Register the Impact Report content type and taxonomies.
	 */
	public function init() {

		$impact_reports = array(
			'labels' => array(
				'name' => 'Impact Reports',
				'singular_name' => 'Impact Report',
				'all_items' => 'All Impact Reports',
				'view_item' => 'View Impact Report',
				'add_new_item' => 'Add New Impact Report',
				'add_new' => 'Add New',
				'edit_item' => 'Edit Impact Report',
				'update_item' => 'Update Impact Report',
				'search_items' => 'Search Impact Reports',
				'not_found' => 'Not found',
				'not_found_in_trash' => 'Not found in Trash',
			),
			'description' => 'Reports on research, teaching and engagement from the CAHNRS and WSU Extension.',
			'public' => true,
			'hierarchical' => false,
			'menu_position' => 5,
			'menu_icon' => 'dashicons-portfolio',
			'supports' => array(
				'title',
				'editor',
				'thumbnail',
				'revisions',
			),
			'taxonomies' => array(
				$this->impact_report_programs,
				$this->impact_report_locations,
				$this->impact_report_categories,
			),
			'has_archive' => true,
			'rewrite' => array(
				'slug' => 'impact',
				'with_front' => false
			),
		);
		register_post_type( $this->impact_report_content_type, $impact_reports );

		$locations = array(
			'labels'       => array(
				'name'          => 'Locations',
				'singular_name' => 'Location',
				'search_items'  => 'Search Locations',
				'all_items'     => 'All Locations',
				'edit_item'     => 'Edit Location',
				'update_item'   => 'Update Location',
				'add_new_item'  => 'Add New Location',
				'new_item_name' => 'New Location Name',
				'menu_name'     => 'Locations',
			),
			'description'  => 'Impact Report Locations',
			'public'       => true,
			'hierarchical' => true,
			'show_ui'      => true,
			'show_in_menu' => true,
			'query_var'    => $this->impact_report_locations,
		);
		register_taxonomy( $this->impact_report_locations, $this->impact_report_content_type, $locations );

		$programs = array(
			'labels'        => array(
				'name'          => 'Programs',
				'singular_name' => 'Program',
				'search_items'  => 'Search Programs',
				'all_items'     => 'All Programs',
				'edit_item'     => 'Edit Program',
				'update_item'   => 'Update Program',
				'add_new_item'  => 'Add New Program',
				'new_item_name' => 'New Program Name',
				'menu_name'     => 'Programs',
			),
			'description'   => 'Impact Report Programs',
			'public'        => true,
			'hierarchical'  => true,
			'show_ui'       => true,
			'show_in_menu'  => true,
			'query_var'     => $this->impact_report_programs,
		);
		register_taxonomy( $this->impact_report_programs, $this->impact_report_content_type, $programs );

		$categories = array(
			'labels'        => array(
				'name'          => 'Categories',
				'singular_name' => 'Category',
				'search_items'  => 'Search Categories',
				'all_items'     => 'All Categories',
				'edit_item'     => 'Edit Category',
				'update_item'   => 'Update Category',
				'add_new_item'  => 'Add New Category',
				'new_item_name' => 'New Category Name',
				'menu_name'     => 'Categories',
			),
			'description'   => 'Impact Report Categories',
			'public'        => true,
			'hierarchical'  => true,
			'show_ui'       => true,
			'show_in_menu'  => true,
			'query_var'     => $this->impact_report_categories,
		);
		register_taxonomy( $this->impact_report_categories, $this->impact_report_content_type, $categories );

	}

	/**
	 * Enqueue scripts and styles for the admin interface.
	 */
	public function admin_enqueue_scripts( $hook ) {
		$screen = get_current_screen();
		if ( ( 'post-new.php' === $hook || 'post.php' === $hook ) && $this->impact_report_content_type === $screen->post_type ) {
			wp_enqueue_style(  'impact-report-admin-style', plugins_url( 'css/admin-impact-report.css', __FILE__ ), array() );
			wp_enqueue_script( 'impact-report-admin-scripts', plugins_url( 'js/admin-impact-report.js', __FILE__ ), array() );
			wp_enqueue_script( 'impact-report-taxonomy-scripts', plugins_url( 'js/admin-impact-report-taxonomy.js', __FILE__ ), array(), '', true );
		}
		if ( 'edit.php' == $hook && $this->impact_report_content_type === $screen->post_type ) {
			wp_enqueue_script( 'impact-report-taxonomy-scripts',plugins_url( 'js/admin-impact-report-taxonomy.js', __FILE__ ), array(), '', true );
		}
	}

	/**
	 * Add options page link to the menu.
	 */
	public function admin_menu() {
		add_submenu_page( 'edit.php?post_type=' . $this->impact_report_content_type, 'Impact Report Settings', 'Settings', 'manage_options', 'settings', array( $this, 'impact_reports_settings_page' ) );
	}

	/**
	 * Options page settings, tinyMCE plugin.
	 */
	public function admin_init() {
		register_setting( 'impact_report_options', 'impact_report_editor_email' );
		add_filter( 'mce_external_plugins', array( $this, 'mce_external_plugins' ) );
	}

	/**
	 * Options page content.
	 */
	public function impact_reports_settings_page() {
		?>
<div class="wrap">
<h2>Impact Report Settings</h2>
<form method="post" action="options.php">
<?php settings_fields( 'impact_report_options' ); ?>
<?php do_settings_sections( 'impact_report_options' ); ?>
<table class="form-table">
<tr valign="top">
<th scope="row">Editor E-mail Address</th>
<td><input type="text" name="impact_report_editor_email" value="<?php echo esc_attr( get_option('impact_report_editor_email') ); ?>" /></td>
</tr>
</table>
<?php submit_button(); ?>
</form>
</div>
		<?php
	}

	/**
	 * Add a tinyMCE plugin for counting characters.
	 *
	 * @return Array
	 */
	public function mce_external_plugins( $plugin_array ) {
		$screen = get_current_screen();
		if ( $this->impact_report_content_type === $screen->post_type ) {
			$plugin_array['impact_report_character_counter'] = plugins_url( 'js/admin-impact-report-character-count.js', __FILE__ );
		}
	 	return $plugin_array;
	}

	/**
	 * Add help tabs.
	 */
	public function admin_head() {

		$screen = get_current_screen();

		if ( $this->impact_report_content_type === $screen->post_type ) {

			$screen->add_help_tab( array(
				'id'			=> 'impact_report_overview',
				'title'	  => 'About',
				'content' => '<p>Impact reports are concise reports on research, teaching, and engagement from CAHNRS and WSU Extension programs. Each report has three main text sections:</p><ol><li><strong>Issue</strong> describes the problem and why the work was undertaken;</li><li><strong>Response</strong> describes what was done to address the issue (outputs); and</li><li><strong>Impacts</strong> documents the actual outcomes, such as changes in knowledge, or actions and condition of participants or a community.</li></ol><p>(See below for further descriptions of each section.)</p><p>The image below shows how your content will be organized on a finished report (<span style="color:#c7aa5b;">yellow</span> denotes optional components).</p><p style="text-align:center;"><img src ="' . plugins_url( 'images/layout.png', __FILE__ ) . '" height="345" width"525" /></p><p>(Approximate word counts: Issue + Response + Impacts = 600. Note, fewer words will fit if material is in bulleted lists.)</p><p>Please click the tabs to the left for descriptions and examples of each component of an impact report. For additional support, please contact the Impact Reports editor at <a href="mailto:' . esc_attr( $this->impact_report_editor ) . '">' . esc_html( $this->impact_report_editor ) . '</a>.</p>',
			) );

			$screen->add_help_tab( array(
				'id'			=> 'impact_report_images',
				'title'	  => 'Images',
				'content' => '<p>Each impact report can feature four images, with the option for one additional image. All images must meet minimum size requirements in order to produce high-quality print results. Images that are not proportional to the required dimensions may be used, but will be automatically cropped, which may yield undesirable results in some cases.</p><p>Front Page</p><p style="padding-left:30px;">The <em>Featured Image</em> is displayed next to the logo at the top of the front page, and should be at least 1370 × 450 pixels (wide × tall).</p><p style="padding-left:30px;">The <em>Bottom Left Column Image</em> is optional. It should be at least 550 pixels wide. There is no height restriction, but note that using an image here will limit the amount of space that is available for “By the Numbers” text, above it.</p><p>Back Page</p><p style="padding-left:30px;">The <em>First Banner Image</em> should be at least 550 × 450 pixels (wide × tall).</p><p style="padding-left:30px;">Both the <em>Second Banner Image</em> and <em>Third Banner Image</em> should be at least 677 × 450 pixels (wide × tall).</p><p><strong>Images that do not meet the minimum size requirements will not display.</strong></p>',
			) );

			$screen->add_help_tab( array(
				'id'			=> 'impact_report_issue',
				'title'	  => 'Issue',
				'content' => '<p>The <em>Issue</em> section describes the context, conditions, and problems that existed and prompted initiation of the project.<br />(Target word count: 150)</p>',
			) );

			$screen->add_help_tab( array(
				'id'			=> 'impact_report_response',
				'title'	  => 'Response',
				'content' => '<p>The <em>Response</em> section describes the work done in response to the issue. This may include grant funds sought and secured; partnerships developed; workshops organized and delivered; publications, web sites, decision tools and other media created, etc.<br />(Target word count: 150)</p>',
			) );

			$screen->add_help_tab( array(
				'id'			=> 'impact_report_impact',
				'title'	  => 'Impacts',
				'content' => '<p>The <em>Impacts</em> section outlines the actual documented effects of the project. These are also referred to as outcomes and include short term changes in knowledge or awareness (learning), intermediate term changes in practice (adoption), and long term changes in conditions (economic, environmental or social).<br />(Target word count: 300)</p>',
			) );

			$screen->add_help_tab( array(
				'id'			=> 'impact_report_numbers',
				'title'	  => 'By the Numbers',
				'content' => '<p>The <em>By the Numbers</em> section shows quantitative results of the project such as number of participants, grant dollars, workshops, resources affected (acres, miles of stream, etc.), and other outputs.<br />(Target word count: 120)</p>',
			) );

			$screen->add_help_tab( array(
				'id'			=> 'impact_report_quotes',
				'title'	  => 'Quotes',
				'content' => '<p>The <em>Quotes</em> section highlights supporting testimony of the project. These can be direct quotes from participants via surveys or other evaluation instruments, or paraphrased statements from project leaders.<br />(Target word count: 120)</p>',
			) );

			$screen->add_help_tab( array(
				'id'			=> 'impact_report_additional',
				'title'	  => 'Additional',
				'content' => '<p>The <em>Additional</em> section is optional. It is for any further information and acknowledgments, such as funding partners, grants, donors, etc., as space allows.</p>',
			) );

			$screen->add_help_tab( array(
				'id'			=> 'impact_report_footers',
				'title'	  => 'Footers',
				'content' => '<p>The <em>Front Page Footer</em> is for listing contact information of the unit leader. The <em>Back Page Footer</em> is for contact information for the program.</p>',
			) );

		}
		
	}

	/**
	 * Add a metabox context after the title.
	 *
	 * @param WP_Post $post
	 */
	public function edit_form_after_title( $post ) {
		if ( $this->impact_report_content_type === $post->post_type ) {
			do_meta_boxes( get_current_screen(), 'after_title', $post );
		}
	}

	/**
	 * Add the wp_editors.
	 *
	 * @param WP_Post $post
	 */
	public function edit_form_after_editor( $post ) {

		if ( $this->impact_report_content_type !== $post->post_type ) {
			return;
		}

		$issue_count = strlen( str_replace( ' ', '', get_post_meta( $post->ID, '_ir_issue', true ) ) );
		$response_count = strlen( str_replace( ' ', '', get_post_meta( $post->ID, '_ir_response', true ) ) );
		$impact_count = strlen( str_replace( ' ', '', get_post_meta( $post->ID, '_ir_impacts', true ) ) );
		$main_total = $issue_count + $response_count + $impact_count;
		$main_remaining = ( $main_total ) ? 4500 - $main_total : '4500';
		$numbers_count = strlen( str_replace( ' ', '', get_post_meta( $post->ID, '_ir_numbers', true ) ) );
		$front_sidebar_remaining = ( $numbers_count ) ? 900 - $numbers_count : '900';
		$quotes_count = strlen( str_replace( ' ', '', get_post_meta( $post->ID, '_ir_quotes', true ) ) );
		$additional_count = strlen( str_replace( ' ', '', get_post_meta( $post->ID, '_ir_additional', true ) ) );
		$back_sidebar_total = $quotes_count + $additional_count;
		$back_sidebar_remaining = ( $back_sidebar_total ) ? 900 - $back_sidebar_total : '900';

		foreach ( $this->impact_report_editors as $i_k => $i_d ) {

			// Checkbox for Impacts Position.
			if ( $i_k == 'ir_impacts' ) {
				$value = get_post_meta( $post->ID, '_ir_impacts_position', true );
				echo '<label for="ir_impacts_position"><input type="checkbox" name="ir_impacts_position" id="ir_impacts_position" value="front"';
				checked( $value, 'front' );
				echo '/> Start on front page of PDF</label>';
			}

			echo '<h3 class="impact-report-title">' . $i_d['title'] . '</h3>';
			echo '<p class="description impact-report-description">' . $i_d['desc'] . '</p>';

			// Title field for Additional area.
			if ( $i_k == 'ir_additional' ) {
				$value = get_post_meta( $post->ID, '_ir_additional_title', true );
				echo '<div id="impact-report-additional-title-wrap">';
				echo '<label';
				if ( $value ) {
					echo ' class="screen-reader-text"';
				}
				echo ' id="impact-report-additional-title-prompt-text" for="ir_additional_title">Enter title for additional area here</label>';
				echo '<input type="text" id="ir_additional_title" name="ir_additional_title" value="' . esc_attr( $value ) . '" class="widefat" />';
				echo '</div>';
			}

			$value = get_post_meta( $post->ID, '_' . $i_k, true );
			
			$editor_columns = ( 'ir_summary' === $i_k || 'ir_footer_front' === $i_k || 'ir_footer_back' === $i_k ) ? 2 : 10;

			$editor_settings = array (
				'textarea_rows' => $editor_columns,
				'media_buttons' => false
			);
			
			wp_editor( $value, $i_k , $editor_settings );

			// Character count for main body components.
			if ( $i_d['type'] == 'main' ) {
				echo '<div class="impact-report-character-count ir-main-counter widget-top find-box-buttons">Main body characters remaining: <span>' . $main_remaining . '</span></div>';
			}
			// Character count for front page sidebar.
			if ( $i_d['type'] == 'front-sidebar' ) {
				echo '<div class="impact-report-character-count ir-front-sidebar-counter widget-top find-box-buttons">Front page sidebar characters remaining: <span>' . $front_sidebar_remaining . '</span></div>';
			}
			// Character count for back page sidebar components.
			if ( $i_d['type'] == 'back-sidebar' ) {
				echo '<div class="impact-report-character-count ir-back-sidebar-counter widget-top find-box-buttons">Back page sidebar characters remaining: <span>' . $back_sidebar_remaining . '</span></div>';
			}

		}

	}

	/**
	 * Add the meta boxes used for the Impact Report content type.
	 *
	 * @param string $post_type The slug of the current post type.
	 */
	public function add_meta_boxes( $post_type ) {
		if ( $this->impact_report_content_type !== $post_type ) {
			return;
		}
		add_meta_box(
			'impact_report_fields',
			'Additional Headings',
			array( $this, 'impact_report_headings' ),
			$this->impact_report_content_type,
			'after_title',
			'high'
		);
		/*add_meta_box(
			'impact_report_review',
			'Ready For Edit',
			array( $this, 'impact_report_review' ),
			$this->impact_report_content_type,
			'side',
			'high'
		);*/
		if ( current_user_can( 'manage_options' ) ) {
			add_meta_box(
				'impact_report_review',
				'Visibility',
				array( $this, 'impact_report_visibility' ),
				$this->impact_report_content_type,
				'side',
				'high'
			);
			add_meta_box(
				'impact_report_pdf_revision',
				'History Revision',
				array( $this, 'impact_report_pdf_revision' ),
				$this->impact_report_content_type,
				'side',
				'core'
			);
		}
		add_meta_box(
			'impact_report_images',
			'Additional Images',
			array( $this, 'impact_report_images' ),
			$this->impact_report_content_type,
			'side',
			'low'
		);
	}

	/**
	 * Heading fields.
	 */
	public function impact_report_headings( $post ) {
		wp_nonce_field( 'impact_report_meta', 'impact_report_meta_nonce' );
		echo '<p class="description">Click the "Help" tab at the top right of the screen for more information on creating an Impact Report.</p>';
		foreach ( $this->impact_report_meta as $i_k => $i_d ) {
			if ( $i_d['type'] == 'text' ) {
				$value = get_post_meta( $post->ID, '_' . $i_k, true );
				echo '<p><strong><label for="' . $i_k . '">' . $i_d['title'] . '</label></strong>';
				if ( $i_d['desc'] != '' ) {
					echo ' (<span class="description">' . $i_d['desc'] . '</span>)';
				}
				echo '<br />';
				echo '<input type="text" id="' . $i_k . '" name="' . $i_k . '" value="'. esc_attr( $value ) .'" class="widefat" /></p>';
			}
		}
	}

	/**
	 * "Submit for Review" button.
	 */
	public function impact_report_review( $post ) {
		echo '<p>Click the "Submit for Review" button below <em>after</em> a draft of the report has been saved and it is ready for an editorial review.</p>';
		echo '<input class="button button-primary button-large impact-report-submit-for-review" name="send-to-edit" type="submit" value="Submit for Review" />';
		echo '<div class="clear"></div>';
	}

	/**
	 * Visibility options.
	 */
	public function impact_report_visibility( $post ) {
		echo '<p class="description">Not all impact reports are intented to be featured on the Extension Impacts page.</p>';
		$value = get_post_meta( $post->ID, '_impact_report_visibility', true );
		echo '<p><label for="_impact_report_visibility"><input type="checkbox" name="_impact_report_visibility" id="_impact_report_visibility" value="1"';
		checked( $value, 'display' );
		echo '/> Display on Extension Impacts page</label></p>';
	}

	/**
	 * PDF revision access.
	 */
	public function impact_report_pdf_revision( $post ) {
		echo "<p class='description'>By default, changes published after December 31 will generate a new PDF. You can manually override this behavior and revise a previous year's PDF by selecting it below.</p>";
	}

	/**
	 * Image upload fields.
	 */
	public function impact_report_images( $post ) {
		foreach ( $this->impact_report_meta as $i_k => $i_d ) {
			if ( $i_d['type'] == 'img' ) {
				$i_meta = get_post_meta( $post->ID, '_' . $i_k, true );
				$i_meta_path = get_post_meta( $post->ID, '_' . $i_k . '-path', true );
				echo '<div class="upload-set-wrapper">';
				echo '<input id="' . $i_k . '" class="upload-image-id" type="text" name="' . $i_k . '" value="'. $i_meta .'" />';
				echo '<p class="hide-if-no-js"><a href="#" title=" ' . $i_d['title'] . '" id="upload-image-button-' . $i_k . '" class="upload-image-button">';
				if ( $i_meta ) {
					$img_array = explode( '$S$', $i_meta );
					$image = wp_get_attachment_image_src( $img_array[0], 'medium' );
					echo '<img src="' . $image[0] . '" /></a></p>';
					echo '<p class="hide-if-no-js"><a href="#" class="remove-ir-image">Remove ' . $i_d['title'] . '</a></p>';
				} else {
					echo 'Set ' . $i_d['title'] . '</a></p>';
				}
				echo '<p class="impact-report-image-size">(<span class="description">' . $i_d['desc'] . '</span>)</p>';
				echo '</div>';
			}
		}
	}

	/**
	 * Save data associated with an Impact Report.
	 *
	 * @param int $post_id
	 *
	 * @return mixed
	 */
	public function save_post( $post_id ) {

		if ( ! isset( $_POST['impact_report_meta_nonce'] ) ) {
			return $post_id;
		}

		$nonce = $_POST['impact_report_meta_nonce'];

		if ( ! wp_verify_nonce( $nonce, 'impact_report_meta' ) ) {
			return $post_id;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { 
			return $post_id;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		// Sanitize and save basic fields.
		foreach ( $this->impact_report_meta as $i_k => $i_d ) {
			if ( $i_d['type'] == 'checkbox' ) {
				if ( isset( $_POST[$i_k] ) ) {
					update_post_meta( $post_id, '_' . $i_k, 'front' );
				} else {
    			delete_post_meta( $post_id, '_' . $i_k );
				}
			} else {
				if ( isset( $_POST[$i_k] ) && $_POST[$i_k] != '' ) {
					update_post_meta( $post_id, '_' . $i_k, sanitize_text_field( $_POST[$i_k] ) );
				} else {
					delete_post_meta( $post_id, '_' . $i_k );
				}
			}
		}

		// Sanitize and save wp_editors.
		foreach ( $this->impact_report_editors as $i_k => $i_d ) {
			if ( isset( $_POST[$i_k] ) && $_POST[$i_k] != '' ) {
				update_post_meta( $post_id, '_' . $i_k, wp_kses_post( $_POST[$i_k] ) );
			} else {
				delete_post_meta( $post_id, '_' . $i_k );
			}
		}

		// Admin-only metas.
		if ( current_user_can( 'manage_options' ) ) {
			// Visibility.
			if ( isset( $_POST['_impact_report_visibility'] ) && $_POST['_impact_report_visibility'] === '1' ) {
				update_post_meta( $post_id, '_impact_report_visibility', 'display' );
			} else {
				delete_post_meta( $post_id, '_impact_report_visibility' );
			}
			// PDF revision
			if ( isset( $_POST['_impact_report_pdf_revision'] ) && $_POST['_impact_report_pdf_revision'] != '' ) {
				update_post_meta( $post_id, '_impact_report_pdf_revision', sanitize_text_field( $_POST['_impact_report_pdf_revision'] ) );
			} else {
				delete_post_meta( $post_id, '_impact_report_pdf_revision' );
			}
		}

		// Generate PDF.
		$upload_directory = wp_upload_dir();
		$upload_path = $upload_directory['basedir'] . '/temp_generated_pdfs';
		$upload_url = get_site_url() . '/wp-content/uploads/temp_generated_pdfs';
		if ( ! file_exists( $upload_path ) ) {
			mkdir( $upload_path, 0777, true );
		}
		$file = array();
		$file['name'] = sanitize_title( get_the_title( $post_id ) ) . '-' . date('Y') . '-' . $post_id;
		$file['path'] = $upload_path . '/' . $file['name'] . '.pdf';
		$file['url']  = $upload_url . '/' . $file['name'] . '.pdf';
		require_once ( plugin_dir_path( __FILE__ ) . 'dompdf/dompdf_config.inc.php' );
		ob_start();
		include plugin_dir_path( __FILE__ ) . 'templates/pdf.php';
		$html = ob_get_clean();
		if ( $html ) {
			$dompdf = new DOMPDF();
			$dompdf->load_html( $html );
			$dompdf->render();
			$is_existing = $this->get_attachment_by_post_name( $file['name'] );
			// Revising a PDF from a previous year
			//if ( ( isset( $_POST['_impact_report_pdf_revision'] ) && $_POST['_impact_report_pdf_revision'] != '' ) || get_post_meta( $post->ID, '_impact_report_pdf_revision', true ) ) {}
			if ( $is_existing ) {
				$url = $is_existing->guid;
				$base = explode( '/wp-content/', $url );
				$path = ABSPATH . 'wp-content/' . $base[1];
				file_put_contents( $path, $dompdf->output() );
				$this->add_impact_report_pdf_url_meta( $post_id, $url );
			} else {
				file_put_contents( $file['path'], $dompdf->output() );
				$this->upload_impact_report_to_library( $file, $post_id );
			}
			return $file;
		} else {
			return false;
		}
	}

	/**
	 * Get attachment by post name.
	 *
	 * @return string
	 */
	private function get_attachment_by_post_name( $post_name ) {
		$args = array(
			'post_per_page' => 1,
			'post_type'     => 'attachment',
			'name'          => trim ( $post_name ),
		);
		$get_posts = new WP_Query( $args );
		if ( $get_posts->posts[0] ) {
			return $get_posts->posts[0];
		} else {
			return false;
    }
	}

	/**
	 * Upload generated PDF to media library.
	 */
	private function upload_impact_report_to_library( $file, $post_id ) {		
		$file_array = array(
			'name' => $file['name'] . '.pdf',
			'tmp_name' => $file['path']
		);
		$id = media_handle_sideload( $file_array, $post_id );
		if ( $id ) {
			$attach_url = wp_get_attachment_url( $id );
			$this->add_impact_report_pdf_url_meta( $post_id, $attach_url );
		}
		return true;
	}

	/**
	 * Add PDF url as post meta to Impact Report.
	 */
	private function add_impact_report_pdf_url_meta( $post_id, $pdf_url ) {
		if ( $pdf_url ) {
			update_post_meta( $post_id, '_pdf_link', $pdf_url );
		}
	}

	/**
	 * Modify post messages.
	 *
	 * @param array $messages Existing post update messages.
	 *
	 * @return array Amended post update messages with Impact Report update messages.
	 */
	public function post_updated_messages( $messages ) {

		$post             = get_post();
		$post_type        = get_post_type( $post );
		$post_type_object = get_post_type_object( $post_type );

		$messages[ $this->impact_report_content_type ] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => sprintf( 'Impact report updated. <a href="%s">View impact report</a>', esc_url( get_permalink($post_ID) ) ),
			2  => 'Custom field updated.',
			3  => 'Custom field deleted.',
			4  => 'Impact report updated.',
			5  => isset( $_GET['revision'] ) ? sprintf( 'Impact report restored to revision from %s', wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => 'Impact report published.',
			7  => 'Impact report saved.',
			8  => 'Your Impact report has been successfully sent to the impact reports editor. If you have any questions, please email the editor at: <a href="mailto:' . esc_attr( $this->impact_report_editor ) . '">' . esc_html( $this->impact_report_editor ) . '</a>.',
			9  => sprintf( 'Impact report scheduled for: <strong>%1$s</strong>.', date_i18n( 'M j, Y @ G:i', strtotime( $post->post_date ) ) ),
			10 => 'Impact report draft updated.'
		);

		return $messages;

	}

	/**
	 * Register a sidebar for the Impact Report archive.
	 */
	public function widgets_init() {
		register_sidebar( array(
			'name'          => 'Impact Report Archive',
			'id'            => 'impact-report-archive',
			'before_widget' => '',
			'after_widget'  => '',
			'before_title'  => '',
			'after_title'   => '',
		));
	}

	/**
	 * Enqueue the scripts and styles used on the front end.
	 */
	public function wp_enqueue_scripts() {
		if ( $this->impact_report_content_type == get_post_type() ) {
			wp_dequeue_script( 'comment-reply' );
			//wp_enqueue_style( 'impact-report-style',  plugins_url( 'css/impact-report-style.css', __FILE__ ), array() );
			//wp_enqueue_script( 'impact-report-script',  plugins_url( 'js/impact-report-scripts.js', __FILE__ ), array( 'jquery' ), '', true );
		}
	}

	/**
	 * Add templates for the Personnel custom content type.
	 *
	 * @param string $template
	 *
	 * @return string
	 */
	public function template_include( $template ) {
		if ( $this->impact_report_content_type == get_post_type() ) {
			$template = plugin_dir_path( __FILE__ ) . 'templates/single.php';
		}
		//if ( is_post_type_archive( $this->impact_report_content_type ) || is_tax( $this->impact_report_locations ) || is_tax( $this->impact_report_programs ) ) {
		if ( is_post_type_archive( $this->impact_report_content_type ) || ( $this->impact_report_content_type === get_post_type() && is_archive() ) ) {
			$template = plugin_dir_path( __FILE__ ) . 'templates/archive.php';
		}
		if ( is_front_page() && isset( $_GET['resized'] ) ) {
			$template = plugin_dir_path( __FILE__ ) . 'templates/image-resized.php';
		}
		return $template;
	}

}

new CAHNRSWP_Impact_Reports();