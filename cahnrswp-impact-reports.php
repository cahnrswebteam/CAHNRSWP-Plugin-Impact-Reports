<?php
/*
Plugin Name: CAHNRSWP Impact Reports
Description: Impact Report content type and PDF generation.
Author: CAHNRS, danialbleile, philcable
Version: 0.1.1
*/

class CAHNRSWP_Impact_Reports {

	/**
	 * @var string Content type slug.
	 */
	var $impact_report_content_type = 'impact';

	/**
	 * @var string "Programs" taxonomy slug.
	 */
	var $impact_report_programs = 'programs';

	/**
	 * @var array Custom field details.
	 */
	var $impact_report_meta = array(
		'impact_report_image_1' => array(
			'title' => 'front page bottom left image',
			'desc'  => 'optional; at least <strong>550</strong> pixels wide',
			'type'  => 'img',
		),
		'impact_report_image_2' => array(
			'title' => 'back page top left image',
			'desc'  => 'at least <strong>550 × 450</strong> pixels [wide × tall]',
			'type'  => 'img',
		),
		'impact_report_image_3' => array(
			'title' => 'back page top center image',
			'desc'  => 'at least <strong>677 × 450</strong> pixels [wide × tall]',
			'type'  => 'img',
		),
		'impact_report_image_4' => array(
			'title' => 'back page top right image',
			'desc'  => 'at least <strong>677 × 450</strong> pixels [wide × tall]',
			'type'  => 'img',
		),
		'impact_report_image_5' => array(
			'title' => 'back page bottom left image',
			'desc'  => 'optional; at least <strong>550</strong> pixels wide',
			'type'  => 'img',
		),
		'impact_report_subtitle' => array(
			'title' => 'Subtitle',
			'desc'  => 'optional',
			'type'  => 'text',
		),
		'impact_report_headline' => array(
			'title' => 'Headline',
			'desc'  => 'optional',
			'type'  => 'text',
		),
		'impact_report_impacts_position' => array(
			'title' => 'Start "Impacts" section on front page',
			'desc'  => '',
			'type'  => 'checkbox',
		),
		'impact_report_additional_title' => array(
			'title' => 'Enter title for additional area here',
			'desc'  => '',
			'type'  => '',
		),
		/*'ir_author' => array(
			'title' => 'Report Author Email Address',
			'desc'  => '(not displayed)',
			'type'  => 'text',
		),*/
	);

	/**
	 * @var array wp_editor details.
	 */
	var $impact_report_editors = array(
		'impact_report_summary' => array(
			'title' => 'Summary',
			'desc'  => 'Summarize the project in 60 words or less',
			'type'  => '',
		),
		'impact_report_issue' => array(
			'title' => 'Issue',
			'desc'  => 'Why the project was initiated',
			'type'  => 'main',
		),
		'impact_report_response' => array(
			'title' => 'Response',
			'desc'  => 'The work that transpired in response to the issue',
			'type'  => 'main',
		),
		'impact_report_impacts' => array(
			'title' => 'Impacts',
			'desc'  => 'The actual or likely result of the project',
			'type'  => 'main',
		),
		'impact_report_numbers' => array(
			'title' => 'By the Numbers',
			'desc'  => 'Quantitative results of the project',
			'type'  => 'front-sidebar',
		),
		'impact_report_quotes' => array(
			'title' => 'Quotes',
			'desc'  => 'Supporting testimony of the project',
			'type'  => 'back-sidebar',
		),
		'impact_report_additional' => array(
			'title' => 'Additional',
			'desc'  => 'Optional area for any further information (e.g. Partners, Grants & Donors, etc.)',
			'type'  => 'back-sidebar',
		),
		'impact_report_footer_front' => array(
			'title' => 'Front page footer',
			'desc'  => 'Contact information for the project lead',
			'type'  => '',
		),
		'impact_report_footer_back' => array(
			'title' => 'Back page footer',
			'desc'  => 'For more information about the project',
			'type'  => '',
		),
	);

	/**
	 * @var string Impact Report editor email.
	 */
	public $impact_report_editor;

