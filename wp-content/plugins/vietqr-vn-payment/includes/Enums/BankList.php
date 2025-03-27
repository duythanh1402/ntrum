<?php
/**
 * Bank list
 *
 * @package VietQR\Enums
 */

namespace VietQR\Enums;

class BankList {
    protected static $bank_list = [
        [1, 'ABB', 'Ngân hàng TMCP An Bình'],
        [2, 'ACB', 'Ngân hàng TMCP Á Châu'],
        [3, 'BAB', 'Ngân hàng TMCP Bắc Á'],
        [4, 'BIDV', 'Ngân hàng TMCP Đầu tư và Phát triển Việt Nam'],
        [5, 'BVB', 'Ngân hàng TMCP Bảo Việt'],
        [6, 'CAKE', 'TMCP Việt Nam Thịnh Vượng - Ngân hàng số CAKE by VPBank'],
        [7, 'CBB', 'Ngân hàng Thương mại TNHH MTV Xây dựng Việt Nam'],
        [8, 'CIMB', 'Ngân hàng TNHH MTV CIMB Việt Nam'],
        [9, 'COOPBANK', 'Ngân hàng Hợp tác xã Việt Nam'],
        [10, 'DBS', 'DBS Bank Ltd - Chi nhánh Thành phố Hồ Chí Minh'],
        [11, 'DOB', 'Ngân hàng TMCP Đông Á'],
        [12, 'EIB', 'Ngân hàng TMCP Xuất Nhập khẩu Việt Nam'],
        [13, 'GPB', 'Ngân hàng Thương mại TNHH MTV Dầu Khí Toàn Cầu'],
        [14, 'HDB', 'Ngân hàng TMCP Phát triển Thành phố Hồ Chí Minh'],
        [15, 'HLBVN', 'Ngân hàng TNHH MTV Hong Leong Việt Nam'],
        [16, 'HSBC', 'Ngân hàng TNHH MTV HSBC (Việt Nam)'],
        [17, 'IBK - HCM', 'Ngân hàng Công nghiệp Hàn Quốc - Chi nhánh TP. Hồ Chí Minh'],
        [18, 'IBK - HN', 'Ngân hàng Công nghiệp Hàn Quốc - Chi nhánh Hà Nội'],
        [19, 'ICB', 'Ngân hàng TMCP Công thương Việt Nam'],
        [20, 'IVB', 'Ngân hàng TNHH Indovina'],
        [21, 'KBank', 'Ngân hàng Đại chúng TNHH Kasikornbank'],
        [22, 'KBHCM', 'Ngân hàng Kookmin - Chi nhánh Thành phố Hồ Chí Minh'],
        [23, 'KBHN', 'Ngân hàng Kookmin - Chi nhánh Hà Nội'],
        [24, 'KLB', 'Ngân hàng TMCP Kiên Long'],
        [25, 'LPB', 'Ngân hàng TMCP Bưu Điện Liên Việt'],
        [26, 'MB', 'Ngân hàng TMCP Quân đội'],
        [27, 'MSB', 'Ngân hàng TMCP Hàng Hải'],
        [28, 'NAB', 'Ngân hàng TMCP Nam Á'],
        [29, 'NCB', 'Ngân hàng TMCP Quốc Dân'],
        [30, 'NHB HN', 'Ngân hàng Nonghyup - Chi nhánh Hà Nội'],
        [31, 'OCB', 'Ngân hàng TMCP Phương Đông'],
        [32, 'Oceanbank', 'Ngân hàng Thương mại TNHH MTV Đại Dương'],
        [33, 'PBVN', 'Ngân hàng TNHH MTV Public Việt Nam'],
        [34, 'PGB', 'Ngân hàng TMCP Xăng dầu Petrolimex'],
        [35, 'PVCB', 'Ngân hàng TMCP Đại Chúng Việt Nam'],
        [36, 'SCB', 'Ngân hàng TMCP Sài Gòn'],
        [37, 'SCVN', 'Ngân hàng TNHH MTV Standard Chartered Bank Việt Nam'],
        [38, 'SEAB', 'Ngân hàng TMCP Đông Nam Á'],
        [39, 'SGICB', 'Ngân hàng TMCP Sài Gòn Công Thương'],
        [40, 'SHB', 'Ngân hàng TMCP Sài Gòn - Hà Nội'],
        [41, 'SHBVN', 'Ngân hàng TNHH MTV Shinhan Việt Nam'],
        [42, 'STB', 'Ngân hàng TMCP Sài Gòn Thương Tín'],
        [43, 'TCB', 'Ngân hàng TMCP Kỹ thương Việt Nam'],
        [44, 'TIMO', 'Ngân hàng số Timo by Ban Viet Bank (Timo by Ban Viet Bank)'],
        [45, 'TPB', 'Ngân hàng TMCP Tiên Phong'],
        [46, 'Ubank', 'TMCP Việt Nam Thịnh Vượng - Ngân hàng số Ubank by VPBank'],
        [47, 'UOB', 'Ngân hàng United Overseas - Chi nhánh TP. Hồ Chí Minh'],
        [48, 'VAB', 'Ngân hàng TMCP Việt Á'],
        [49, 'VBA', 'Ngân hàng Nông nghiệp và Phát triển Nông thôn Việt Nam'],
        [50, 'VCB', 'Ngân hàng TMCP Ngoại Thương Việt Nam'],
        [51, 'VCCB', 'Ngân hàng TMCP Bản Việt'],
        [52, 'VIB', 'Ngân hàng TMCP Quốc tế Việt Nam'],
        [53, 'VIETBANK', 'Ngân hàng TMCP Việt Nam Thương Tín'],
        [54, 'VNPTMONEY', 'Trung tâm dịch vụ tài chính số VNPT - Chi nhánh Tổng công ty truyền thông (VNPT Fintech)'],
        [55, 'VPB', 'Ngân hàng TMCP Việt Nam Thịnh Vượng'],
        [56, 'VRB', 'Ngân hàng Liên doanh Việt - Nga'],
        [57, 'VTLMONEY', 'Tổng Công ty Dịch vụ số Viettel - Chi nhánh tập đoàn công nghiệp viễn thông Quân Đội'],
        [58, 'WVN', 'Ngân hàng TNHH MTV Woori Việt Nam']
    ];

    /**
     * Get bank list
     *
     * @return array
     */
    public static function get_list(): array {
        return self::$bank_list;
    }

    /**
     * Get bank info from code
     *
     * @param string $bank_code
     * @return array
     */
    public static function get_info_from_code(string $bank_code): array {
        $bank_list = self::get_list();
        foreach ($bank_list as $bank) {
            if ($bank[1] === $bank_code) {
                return $bank;
            }
        }
        return [];
    }

    /**
     * Get bank info from id
     *
     * @param int $bank_id
     * @return array    
     */
    public static function get_info_from_id(int $bank_id): array {
        $bank_list = self::get_list();
        foreach ($bank_list as $bank) {
            if ($bank[0] === $bank_id) {
                return $bank;
            }
        }
        return [];
    }
}