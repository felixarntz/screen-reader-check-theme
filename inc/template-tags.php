<?php
/**
 * Theme template tags
 *
 * @package ScreenReaderCheckTheme
 * @since 1.0.0
 */

function srctheme_comment_form() {
	$commenter = wp_get_current_commenter();

	$req = get_option( 'require_name_email' );
	$required_attr = 'aria-required="true" required';
	$required_indicator = ' <span class="required">*</span>';

	$args['fields'] = array(
		'author'	=> '<div class="comment-form-author form-group row"><label class="control-label col-sm-3" for="author">' . __( 'Name', 'leavesandlove-v5' ) . ( $req ? $required_indicator : '' ) . '</label><div class="col-sm-9"><input type="text" id="author" name="author" class="form-control" value="' . esc_attr( $commenter['comment_author'] ) . '"' . ( $req ? $required_attr : '' ) . '></div></div>',
		'email'		=> '<div class="comment-form-email form-group row"><label class="control-label col-sm-3" for="email">' . __( 'Email', 'leavesandlove-v5' ) . ( $req ? $required_indicator : '' ) . '</label><div class="col-sm-9"><input type="email" id="email" name="email" class="form-control" value="' . esc_attr( $commenter['comment_author_email'] ) . '"' . ( $req ? $required_attr : '' ) . '></div></div>',
		'url'		=> '<div class="comment-form-url form-group row"><label class="control-label col-sm-3" for="url">' . __( 'Website', 'leavesandlove-v5' ) . '</label><div class="col-sm-9"><input type="url" id="url" name="url" class="form-control" value="' . esc_attr( $commenter['comment_author_url'] ) . '"></div></div>',
	);
	$args['comment_field'] = '<div class="comment-form-comment form-group row"><label class="control-label col-sm-3" for="comment">' . __( 'Comment', 'leavesandlove-v5' ) . $required_indicator . '</label><div class="col-sm-9"><textarea id="comment" name="comment" class="form-control" rows="8"' . $required_attr . '></textarea></div></div>';
	$args['submit_field'] = '<div class="form-submit form-group row"><div class="col-sm-9 col-sm-offset-3">%1$s %2$s</div></div>';
	$args['class_submit'] = 'submit btn btn-primary';
	$args['format'] = 'html5';

	comment_form( $args );
}

function srctheme_list_comments( $args = array() ) {
	$echo = ! isset( $args['echo'] ) || $args['echo'];

	$args['style'] = 'div';
	//$args['callback'] = 'srctheme_render_comment';
	//$args['end-callback'] = 'srctheme_end_comment';
	$args['echo'] = false;

	if ( ! isset( $args['avatar_size'] ) ) {
		$args['avatar_size'] = 100;
	}

	$output = wp_list_comments( $args );

	if ( ! $output ) {
		return '';
	}

	$output = '<ul class="comment-list/* media-list*/">' . $output . '</ul>';

	if ( isset( $args['before'] ) ) {
		$output = $args['before'] . $output;
	}
	if ( isset( $args['after'] ) ) {
		$output .= $args['after'];
	}

	if ( $echo ) {
		echo $output;
	} else {
		return $output;
	}
}

function srctheme_nav_menu( $args = array() ) {
	return SRCTheme_Walker_Nav_Menu::render( $args );
}

/* --------- Internals are following. ----------- */

function srctheme_get_the_password_form( $output ) {
	$filename = locate_template( array( 'passwordform.php' ), false, false );

	if ( ! $filename ) {
		return $output;
	}

	ob_start();
	load_template( $filename, false );
	$output = ob_get_clean();

	return $output;
}
add_filter( 'the_password_form', 'srctheme_get_the_password_form' );

