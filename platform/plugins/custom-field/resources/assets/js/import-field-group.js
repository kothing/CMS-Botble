export class Helpers {
    static jsonDecode(jsonString, defaultValue) {
        if (typeof jsonString === 'string') {
            let result
            try {
                result = $.parseJSON(jsonString)
            } catch (err) {
                result = defaultValue
            }
            return result
        }
        return null
    }
}

;(($) => {
    let $body = $('body')

    $body.on('click', 'form.import-field-group button.btn.btn-secondary.action-item:nth-child(2)', (event) => {
        event.preventDefault()
        event.stopPropagation()
        let $form = $(event.currentTarget).closest('form')
        $form.find('input[type=file]').val('').trigger('click')
    })

    $body.on('change', 'form.import-field-group input[type=file]', (event) => {
        let $form = $(event.currentTarget).closest('form')
        let file = event.currentTarget.files[0]
        if (file) {
            let reader = new FileReader()
            reader.readAsText(file)
            reader.onload = (e) => {
                let json = Helpers.jsonDecode(e.target.result)
                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: {
                        json_data: json,
                    },
                    dataType: 'json',
                    beforeSend: () => {
                        Botble.blockUI()
                    },
                    success: (res) => {
                        Botble.showNotice(res.error ? 'error' : 'success', res.messages)
                        if (!res.error) {
                            const tableId = $form.find('table').prop('id')
                            if (window.LaravelDataTables[tableId]) {
                                window.LaravelDataTables[tableId].draw()
                            }
                        }
                        Botble.unblockUI()
                    },
                    complete: () => {
                        Botble.unblockUI()
                    },
                    error: (res) => {
                        Botble.showError(res.message ? res.message : 'Some error occurred')
                    },
                })
            }
        }
    })
})(jQuery)
