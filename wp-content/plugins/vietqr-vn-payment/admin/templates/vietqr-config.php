<?php
/**
 * Template for the VietQR Config page.
 * 
 * @var string $ws_url Websocket URL
 * @var string $qr_code QR code Base64
 * @var array $bank_accounts List of current bank accounts
 * @var string $selected_bank_account Selected bank account info (account_number, account_name, bank_code)
 * 
 * @package VietQR
 */

if (!defined('ABSPATH')) {
    exit;
}

vr_get_admin_template( 'parts/header' );
?>

<!-- WebSocket for Active Code -->
<script>
    var $ = jQuery;

    const socket = new WebSocket("<?php echo $ws_url; ?>");

    socket.onopen = function(event) {
        console.log('Connection established');
    };

    socket.onmessage = function(event) {
        showLoading();
        const data = JSON.parse(event.data);

        // Check type
        if (data.notificationType != 'N22') {
            return;
        }

        // Store bank account
        $.ajax({
            url: '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'vr_store_bank_account',
                bank_account: {
                    account_number: data.bankAccount,
                    account_name: data.userBankName,
                    bank_code:  data.bankCode,
                },
                notification_type: data.notificationType,
                _wpnonce: '<?php echo wp_create_nonce('vr_store_bank_account'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    console.log('Bank account stored successfully');
                    let message = "Cập nhật tài khoản ngân hàng thành công \n";
                    message += `${data.bankAccount} - ${data.userBankName} - ${data.bankCode}`;
                    showSuccess(message);
                    setTimeout(function() {
                        window.location.reload(); // Reload page
                    }, 2000);
                } else {
                    console.error('Failed to select bank account');
                    showError(response.data);
                }
                hideLoading();
            },
            error: function(xhr, status, error) {
                console.error('Failed to store bank account: ' + error);
                const message = "Cập nhật tài khoản ngân hàng thất bại";
                showError(message);
                hideLoading();
            }
        });
    };

    socket.onclose = function(event) {
        console.log('Connection closed');
    };

    socket.onerror = function(error) {
        console.log('Error: ' + error);
    };
</script>

<script>
    // FUNCTIONS
    var $ = jQuery;

    // Store bank transfer enabled
    function update_bank_transfer_enabled(event) {
        showLoading();
        const bank_transfer_enabled = $('input[name="vietqr_bank_transfer_enabled"]').is(':checked');
        const message = bank_transfer_enabled ? "VietQr enabled successfully" : "VietQr disabled successfully";

        $.ajax({
            url: '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'vr_store_bank_transfer_enabled',
                bank_transfer_enabled: bank_transfer_enabled,
                _wpnonce: '<?php echo wp_create_nonce('vr_store_bank_transfer_enabled'); ?>'
            },
            success: function(response) {
                console.log('Bank transfer enabled stored successfully');
                showSuccess(message);
                hideLoading();
            },
            error: function(xhr, status, error) {
                console.error('Failed to store bank transfer enabled: ' + error);
                showError("Failed to store bank transfer enabled: " + error);
                hideLoading();
            }
        });
    }

    // Select bank account
    function select_bank_account(event) {
        showLoading();
        const bank_account_id = $('input[name="selected_bank_account"]:checked').val();

        $.ajax({
            url: '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'vr_select_bank_account',
                id: bank_account_id, // id of bank account
                _wpnonce: '<?php echo wp_create_nonce('vr_select_bank_account'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    console.log('Bank account selected successfully');
                    showSuccess(response.data);
                } else {
                    console.error('Failed to select bank account');
                    showError(response.data);
                }
                hideLoading();
            },
            error: function(xhr, status, error) {
                console.error('Failed to select bank account: ' + error);
                showError("Failed to select bank account: " + error);
                hideLoading();
            }
        });
    }

    // INIT
    $(document).ready(function() {
        // $('input[name="vietqr_bank_transfer_enabled"]').on('change', update_bank_transfer_enabled);

        // Select bank account
        $('input[name="selected_bank_account"]').on('change', select_bank_account);

        // Tab functionality
        $('.nav-tabs .nav-item').click(function(e) {
            e.preventDefault();
            var target = $(this).children('a').attr('href');
            $('.tab-pane').removeClass('active show');
            $(target).addClass('active show');
            // $('.nav-tabs a').removeClass('active');
            $('.nav-tabs .nav-item').removeClass('active');
            // $(this).addClass('active');
            $(this).addClass('active');
        });
    });
