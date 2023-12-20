$(document).ready(function () {
    let languageTable = $('.table-language')

    languageTable.on('click', '.delete-locale-button', (event) => {
        event.preventDefault()

        $('.delete-crud-entry').data('url', $(event.currentTarget).data('url'))
        $('.modal-confirm-delete').modal('show')
    })

    $(document).on('click', '.delete-crud-entry', (event) => {
        event.preventDefault()
        $('.modal-confirm-delete').modal('hide')

        let deleteURL = $(event.currentTarget).data('url')
        $(this).prop('disabled', true).addClass('button-loading')

        $.ajax({
            url: deleteURL,
            type: 'POST',
            data: { _method: 'DELETE' },
            success: (data) => {
                if (data.error) {
                    Botble.showError(data.message)
                } else {
                    if (data.data) {
                        languageTable.find('i[data-locale=' + data.data + ']').unwrap()
                        $('.tooltip').remove()
                    }
                    languageTable
                        .find('a[data-url="' + deleteURL + '"]')
                        .closest('tr')
                        .remove()
                    Botble.showSuccess(data.message)
                }
                $(this).prop('disabled', false).removeClass('button-loading')
            },
            error: (data) => {
                $(this).prop('disabled', false).removeClass('button-loading')
                Botble.handleError(data)
            },
        })
    })

    $(document).on('click', '.add-locale-form button[type=submit]', function (event) {
        event.preventDefault()
        event.stopPropagation()
        $(this).prop('disabled', true).addClass('button-loading')

        $.ajax({
            type: 'POST',
            cache: false,
            url: $(this).closest('form').prop('action'),
            data: new FormData($(this).closest('form')[0]),
            contentType: false,
            processData: false,
            success: (res) => {
                if (!res.error) {
                    Botble.showSuccess(res.message)
                    languageTable.load(window.location.href + ' .table-language > *')
                } else {
                    Botble.showError(res.message)
                }

                $(this).prop('disabled', false).removeClass('button-loading')
            },
            error: (res) => {
                $(this).prop('disabled', false).removeClass('button-loading')
                Botble.handleError(res)
            },
        })
    })

    let $availableRemoteLocales = $('#available-remote-locales')

    if ($availableRemoteLocales.length) {
        let getRemoteLocales = () => {
            $.ajax({
                url: $availableRemoteLocales.data('url'),
                type: 'GET',
                success: (res) => {
                    if (res.error) {
                        Botble.showError(res.message)
                    } else {
                        languageTable.load(window.location.href + ' .table-language > *')
                        $availableRemoteLocales.html(res.data)
                    }
                },
                error: (res) => {
                    Botble.handleError(res)
                },
            })
        }

        getRemoteLocales()

        $(document).on('click', '.btn-import-remote-locale', function (event) {
            event.preventDefault()

            $('.button-confirm-import-locale').data('url', $(this).data('url'))
            $('.modal-confirm-import-locale').modal('show')
        })

        $('.button-confirm-import-locale').on('click', (event) => {
            event.preventDefault()
            let _self = $(event.currentTarget)

            _self.addClass('button-loading')

            let url = _self.data('url')

            $.ajax({
                url: url,
                type: 'POST',
                success: (res) => {
                    if (res.error) {
                        Botble.showError(res.message)
                    } else {
                        Botble.showSuccess(res.message)
                        getRemoteLocales()
                    }

                    _self.closest('.modal').modal('hide')
                    _self.removeClass('button-loading')
                },
                error: (data) => {
                    Botble.handleError(data)
                    _self.removeClass('button-loading')
                },
            })
        })
    }
})
