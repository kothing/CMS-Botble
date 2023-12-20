class ThemeManagement {
    init() {
        $(document).on('click', '.btn-trigger-active-theme', (event) => {
            event.preventDefault()
            let _self = $(event.currentTarget)
            _self.addClass('button-loading')

            $.ajax({
                url: route('theme.active'),
                data: {
                    theme: _self.data('theme'),
                },
                type: 'POST',
                success: (data) => {
                    if (data.error) {
                        Botble.showError(data.message)
                    } else {
                        Botble.showSuccess(data.message)
                        window.location.reload()
                    }
                    _self.removeClass('button-loading')
                },
                error: (data) => {
                    Botble.handleError(data)
                    _self.removeClass('button-loading')
                },
            })
        })

        $(document).on('click', '.btn-trigger-remove-theme', (event) => {
            event.preventDefault()
            $('#confirm-remove-theme-button').data('theme', $(event.currentTarget).data('theme'))
            $('#remove-theme-modal').modal('show')
        })

        $(document).on('click', '#confirm-remove-theme-button', (event) => {
            event.preventDefault()
            let _self = $(event.currentTarget)
            _self.addClass('button-loading')

            $.ajax({
                url: route('theme.remove', { theme: _self.data('theme') }),
                type: 'POST',
                success: (data) => {
                    if (data.error) {
                        Botble.showError(data.message)
                    } else {
                        Botble.showSuccess(data.message)
                        window.location.reload()
                    }
                    _self.removeClass('button-loading')
                    $('#remove-theme-modal').modal('hide')
                },
                error: (data) => {
                    Botble.handleError(data)
                    _self.removeClass('button-loading')
                    $('#remove-theme-modal').modal('hide')
                },
            })
        })
    }
}

$(document).ready(() => {
    new ThemeManagement().init()
})