	/**
	 * Start the plugin and apply associated hooks.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ), 11 );
		add_action( 'init', array( $this, 'add_taxonomies' ), 12 );
		add_filter( 'map_meta_cap', array( $this, 'map_meta_cap' ), 10, 4 );
		add_filter( 'user_has_cap', array( $this, 'user_has_cap' ) );
		add_filter( 'registered_taxonomy', array( $this, 'registered_taxonomy' ), 10, 3 );
		register_activation_hook( __FILE__, array( $this, 'rewrite_flush' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_filter( 'manage_taxonomies_for_impact_columns', array( $this, 'impact_columns' ) );
		add_action( 'edit_form_after_title', array( $this, 'edit_form_after_title' ) );
		add_filter( 'admin_post_thumbnail_html', array( $this, 'admin_post_thumbnail_html' ), 10, 1 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 10, 2 );
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
		add_filter( 'transition_post_status', array( $this, 'transition_post_status' ), 10, 3 );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_filter( 'template_include', array( $this, 'template_include' ), 1 );
		add_filter( 'nav_menu_css_class', array( $this, 'nav_menu_css_class'), 100, 3 );
		add_action( 'wp_ajax_nopriv_ajax_post_request', array( $this, 'ajax_post_request' ) );
		add_action( 'wp_ajax_ajax_post_request', array( $this, 'ajax_post_request' ) );
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
			'capability_type' => 'impact_report',
			'capabilities' => array(
				'publish_posts'       => 'publish_impact_reports',
				'edit_posts'          => 'edit_impact_reports',
				'edit_others_posts'   => 'edit_others_impact_reports',
				'delete_posts'        => 'delete_impact_reports',
				'delete_others_posts' => 'delete_others_impact_reports',
				'read_private_posts'  => 'read_private_impact_reports',
				'edit_post'           => 'edit_impact_report',
				'delete_post'         => 'delete_impact_report',
				'read_post'           => 'read_impact_report',
			),
			'hierarchical' => false,
			'menu_position' => 5,
			'menu_icon' => 'dashicons-portfolio',
			'supports' => array(
				'title',
				'editor',
				'thumbnail',
				'revisions',
				'author',
			),
			'taxonomies' => array(
				$this->impact_report_programs,
			),
			'has_archive' => true,
			'rewrite' => array(
				'slug' => $this->impact_report_content_type,
				'with_front' => false
			),
		);
		register_post_type( $this->impact_report_content_type, $impact_reports );

		$programs = array(
			'labels'        => array(
				'name'          => 'Program',
				'singular_name' => 'Program',
				'search_items'  => 'Search Programs',
				'all_items'     => 'All Programs',
				'edit_item'     => 'Edit Program',
				'update_item'   => 'Update Program',
				'add_new_item'  => 'Add New Program',
				'new_item_name' => 'New Program Name',
				'menu_name'     => 'Programs',
			),
			'description'       => 'Impact Report Programs',
			'public'            => true,
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_menu'      => true,
			'query_var'         => $this->impact_report_programs,
			'meta_box_cb'       => array( $this, programs_meta_box),
		);
		register_taxonomy( $this->impact_report_programs, $this->impact_report_content_type, $programs );

	}

	/**
	 * Display the "Program" taxonomy items as radio buttons instead of checkboxes.
	 */
	public function programs_meta_box( $post, $meta_box_properties ) {
		$taxonomy = $meta_box_properties['args']['taxonomy'];
  	$tax = get_taxonomy( $taxonomy );
  	$terms = get_terms( $taxonomy, array( 'hide_empty' => 0 ) );
  	$name = 'tax_input[' . $taxonomy . ']';
  	$postterms = get_the_terms( $post->ID, $taxonomy );
  	$current = ( $postterms ? array_pop( $postterms ) : false );
  	$current = ( $current ? $current->term_id : 0 );
		?>
		<input type="hidden" name="tax_input[<?php echo $taxonomy; ?>][]" value="0">
    <ul id="<?php echo $taxonomy; ?>checklist" data-wp-lists="list:programs" class="categorychecklist form-no-clear">
			<?php foreach( $terms as $term ) : ?>
      <li id="<?php echo $id; ?>">
      	<label class="selectit">
        	<input value="<?php echo $term->term_id; ?>" type="radio" name="tax_input[<?php echo $taxonomy; ?>][]" id="in-<?php echo $id; ?>"<?php if ( $current === (int)$term->term_id ) { ?> checked="checked"<?php } ?>> <?php echo $term->name; ?>
        </label>
      </li>
      <?php endforeach; ?>
		</ul>
		<?php
	}

	/**
	 * Add support for CAHNRS Topics and WSU University Locations.
	 */
	public function add_taxonomies() {
		register_taxonomy_for_object_type( 'topic', $this->impact_report_content_type );
		register_taxonomy_for_object_type( 'wsuwp_university_location', $this->impact_report_content_type );
	}

