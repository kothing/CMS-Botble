'use strict'
$(document).ready(function () {
    $(document).on('click', '.btn-trigger-cleanup', (event) => {
        event.preventDefault()
        $('#cleanup-modal').modal('show')
    })

    $(document).on('click', '#cleanup-submit-action', (event) => {
        event.preventDefault()
        event.stopPropagation()
        const _self = $(event.currentTarget)

        _self.addClass('button-loading')

        const $form = $('#form-cleanup-database')
        const $modal = $('#cleanup-modal')

        $.ajax({
            type: 'POST',
            cache: false,
            url: $form.prop('action'),
            data: new FormData($form[0]),
            contentType: false,
            processData: false,
            success: (res) => {
                if (!res.error) {
                    Botble.showSuccess(res.message)
                } else {
                    Botble.showError(res.message)
                }

                _self.removeClass('button-loading')
                $modal.modal('hide')
            },
            error: (res) => {
                _self.removeClass('button-loading')
                $modal.modal('hide')

                Botble.handleError(res)
            },
        })
    })
})
