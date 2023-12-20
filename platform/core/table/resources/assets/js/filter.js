class TableFilter {
    loadData($element) {
        $.ajax({
            type: 'GET',
            url: $('.filter-data-url').val(),
            data: {
                class: $('.filter-data-class').val(),
                key: $element.val(),
                value: $element.closest('.filter-item').find('.filter-column-value').val(),
            },
            success: (res) => {
                let data = $.map(res.data, (value, key) => {
                    return { id: key, name: value }
                })
                $element.closest('.filter-item').find('.filter-column-value-wrap').html(res.html)

                let $input = $element.closest('.filter-item').find('.filter-column-value')
                if ($input.length && $input.prop('type') === 'text') {
                    $input.typeahead({ source: data })
                    $input.data('typeahead').source = data
                }

                Botble.initResources()
            },
            error: (error) => {
                Botble.handleError(error)
            },
        })
    }

    init() {
        let that = this
        $.each($('.filter-items-wrap .filter-column-key'), (index, element) => {
            if ($(element).val()) {
                that.loadData($(element))
            }
        })

        $(document).on('change', '.filter-column-key', (event) => {
            that.loadData($(event.currentTarget))
        })

        $(document).on('click', '.btn-reset-filter-item', (event) => {
            event.preventDefault()
            let _self = $(event.currentTarget)
            _self.closest('.filter-item').find('.filter-column-key').val('').trigger('change')
            _self.closest('.filter-item').find('.filter-column-operator').val('=')
            _self.closest('.filter-item').find('.filter-column-value').val('')
        })

        $(document).on('click', '.add-more-filter', () => {
            let $template = $(document).find('.sample-filter-item-wrap')
            let html = $template.html()

            $(document).find('.filter-items-wrap').append(html.replace('<script>', '').replace('<\\/script>', ''))
            Botble.initResources()

            let element = $(document).find('.filter-items-wrap .filter-item:last-child').find('.filter-column-key')
            if ($(element).val()) {
                that.loadData(element)
            }
        })

        $(document).on('click', '.btn-remove-filter-item', (event) => {
            event.preventDefault()
            $(event.currentTarget).closest('.filter-item').remove()
        })
    }
}

$(document).ready(() => {
    new TableFilter().init()
})
