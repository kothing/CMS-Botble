jQuery(document).ready(($) => {
    $('.editable')
        .editable({ mode: 'inline' })
        .on('hidden', (e, reason) => {
            let locale = $(event.currentTarget).data('locale')
            if (reason === 'save') {
                $(event.currentTarget).removeClass('status-0').addClass('status-1')
            }
            if (reason === 'save' || reason === 'nochange') {
                let $next = $(event.currentTarget)
                    .closest('tr')
                    .next()
                    .find('.editable.locale-' + locale)
                setTimeout(() => {
                    $next.editable('show')
                }, 300)
            }
        })

    $('.group-select').on('change', (event) => {
        let group = $(event.currentTarget).val()
        if (group) {
            window.location.href = route('translations.index') + '?group=' + encodeURI($(event.currentTarget).val())
        } else {
            window.location.href = route('translations.index')
        }
    })

    $('.box-translation').on('click', '.button-import-groups', (event) => {
        event.preventDefault()
        let _self = $(event.currentTarget)
        _self.addClass('button-loading')

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

    $(document).on('click', '.button-publish-groups', function (event) {
        event.preventDefault()
        $('#confirm-publish-modal').modal('show')
    })

    $('#confirm-publish-modal').on('click', '#button-confirm-publish-groups', function (event) {
        event.preventDefault()

        let _self = $(event.currentTarget)
        _self.addClass('button-loading')

        let $form = $('.button-publish-groups').closest('form')

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

                _self.closest('.modal').modal('hide')
            },
            error: (data) => {
                _self.removeClass('button-loading')
                Botble.handleError(data)
            },
        })
    })
})
