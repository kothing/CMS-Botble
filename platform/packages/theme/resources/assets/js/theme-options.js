$(document).ready(() => {
    if ($(document).find('.colorpicker-input').length > 0) {
        $(document).find('.colorpicker-input').colorpicker()
    }

    if ($(document).find('.iconpicker-input').length > 0) {
        $(document).find('.iconpicker-input').iconpicker({
            selected: true,
            hideOnSelect: true,
        })
    }

    $(document).ready(function () {
        $(document).on('click', '.button-save-theme-options', (event) => {
            event.preventDefault()
            let _self = $(event.currentTarget)
            _self.addClass('button-loading')

            if (typeof tinymce != 'undefined') {
                for (var instance in tinymce.editors) {
                    if (tinymce.editors[instance].getContent) {
                        $('#' + instance).html(tinymce.editors[instance].getContent())
                    }
                }
            }

            let $form = _self.closest('form')

            $.ajax({
                url: $form.prop('action'),
                type: 'POST',
                data: $form.serialize(),
                success: (data) => {
                    _self.removeClass('button-loading')

                    if (data.error) {
                        Botble.showError(data.message)
                    } else {
                        Botble.showSuccess(data.message)
                        $form.removeClass('dirty')
                    }
                },
                error: (data) => {
                    _self.removeClass('button-loading')
                    Botble.handleError(data)
                },
            })
        })
    })
})
