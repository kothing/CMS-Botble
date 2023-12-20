class ContactPluginManagement {
    init() {
        $(document).on('click', '.answer-trigger-button', (event) => {
            event.preventDefault()
            event.stopPropagation()

            const answerWrapper = $('.answer-wrapper')
            if (answerWrapper.is(':visible')) {
                answerWrapper.fadeOut()
            } else {
                answerWrapper.fadeIn()
            }

            new EditorManagement().init()
        })

        $(document).on('click', '.answer-send-button', (event) => {
            event.preventDefault()
            event.stopPropagation()

            $(event.currentTarget).addClass('button-loading')

            let message = $('#message').val()
            if (typeof tinymce != 'undefined') {
                message = tinymce.get('message').getContent()
            }

            $.ajax({
                type: 'POST',
                cache: false,
                url: route('contacts.reply', $('#input_contact_id').val()),
                data: {
                    message: message,
                },
                success: (res) => {
                    if (!res.error) {
                        $('.answer-wrapper').fadeOut()
                        if (typeof tinymce != 'undefined') {
                            tinymce.get('message').setContent('')
                        } else {
                            $('#message').val('')
                            const domEditableElement = document.querySelector('.answer-wrapper .ck-editor__editable')
                            if (domEditableElement) {
                                const editorInstance = domEditableElement.ckeditorInstance

                                if (editorInstance) {
                                    editorInstance.setData('')
                                }
                            }
                        }

                        Botble.showSuccess(res.message)
                        $('#reply-wrapper').load(window.location.href + ' #reply-wrapper > *')
                    }

                    $(event.currentTarget).removeClass('button-loading')
                },
                error: (res) => {
                    $(event.currentTarget).removeClass('button-loading')
                    Botble.handleError(res)
                },
            })
        })
    }
}

$(document).ready(() => {
    new ContactPluginManagement().init()
})