function srctheme_gallery_shortcode( $output, $attr, $instance ) {
	$post = get_post();

	if ( $output != '' ) {
		return $output;
	}

	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( ! $attr['orderby'] ) {
			unset($attr['orderby']);
		}
	}

	extract( shortcode_atts( array(
		'order'			=> 'ASC',
		'orderby'		=> 'menu_order ID',
		'id'			=> $post->ID,
		'itemtag'		=> '',
		'icontag'		=> '',
		'captiontag'	=> '',
		'columns'		=> 3,
		'size'			=> 'thumbnail',
		'include'		=> '',
		'exclude'		=> '',
		'link'			=> '',
	), $attr ) );

	$id = absint( $id );
	$columns = ( 12 % $columns == 0 ) ? $columns: 4;
	$xs_columns = 1;
	if ( $columns % 2 == 0 ) {
		$xs_columns = $columns / 2;
	}
	$grid = sprintf( 'col-xs-%2$s col-sm-%1$s col-lg-%1$s', 12 / $columns, 12 / $xs_columns);

	if ( $order === 'RAND' ) {
		$orderby = 'none';
	}

	if ( ! empty( $include ) ) {
		$_attachments = get_posts( array(
			'include'			=> $include,
			'post_status'		=> 'inherit',
			'post_type'			=> 'attachment',
			'post_mime_type'	=> 'image',
			'order'				=> $order,
			'orderby'			=> $orderby,
		) );

		$attachments = array();
		foreach ( $_attachments as $key => $val )
		{
			$attachments[$val->ID] = $_attachments[$key];
		}
	} elseif ( ! empty( $exclude ) ) {
		$attachments = get_children( array(
			'post_parent'		=> $id,
			'exclude'			=> $exclude,
			'post_status'		=> 'inherit',
			'post_type'			=> 'attachment',
			'post_mime_type'	=> 'image',
			'order'				=> $order,
			'orderby'			=> $orderby,
		) );
	} else {
		$attachments = get_children( array(
			'post_parent'		=> $id,
			'post_status'		=> 'inherit',
			'post_type'			=> 'attachment',
			'post_mime_type'	=> 'image',
			'order'				=> $order,
			'orderby'			=> $orderby,
		) );
	}

	if ( empty( $attachments ) ) {
		return '';
	}

	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $att_id => $attachment )
		{
			$output .= wp_get_attachment_link( $att_id, $size, true ) . "\n";
		}
		return $output;
	}

	add_filter( 'wp_get_attachment_link', 'srctheme_fix_attachment_link', 10, 6 );

	$unique = ( get_query_var( 'page' ) ) ? $instance . '-p' . get_query_var( 'page' ): $instance;
	$output = '<div id="gallery-' . $id . '-' . $unique . '" class="gallery">';

	$i = 0;
	foreach ( $attachments as $id => $attachment ) {
		switch ( $link ) {
			case 'file':
				$image = wp_get_attachment_link( $id, $size, false, false );
				break;
			case 'none':
				$image = wp_get_attachment_image( $id, $size, false, array( 'class' => 'img-thumbnail' ) );
				break;
			default:
				$image = wp_get_attachment_link( $id, $size, true, false );
		}
		$output .= ( $i % $columns == 0 ) ? '<div class="row gallery-row">' : '';
		$output .= '<div class="' . $grid .' text-xs-center">' . $image;

		if ( trim( $attachment->post_excerpt ) != '' ) {
			$output .= '<div class="caption hidden">' . wptexturize( $attachment->post_excerpt ) . '</div>';
		}

		$output .= '</div>';
		$i++;
		$output .= ( $i % $columns == 0 ) ? '</div>' : '';
	}

	$output .= ( $i % $columns != 0 ) ? '</div>' : '';
	$output .= '</div>';

	remove_filter( 'wp_get_attachment_link', 'srctheme_fix_attachment_link', 10, 6 );

	return $output;
}
add_filter( 'post_gallery', 'srctheme_gallery_shortcode', 10, 3 );
add_filter( 'use_default_gallery_style', '__return_null' );

function srctheme_fix_attachment_link( $html, $id ) {
	$html = str_replace( '<a', '<a title="' . get_the_title( $id ) . '"', $html );
	$html = str_replace( '<a', '<a class="img-thumbnail"', $html );
	return $html;
}

final class SRCTheme_Walker_Nav_Menu extends Walker_Nav_Menu {
	private static $li_class = '';
	private static $a_class = '';

	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$html = '';

