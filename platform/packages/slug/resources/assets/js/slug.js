class SlugBoxManagement {
    init() {
        let $slugBox = $('#edit-slug-box')
        $(document).on('click', '#change_slug', (event) => {
            $('.default-slug').unwrap()
            let $slugInput = $('#editable-post-name')
            $slugInput.html(
                '<input type="text" id="new-post-slug" class="form-control" value="' +
                $slugInput.text() +
                '" autocomplete="off">'
            )
            $('#edit-slug-box .cancel').show()
            $('#edit-slug-box .save').show()
            $(event.currentTarget).hide()
        })

        $(document).on('click', '#edit-slug-box .cancel', () => {
            let currentSlug = $('#current-slug').val()
            let $permalink = $('#sample-permalink')
            $permalink.html(
                '<a class="permalink" href="' +
                $('#slug_id').data('view') +
                currentSlug.replace('/', '') +
                '">' +
                $permalink.html() +
                '</a>'
            )
            $('#editable-post-name').text(currentSlug)
            $('#edit-slug-box .cancel').hide()
            $('#edit-slug-box .save').hide()
            $('#change_slug').show()
        })

        let createSlug = (name, id, exist) => {
            $.ajax({
                url: $('#slug_id').data('url'),
                type: 'POST',
                data: {
                    value: name,
                    slug_id: id,
                    model: $('input[name=model]').val(),
                },
                success: (data) => {
                    let $permalink = $('#sample-permalink')
                    let $slugId = $('#slug_id')
                    if (exist) {
                        $permalink.find('.permalink').prop('href', $slugId.data('view') + data.replace('/', ''))
                    } else {
                        $permalink.html(
                            '<a class="permalink" target="_blank" href="' +
                            $slugId.data('view') +
                            data.replace('/', '') +
                            '">' +
                            $permalink.html() +
                            '</a>'
                        )
                    }

                    $('.page-url-seo p').text($slugId.data('view') + data.replace('/', ''))

                    $('#editable-post-name').text(data)
                    $('#current-slug').val(data)
                    $('#edit-slug-box .cancel').hide()
                    $('#edit-slug-box .save').hide()
                    $('#change_slug').show()
                    $slugBox.removeClass('hidden')
                },
                error: (data) => {
                    Botble.handleError(data)
                },
            })
        }

        $(document).on('click', '#edit-slug-box .save', () => {
            let $slugField = $('#new-post-slug')
            let name = $slugField.val()
            let id = $('#slug_id').data('id')
            if (id == null) {
                id = 0
            }
            if (name != null && name !== '') {
                createSlug(name, id, false)
            } else {
                $slugField.closest('.form-group').addClass('has-error')
            }
        })

        $(document).on('blur', '#' + $slugBox.data('field-name'), (e) => {
            if ($slugBox.hasClass('hidden')) {
                let value = $(e.currentTarget).val()

                if (value !== null && value !== '') {
                    createSlug(value, 0, true)
                }
            }
        })
    }
}

$(() => {
    new SlugBoxManagement().init()
})
