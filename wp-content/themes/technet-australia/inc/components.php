<?php
/**
 * PHP ports of the design system's component set (components/core/*.jsx and
 * components/forms/*.jsx). Each function mirrors the JSX component's props
 * as closely as PHP allows and renders the same class names defined in
 * assets/css/components.css. Only components used by a current page
 * template are ported — see assets/css/components.css header comment.
 *
 * @package TechNet_Australia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Button — ports components/core/button/Button.jsx.
 *
 * @param array $args {
 *     @type string $label   Visible text (required).
 *     @type string $variant primary|accent|secondary|ghost. Default 'primary'.
 *     @type string $size    sm|md|lg. Default 'md'.
 *     @type string $href    If set, renders an <a> styled as a button instead of <button>.
 *     @type string $type    Button type attribute when not a link. Default 'button'.
 *     @type string $name    Form field name (buttons used as submits).
 *     @type bool   $disabled
 *     @type string $class   Extra class names.
 * }
 * @return string
 */
function technet_button( $args ) {
	$args = wp_parse_args(
		$args,
		array(
			'label'    => '',
			'variant'  => 'primary',
			'size'     => 'md',
			'href'     => '',
			'type'     => 'button',
			'name'     => '',
			'disabled' => false,
			'class'    => '',
		)
	);

	$classes = array( 'tn-btn', 'tn-btn--' . $args['variant'] );
	if ( 'md' !== $args['size'] ) {
		$classes[] = 'tn-btn--' . $args['size'];
	}
	if ( $args['class'] ) {
		$classes[] = $args['class'];
	}
	$class_attr = esc_attr( implode( ' ', $classes ) );

	if ( $args['href'] ) {
		return sprintf(
			'<a href="%1$s" class="%2$s"%3$s>%4$s</a>',
			esc_url( $args['href'] ),
			$class_attr,
			$args['disabled'] ? ' aria-disabled="true"' : '',
			esc_html( $args['label'] )
		);
	}

	return sprintf(
		'<button type="%1$s" class="%2$s"%3$s%4$s>%5$s</button>',
		esc_attr( $args['type'] ),
		$class_attr,
		$args['name'] ? ' name="' . esc_attr( $args['name'] ) . '"' : '',
		$args['disabled'] ? ' disabled' : '',
		esc_html( $args['label'] )
	);
}

/**
 * Card — ports components/core/card/Card.jsx. Content is passed in already
 * escaped/rendered (it's a layout wrapper, not a form field), matching how
 * Card.jsx just wraps arbitrary children.
 *
 * @param string $content Inner HTML.
 * @param array  $args    { @type bool $flush, @type bool $row, @type string $class }.
 * @return string
 */
function technet_card( $content, $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'flush' => false,
			'row'   => false,
			'class' => '',
		)
	);

	$classes = array( 'tn-card' );
	if ( $args['flush'] ) {
		$classes[] = 'tn-card--flush';
	}
	if ( $args['row'] ) {
		$classes[] = 'tn-card--row';
	}
	if ( $args['class'] ) {
		$classes[] = $args['class'];
	}

	return sprintf(
		'<div class="%1$s">%2$s</div>',
		esc_attr( implode( ' ', $classes ) ),
		$content
	);
}

/**
 * Media card — image-topped Card variant (not in the original design
 * system; added for the site's image-rich direction, see
 * docs/design-changelog.md). Renders a placeholder block instead of the
 * image when $image_url is empty, so a post/speaker without a featured
 * image yet still lays out cleanly rather than showing a broken image.
 *
 * @param string $image_url
 * @param string $image_alt
 * @param string $body_html Already-escaped/rendered inner HTML for the card body.
 * @param array  $args      { @type string $class }
 * @return string
 */
function technet_media_card( $image_url, $image_alt, $body_html, $args = array() ) {
	$args    = wp_parse_args( $args, array( 'class' => '' ) );
	$classes = trim( 'tn-card--media ' . $args['class'] );

	$content = $image_url
		? sprintf(
			'<img class="tn-card__image" src="%1$s" alt="%2$s" loading="lazy">',
			esc_url( $image_url ),
			esc_attr( $image_alt )
		)
		: '<div class="tn-card__image tn-card__image--placeholder"></div>';
	$content .= '<div class="tn-card__body">' . $body_html . '</div>';

	return technet_card( $content, array( 'flush' => true, 'class' => $classes ) );
}

/**
 * Badge — ports components/core/badge/Badge.jsx.
 *
 * @param string $label
 * @param string $tone neutral|success|warning|error|info. Default 'neutral'.
 * @return string
 */
function technet_badge( $label, $tone = 'neutral' ) {
	$classes = array( 'tn-badge' );
	if ( 'neutral' !== $tone ) {
		$classes[] = 'tn-badge--' . $tone;
	}
	return sprintf(
		'<span class="%1$s">%2$s</span>',
		esc_attr( implode( ' ', $classes ) ),
		esc_html( $label )
	);
}

/**
 * Tag — ports components/core/tag/Tag.jsx.
 *
 * @param string $label
 * @return string
 */