		parent::start_lvl( $html, $depth, $args );

		$html = str_replace( 'sub-menu', 'dropdown-menu', $html );

		$output .= $html;
	}

	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$html = '';

		parent::start_el( $html, $item, $depth, $args );

		if ( $item->is_dropdown && ( $depth === 0 ) ) {
			if ( false !== strpos( $html, '<a class="' ) ) {
				$html = str_replace( '<a class="', '<a data-toggle="dropdown" data-target="#" class="dropdown-toggle ', $html );
			} else {
				$html = str_replace( '<a', '<a class="dropdown-toggle" data-toggle="dropdown" data-target="#"', $html );
			}
			$html = str_replace( '</a>', ' <b class="caret"></b></a>', $html );
		} elseif( stristr( $html, 'li class="divider' ) ) {
			$html = preg_replace( '/<a[^>]*>.*?<\/a>/iU', '', $html );
		} elseif( stristr( $html, 'li class="dropdown-header' ) ) {
			$html = preg_replace( '/<a[^>]*>(.*)<\/a>/iU', '$1', $html );
		}

		$output .= $html;
	}

	public function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output ) {
		$element->is_dropdown = ( ( !empty( $children_elements[ $element->ID ] ) && ( ( $depth + 1 ) < $max_depth || ( $max_depth === 0 ) ) ) );

		if ( $element->is_dropdown ) {
			$element->classes[] = 'dropdown';
		}

		parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}

	public static function render( $args = array() ) {
		if ( ! isset( $args['container'] ) ) {
			$args['container'] = false;
		}
		if ( ! isset( $args['items_wrap'] ) ) {
			$args['items_wrap'] = '<ul class="%2$s">%3$s</ul>';
		}

		if ( isset( $args['menu_class'] ) ) {
			$menu_classes = explode( ' ', $args['menu_class'] );

			if ( in_array( 'list-inline', $menu_classes, true ) ) {
				$args['li_class'] = 'list-inline-item';
				$args['depth'] = 1;
			} elseif ( in_array( 'navbar-nav', $menu_classes, true ) ) {
				$args['li_class'] = 'nav-item';
				$args['a_class'] = 'nav-link';
			}
		}

		$args['walker'] = new self();

		if ( isset( $args['li_class'] ) ) {
			self::$li_class = $args['li_class'];
			unset( $args['li_class'] );
		}
		if ( isset( $args['a_class'] ) ) {
			self::$a_class = $args['a_class'];
			unset( $args['a_class'] );
		}

		$output = wp_nav_menu( $args );

		self::$li_class = '';
		self::$a_class = '';

		return $output;
	}

	public static function init() {
		add_filter( 'nav_menu_css_class', array( __CLASS__, 'fix_css_classes' ), 10, 4 );
		add_filter( 'nav_menu_link_attributes', array( __CLASS__, 'fix_link_attributes' ), 10, 4 );
		add_filter( 'nav_menu_item_id', '__return_null' );
	}

	public static function fix_css_classes( $classes, $item, $args, $depth ) {
		$slug = sanitize_title( $item->title );

		$classes = preg_replace( '/(current(-menu-|[-_]page[-_])(item|parent|ancestor))/', 'active', $classes );
		$classes = preg_replace( '/^((menu|page)[-_\w+]+)+/', '', $classes );
		$classes[] = 'menu-' . $slug;

		if ( ! empty( self::$li_class ) ) {
			$classes[] = self::$li_class;
		}

		$classes = array_unique( $classes );

		return array_filter( $classes, array( __CLASS__, 'is_class_valid' ) );
	}

	public static function fix_link_attributes( $atts, $item, $args, $depth ) {
		if ( ! empty( self::$a_class ) ) {
			$atts = array_merge( array( 'class' => self::$a_class ), $atts );
		}

		return $atts;
	}

	public static function is_class_valid( $class ) {
		$class = trim( $class );

		return empty( $class ) ? false : true;
	}
}

SRCTheme_Walker_Nav_Menu::init();
