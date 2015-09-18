<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Load the HaloSocial metabox in the WP Nav Menu Admin UI
add_action( 'load-nav-menus.php', 'halo_admin_wp_nav_menu_meta_box' );
function halo_admin_wp_nav_menu_meta_box(){
	add_meta_box( 'add-halo-nav-menu', __( 'HaloSocial', 'halosocial' ), 'halo_admin_do_wp_nav_menu_meta_box', 'nav-menus', 'side', 'default' );
}

/**
 * Build and populate the HaloSocial accordion on Appearance > Menus.
 *
 * @global $nav_menu_selected_id
 */
function halo_admin_do_wp_nav_menu_meta_box() {
	haloBoot();
	global $nav_menu_selected_id;

	$walker = new HALO_Walker_Nav_Menu_Checklist( false );
	$args   = array( 'walker' => $walker );
	$post_type_name = 'halosocial';

	$menuItems  = halo_nav_menu_get_loggedin_pages();
	
	?>

	<div id="halosocial-menu" class="posttypediv">
		<p><?php _e( '<em>Logged-In</em> links are relative to the current user, and are not visible to visitors who are not logged in.', 'halosocial' ) ?></p>
		<div id="tabs-panel-posttype-<?php echo $post_type_name; ?>-loggedin" class="tabs-panel tabs-panel-active">
			<ul id="halosocial-menu-checklist-loggedin" class="categorychecklist form-no-clear">
				<?php echo walk_nav_menu_tree( array_map( 'wp_setup_nav_menu_item', $menuItems ), 0, (object) $args );?>
			</ul>
		</div>

		<p class="button-controls">
			<span class="add-to-menu">
				<input type="submit"<?php if ( function_exists( 'wp_nav_menu_disabled_check' ) ) : wp_nav_menu_disabled_check( $nav_menu_selected_id ); endif; ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu', 'halosocial' ); ?>" name="add-custom-menu-item" id="submit-halosocial-menu" />
				<span class="spinner"></span>
			</span>
		</p>
	</div>

	<?php
}

function halo_nav_menu_get_loggedin_pages(){
	$page_args = array();
	$page_args['register'] = (object) array(
										'ID'             => -1,
										'post_title'     => __halotext('Register'),
										'post_author'    => 0,
										'post_date'      => 0,
										'post_excerpt'   => 'register',
										'post_type'      => 'page',
										'post_status'    => 'publish',
										'comment_status' => 'closed',
										'guid'           => UserModel::getRegisterLink()
									);
	$page_args['login'] = (object) array(
										'ID'             => -1,
										'post_title'     => __halotext('Login'),
										'post_author'    => 0,
										'post_date'      => 0,
										'post_excerpt'   => 'login',
										'post_type'      => 'page',
										'post_status'    => 'publish',
										'comment_status' => 'closed',
										'guid'           => URL::to('?view=user&task=login')
									);
	$page_args['logout'] = (object) array(
										'ID'             => -1,
										'post_title'     => __halotext('Logout'),
										'post_author'    => 0,
										'post_date'      => 0,
										'post_excerpt'   => 'logout',
										'post_type'      => 'page',
										'post_status'    => 'publish',
										'comment_status' => 'closed',
										'guid'           => URL::to('?view=user&task=logout')
									);
	$page_args['notification'] = (object) array(
										'ID'             => -1,
										'post_title'     => __halotext('Notification'),
										'post_author'    => 0,
										'post_date'      => 0,
										'post_excerpt'   => 'notification',
										'post_type'      => 'page',
										'post_status'    => 'publish',
										'comment_status' => 'closed',
										'guid'           => URL::to('?view=notification&task=show')
									);
	$page_args['message'] = (object) array(
										'ID'             => -1,
										'post_title'     => __halotext('Messages'),
										'post_author'    => 0,
										'post_date'      => 0,
										'post_excerpt'   => 'messages',
										'post_type'      => 'page',
										'post_status'    => 'publish',
										'comment_status' => 'closed',
										'guid'           => URL::to('?view=message&task=show')
									);
	$page_args['myprofile'] = (object) array(
										'ID'             => -1,
										'post_title'     => __halotext('My Profile'),
										'post_author'    => 0,
										'post_date'      => 0,
										'post_excerpt'   => 'myprofile',
										'post_type'      => 'page',
										'post_status'    => 'publish',
										'comment_status' => 'closed',
										'guid'           => URL::to('?view=user&task=show')
									);
	return $page_args;
}

function halo_setup_nav_menu_item( $menu_item ) {
	if ( is_admin() ) {
		return $menu_item;
	}

	// We use information stored in the CSS class to determine what kind of
	// menu item this is, and how it should be treated
	preg_match( '/\shalo-(.*)-nav/', implode( ' ', $menu_item->classes), $matches );

	// If this isn't a BP menu item, we can stop here
	if ( empty( $matches[1] ) ) {
		return $menu_item;
	}
	switch ( $matches[1] ) {
		case 'login' :
			if ( is_user_logged_in() ) {
				$menu_item->_invalid = true;
			}

			break;

		case 'logout' :
			if ( ! is_user_logged_in() ) {
				$menu_item->_invalid = true;
			}

			break;

		case 'myprofile' :
			if ( ! is_user_logged_in() ) {
				$menu_item->_invalid = true;
			}

			break;

		// Don't show the Register link to logged-in users
		case 'register' :
			if ( is_user_logged_in() ) {
				$menu_item->_invalid = true;
			}

			break;

		// All other BP nav items are specific to the logged-in user,
		// and so are not relevant to logged-out users
		default:
			if ( is_user_logged_in() ) {
				// var_dump($matches[1]);exit;
				// $menu_item->url = bp_nav_menu_get_item_url( $matches[1] );
			} else {
				$menu_item->_invalid = true;
			}

			break;
	}

	// If component is deactivated, make sure menu item doesn't render
	if ( empty( $menu_item->url ) ) {
		$menu_item->_invalid = true;

	// Highlight the current page
	} else {
		$current = bp_get_requested_url();
		if ( strpos( $current, $menu_item->url ) !== false ) {
			$menu_item->classes[] = 'current_page_item';
		}
	}

	return $menu_item;
}