function technet_tag( $label ) {
	return sprintf( '<span class="tn-tag">%s</span>', esc_html( $label ) );
}

/**
 * Input — ports components/forms/input/Input.jsx.
 *
 * @param array $args {
 *     @type string $label, $name, $type, $placeholder, $value, $error, $id
 *     @type bool   $required, $disabled
 * }
 * @return string
 */
function technet_input( $args ) {
	$args = wp_parse_args(
		$args,
		array(
			'label'       => '',
			'name'        => '',
			'type'        => 'text',
			'placeholder' => '',
			'value'       => '',
			'error'       => '',
			'id'          => '',
			'required'    => false,
			'disabled'    => false,
		)
	);

	$id = $args['id'] ? $args['id'] : 'tn-field-' . sanitize_html_class( $args['name'] );

	$html  = '<label class="tn-field" for="' . esc_attr( $id ) . '">';
	if ( $args['label'] ) {
		$html .= '<span>' . esc_html( $args['label'] ) . ( $args['required'] ? ' *' : '' ) . '</span>';
	}
	$html .= sprintf(
		'<input type="%1$s" id="%2$s" name="%3$s" placeholder="%4$s" value="%5$s" class="tn-input%6$s"%7$s%8$s>',
		esc_attr( $args['type'] ),
		esc_attr( $id ),
		esc_attr( $args['name'] ),
		esc_attr( $args['placeholder'] ),
		esc_attr( $args['value'] ),
		$args['error'] ? ' tn-input--error' : '',
		$args['required'] ? ' required' : '',
		$args['disabled'] ? ' disabled' : ''
	);
	if ( $args['error'] ) {
		$html .= '<span class="tn-field__error">' . esc_html( $args['error'] ) . '</span>';
	}
	$html .= '</label>';

	return $html;
}

/**
 * Select — ports components/forms/select/Select.jsx.
 *
 * @param array $args {
 *     @type string $label, $name, $value, $id
 *     @type array  $options Associative value=>label, or plain list (value==label).
 *     @type bool   $required, $disabled
 * }
 * @return string
 */
function technet_select( $args ) {
	$args = wp_parse_args(
		$args,
		array(
			'label'    => '',
			'name'     => '',
			'value'    => '',
			'id'       => '',
			'options'  => array(),
			'required' => false,
			'disabled' => false,
		)
	);

	$id = $args['id'] ? $args['id'] : 'tn-field-' . sanitize_html_class( $args['name'] );

	$html  = '<label class="tn-field" for="' . esc_attr( $id ) . '">';
	if ( $args['label'] ) {
		$html .= '<span>' . esc_html( $args['label'] ) . ( $args['required'] ? ' *' : '' ) . '</span>';
	}
	$html .= sprintf(
		'<select id="%1$s" name="%2$s" class="tn-select"%3$s%4$s>',
		esc_attr( $id ),
		esc_attr( $args['name'] ),
		$args['required'] ? ' required' : '',
		$args['disabled'] ? ' disabled' : ''
	);
	foreach ( $args['options'] as $value => $label ) {
		if ( is_int( $value ) ) {
			$value = $label;
		}
		$html .= sprintf(
			'<option value="%1$s"%2$s>%3$s</option>',
			esc_attr( $value ),
			selected( $args['value'], $value, false ),
			esc_html( $label )
		);
	}
	$html .= '</select></label>';

	return $html;
}

/**
 * Radio — ports components/forms/radio/Radio.jsx.
 *
 * @param array $args { @type string $label, $name, $value, $id @type bool $checked, $disabled }
 * @return string
 */
function technet_radio( $args ) {
	return technet_choice_input( 'radio', $args );
}

/**
 * Checkbox — ports components/forms/checkbox/Checkbox.jsx.
 *
 * @param array $args { @type string $label, $name, $value, $id @type bool $checked, $disabled }
 * @return string
 */
function technet_checkbox( $args ) {
	return technet_choice_input( 'checkbox', $args );
}

/**
 * Shared renderer for Radio/Checkbox, which are identical in the design
 * system apart from the `type` attribute.
 *
 * @param string $type 'radio'|'checkbox'.
 * @param array  $args
 * @return string
 */
function technet_choice_input( $type, $args ) {
	$args = wp_parse_args(
		$args,
		array(
			'label'    => '',
			'name'     => '',
			'value'    => '1',
			'id'       => '',
			'checked'  => false,
			'disabled' => false,
		)
	);

	$id = $args['id'] ? $args['id'] : 'tn-field-' . sanitize_html_class( $args['name'] . '-' . $args['value'] );

	return sprintf(
		'<label class="tn-choice%1$s" for="%2$s"><input type="%3$s" id="%2$s" name="%4$s" value="%5$s"%6$s%7$s>%8$s</label>',
		$args['disabled'] ? ' tn-choice--disabled' : '',
		esc_attr( $id ),
		esc_attr( $type ),
		esc_attr( $args['name'] ),
		esc_attr( $args['value'] ),
		checked( $args['checked'], true, false ),
		$args['disabled'] ? ' disabled' : '',
		esc_html( $args['label'] )
	);
}
