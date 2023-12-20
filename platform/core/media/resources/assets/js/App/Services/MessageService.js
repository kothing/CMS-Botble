import { Helpers } from '../Helpers/Helpers'

export class MessageService {
    static showMessage(type, message) {
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: 'toast-bottom-right',
            onclick: null,
            showDuration: 1000,
            hideDuration: 1000,
            timeOut: 10000,
            extendedTimeOut: 1000,
            showEasing: 'swing',
            hideEasing: 'linear',
            showMethod: 'fadeIn',
            hideMethod: 'fadeOut',
        }

        let messageHeader = ''

        switch (type) {
            case 'error':
                messageHeader = Helpers.trans('message.error_header')
                break
            case 'success':
                messageHeader = Helpers.trans('message.success_header')
                break
        }
        toastr[type](message, messageHeader)
    }

    static handleError(data) {
        if (typeof data.responseJSON !== 'undefined' && ! Helpers.isArray(data.errors)) {
            MessageService.handleValidationError(data.responseJSON.errors)
        } else {
            if (typeof data.responseJSON !== 'undefined') {
                if (typeof data.responseJSON.errors !== 'undefined') {
                    if (data.status === 422) {
                        MessageService.handleValidationError(data.responseJSON.errors)
                    }
                } else if (typeof data.responseJSON.message !== 'undefined') {
                    MessageService.showMessage('error', data.responseJSON.message)
                } else {
                    $.each(data.responseJSON, (index, el) => {
                        $.each(el, (key, item) => {
                            MessageService.showMessage('error', item)
                        })
                    })
                }
            } else {
                MessageService.showMessage('error', data.statusText)
            }
        }
    }

    static handleValidationError(errors) {
        let message = ''
        $.each(errors, (index, item) => {
            message += item + '<br />'

            let $input = $('*[name="' + index + '"]')
            $input.addClass('field-has-error')

            let $input_array = $('*[name$="[' + index + ']"]')
            $input_array.addClass('field-has-error')
        })
        MessageService.showMessage('error', message)
    }
}