	/**
	 * Map custom meta capabilities.
	 *
	 * @param array $caps A list of required capabilities for this action.
	 * @param string $cap The capability being checked.
	 * @param int $user_id The current user ID.
	 * @param array $args A numerically indexed array of additional arguments dependent on the meta cap being used.
	 *
	 * @return array $caps Actual capabilities for meta capability.
	 */
	public function map_meta_cap( $caps, $cap, $user_id, $args ) {

		// If publishing, editing, deleting, or reading an Impact Report, get the post and post type object.
		if ( 'edit_impact_report' == $cap || 'delete_impact_report' == $cap || 'read_impact_report' == $cap ) {
			$post = get_post( $args[0] );
			$post_type = get_post_type_object( $post->post_type );

			// Set an empty array for the capabilities.
			$caps = array();
		}

		// Don't allow Impact Report Contributors to edit 'featured' reports.
		if ( ( 'edit_impact_report' == $cap || 'delete_impact_report' == $cap ) && ! user_can( $user_id, 'edit_posts' ) && 'display' === get_post_meta( $args[0], '_impact_report_visibility', true ) ) {
			$caps[] = 'do_not_allow';
		}

		if ( 'edit_impact_report' == $cap ) {
			if ( $user_id == $post->post_author ) {
				$caps[] = $post_type->cap->edit_posts;
			} else {
				$caps[] = $post_type->cap->edit_others_posts;
			}
		} else if ( 'delete_impact_report' == $cap ) {
			if ( $user_id == $post->post_author ) {
				$caps[] = $post_type->cap->delete_posts;
			} else {
				$caps[] = $post_type->cap->delete_others_posts;
			}
		} else if ( 'read_impact_report' == $cap ) {
			if ( 'private' != $post->post_status ) {
				$caps[] = 'read';
			} else if ( $user_id == $post->post_author ) {
				$caps[] = 'read';
			} else {
				$caps[] = $post_type->cap->read_private_posts;
			}
		}

		return $caps;

	}

	/**
	 * "Assign" custom capabilities to default roles.
	 *
	 * @param array $caps Capabilities of the user.
	 */
	public function user_has_cap( $caps ) {
		if ( ! empty( $caps['publish_posts'] ) ) {
			$caps['publish_impact_reports'] = true;
		}
		if ( ! empty( $caps['edit_posts'] ) ) {
			$caps['edit_impact_reports'] = true;
		}
		if ( ! empty( $caps['edit_others_posts'] ) ) {
			$caps['edit_others_impact_reports'] = true;
		}
		if ( ! empty( $caps['delete_posts'] ) ) {
			$caps['delete_impact_reports'] = true;
		}
		if ( ! empty( $caps['delete_others_posts'] ) ) {
			$caps['delete_others_impact_reports'] = true;
		}
		if ( ! empty( $caps['read_private_posts'] ) ) {
			$caps['read_private_impact_reports'] = true;
		}
		return $caps;
	}

	/**
	 * Allow Impact Report Contributors to assign taxonomy terms.
	 *
	 * @param string $taxonomy Taxonomy key.
	 * @param array|string $object_type Name of the object type for the taxonomy object.
	 * @param array|string $args Optional args used in taxonomy registration.
	 */
	public function registered_taxonomy( $taxonomy, $object_type, $args ) {

		global $wp_taxonomies;

		if ( ( $this->impact_report_programs == $taxonomy || 'topic' == $taxonomy || 'wsuwp_university_location' == $taxonomy ) && $this->impact_report_content_type == $object_type ) {
			$wp_taxonomies[ $taxonomy ]->cap->assign_terms = 'edit_impact_report';
		}

	}

	/**
	 * Flush rewrites, add a custom role.
	 */
	public function rewrite_flush() {

		// Flush rewrites on plugin activation.
		$this->init();
		flush_rewrite_rules();

		// Add the Impact Report Contributor role.
		add_role(
			'impact_report_contributor',
			'Impact Report Contributor',
			array(
				'read'                  => true,
				'delete_impact_reports' => true,
				'edit_impact_reports'   => true,
				'upload_files'          => true,
			)
		);

	}

