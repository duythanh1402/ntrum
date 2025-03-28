<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VI_SCT_SALES_COUNTDOWN_TIMER_Frontend_Shortcode {
	protected $settings;
	public static $cache = array();

	public function __construct() {
		$this->settings = new VI_SCT_SALES_COUNTDOWN_TIMER_Data();
		/*Register scripts*/
		add_action( 'init', array( $this, 'shortcode_init' ) );
		add_action( 'wp_print_styles', array( $this, 'sctv_countdown_css' ) );
	}

	public function sctv_countdown_css() {
		$css = VI_SCT_SALES_COUNTDOWN_TIMER_Countdown_Style::get_frontend_countdown_css();
		if ( $css ) {
			echo '<style id="woo-sctr-frontend-countdown-style" type="text/css">' . esc_attr( $css ) . '</style>';
		}
	}

	public function shortcode_init() {
		if ( ! empty( $_GET['sctv_countdown_job'] ) ) {
			if ( wp_safe_redirect( @remove_query_arg( 'sctv_countdown_job' ) ) ) {
				exit();
			}
		}
		add_shortcode( 'sales_countdown_timer', array( $this, 'register_shortcode' ) );
		add_shortcode( 'sales_countdown_timer_list', array( $this, 'register_shortcode_list' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'shortcode_enqueue_script' ) );
	}

	public function register_shortcode( $atts ) {
		extract( shortcode_atts( array(
			'type'                          => '',
			'id'                            => '',
			'product_id'                    => '',
			'is_elementor'                  => 0,
			'name'                          => '',
			'active'                        => '',
			'message'                       => '',
			'message_position'              => '',
			'sale_from_date'                => '',
			'sale_to_date'                  => 0,
			'sale_from_time'                => 0,
			'sale_to_time'                  => 0,
			'sale_from'                     => 0,
			'sale_to'                       => 0,
			'checkout_time_minute'          => 0,
			'checkout_time_second'          => 0,
			'checkout_to_time'              => 0,
			'time_units'                    => '',
			'time_separator'                => '',
			'datetime_format'               => '',
			'datetime_format_custom_date'   => '',
			'datetime_format_custom_hour'   => '',
			'datetime_format_custom_minute' => '',
			'datetime_format_custom_second' => '',
			'animation_style'               => '',
			'layout_style'                  => '',
			'display_type'                  => '',
			'resize_archive_page_enable'    => '',
			'checkout_inline'               => '',
			'sale_countdown_timer_id_t'     => '',
			'countdown_time_reset'          => '',
		), $atts ) );
		if ( ! $id ) {
			return false;
		}
		$this->settings = new VI_SCT_SALES_COUNTDOWN_TIMER_Data();
		$index          = array_search( $id, $this->settings->get_params( 'sale_countdown_id' ) );
		if ( $index === false ) {
			return false;
		}
		global $sale_countdown_timer_count;
		$sale_countdown_timer_count ++;
		$sale_countdown_timer_id = $sale_countdown_timer_id_t ? $sale_countdown_timer_id_t . '_' . $sale_countdown_timer_count : $sale_countdown_timer_count;
		$now                     = current_time( 'timestamp' );
		$text_before             = $text_after = '';
		$day                     = $hour = $minute = $second = '';
		$continue                = true;
		$language                = self::get_language();
		switch ( $type ) {
			case 'checkout':
				if ( ! $this->settings->get_params( 'checkout_countdown_enable' ) ) {
					$continue = false;
					break;
				}
				if ( ! $checkout_to_time || $checkout_to_time < $now ) {
					$continue = false;
					break;
				}
				$end_time             = (int) $checkout_to_time - $now;
				$text                 = $message;
				$countdown_time_reset = '';
				break;
			case 'product':
			    if ( isset( $is_elementor ) && 1 == $is_elementor ) {
			        if ( '' == $product_id ) {
			            global $product;
			            if ( $product ) {
				            $product_id = $product->get_id();
				            $offset         = get_option( 'gmt_offset' );
				            $sale_from      = ( $product->get_date_on_sale_from( 'edit' ) ) ? ( $product->get_date_on_sale_from( 'edit' )->getTimestamp() + $offset * 3600 ) : 0;
				            $sale_to        = ( $product->get_date_on_sale_to( 'edit' ) ) ? ( $product->get_date_on_sale_to( 'edit' )->getTimestamp() + $offset * 3600 ) : 0;
                        }
                    }
                }
				if ( ! $product_id ) {
					$continue = false;
					break;
				}
				$active = $active ? $active : $this->settings->get_current_countdown( 'sale_countdown_active', $index );
				if ( ! $active ) {
					$continue = false;
					break;
				}
				if ( $term_countdown = get_transient( 'sales_countdown_timer_params_product_' . $product_id ) ) {
					$sale_from = $term_countdown['sale_countdown_fom'] ?? 0;
					$sale_to   = $term_countdown['sale_countdown_to'] ?? 0;
					delete_transient( 'sales_countdown_timer_params_product_' . $product_id );
				}
				$sale_from = (int) $sale_from;
				$sale_to   = (int) $sale_to;
				if ( $sale_to < $sale_from ) {
					$continue = false;
					break;
				}
				if ( $sale_from > $now ) {
					if ( $this->settings->get_current_countdown( 'sale_countdown_upcoming_enable', $index ) ) {
						$end_time           = $sale_from - $now;
						$text               = $this->settings->get_current_countdown_language( 'sale_countdown_upcoming_message', '_' . $language, $index );
						$countdown_time_end = $sale_from;
					} else {
						$continue = false;
						break;
					}
				} else {
					if ( $sale_to < $now ) {
						$continue = false;
						break;
					}
					$end_time           = $sale_to - $now;
					$countdown_time_end = $sale_to;
					$text               = $message ? $message : $this->settings->get_current_countdown_language( 'sale_countdown_message', '_' . $language, $index );
				}
				if ( is_numeric( $countdown_time_reset ) ) {
					$countdown_time_from = date( 'Y-m-d H:i:s', $sale_from );
					$countdown_time_to   = date( 'Y-m-d H:i:s', $sale_to );
				}
				if ( $product = wc_get_product( $product_id ) ) {
					$regular_price         = $product->get_regular_price();
					$sale_price            = $product->get_sale_price();
					$price_suffix          = $product->get_price_suffix();
					$product_type          = $product->get_type();
					$countdown_data_change = array(
						'upcoming_enable'    => $this->settings->get_current_countdown( 'sale_countdown_upcoming_enable', $index ),
						'upcoming_message'   => $this->settings->get_current_countdown_language( 'sale_countdown_upcoming_message', '_' . $language, $index ),
						'message'            => $message ? $message : $this->settings->get_current_countdown_language( 'sale_countdown_message', '_' . $language, $index ),
						'product_type'       => $product_type,
						'remove_sale'        => $sale_from < $now,
						'product_id'         => $product_id,
						'regular_price_html' => wc_price( wc_get_price_to_display( $product, array( 'price' => $regular_price ) ) ) . $price_suffix,
						'sale_price_html'    => wc_format_sale_price( wc_get_price_to_display( $product, array( 'price' => $regular_price ) ), wc_get_price_to_display( $product, array( 'price' => $sale_price ) ) ) . $price_suffix,
					);
					$countdown_data_change = wp_json_encode( $countdown_data_change );
					$countdown_data_change = function_exists( 'wc_esc_json' ) ? wc_esc_json( $countdown_data_change ) : _wp_specialchars( $countdown_data_change, ENT_QUOTES, 'UTF-8', true );
				}
				break;
			default:
				$active = $active ? $active : $this->settings->get_current_countdown( 'sale_countdown_active', $index );
				if ( ! $active ) {
					$continue = false;
					break;
				}
				$sale_from = $sale_to = 0;
				if ( $term_countdown = get_transient( 'sales_countdown_timer_params_' . $id ) ) {
					$sale_from = $term_countdown['sale_countdown_fom'] ?? 0;
					$sale_to   = $term_countdown['sale_countdown_to'] ?? 0;
					delete_transient( 'sales_countdown_timer_params_' . $id );
				}
				if ( ! $sale_from && ! $sale_to ) {
					$sale_from_date = $sale_from_date ? $sale_from_date : $this->settings->get_current_countdown( 'sale_countdown_fom_date', $index );
					$sale_to_date   = $sale_to_date ? $sale_to_date : $this->settings->get_current_countdown( 'sale_countdown_to_date', $index );
					$sale_from_time = $sale_from_time ? $sale_from_time : $this->settings->get_current_countdown( 'sale_countdown_fom_time', $index );
					$sale_to_time   = $sale_to_time ? $sale_to_time : $this->settings->get_current_countdown( 'sale_countdown_to_time', $index );
					$sale_from_date = strtotime( $sale_from_date );
					$sale_to_date   = strtotime( $sale_to_date );
					$sale_from_time = woo_ctr_time( $sale_from_time );
					$sale_to_time   = woo_ctr_time( $sale_to_time );
					$sale_from      = $sale_from_date + $sale_from_time;
					$sale_to        = $sale_to_date + $sale_to_time;
				}
				$sale_from = (int) $sale_from;
				$sale_to   = (int) $sale_to;
				if ( $sale_to < $sale_from ) {
					$continue = false;
					break;
				}
				if ( $sale_from > $now || $sale_to < $now ) {
					$continue = false;
				}
				$end_time = $sale_to - $now;
				$text     = $message ? $message : $this->settings->get_current_countdown_language( 'sale_countdown_message', '_' . $language, $index );
				if ( ! $countdown_time_reset && $this->settings->get_current_countdown( 'sale_countdown_loop_enable', $index ) ) {
					$countdown_time_reset = (int) $this->settings->get_current_countdown( 'sale_countdown_loop_time_val', $index );
					if ( $countdown_time_reset ) {
						$countdown_time_reset = $this->get_loop_time_val( $countdown_time_reset, $this->settings->get_current_countdown( 'sale_countdown_loop_time_type', $index ) );
					}
				}
				if ( is_numeric( $countdown_time_reset ) ) {
					$countdown_time_from = date( 'Y-m-d H:i:s', $sale_from );
					$countdown_time_to   = date( 'Y-m-d H:i:s', $sale_to );
				}
		}
		if ( ! $continue || empty( $text ) ) {
			return false;
		}
		/*message*/
		$text = explode( '{countdown_timer}', $text );
		if ( count( $text ) < 2 ) {
			return false;
		}
		$text_before                   = $text[0];
		$text_after                    = $text[1];
		$message_position              = $message_position ? $message_position : $this->settings->get_current_countdown( 'sale_countdown_message_position', $index );
		$time_units                    = $time_units ? $time_units : $this->settings->get_current_countdown( 'sale_countdown_time_units', $index );
		$datetime_format               = $datetime_format ? $datetime_format : $this->settings->get_current_countdown( 'sale_countdown_datetime_format', $index );
		$datetime_format_custom_date   = $datetime_format_custom_date ? $datetime_format_custom_date : $this->settings->get_current_countdown_language( 'sale_countdown_datetime_format_custom_date', '_' . $language, $index );
		$datetime_format_custom_hour   = $datetime_format_custom_hour ? $datetime_format_custom_hour : $this->settings->get_current_countdown_language( 'sale_countdown_datetime_format_custom_hour', '_' . $language, $index );
		$datetime_format_custom_minute = $datetime_format_custom_minute ? $datetime_format_custom_minute : $this->settings->get_current_countdown_language( 'sale_countdown_datetime_format_custom_minute', '_' . $language, $index );
		$datetime_format_custom_second = $datetime_format_custom_second ? $datetime_format_custom_second : $this->settings->get_current_countdown_language( 'sale_countdown_datetime_format_custom_second', '_' . $language, $index );
		$time_separator                = $time_separator ? $time_separator : $this->settings->get_current_countdown( 'sale_countdown_time_separator', $index );
		$animation_style               = $animation_style ? $animation_style : $this->settings->get_current_countdown( 'sale_countdown_animation_style', $index );
		$layout_style                  = $this->settings->get_current_countdown( 'sale_countdown_layout', $index );
		$display_type                  = $display_type ? $display_type : $this->settings->get_current_countdown( 'sale_countdown_display_type', $index );
//		if ( ! wp_script_is( 'woo-sctr-shortcode-countdown-script', 'enqueued' ) ) {
//			wp_enqueue_script( 'woo-sctr-shortcode-countdown-script' );
//		}
//		if ( ! wp_style_is( 'woo-sctr-shortcode-countdown-style', 'enqueued' ) ) {
//			wp_enqueue_style( 'woo-sctr-shortcode-countdown-style' );
//		}
		$end_time = (int) $end_time - 1;
		if ( $end_time ) {
			$day_left  = (int) floor( $end_time / 86400 );
			$hour_left = (int) floor( ( $end_time - 86400 * $day_left ) / 3600 );
			$min_left  = (int) floor( ( $end_time - 86400 * $day_left - 3600 * $hour_left ) / 60 );
			$sec_left  = $end_time - 86400 * $day_left - 3600 * $hour_left - 60 * $min_left;
		} else {
			$day_left = $hour_left = $min_left = $sec_deg = 0;
		}
		$time_units_arg    = ( ! $time_units || $display_type === '7' || in_array( $type, [
				'checkout',
				'product'
			] ) ) ? array() : explode( ',', $time_units );
		$unit_day_class    = array( 'woo-sctr-countdown-unit-wrap woo-sctr-countdown-date-wrap' );
		$unit_hour_class   = array( 'woo-sctr-countdown-unit-wrap woo-sctr-countdown-hour-wrap' );
		$unit_minute_class = array( 'woo-sctr-countdown-unit-wrap woo-sctr-countdown-minute-wrap' );
		$unit_second_class = array( 'woo-sctr-countdown-unit-wrap woo-sctr-countdown-second-wrap' );
		if ( $day_left === 0 ) {
			$time_units_arg = array( 'hour', 'minute', 'second' );
			if ( $hour_left === 0 ) {
				$t_hour = array_search( 'hour', $time_units_arg );
				if ( $t_hour !== false ) {
					unset( $time_units_arg[ $t_hour ] );
				}
			}
		}
		if ( count( $time_units_arg ) ) {
			if ( ! in_array( 'day', $time_units_arg ) ) {
				$hour_left        = $hour_left + 24 * $day_left;
				$unit_day_class[] = 'woo-sctr-countdown-hidden';
			}
			if ( ! in_array( 'hour', $time_units_arg ) ) {
				$min_left          = $hour_left * 60 + $min_left;
				$unit_hour_class[] = 'woo-sctr-countdown-hidden';
			}
			if ( ! in_array( 'minute', $time_units_arg ) ) {
				$sec_left            = $min_left * 60 + $sec_left;
				$unit_minute_class[] = 'woo-sctr-countdown-hidden';
			}
			if ( ! in_array( 'second', $time_units_arg ) ) {
				$unit_second_class[] = 'woo-sctr-countdown-hidden';
			}
		}
		$day_left_t  = zeroise( $day_left, 2 );
		$hour_left_t = zeroise( $hour_left, 2 );
		$min_left_t  = zeroise( $min_left, 2 );
		$sec_left_t  = zeroise( $sec_left, 2 );
		if ( $animation_style === 'default' ) {
			$sec_left_t = zeroise( $sec_left === 59 ? 0 : $sec_left + 1, 2 );
			if ( $sec_left === 59 ) {
				$min_left_t = zeroise( $min_left === 59 ? 0 : $min_left + 1, 2 );
			}
		}
		$day_deg  = $day_left;
		$hour_deg = $hour_left * 15;
		$min_deg  = $min_left * 6;
		$sec_deg  = ( $sec_left + 1 ) * 6;
		$css      = '';
		switch ( $display_type ) {
			case '4':
				/*set circle fill*/
				$css .= '.woo-sctr-shortcode-countdown-timer-wrap-' . $sale_countdown_timer_id . ' .woo-sctr-countdown-timer-4 .woo-sctr-countdown-date-value-wrap .woo-sctr-value-bar {' . esc_attr__( 'transform: rotate(' ) . $day_deg . 'deg);}';
				$css .= '.woo-sctr-shortcode-countdown-timer-wrap-' . $sale_countdown_timer_id . ' .woo-sctr-countdown-timer-4 .woo-sctr-countdown-hour-value-wrap .woo-sctr-value-bar {' . esc_attr__( 'transform: rotate(' ) . $hour_deg . 'deg);}';
				$css .= '.woo-sctr-shortcode-countdown-timer-wrap-' . $sale_countdown_timer_id . ' .woo-sctr-countdown-timer-4 .woo-sctr-countdown-minute-value-wrap .woo-sctr-value-bar {' . esc_attr__( 'transform: rotate(' ) . $min_deg . 'deg);}';
				$css .= '.woo-sctr-shortcode-countdown-timer-wrap-' . $sale_countdown_timer_id . ' .woo-sctr-countdown-timer-4 .woo-sctr-countdown-second-value-wrap .woo-sctr-value-bar {' . esc_attr__( 'transform: rotate(' ) . $sec_deg . 'deg);}';
				break;
			case '5':
				/*set circle fill*/
				$css .= '.woo-sctr-shortcode-countdown-timer-wrap-' . $sale_countdown_timer_id . ' .woo-sctr-countdown-timer-5 .woo-sctr-countdown-date .woo-sctr-value-bar {' . esc_attr__( 'transform: rotate(' ) . $day_deg . 'deg);}';
				$css .= '.woo-sctr-shortcode-countdown-timer-wrap-' . $sale_countdown_timer_id . ' .woo-sctr-countdown-timer-5 .woo-sctr-countdown-hour .woo-sctr-value-bar {' . esc_attr__( 'transform: rotate(' ) . $hour_deg . 'deg);}';
				$css .= '.woo-sctr-shortcode-countdown-timer-wrap-' . $sale_countdown_timer_id . ' .woo-sctr-countdown-timer-5 .woo-sctr-countdown-minute .woo-sctr-value-bar {' . esc_attr__( 'transform: rotate(' ) . $min_deg . 'deg);}';
				$css .= '.woo-sctr-shortcode-countdown-timer-wrap-' . $sale_countdown_timer_id . ' .woo-sctr-countdown-timer-5 .woo-sctr-countdown-second .woo-sctr-value-bar {' . esc_attr__( 'transform: rotate(' ) . $sec_deg . 'deg);}';
				break;
		}
		wp_add_inline_style( 'woo-sctr-shortcode-countdown-style', $css );
		/*datetime format*/
		switch ( $datetime_format ) {
			case 1:
				$day    = esc_html__( 'days', 'sales-countdown-timer' );
				$hour   = esc_html__( 'hrs', 'sales-countdown-timer' );
				$minute = esc_html__( 'mins', 'sales-countdown-timer' );
				$second = esc_html__( 'secs', 'sales-countdown-timer' );
				break;
			case 2:
				$day    = esc_html__( 'days', 'sales-countdown-timer' );
				$hour   = esc_html__( 'hours', 'sales-countdown-timer' );
				$minute = esc_html__( 'minutes', 'sales-countdown-timer' );
				$second = esc_html__( 'seconds', 'sales-countdown-timer' );
				break;
			case 3:
				$day    = '';
				$hour   = '';
				$minute = '';
				$second = '';
				break;
			case 4:
				$day    = esc_html__( 'd', 'sales-countdown-timer' );
				$hour   = esc_html__( 'h', 'sales-countdown-timer' );
				$minute = esc_html__( 'm', 'sales-countdown-timer' );
				$second = esc_html__( 's', 'sales-countdown-timer' );
				break;
			case '#other':
				$day    = $datetime_format_custom_date;
				$hour   = $datetime_format_custom_hour;
				$minute = $datetime_format_custom_minute;
				$second = $datetime_format_custom_second;
				break;
		}
		switch ( $time_separator ) {
			case 'colon':
				$time_separator = ':';
				break;
			case 'comma':
				$time_separator = ',';
				break;
			case 'dot':
				$time_separator = '.';
				break;
			default:
				$time_separator = '';
		}
		if ( count( $time_units_arg ) !== 1 && $time_separator ) {
			$unit_day_class[] = 'woo-sctr-countdown-unit-wrap-two';
			if ( empty( $time_units_arg ) || in_array( 'minute', $time_units_arg ) || in_array( 'second', $time_units_arg ) ) {
				$unit_hour_class[] = 'woo-sctr-countdown-unit-wrap-two';
			}
			if ( empty( $time_units_arg ) || in_array( 'second', $time_units_arg ) ) {
				$unit_minute_class[] = 'woo-sctr-countdown-unit-wrap-two';
			}
		}
		$countdown_layout_class   = array( 'woo-sctr-countdown-timer-layout woo-sctr-layout-' . $layout_style );
		$countdown_layout_class[] = $checkout_inline ? 'woo-sctr-countdown-timer-layout-same-line' : '';
		$countdown_layout_class[] = $message_position === 'inline_countdown' ? 'woo-sctr-countdown-timer-layout-inline' : '';
		$countdown_layout_class   = trim( implode( ' ', $countdown_layout_class ) );
		$countdown_template_class = array(
			'woo-sctr-countdown-timer',
			'woo-sctr-countdown-timer-' . $display_type,
			'woo-sctr-shortcode-countdown-unit-animation-' . $animation_style,
		);
		switch ( count( $time_units_arg ) ) {
			case 1:
				$countdown_template_class[] = 'woo-sctr-shortcode-countdown-count-unit-grid-one';
				break;
			case 2:
				$countdown_template_class[] = 'woo-sctr-shortcode-countdown-count-unit-grid-two';
				break;
			case 3:
				$countdown_template_class[] = 'woo-sctr-shortcode-countdown-count-unit-grid-three';
				break;
			default:
				$countdown_template_class[] = 'woo-sctr-shortcode-countdown-count-unit-grid-four';
		}
		$countdown_template_class = trim( implode( ' ', $countdown_template_class ) );
		$div_container_class      = array(
			'woo-sctr-shortcode-countdown-timer-wrap',
			'woo-sctr-shortcode-countdown-timer-wrap-' . $sale_countdown_timer_id,
			'woo-sctr-shortcode-countdown-timer-wrap-shortcode-' . $id,
			'woo-sctr-shortcode-countdown-timer-wrap-type-' . ( $type ? $type : 'shortcode' ),
		);
		$div_container_class[]    = $resize_archive_page_enable ? 'woo-sctr-shortcode-countdown-timer-wrap-loop' : '';
		$div_container_class      = trim( implode( ' ', $div_container_class ) );
		$unit_day_class           = trim( implode( ' ', $unit_day_class ) );
		$unit_hour_class          = trim( implode( ' ', $unit_hour_class ) );
		$unit_minute_class        = trim( implode( ' ', $unit_minute_class ) );
		$unit_second_class        = trim( implode( ' ', $unit_second_class ) );
		$countdown_time_end       = ! empty( $countdown_time_end ) ? $countdown_time_end + 1 - ( get_option( 'gmt_offset' ) * 3600 ) : $now + $end_time + 1 - ( get_option( 'gmt_offset' ) * 3600 );
		ob_start();
		?>
        <div class="<?php echo esc_attr( $div_container_class ); ?>">
            <div class="<?php echo esc_attr( $countdown_layout_class ); ?>">
				<?php
				if ( $text_before ) {
					?>
                    <div class="woo-sctr-countdown-timer-text-wrap woo-sctr-countdown-timer-text-before-wrap">
                        <span class="woo-sctr-countdown-timer-text-before"><?php echo wp_kses_post( $text_before ); ?></span>
                    </div>
					<?php
				}
				echo sprintf( '<div class="woo-sctr-countdown-timer-wrap"><input type="hidden" class="woo-sctr-countdown-end-time" value="%s" data-countdown_data_change="%s" data-countdown_time_reset="%s" data-countdown_time_from="%s" data-countdown_time_to="%s" data-countdown_time_end="%s" data-countdown_time_start="%s">',
					esc_attr( $end_time ), esc_attr( $countdown_data_change ?? '' ), esc_attr( $countdown_time_reset ?? '' ), esc_attr( $countdown_time_from ?? '' ), esc_attr( $countdown_time_to ?? '' ), esc_attr( date( 'Y-m-d H:i:s', $countdown_time_end ) ), esc_attr( date( 'Y-m-d H:i:s', $now ) )
				);
				$args = array(
					'index'                    => $index,
					'settings'                 => $this->settings,
					'animation_style'          => $animation_style,
					'time_separator'           => $time_separator,
					'countdown_template_class' => $countdown_template_class,
					'unit_day_class'           => $unit_day_class,
					'day'                      => $day,
					'day_deg'                  => $day_deg,
					'day_left'                 => $day_left,
					'day_left_t'               => $day_left_t,
					'unit_hour_class'          => $unit_hour_class,
					'hour'                     => $hour,
					'hour_deg'                 => $hour_deg,
					'hour_left'                => $hour_left,
					'hour_left_t'              => $hour_left_t,
					'unit_minute_class'        => $unit_minute_class,
					'minute'                   => $minute,
					'min_deg'                  => $min_deg,
					'min_left'                 => $min_left,
					'min_left_t'               => $min_left_t,
					'unit_second_class'        => $unit_second_class,
					'second'                   => $second,
					'sec_deg'                  => $sec_deg,
					'sec_left'                 => $sec_left,
					'sec_left_t'               => $sec_left_t,
					'is_preview'               => false,
				);
				if ( function_exists( 'wc_get_template' ) ) {
					wc_get_template( 'countdown-' . $display_type . '.php', $args, 'sctv-sales-countdown-timer' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR,
						VI_SCT_SALES_COUNTDOWN_TIMER_TEMPLATES );
				} else {
					sctv_get_template( 'countdown-' . $display_type . '.php', $args, 'sctv-sales-countdown-timer' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR, VI_SCT_SALES_COUNTDOWN_TIMER_TEMPLATES );
				}
				echo sprintf( '</div>' );
				if ( $text_after ) {
					?>
                    <div class="woo-sctr-countdown-timer-text-wrap woo-sctr-countdown-timer-text-after-wrap">
                        <span class="woo-sctr-countdown-timer-text-after"><?php echo wp_kses_post( $text_after ); ?></span>
                    </div>
					<?php
				}
				?>
            </div>
        </div>
		<?php
		$html = ob_get_clean();
		$html = str_replace( "\n", '', $html );
		$html = str_replace( "\r", '', $html );
		$html = str_replace( "\t", '', $html );
		$html = str_replace( "\l", '', $html );
		$html = str_replace( "\0", '', $html );

		return ent2ncr( $html );
	}

	public function register_shortcode_list( $atts ) {
		global $pagenow;
		if ( ( $pagenow == 'post.php' ) || ( get_post_type() == 'post' ) ) {
            return;
		}
		extract( shortcode_atts( array(
			'products_per_page'             => 50,
		), $atts ) );

		if( ! function_exists('wc_get_products' ) ) {
			return;
		}

		$paged                   = (get_query_var('paged')) ? absint(get_query_var('paged')) : 1;
        $paged                   = isset( $_REQUEST['page'] ) ? intval( wc_clean( wp_unslash( $_REQUEST['page'] ) ) ) : $paged;
		$ordering                = WC()->query->get_catalog_ordering_args();
		$orderby_filter          = explode(' ', $ordering['orderby'] );
		$ordering['orderby']     = array_shift( $orderby_filter );
		$ordering['orderby']     = stristr($ordering['orderby'], 'price') ? 'meta_value_num' : $ordering['orderby'];
		$products_per_page       = isset( $products_per_page ) && ! empty( $products_per_page ) ? $products_per_page : apply_filters('loop_shop_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page());

		$products_ids            = wc_get_products(array(
			'status'               => 'publish',
			'limit'                => $products_per_page,
			'page'                 => $paged,
			'paginate'             => true,
			'return'               => 'ids',
			'orderby'              => $ordering['orderby'],
			'order'                => $ordering['order'],
		));

		wc_set_loop_prop('current_page', $paged);
		wc_set_loop_prop('is_paginated', wc_string_to_bool(true));
		wc_set_loop_prop('page_template', get_page_template_slug());
		wc_set_loop_prop('per_page', $products_per_page);
		wc_set_loop_prop('total', $products_ids->total);
		wc_set_loop_prop('total_pages', $products_ids->max_num_pages);
        ob_start();
		if( $products_ids ) {
			do_action('woocommerce_before_shop_loop' );
			woocommerce_product_loop_start();
			foreach( $products_ids->products as $featured_product ) {
				$post_product = wc_get_product( $featured_product );
				if ( $post_product ) {
					if ( $post_product->is_type( 'variable' ) ) {
						$featured_product_id = get_post_meta( $featured_product, '_woo_ctr_display_enable', true );
						if ( $featured_product_id && get_post_meta( $featured_product_id, '_sale_price', true ) ) {
							$_id = get_post_meta( $featured_product_id, '_woo_ctr_select_countdown_timer', true );
						} else {
							$_id = '';
						    if ( $post_product->has_child() ) {
							    $product_children = $post_product->get_children();
							    if ( count( $product_children ) ) {
								    foreach ( $product_children as $product_child ) {
									    $child_wc = wc_get_product( $product_child );
									    if (  ! $child_wc->get_date_on_sale_from( 'edit' ) || ! $child_wc->get_date_on_sale_to( 'edit' )  ) {
										    continue;
									    }
									    $_id = get_post_meta( $featured_product, '_woo_ctr_select_countdown_timer', true );
								    }
							    }
                            }
						}
					} else {
						if (  ! $post_product->get_date_on_sale_from( 'edit' ) && ! $post_product->get_date_on_sale_to( 'edit' )  ) {
							continue;
						}
						$_id = get_post_meta( $featured_product, '_woo_ctr_select_countdown_timer', true );
					}
					if ( $_id === '' ) continue;
					$post_object = get_post( $featured_product );
					setup_postdata( $GLOBALS['post'] =& $post_object );
					wc_get_template_part('content', 'product' );
                }
			}
			wp_reset_postdata();
			woocommerce_product_loop_end();
			do_action('woocommerce_after_shop_loop');
		} else {
			do_action('woocommerce_no_products_found');
		}
		$html = ob_get_clean();

		return ent2ncr( $html );
	}

	private function get_loop_time_val( $loop, $type ) {
		switch ( $type ) {
			case 'day':
				$result = $loop * 86400;
				break;
			case 'hour':
				$result = $loop * 3600;
				break;
			default:
				$result = $loop * 60;
		}

		return $result;
	}

	public function shortcode_enqueue_script() {
		$suffix = WP_DEBUG ? '' : 'min.';
		if ( ! wp_style_is( 'woo-sctr-shortcode-countdown-style', 'registered' ) ) {
			wp_register_style( 'woo-sctr-shortcode-countdown-style', VI_SCT_SALES_COUNTDOWN_TIMER_CSS . 'shortcode-countdown.' . $suffix . 'css', array(), VI_SCT_SALES_COUNTDOWN_TIMER_VERSION );
		}

		$sctv_array = $this->settings->get_params( 'sale_countdown_id' );
		$conditional_tags = false;
		//check enable?
		if ( ! empty( $sctv_array ) ) {
			if ( is_product() && is_single() ) {
				$conditional_tags = true;
			} else {
				foreach ( $sctv_array as $s_key => $s_val ) {
					if ( $this->settings->get_current_countdown( 'sale_countdown_active', $s_key ) &&
                         $this->settings->get_current_countdown( 'sale_countdown_archive_page_enable', $s_key ) ) {
						$assign_page = wp_unslash( $this->settings->get_current_countdown( 'sale_countdown_archive_page_assign', $s_key ) );
						if ( $assign_page ) {
							if ( stristr( $assign_page, "return" ) === false ) {
								$assign_page = "return (" . $assign_page . ");";
							}
							if ( eval( $assign_page ) ) $conditional_tags = true;
							continue;
						} else {
							$conditional_tags = true;
							continue;
						}
					}
				}
			}
		}
		$checkout_conditional_tags = false;
		if ( $this->settings->get_params('checkout_countdown_enable') ) {
		    $checkout_display_on = $this->settings->get_params('checkout_countdown_display_on_page');

			if ( count( $checkout_display_on ) ) {
				$page_show_t = '';
				foreach ( $checkout_display_on as $item ) {
					switch ( $item ) {
						case 'shop':
							$page_show_t .= '||is_shop()';
							break;
						case 'cart':
							$page_show_t .= '||is_cart()';
							break;
						case 'checkout':
							$page_show_t .= '||is_checkout()';
							break;
						case 'myaccount':
							$page_show_t .= '||is_account_page()';
							break;
						case 'home':
							$page_show_t .= '||is_front_page()';
							break;
						case 'product':
							$page_show_t .= '||is_product()';
							break;
						case 'category':
							$page_show_t .= '||is_category()';
							break;
						case 'assign':
							$checkout_display_other = $this->settings->get_params('checkout_countdown_display_on_assign_page');
							$page_show_t .= $checkout_display_other ? '||' . $checkout_display_other : '';
							break;
					}
				}
				if ( $page_show_t ) {
					$page_show_t = trim( $page_show_t, '||' );
					if ( stristr( $page_show_t, "return" ) === false ) {
						$page_show_t = "return (" . $page_show_t . ");";
					}
					if ( eval( $page_show_t ) ) {
						$checkout_conditional_tags = true;
                    }
				}
			}
        }

		if ( ! wp_script_is( 'woo-sctr-shortcode-countdown-script', 'registered' ) ) {
			wp_register_script( 'woo-sctr-shortcode-countdown-script', VI_SCT_SALES_COUNTDOWN_TIMER_JS . 'shortcode-countdown.' . $suffix . 'js', array( 'jquery' ), VI_SCT_SALES_COUNTDOWN_TIMER_VERSION );
			wp_localize_script(
				'woo-sctr-shortcode-countdown-script', 'visctv_param',
				array(
					'ajax_url'  => admin_url( 'admin-ajax.php' ),
					'has_cache' => $this->settings->get_params( 'use_cache_plugin' ) ? 1 : '',
					'conditional_tag' => esc_attr( $conditional_tags || $checkout_conditional_tags ),
				) );
		}
		if ( ! wp_script_is( 'woo-sctr-shortcode-countdown-script', 'enqueued' ) ) {
			wp_enqueue_script( 'woo-sctr-shortcode-countdown-script' );
		}
		if ( ! wp_style_is( 'woo-sctr-shortcode-countdown-style', 'enqueued' ) ) {
			wp_enqueue_style( 'woo-sctr-shortcode-countdown-style' );
		}
	}

	public static function get_language() {
		if ( isset( self::$cache['language'] ) ) {
			return self::$cache['language'];
		}
		if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
			$default_lang     = apply_filters( 'wpml_default_language', null );
			$current_language = apply_filters( 'wpml_current_language', null );
			if ( $current_language && $current_language !== $default_lang ) {
				$language = $current_language;
			}
		} else if ( class_exists( 'Polylang' ) && function_exists( 'pll_default_language' ) ) {
			$default_lang     = pll_default_language( 'slug' );
			$current_language = pll_current_language( 'slug' );
			if ( $current_language && $current_language !== $default_lang ) {
				$language = $current_language;
			}
		}
		self::$cache['language'] = $language ?? '';

		return self::$cache['language'];
	}

	public static function get_languages() {
		if ( isset( self::$cache['languages'] ) ) {
			return self::$cache['languages'];
		}
		if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
			/*wpml*/
			$languages = icl_get_languages( 'skip_missing=N&orderby=KEY&order=DIR&link_empty_to=str' );
		} elseif ( class_exists( 'Polylang' ) && function_exists( 'pll_default_language' ) ) {
			/*Polylang*/
			$languages = pll_languages_list();
		}
		self::$cache['languages'] = $languages ?? array();

		return self::$cache['languages'];
	}
}