</script>

<!-- Template -->
<div class="wrap">
    
    <h1 class="vr-font-bold"><?php echo __('CONFIG WORDPRESS TO VIETQR CONNECTION', 'vietqr'); ?></h1>

    <!-- VietQR config tabs -->
    <div class="vietqr-config-tabs md:w-fit">
        <ul class="nav-tabs">
            <li class="nav-item active">
                <a class="nav-link" id="scan-tab" data-toggle="tab" href="#scan-content">Scan QR</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="manual-tab" data-toggle="tab" href="#manual-content">Manual</a>
            </li>
        </ul>
        
        <div class="tab-content">
            <!-- VietQR config Scan QR method -->
            <div id="scan-content" class="tab-pane fade show active">
                <div class="vietqr-config vietqr-config-scan vr-grid vr-grid-cols-1 vr-md:vr-grid-cols-2">
                    <div class="vietqr-config-left">
                        <table class="form-table">
                            <!-- Selected bank account -->
                            <tr>
                                <th scope="row"><?php echo __('Selected bank account', 'vietqr'); ?></th>
                                <td>
                                    <?php if (!empty($bank_accounts)): ?>
                                        <?php foreach ($bank_accounts as $account): ?>
                                            <div>
                                                <label for="bank_account_<?php echo esc_attr($account['id']); ?>">
                                                    <input type="radio" 
                                                        id="bank_account_<?php echo esc_attr($account['id']); ?>" 
                                                        name="selected_bank_account" 
                                                        value="<?php echo esc_attr($account['id']); ?>" 
                                                        <?php checked(intval($account['is_selected']), 1); ?>
                                                        > 
                                                        <!-- Selected bank account -->
                                                        <?php echo esc_html($account['account_number'] . ' - ' . 
                                                                            $account['account_name'] . ' - ' . 
                                                                            $account['bank_code']); ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p><?php _e('No bank accounts available', 'vietqr'); ?></p>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="vietqr-config-right vr-d-flex vr-flex-column vr-align-center">
                        <div class="vietqr-logo vr-mb-[10px] vr-text-center">
                            <img src="<?php echo VIETQR_URL . "/admin/img/vietqr_payment_1x.png" ?>" alt="vietqr_payment">
                        </div>

                        <!-- Active QR -->
                        <div class="vietqr-active-code">
                            <img width="300px" src="<?php echo $qr_code; ?>" alt="vietqr_active_code">
                            <p class="vr-text-center vr-mt-[0px]">Scan mã để kích hoạt</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- VietQR config manual method -->
            <div id="manual-content" class="tab-pane fade">
                <div class="vietqr-config vietqr-config-manual vr-grid vr-grid-cols-1 vr-md:vr-grid-cols-2">
                    <div class="vietqr-config-left">
                        <table class="form-table">
                            <!-- Selected bank account -->
                            
                        </table>
                    </div>

                    <div class="vietqr-config-right vr-d-flex vr-flex-column vr-align-center">
                        <div class="vietqr-logo vr-mb-[10px] vr-text-center">
                            <img src="<?php echo VIETQR_URL . "/admin/img/vietqr_payment_1x.png" ?>" alt="vietqr_payment">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- contact info-->
    <p class="contact-info">
        <span id="">Power by <a href="https://vietqr.vn/">Vietqr.vn</a> / <a href="https://vietqr.com/">Vietqr.com</a> / <a href="https://vietqr.org/">Vietqr.org</a></span>
        <span><a href="tel:19006234">Hotline: 1900 6234</a></span>
    </p>

    <!-- QR code -->
    <div class="qr-code">
    </div>
</div>