	/**
	 * Enqueue scripts and styles for the admin interface.
	 */
	public function admin_enqueue_scripts( $hook ) {
		$screen = get_current_screen();
		if ( ( 'post-new.php' === $hook || 'post.php' === $hook ) && $this->impact_report_content_type === $screen->post_type ) {
			wp_enqueue_style( 'impact-report-admin-style', plugins_url( 'css/admin-impact-report.css', __FILE__ ), array() );
			wp_enqueue_script( 'impact-report-admin-scripts', plugins_url( 'js/admin-impact-report.js', __FILE__ ), array() );
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
		register_setting( 'impact_report_options', 'impact_report_archive_text' );
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
						<td><input type="text" name="impact_report_editor_email" value="<?php echo esc_attr( get_option( 'impact_report_editor_email' ) ); ?>" /></td>
					</tr>
					<tr valign="top">
						<th scope="row">Archive Introductory Text</th>
						<td><?php wp_editor( wp_kses_post( get_option( 'impact_report_archive_text' ) ), 'impact_report_archive_text' ); ?></td>
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
				'content' => '<p>Impact Reports are concise reports on research, teaching, and engagement from CAHNRS and WSU Extension programs. Each report has three main text sections:</p><ol><li><strong>Issue</strong> describes the problem and why the work was undertaken;</li><li><strong>Response</strong> describes what was done to address the issue (outputs); and</li><li><strong>Impacts</strong> documents the actual outcomes, such as changes in knowledge, or actions and condition of participants or a community.</li></ol><p>(See below for further descriptions of each section.)</p><p>The image below shows how your content will be organized on a finished report (<span style="color:#c7aa5b;">yellow</span> denotes optional components).</p><p style="text-align:center;"><img src ="' . plugins_url( 'images/layout.png', __FILE__ ) . '" height="345" width"525" /></p><p>(Approximate word counts: Issue + Response + Impacts = 600. Note, fewer words will fit if material is in bulleted lists.)</p><p>Please click the tabs to the left for descriptions and examples of each component of an Impact Report. For additional support, please contact the Impact Reports editor at <a href="mailto:' . esc_attr( $this->impact_report_editor ) . '">' . esc_html( $this->impact_report_editor ) . '</a>.</p>',
			) );

