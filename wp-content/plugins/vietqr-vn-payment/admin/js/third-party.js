var $ = jQuery;

// loading.io
window.showLoading = function() {
    $('body').append('<div class="ldld full" style="z-index: 10000"></div>');
    (new window.ldLoader({root: ".ldld.full"})).on();
};

window.hideLoading = function() {
    $('.ldld.full').remove();
};

// Sweet Alert
window.showSuccess = function(message) {
    Swal.fire({
        title: "Success!",
        text: message,
        icon: "success"
      });
};

window.showError = function(message) {
    Swal.fire({
        title: "Error!",
        text: message,
        icon: "error"
    });
};