let HelperSwal = (function() {

    function HelperSwal(){}

    HelperSwal.wait = function(title, html) {
        Swal.fire({
            title: title,
            html: html,
            onBeforeOpen () {
                Swal.showLoading ()
            },
            onAfterClose () {
                Swal.hideLoading()
            },
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false
        });
    }

    HelperSwal.error = function(title, html,timer = 7500) {
        Swal.fire({
            icon: "error",
            position: 'center',
            title: title,
            html: html,
            showConfirmButton: false,
            timer: timer
        });
    }

    HelperSwal.success = function (title, html) {
        Swal.fire({
            icon: "success",
            position: 'center',
            title: title,
            html: html,
            showConfirmButton: false,
            timer: 1000
        });
    }

    HelperSwal.close = function () {
        Swal.close();
    }

    return HelperSwal;
})();