			$screen->add_help_tab( array(
				'id'			=> 'impact_report_images',
				'title'	  => 'Images',
				'content' => '<p>Each Impact Report can feature four images, with the option for one additional image. All images must meet minimum size requirements in order to produce high-quality print results. Images that are not proportional to the required dimensions may be used, but will be automatically cropped, which may yield undesirable results in some cases.</p><p>Front Page</p><p style="padding-left:30px;">The <em>Featured Image</em> is displayed next to the logo at the top of the front page, and should be at least 1370 × 450 pixels (wide × tall).</p><p style="padding-left:30px;">The bottom left image is optional. It should be at least 550 pixels wide. There is no height restriction, but note that using an image here will limit the amount of space that is available for “By the Numbers” text, above it.</p><p>Back Page</p><p style="padding-left:30px;">The top left image should be at least 550 × 450 pixels (wide × tall).</p><p style="padding-left:30px;">Both the top center and top right images should be at least 677 × 450 pixels (wide × tall).</p><p><strong>Images that do not meet the minimum size requirements will not display.</strong></p>',
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
	 * Add "Topic" and "University Location" taxonomy columns to the "All Impact Reports" page.
	 *
	 * @param $taxonomies
	 */
	public function impact_columns( $taxonomies ) {
		$taxonomies[] = 'topic';
		$taxonomies[] = 'wsuwp_university_location';
		return $taxonomies;
	}

	/**
	 * Add a metabox context after the title.
	 *
	 * @param WP_Post $post
	 */
	public function edit_form_after_title( $post ) {

		if ( $this->impact_report_content_type !== $post->post_type ) {
			return;
		}
		
		do_meta_boxes( get_current_screen(), 'after_title', $post );

		$summary_count = mb_strlen( strip_tags( get_post_meta( $post->ID, '_impact_report_summary', true ) ), 'utf8' );
		$summary_remaining = ( $summary_count ) ? 140 - $summary_count : '140';
		$issue_count = mb_strlen( strip_tags( get_post_meta( $post->ID, '_impact_report_issue', true ) ), 'utf8' );
		$response_count = mb_strlen( strip_tags( get_post_meta( $post->ID, '_impact_report_response', true ) ), 'utf8' );
		$impact_count = mb_strlen( strip_tags( get_post_meta( $post->ID, '_impact_report_impacts', true ) ), 'utf8' );
		$main_total = $issue_count + $response_count + $impact_count;
		$main_remaining = ( $main_total ) ? 4500 - $main_total : '4500';
		$numbers_count = mb_strlen( strip_tags( get_post_meta( $post->ID, '_impact_report_numbers', true ), 'utf8' ) );
		$front_sidebar_remaining = ( $numbers_count ) ? 900 - $numbers_count : '900';
		$quotes_count = mb_strlen( strip_tags( get_post_meta( $post->ID, '_impact_report_quotes', true ), 'utf8' ) );
		$additional_count = mb_strlen( strip_tags( get_post_meta( $post->ID, '_impact_report_additional', true ), 'utf8' ) );
		$back_sidebar_total = $quotes_count + $additional_count;
		$back_sidebar_remaining = ( $back_sidebar_total ) ? 900 - $back_sidebar_total : '900';

		foreach ( $this->impact_report_editors as $i_k => $i_d ) {

			// Checkbox for Impacts Position.
			if ( $i_k == 'impact_report_impacts' ) {
				$value = get_post_meta( $post->ID, '_impact_report_impacts_position', true );
				echo '<label for="impact_report_impacts_position"><input type="checkbox" name="impact_report_impacts_position" id="impact_report_impacts_position" value="front"';
				checked( $value, 'front' );
				echo '/> Start on front page of PDF</label>';
			}

			echo '<h3 class="impact-report-title">' . $i_d['title'] . '</h3>';
			echo '<p class="description impact-report-description">' . $i_d['desc'] . '</p>';

			// Title field for Additional area.
			if ( $i_k == 'impact_report_additional' ) {
				$value = get_post_meta( $post->ID, '_impact_report_additional_title', true );
				echo '<div id="impact-report-additional-title-wrap">';
				echo '<label';
				if ( $value ) {
					echo ' class="screen-reader-text"';
				}
				echo ' id="impact-report-additional-title-prompt-text" for="impact_report_additional_title">Enter title for additional area here</label>';
				echo '<input type="text" id="impact_report_additional_title" name="impact_report_additional_title" value="' . esc_attr( $value ) . '" class="widefat" />';
				echo '</div>';
			}

			$value = get_post_meta( $post->ID, '_' . $i_k, true );
			
			$editor_columns = ( 'impact_report_summary' === $i_k || 'impact_report_footer_front' === $i_k || 'impact_report_footer_back' === $i_k ) ? 2 : 10;

			$editor_settings = array (
				'textarea_rows' => $editor_columns,
				'media_buttons' => false
			);
			
			wp_editor( $value, $i_k, $editor_settings );

			// Character count for summary.
			if ( $i_k == 'impact_report_summary' ) {
				echo '<div class="impact-report-character-count ir-summary-counter widget-top find-box-buttons">Summary characters remaining: <span>' . $summary_remaining . '</span></div>';
			}
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
	 * Get the post type.
	 *
	 * @return string The post type
	 */
	public function get_featured_image_metabox_post_type() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			parse_str( parse_url( wp_get_referer(), PHP_URL_QUERY ), $query_array );
			if ( array_key_exists( 'post_type', $query_array ) ) {
				return $query_array['post_type'];
			} else if ( array_key_exists( 'post', $query_array ) ) {
				return get_post_type( $query_array['post'] );
			}
		} else {
			$screen = get_current_screen();
			return $screen->post_type;
		}
	}

	/**
	 * Add dimension requirements note to the featured image block.
	 *
	 * @param string $content Admin post thumbnail HTML markup.
	 *
	 * @return string
	 */
	public function admin_post_thumbnail_html( $content ) {
		if ( $this->get_featured_image_metabox_post_type() === $this->impact_report_content_type ) {
			$content = $content . '<p class="impact-report-image-size">(<span class="description">at least <strong>1370 × 450</strong> pixels [wide × tall]</span>)</p>';
		}
		return $content;
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
		if ( current_user_can( 'manage_options' ) ) {
			add_meta_box(
				'impact_report_review',
				'Editor Options',
				array( $this, 'impact_report_editor_options' ),
				$this->impact_report_content_type,
				'side',
				'high'
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
	 * Editorial management options.
	 */
	public function impact_report_editor_options( $post ) {
		// Visibility
		echo '<p><label for="_impact_report_visibility"><input type="checkbox" name="_impact_report_visibility" id="_impact_report_visibility" value="1"';
		checked( get_post_meta( $post->ID, '_impact_report_visibility', true ), 'display' );
		echo '/> <strong>Display on Extension Impacts page</strong></label></p>';

		// PDF revision - display only if report has more than one PDF.
		$pdf_meta = get_post_meta( $post->ID, '_impact_report_pdfs',true );
		if ( $pdf_meta && ( count( $pdf_meta ) > 1 ) ) {
			echo '<p><strong>Revision Access</strong><br /><span class="description">By default, changes published after December 31 will generate a new PDF. You can override this behavior and revise a previous year\'s PDF by selecting it below. Be sure to reset to "Current" when finished.</span></p>';
			echo '<select name="_impact_report_pdf_revision" id="_impact_report_pdf_revision">';
			echo '<option value="">Current</option>';
			foreach ( $pdf_meta as $year => $file ) {
				if ( (int) $year != date('Y') ) {
					echo '<option value="' . $year . '"';
					selected( get_post_meta( $post->ID, '_impact_report_pdf_revision', true ), $year );
					echo '>' . $year . '</option>';
				}
			}
			echo '</select>';
		}
	}

	/**
	 * Image upload fields.
	 */
	public function impact_report_images( $post ) {
		foreach ( $this->impact_report_meta as $i_k => $i_d ) {
			if ( $i_d['type'] == 'img' ) {
				$value = get_post_meta( $post->ID, '_' . $i_k, true );
				?>
				<div class="upload-set-wrapper">
					<input id="<?php echo $i_k; ?>" class="upload-image-id" type="text" name="<?php echo $i_k; ?>" value="<?php echo esc_attr( $value ); ?>" />
					<p class="hide-if-no-js"><a href="#" title="<?php echo $i_d['title']; ?>" id="upload-image-button-<?php echo $i_k; ?>" class="upload-image-button">
					<?php if ( $value ) : ?>
						<?php
            	$img_array = explode( '$S$', $value );
							$image = wp_get_attachment_image_src( $img_array[0], 'thumbnail' );
						?>
						<img src="<?php echo esc_url( $image[0] ); ?>" /></a></p>
						<p class="hide-if-no-js"><a href="#" class="remove-ir-image">Remove <?php echo $i_d['title']; ?></a></p>
					<?php else : ?>
						Set <?php echo $i_d['title']; ?></a></p>
					<?php endif; ?>
					<p class="impact-report-image-size">(<span class="description"><?php echo $i_d['desc']; ?></span>)</p>
				</div>
				<?php
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

		// Editor-only meta fields.
		if ( current_user_can( 'edit_pages' ) ) {
			// Visibility.
			if ( isset( $_POST['_impact_report_visibility'] ) && '1' === $_POST['_impact_report_visibility'] ) {
				update_post_meta( $post_id, '_impact_report_visibility', 'display' );
			} else {
				delete_post_meta( $post_id, '_impact_report_visibility' );
			}
			// PDF revision
			if ( isset( $_POST['_impact_report_pdf_revision'] ) && '' != $_POST['_impact_report_pdf_revision'] ) {
				update_post_meta( $post_id, '_impact_report_pdf_revision', sanitize_text_field( $_POST['_impact_report_pdf_revision'] ) );
			} else {
				delete_post_meta( $post_id, '_impact_report_pdf_revision' );
			}
		}

		// Copy specific meta field content into the_content.
		if ( ! wp_is_post_revision( $post_id ) ){

			// unhook this function so it doesn't loop infinitely.
			remove_action( 'save_post', array( $this, 'save_post' ) );

			$content = '';

			$subtitle = $_POST['impact_report_subtitle'];
			$headline = $_POST['impact_report_headline'];
			$additional_title = $_POST['impact_report_additional_title'];

			if ( isset( $subtitle ) && $subtitle != '' ) {
				$content .= sanitize_text_field( $subtitle ) . "\n\n";
			}

			if ( isset( $headline ) && $headline != '' ) {
				$content .= sanitize_text_field( $headline ) . "\n\n";
			}

			foreach ( $this->impact_report_editors as $i_k => $i_d ) {
				if ( isset( $_POST[$i_k] ) && $_POST[$i_k] != '' ) {
					if ( 'impact_report_additional' == $i_k ) {
						if ( isset( $additional_title ) && $additional_title != '' ) {
							$content .= sanitize_text_field( $additional_title ) . "\n\n";
						}
					}
					$content .= wp_kses_post( $_POST[$i_k] ) . "\n\n";
				}
			}

			$updated_post = array(
				'ID'           => $post_id,
				'post_content' => $content,
			);

			// update the post, which calls save_post again.
			wp_update_post( $updated_post );

			// re-hook this function.
			add_action( 'save_post', array( $this, 'save_post' ) );
		}

		// Generate PDF. (Should this be editor only, too?)
		$upload_directory = wp_upload_dir();
		$upload_path = $upload_directory['basedir'] . '/temp_generated_pdfs';
		$upload_url = get_site_url() . '/wp-content/uploads/temp_generated_pdfs';
		if ( ! file_exists( $upload_path ) ) {
			mkdir( $upload_path, 0777, true );
		}
		$file = array();
		if ( isset( $_POST['_impact_report_pdf_revision'] ) && '' != $_POST['_impact_report_pdf_revision'] ) {
			$year = $_POST['_impact_report_pdf_revision'];
		} else if ( get_post_meta( $post->ID, '_impact_report_pdf_revision', true ) && '' != $_POST['_impact_report_pdf_revision'] ) {
			$year = get_post_meta( $post->ID, '_impact_report_pdf_revision', true );
		} else {
			$year = date('Y');
		}
		$file['name'] = sanitize_title( get_the_title( $post_id ) ) . '-' . $year;
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
			if ( $is_existing ) {
				$pdf_url = $is_existing->guid;
				$base = explode( '/wp-content/', $pdf_url );
				$path = ABSPATH . 'wp-content/' . $base[1];
				file_put_contents( $path, $dompdf->output() );
				$this->add_impact_report_pdf_url_meta( $post_id, $pdf_url, $year );
			} else {
				file_put_contents( $file['path'], $dompdf->output() );
				$this->upload_impact_report_to_library( $file, $post_id, $year );
			}
			return $file;
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
			'name'          => trim( $post_name ),
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
	private function upload_impact_report_to_library( $file, $post_id, $year ) {		
		$file_array = array(
			'name' => $file['name'] . '.pdf',
			'tmp_name' => $file['path']
		);
		$id = media_handle_sideload( $file_array, $post_id );
		if ( $id ) {
			$pdf_url = wp_get_attachment_url( $id );
			$this->add_impact_report_pdf_url_meta( $post_id, $pdf_url, $year );
		}
		return true;
	}

	/**
	 * Add PDF url as post meta to Impact Report.
	 */
	private function add_impact_report_pdf_url_meta( $post_id, $pdf_url, $year ) {
		if ( $pdf_url ) {
			$existing_meta = get_post_meta( $post_id, '_impact_report_pdfs', true );
			if ( $existing_meta ) {
				$existing_meta[$year] = $pdf_url;
				$value = $existing_meta;
			} else {
				$value = array( $year => $pdf_url );
			}
			update_post_meta( $post_id, '_impact_report_pdfs', $value );
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
			1  => sprintf( 'Impact Report updated. <a href="%s">View impact report</a>', esc_url( get_permalink($post_ID) ) ),
			2  => 'Custom field updated.',
			3  => 'Custom field deleted.',
			4  => 'Impact Report updated.',
			5  => isset( $_GET['revision'] ) ? sprintf( 'Impact report restored to revision from %s', wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => 'Impact Report published.',
			7  => 'Impact Report saved.',
			8  => 'Your Impact Report has been sent to the impact reports editor. If you have any questions, please email the editor at: <a href="mailto:' . esc_attr( $this->impact_report_editor ) . '">' . esc_html( $this->impact_report_editor ) . '</a>.',
			9  => sprintf( 'Impact Report scheduled for: <strong>%1$s</strong>.', date_i18n( 'M j, Y @ G:i', strtotime( $post->post_date ) ) ),
			10 => 'Impact Report draft updated.'
		);

		return $messages;

	}

	/**
	 * Email Impact Report Editor when a post is submitted for review.
	 *
	 * @param string $new_status New post status after an update.
	 * @param string $old_status Previous post status.
	 * @param object $post The post object.
	 */
	public function transition_post_status( $new_status, $old_status, $post ) {
		
		if ( $this->impact_report_content_type == $post->post_type && 'draft' == $old_status && 'pending' == $new_status ) {

			$ir_title = get_the_title( $post->ID );
			$ir_link  = get_edit_post_link( $post->ID, '&' );

			$to = esc_html( $this->impact_report_editor );
			$subject = 'Impact Report ready for review';
			$message = 'Please review <a href="' . $ir_link . '">' . $ir_title . '</a> and take any necessary action. Thank you.';
			add_filter( 'wp_mail_content_type', array( $this, 'set_html_content_type' ) );
			wp_mail( $to, $subject, $message );
			remove_filter( 'wp_mail_content_type', array( $this, 'set_html_content_type' ) );

		}
	}

	/**
	 * Filter for sending HTML emails.
	 */
	public function set_html_content_type() {
		return 'text/html';
	}

	/**
	 * Enqueue the scripts and styles used on the front end.
	 */
	public function wp_enqueue_scripts() {
		if ( is_single() && $this->impact_report_content_type == get_post_type() ) {
			wp_enqueue_style( 'impact-report', plugins_url( 'css/impact-report.css', __FILE__ ), array( 'wsu-spine' ) );
			wp_enqueue_script( 'impact-report', plugins_url( 'js/impact-report.js', __FILE__ ), array( 'jquery' ), '', true );
		}
		if ( is_post_type_archive( $this->impact_report_content_type ) || ( $this->impact_report_content_type === get_post_type() && is_archive() ) ) {
			global $wp_query;
			wp_enqueue_style( 'impact-report-archive', plugins_url( 'css/impact-report-archive.css', __FILE__ ), array( 'spine-theme' ) );
			wp_enqueue_script( 'impact-report-archive', plugins_url( 'js/impact-report-archive.js', __FILE__ ), array( 'jquery' ), '', true );
			wp_localize_script( 'impact-report-archive', 'impacts', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		}
	}

	/**
	 * Add templates for the Impact Reports content type.
	 *
	 * @param string $template
	 *
	 * @return string template path
	 */
	public function template_include( $template ) {
		if ( $this->impact_report_content_type == get_post_type() ) {
			$template = plugin_dir_path( __FILE__ ) . 'templates/single.php';
		}
		if ( is_post_type_archive( $this->impact_report_content_type ) || ( $this->impact_report_content_type === get_post_type() && is_archive() ) ) {
			$template = plugin_dir_path( __FILE__ ) . 'templates/index.php';
		}
		if ( is_front_page() && isset( $_GET['resized'] ) ) {
			$template = plugin_dir_path( __FILE__ ) . 'templates/image-resized.php';
		}
		return $template;
	}

	/**
	 * Apply 'dogeared' class to the Impact Report menu item when viewing an impact report.
	 *
	 * @param array $classes Current list of nav menu classes.
	 * @param WP_Post $item Post object representing the menu item.
	 * @param stdClass $args Arguments used to create the menu.
	 *
	 * @return array Modified list of nav menu classes.
	 */
	public function nav_menu_css_class( $classes, $item, $args ) {
		$url = site_url() . '/' . $this->impact_report_content_type . '/';
		if ( 'site' === $args->theme_location && $this->impact_report_content_type == get_post_type() && $item->url == $url ) {
			$classes[] = 'dogeared';
		}
		return $classes;
	}

	/**
	 * AJAX post requests.
	 */
	public function ajax_post_request() {

		$ajax_args = array(
			'post_type' => $this->impact_report_content_type,
			'meta_key'   => '_impact_report_visibility',
			'meta_value' => 'display'
		);

		if ( $_POST['page'] ) {
			$ajax_args['paged'] = $_POST['page'];
			$ajax_args['posts_per_page'] = 12;
		}

		if ( $_POST['type'] ) {
			$ajax_args['tax_query'] = array(
				array(
					'taxonomy' => $_POST['type'],
					'field'    => 'slug',
					'terms'    => $_POST['term'],
				),
			);
			$ajax_args['posts_per_page'] = -1;
		}

		if ( $_POST['reset'] ) {
			$posts = (int) $_POST['reset'] * 12;
			$ajax_args['posts_per_page'] = $posts;
		}

		$posts = new WP_Query( $ajax_args );
    if ( $posts->have_posts() ) {
			while ( $posts->have_posts() ) : $posts->the_post();
				load_template( dirname( __FILE__ ) . '/templates/archive-single.php', false );
      endwhile;
		} else {
			echo 'Sorry, no Impact Reports match the criteria.';
		}

		exit;
	}

	/**
	 * Image processing for PDF output.
	 */
	public function process_image_for_pdf( $image, $width, $height, $class, $style ) {
		$height_param = ( $height !== false ) ? '&height=' . $height : '';
		$class = ( $class !== false ) ? ' class="' . $class . '"' : '';
		$style = ( $style !== false ) ? ' style="' . $style . '"' : '';
		if ( $image[1] === $width && $image[2] === $height ) {
			echo '<img src="' . $image[0] . '"' . $class . $style . ' />';
		} else if ( $image[1] >= $width && $image[2] >= $height ) {
			echo '<img src="' . get_home_url() . '/?resized&width=' . $width . $height_param . '&img=' . $image[0] . '"' . $class . $style . ' />';
		}
	}

}

new CAHNRSWP_Impact_Reports();