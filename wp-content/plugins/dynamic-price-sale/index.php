<?php
/**
 * Plugin name: Khuyến mãi theo số lượng
 * Author: Thanhpd
 * Plugin URI: https://nontrum.vn/
 * Author Url: https://m.me/duythanh1402
 * Description: Cập nhật giá trong giỏ hàng </br> Xác định giỏ hàng có sp không ở mục phụ kiện => có sp chính </br> nếu có sp chính. tất cả phụ kiện giảm 20% </br> xác định danh sách những sp ko có khuyến mãi </br> tính công thức cho các sp không có khuyến mãi </br> sp 1: giá gốc </br> sp 2: giảm 10% - tối đa 100k </br> sp 3: giảm 15% - tối đa 150k </br> sp 4-n: giảm 20% - tối đa 200k </br>
 * 
 */


 
// add_action( 'woocommerce_update_cart_action_cart_updated', 'thanhpd_quantity_based_pricing', 9999 );
add_action( 'woocommerce_before_calculate_totals', 'thanhpd_quantity_based_pricing', 9999 );

function thanhpd_quantity_based_pricing( $cart ) {
 
    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;
 
 
	// $san_pham_tham_gia = get_field('san_pham_tham_gia', 131668 );
	$san_pham_tham_gia = [];
    if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) return;
	$is_have_main_product = false;
	$is_giam_max_10 = false;
	$cat_phukien = [23]; // phu kien
	//$cat_main = [18,358]; // non + thuogn hieu ban chay
	// if sp không thuộc cat_phukien => sp nón
	foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
		if( empty( array_intersect( $cart_item['data']->get_category_ids(), $cat_phukien ) ) ){
			$is_have_main_product = true;
			break;
		}
	}
	
	// xu ly phu kiện
	foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {		
		$id = $cart_item['data']->get_id();		
		if( !empty( array_intersect( $cart_item['data']->get_category_ids(), $cat_phukien ) ) ){
			if( $is_have_main_product  ){
				// la phu kien va gio hang co sp chinh
				// giảm giá 20% phụ kiện khi mua kèm sp chính
				//$tiet_kiem = $cart_item['data']->get_price() *  0.2 ;
				$price = ( $cart_item['data']->get_price() * (1 - 0.2 ) );
				$cart_item['data']->set_price( $price );
			}
			
		}		
	}
	// khai bao danh sach sp khong km
	$sanpham_giagoc=[];
    foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {		
		$id = $cart_item['data']->get_id();			
		$is_on_sale = false;
		$salePrice = get_post_meta($id, '_sale_price', true);
		if( empty ($salePrice) ){			
			$sanpham_giagoc[$cart_item['data']->get_price()] = ["id"=>$id,"qty" =>$cart_item['quantity'],"price" =>$cart_item['data']->get_price()];
			
			//$sanpham_giagoc["ids"][]=$id;
			
		}
	}
	//print_r( $sanpham_giagoc );
	krsort ($sanpham_giagoc);
	//print_r( $sanpham_giagoc );
	
	$dem = 0;
	$tong_giam_gia = $_SESSION['tong_giam_gia'] = 0;
	$str_tong_giam_gia = '';
	foreach ( $sanpham_giagoc as $price => $item){	
		$itemoff = 0;	
		for( $i = 1; $i<= $item['qty']; $i++ ){
			$dem++;
			$off =0;
			if($dem >= 4){
				$off = $item['price'] * 0.2 ;
				if( $off >200000 ){
					$off = 200000;
				}
			}else if( $dem == 3){
				$off = $item['price'] * 0.15 ;
				if( $off >150000 ){
					$off = 150000;
				}
				
			}else if($dem ==2){
				$off = $item['price'] * 0.1 ;
				if( $off >100000 ){
					$off = 100000;
				}				
			}	
			$itemoff += $off;
			$str_tong_giam_gia .=  $item['id'] .  ': ' .$item['price'] . " / " . $off . " | \t\r\n";
		}
		$tong_giam_gia += $itemoff;
		//$sanpham_giagoc[$price]["discount"] = $itemoff;
	}
	// xử lý sp không có km
	//echo $tong_giam_gia;
    $_SESSION['tong_giam_gia'] = $tong_giam_gia;
    $_SESSION['str_tong_giam_gia'] = $str_tong_giam_gia;
 }
 
 // Hook before calculate fees
add_action('woocommerce_cart_calculate_fees' , 'add_custom_fees');

/**
 * Add custom fee if more than three article
 * @param WC_Cart $cart
 */
function add_custom_fees( WC_Cart $cart ){
    if( empty( $_SESSION['tong_giam_gia'] ) ){
        return;
    }
    
    // Calculate the amount to reduce
   // $discount = $cart->subtotal * 0.1;
   // $str = $_SESSION['str_tong_giam_gia'];
   $str = "Giảm giá đặt biệt";
    $cart->add_fee( $str , -$_SESSION['tong_giam_gia']);
}