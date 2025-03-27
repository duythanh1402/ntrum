<?php
/**
 * Woocommerce custom thanks you page 
 *
 * @var $order WooCommerce order
 * @var $transaction VietQR transaction corresponding to the order
 * @var $qr_code QR code Base64
 * @var $ws_url Websocket URL
 * @var $is_completed Boolean indicating if the order is completed
 * @var $payment_method Payment method used for the order
 * @var $bank_account Selected bank account info (account_number, account_name, bank_code)
 * @var $order_id Order ID
 * @var $order_code Order code
 * 
 * @package VietQR
 */

?>

<?php if (!empty($bank_account)): ?>

    <!-- Dom to image -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dom-to-image/2.6.0/dom-to-image.min.js" 
            integrity="sha512-01CJ9/g7e8cUmY0DFTMcUw/ikS799FHiOA0eyHsUWfOetgbx/t6oV4otQ5zXKQyIrQGTHSmRVPIgrgLcZi/WMA==" 
            crossorigin="anonymous" 
            referrerpolicy="no-referrer"></script>

    <!-- Websocket -->
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
            if (data.notificationType != 'N05') {
                return;
            }

            // Update order
            $.ajax({
                url: vietqrAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'vr_update_order_completed',
                    data: {
                        ...data,
                    },
                    _wpnonce: '<?php echo wp_create_nonce('vr_update_order_completed'); ?>'
                },
                success: function(response) {
                    if ( response.success == true ) {
                        showSuccess(response.data); // Message from server
                        setTimeout(function() {
                            window.location.reload(); // Reload page
                        }, 2000);
                    } else {
                        showError(response.data); // Message from server
                    }
                    hideLoading();
                },
                error: function(xhr, status, error) {
                    console.log(error)
                    showError(error.data); // Message from server
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
        (function ($) {
            // Copy
            const copyToClipboard = (elementId, processValue = (value) => value) => {
                const element = document.getElementById(elementId);
                const input = document.createElement('input');
                input.value = processValue(element.innerText);
                document.body.appendChild(input);
                input.select();
                input.setSelectionRange(0, 99999);
                document.execCommand('copy');
                document.body.removeChild(input);
            };

            const copyBank = () => copyToClipboard('transaction-bank');
            const copyAccount = () => copyToClipboard('transaction-account');
            const copyAmount = () => copyToClipboard('transaction-amount', (value) => parseInt(value.replace(/\./g, '')));
            const copyContent = () => copyToClipboard('transaction-content');

            // Download image
            const handleImageOperation = (operation) => {
                const originalElement = document.querySelector('.vietqr-manual__content');
                const clonedElement = originalElement.cloneNode(true);
                const image = document.getElementById("vietqr-scan__print-area");
                image.appendChild(clonedElement);

                domtoimage.toJpeg(document.getElementById('vietqr-scan__print-area'), {bgcolor: 'white'})
                    .then((dataUrl) => {
                        if (operation === 'print') {
                            printJS(dataUrl, 'image');
                        } else if (operation === 'save') {
                            const link = document.createElement('a');
                            // const filename = document.getElementById('transaction-temp-code').innerHTML;
                            const filename = document.getElementById('transaction-bank_code').innerHTML + document.getElementById('transaction-account').innerHTML;
                            link.download = `${filename}.jpeg`;
                            link.href = dataUrl;
                            link.click();
                        }
                        image.removeChild(clonedElement);
                    });
            };
            const handleSaveQrImageOnly = () => {
                const originalElement = document.querySelector('.vietqr-manual__content');
                const clonedElement = originalElement.cloneNode(true);
                const image = document.getElementById("vietqr-code-scan");
                image.appendChild(clonedElement);

                domtoimage.toJpeg(document.getElementById('vietqr-code-scan'), {bgcolor: 'white'})
                .then((dataUrl) => {
                    const link = document.createElement('a');
                    // const filename = document.getElementById('transaction-temp-code').innerHTML;
                    const filename = document.getElementById('transaction-bank_code').innerHTML + document.getElementById('transaction-account').innerHTML;
                    link.download = `${filename}.jpeg`;
                    link.href = dataUrl;
                    link.click();
                    image.removeChild(clonedElement);
                });
            };

            const printQrImage = () => handleImageOperation('print');
            const saveQrImage = () => handleImageOperation('save');
            const saveQrImageOnly = () => handleSaveQrImageOnly();

            // Popup
            const closePopup = () => {
                $('#transaction-info-popup').addClass('d-none');
                $('body').removeClass('no-scroll');
            }

            const openPopup = () => {
                $('#transaction-info-popup').removeClass('d-none');
                $('body').addClass('no-scroll');
            }

            // Manual check
            const handleManualCheck = () => {
                showLoading();

                // Send request to check transaction
                $.ajax({
                    url: vietqrAjax.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'vr_manual_check',
                        orderId: '<?php echo $transaction['orderId']; ?>', 
                        _wpnonce: '<?php echo wp_create_nonce('vr_manual_check'); ?>'
                    },
                    success: function(response) {
                        if ( response.success == true ) {
                            showSuccess(response.data); // Message from server
                            setTimeout(function() {
                                window.location.reload(); // Reload page
                            }, 2000);
                        } else {
                            showError(response.data); // Message from server
                        }
                        hideLoading();
                    },
                    error: function(xhr, status, error) {
                        console.log(error)
                        showError(error.data); // Message from server
                        hideLoading();
                    }
                });
            }

            // INIT
            $(document).ready(function () {
                // Copy
                $("#copy-bank").click(copyBank);
                $("#copy-account").click(copyAccount);
                $("#copy-amount").click(copyAmount);
                $("#copy-content").click(copyContent);
                
                // Image
                $("#vietqr-save-qr").click(saveQrImage);
                $("#vietqr-save-qr-only").click(saveQrImageOnly);
                // $("#vietqr-print-qr").click(printQrImage);

                // Popup
                $("#vr-transaction-info__btn").click(openPopup);
                $(".vietqr-transaction-overlay").click(closePopup);
                $("#vr-transaction-close").click(closePopup);
                $(document).keydown(function(e) {
                    if (e.key === 'Escape') {
                        closePopup();
                    }
                });

                // Manual check
                $("#vietqr-manual-check").click(handleManualCheck);
            })

        })(jQuery)
    </script>

    <!-- VietQR transaction info button -->
    <div class="vr-transaction-info">
        <img src="<?php echo VIETQR_URL . "/public/img/check-mark.png" ?>" alt="check mark">
        <div>
            <p>Đặt hàng thành công</p>
            <p>Mã đơn hàng <span><?php echo $order_id; ?></span></p>
            <p>Cám ơn bạn đã mua hàng</p>
            <p><a id="vr-transaction-info__btn" class="vietqr-button" >
                <?php echo __("Xem thông tin thanh toán", "vietqr-plugin"); ?>
                </a></p>
        </div>
    </div>

    <!-- Info popup -->
    <div id="transaction-info-popup" 
        class="vr-transaction-wrapper <?php echo ($payment_method !== 'bacs') ? "d-none" : "" ?> " 
        >
        
        <div class="vr-transaction-overlay"></div>
        
        <div class="vr-transaction-content <?php echo ($is_completed) ? "complete" : "" ?>">
            <button id="vr-transaction-close" type="button" class="vr-transaction-close">&times;</button>

            <!-- Success transaction -->
            <div id="vr-transaction-completed" 
                class="vr-transaction vr-transaction-success <?php echo ($is_completed && $payment_method == 'bacs') ? "d-block": "d-none" ;?>" 
                >
                <p class="text-right mb-0">
                    <small>Power by <a href="https://vietqr.vn/" target="_blank">Vietqr.vn</a> / 
                    <a href="https://vietqr.com/" target="_blank">Vietqr.com</a> / 
                    <a target="_blank" href="https://vietqr.org/">Vietqr.org</a></small>
                </p>
                <p class="vr-transaction-success__title"><?php echo __("ĐƠN HÀNG ĐÃ THANH TOÁN THÀNH CÔNG", "vietqr-plugin") ?></p>
                <p class="vr-transaction-success__total"><?php echo number_format($order->get_total()); ?> <small>VNĐ</small></p>

                <table>
                    <tr>
                        <td>Thời gian:</td>
                        <td>
                            <?php 
                                $time = $transaction['transactionTime'];

                                if (!empty($time) && is_numeric($time)) {
                                    echo $time = vr_convert_timestamp_to_date($time, "d-m-Y h:i:s");
                                } else {
                                    echo "";
                                } 
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Ngân hàng:</td>
                        <td><?php echo $bank_account["account_name"]; ?></td>
                    </tr>
                    <tr>
                        <td>Tài khoản:</td>
                        <td><?php echo $bank_account["account_number"]; ?></td>
                    </tr>
                    <tr>
                        <td>Nội dung:</td>
                        <td><?php echo $transaction['content']; ?></td>
                    </tr>
                    <tr>
                        <td>Mã đơn hàng:</td>
                        <td><?php echo $order_code; ?></td>
                    </tr>
                </table>
            </div>

            <!-- Not completed transaction -->
            <div id="vr-transaction-not-completed" 
                class="vr-transaction <?php echo ($is_completed) ? "d-none" : ""; ?>" 
                >

                <h2 id="popup-modal-title vr-transaction__title" class="vr-text-center vr-font-bold">
                    <?php echo __("Chuyển khoản ngân hàng", "vietqr-plugin"); ?>
                </h2>

                <div class="vr-transaction__content">
                    <div class="vietqr-scan" class="vr-text-center">
                        <h4><?php echo __("Cách 1: Chuyển khoản bằng mã QR", "vietqr-plugin"); ?></h4>
                        <div class="vietqr-scan__content">
                            <div id="vietqr-scan__print-area" 
                                class="vietqr-scan__print-area vr-py-[0px] vr-px-[8px]" 
                                >
                                <p><?php echo __("Mở App Ngân Hàng Quét QR Code", "vietqr-plugin"); ?></p>

                                <!--QR start-->
                                <div id="vietqr-scan__img" class="vietqr-scan__img">
                                    <div><img width="120px" 
                                        src="<?php echo VIETQR_URL . "/public/img/vietqr_payment_1x.png" ?>" 
                                        alt="vietqr_payment">
                                    </div>
                                    <div><img id="vietqr-code-scan" 
                                        width="250px" 
                                        height="250px" 
                                        src="<?php echo $qr_code ?>" 
                                        alt="QR Code" />
                                    </div>
                                </div>
                                <!--QR end-->

                                <p class="vr-mb-[18px]">
                                    <?php echo __("58 ngân hàng hỗ trợ quét mã VietQR", "vietqr-plugin"); ?>
                                </p>
                            </div>

                            <p class="vr-mb-[0px]">
                                <button 
                                    id="vietqr-save-qr-only" 
                                    class="vietqr-button vietqr-save-qr"
                                    >
                                    <?php  echo __("Lưu mã QR","vietqr-plugin");  ?>
                                </button>
                                <button 
                                    id="vietqr-save-qr" 
                                    class="vietqr-button vietqr-save-qr vr-mr-[10px]"
                                    >
                                    <?php echo __("Lưu nội dung","vietqr-plugin"); ?>
                                </button>
                            </p>
                        </div>
                    </div> <!-- scan -->

                    <div class="vietqr-manual">
                        <h4><?php echo __('Cách 2: Chuyển khoản thủ công theo thông tin', "vietqr-plugin"); ?></h4>
                        <div class="vietqr-manual__content">
                            <table id="vietqr-manual__table" class="vietqr-manual__table">
                                <tr>
                                    <td><?php echo __('Ngân hàng'); ?></td>
                                    <td id="transaction-bank_code"><?php echo $bank_account["bank_code"] ?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><?php echo __('Chủ tài khoản'); ?></td>
                                    <td><span id="transaction-bank"><?php echo $bank_account["account_name"] ?></span></td>
                                    <td><button class="vietqr-button" id="copy-bank"><?php echo __('Sao chép'); ?></button></td>
                                </tr>
                                <tr>
                                    <td><?php echo __('Số tài khoản'); ?></td>
                                    <td><span id="transaction-account"><?php echo $bank_account["account_number"] ?></span></td>
                                    <td><button class="vietqr-button" id="copy-account"><?php echo __('Sao chép'); ?></button></td>
                                </tr>
                                <tr>
                                    <td><?php echo __('Số tiền'); ?></td>
                                    <td><span id="transaction-amount"><?php echo number_format($order->get_total()); ?></span></td>
                                    <td><button class="vietqr-button" id="copy-amount"><?php echo __('Sao chép'); ?></button></td>
                                </tr>
                                <tr>
                                    <td><?php echo __('Nội dung'); ?></td>
                                    <td><span id="transaction-content" class="vr-transaction-text"><?php echo $transaction["content"] ?? ""; ?></span></td>
                                    <td><button class="vietqr-button" id="copy-content"><?php echo __('Sao chép'); ?></button></td>
                                </tr>
                                <tr>
                                    <td><?php echo __('Mã đơn hàng'); ?></td>
                                    <td><span id="transaction-temp-code" class="vr-transaction-text"><?php echo $order_code; ?></span></td>
                                    <td><button class="vietqr-button" id="copy-content"><?php echo __('Sao chép'); ?></button></td>
                                </tr>
                            </table>

                            <p class="vr-text-left">
                                Lưu ý: nhập chính xác nội dung 
                                <span class="vr-transaction-text"><?php echo $transaction["content"] ?? ""; ?></span> 
                                khi chuyển khoản bạn sẽ nhận được email (hoặc SMS) xác nhận khi giao dịch thành công.
                            </p>

                            <p class="vr-text-right vr-mb-[0px]">
                                <small>
                                    Power by <a href="https://vietqr.vn/" target="_blank">Vietqr.vn</a> / 
                                    <a href="https://vietqr.com/" target="_blank">Vietqr.com</a> / 
                                    <a target="_blank" href="https://vietqr.org/">Vietqr.org</a>
                                </small>
                            </p>
                        </div>
                    </div> <!-- manual -->
                    
                </div>

                <button id="vietqr-manual-check" class="vietqr-button btn-manual-check">Tôi đã thanh toán</button>

            </div>
        </div> <!-- content -->

    </div> <!-- popup -->
<?php endif; ?>