add_filter( 'wp_setup_nav_menu_item', 'halo_setup_nav_menu_item', 10, 1 );

class HALO_Walker_Nav_Menu_Checklist extends Walker_Nav_Menu {

	/**
	 * Constructor.
	 *
	 * @see Walker_Nav_Menu::__construct() for a description of parameters.
	 *
	 * @param array $fields See {@link Walker_Nav_Menu::__construct()}.
	 */
	public function __construct( $fields = false ) {
		if ( $fields ) {
			$this->db_fields = $fields;
		}
	}

	/**
	 * Create the markup to start a tree level.
	 *
	 * @see Walker_Nav_Menu::start_lvl() for description of parameters.
	 *
	 * @param string $output See {@Walker_Nav_Menu::start_lvl()}.
	 * @param int $depth See {@Walker_Nav_Menu::start_lvl()}.
	 * @param array $args See {@Walker_Nav_Menu::start_lvl()}.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat( "\t", $depth );
		$output .= "\n$indent<ul class='children'>\n";
	}

	/**
	 * Create the markup to end a tree level.
	 *
	 * @see Walker_Nav_Menu::end_lvl() for description of parameters.
	 *
	 * @param string $output See {@Walker_Nav_Menu::end_lvl()}.
	 * @param int $depth See {@Walker_Nav_Menu::end_lvl()}.
	 * @param array $args See {@Walker_Nav_Menu::end_lvl()}.
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat( "\t", $depth );
		$output .= "\n$indent</ul>";
	}

	/**
	 * Create the markup to start an element.
	 *
	 * @see Walker::start_el() for description of parameters.
	 *
	 * @param string $output Passed by reference. Used to append additional
	 *        content.
	 * @param object $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param object $args See {@Walker::start_el()}.
	 * @param int $id See {@Walker::start_el()}.
	 */
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		global $_nav_menu_placeholder;

		$_nav_menu_placeholder = ( 0 > $_nav_menu_placeholder ) ? intval($_nav_menu_placeholder) - 1 : -1;
		$possible_object_id = isset( $item->post_type ) && 'nav_menu_item' == $item->post_type ? $item->object_id : $_nav_menu_placeholder;
		$possible_db_id = ( ! empty( $item->ID ) ) && ( 0 < $possible_object_id ) ? (int) $item->ID : 0;

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$output .= $indent . '<li>';
		$output .= '<label class="menu-item-title">';
		$output .= '<input type="checkbox" class="menu-item-checkbox';

		if ( property_exists( $item, 'label' ) ) {
			$title = $item->label;
		}

		$output .= '" name="menu-item[' . $possible_object_id . '][menu-item-object-id]" value="'. esc_attr( $item->object_id ) .'" /> ';
		$output .= isset( $title ) ? esc_html( $title ) : esc_html( $item->title );
		$output .= '</label>';

		if ( empty( $item->url ) ) {
			$item->url = $item->guid;
		}

		if ( ! in_array( array( 'halo-menu', 'halo-'. $item->post_excerpt .'-nav' ), $item->classes ) ) {
			$item->classes[] = 'halo-menu';
			$item->classes[] = 'halo-'. $item->post_excerpt .'-nav';
		}

		// Menu item hidden fields
		$output .= '<input type="hidden" class="menu-item-db-id" name="menu-item[' . $possible_object_id . '][menu-item-db-id]" value="' . $possible_db_id . '" />';
		$output .= '<input type="hidden" class="menu-item-object" name="menu-item[' . $possible_object_id . '][menu-item-object]" value="'. esc_attr( $item->object ) .'" />';
		$output .= '<input type="hidden" class="menu-item-parent-id" name="menu-item[' . $possible_object_id . '][menu-item-parent-id]" value="'. esc_attr( $item->menu_item_parent ) .'" />';
		$output .= '<input type="hidden" class="menu-item-type" name="menu-item[' . $possible_object_id . '][menu-item-type]" value="custom" />';
		$output .= '<input type="hidden" class="menu-item-title" name="menu-item[' . $possible_object_id . '][menu-item-title]" value="'. esc_attr( $item->title ) .'" />';
		$output .= '<input type="hidden" class="menu-item-url" name="menu-item[' . $possible_object_id . '][menu-item-url]" value="'. esc_attr( $item->url ) .'" />';
		$output .= '<input type="hidden" class="menu-item-target" name="menu-item[' . $possible_object_id . '][menu-item-target]" value="'. esc_attr( $item->target ) .'" />';
		$output .= '<input type="hidden" class="menu-item-attr_title" name="menu-item[' . $possible_object_id . '][menu-item-attr_title]" value="'. esc_attr( $item->attr_title ) .'" />';
		$output .= '<input type="hidden" class="menu-item-classes" name="menu-item[' . $possible_object_id . '][menu-item-classes]" value="'. esc_attr( implode( ' ', $item->classes ) ) .'" />';
		$output .= '<input type="hidden" class="menu-item-xfn" name="menu-item[' . $possible_object_id . '][menu-item-xfn]" value="'. esc_attr( $item->xfn ) .'" />';
	}
}