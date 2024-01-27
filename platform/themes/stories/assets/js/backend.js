var showError = message => {
    window.showAlert('alert-danger', message);
}

var showSuccess = message => {
    window.showAlert('alert-success', message);
}

var handleError = data => {
    if (typeof (data.errors) !== 'undefined' && data.errors.length) {
        handleValidationError(data.errors);
    } else if (typeof (data.responseJSON) !== 'undefined') {
        if (typeof (data.responseJSON.errors) !== 'undefined') {
            if (data.status === 422) {
                handleValidationError(data.responseJSON.errors);
            }
        } else if (typeof (data.responseJSON.message) !== 'undefined') {
            showError(data.responseJSON.message);
        } else {
            $.each(data.responseJSON, (index, el) => {
                $.each(el, (key, item) => {
                    showError(item);
                });
            });
        }
    } else {
        showError(data.statusText);
    }
}

var handleValidationError = errors => {
    let message = '';
    $.each(errors, (index, item) => {
        if (message !== '') {
            message += '<br />';
        }
        message += item;
    });
    showError(message);
}

window.showAlert = (messageType, message) => {
    if (messageType && message !== '') {
        let alertId = Math.floor(Math.random() * 1000);

        let html = `<div class="alert ${messageType} alert-dismissible" id="${alertId}">
                            <span class="close elegant-icon icon_close" data-dismiss="alert" aria-label="close"></span>
                            <i class="elegant-icon icon_` + (messageType === 'alert-success' ? 'info' : 'error-circle_alt') + ` message-icon"></i>
                            ${message}
                        </div>`;

        $('#alert-container').append(html).ready(() => {
            window.setTimeout(() => {
                $(`#alert-container #${alertId}`).remove();
            }, 6000);
        });
    }
}

$(document).on('click', '.newsletter-form button[type=submit]', function (event) {
    event.preventDefault();
    event.stopPropagation();

    let _self = $(this);

    _self.addClass('button-loading');

    $.ajax({
        type: 'POST',
        cache: false,
        url: _self.closest('form').prop('action'),
        data: new FormData(_self.closest('form')[0]),
        contentType: false,
        processData: false,
        success: res => {
            _self.removeClass('button-loading');

            if (typeof refreshRecaptcha !== 'undefined') {
                refreshRecaptcha();
            }

            if (res.error) {
                showError(res.message);
                return false;
            }

            _self.closest('form').find('input[type=email]').val('');
            showSuccess(res.message);
        },
        error: res => {
            if (typeof refreshRecaptcha !== 'undefined') {
                refreshRecaptcha();
            }
            _self.removeClass('button-loading');
            handleError(res);
        }
    });
});

$(document).ready(function () {
    $.ajax({
        type: 'GET',
        url: $('#sidebar-wrapper').data('load-url'),
        success: res =>  {
            if (res.error) {
                return false;
            }

            $('.sidebar-inner').html(res.data);
        },
        error: res =>  {
            console.log(res);
        }
    });
});
