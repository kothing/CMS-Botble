import CKEditorUploadAdapter from './ckeditor-upload-adapter'

class EditorManagement {
    constructor() {
        this.CKEDITOR = {}
        this.shortcodes = []
    }

    initCkEditor(element, extraConfig) {
        if (this.CKEDITOR[element] || !$('#' + element).is(':visible')) {
            return false
        }

        const editor = document.querySelector('#' + element)

        ClassicEditor.create(editor, {
            fontSize: {
                options: [9, 11, 13, 'default', 17, 16, 18, 19, 21, 22, 23, 24],
            },
            alignment: {
                options: ['left', 'right', 'center', 'justify'],
            },
            shortcode: {
                onEdit: (shortcode, name = () => {}) => {
                    let description = null
                    if (this.shortcodes.length) {
                        this.shortcodes.forEach(function (item) {
                            if (item.key === name) {
                                description = item.description
                                return true
                            }
                        })
                    }

                    this.shortcodeCallback({
                        key: name,
                        href: route('short-codes.ajax-get-admin-config', name),
                        data: {
                            code: shortcode,
                        },
                        description: description,
                        previewImage: '',
                        update: true,
                    })
                },
                shortcodes: this.getShortcodesAvailable(editor) || [],
                onCallback: (shortcode, options) => {
                    this.shortcodeCallback({
                        key: shortcode,
                        href: options.url,
                        previewImage: '',
                    })
                },
            },

            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                ],
            },
            placeholder: ' ',
            toolbar: {
                items: [
                    'heading',
                    '|',
                    'fontColor',
                    'fontSize',
                    'fontBackgroundColor',
                    'fontFamily',
                    'bold',
                    'italic',
                    'underline',
                    'link',
                    'strikethrough',
                    'bulletedList',
                    'numberedList',
                    '|',
                    'alignment',
                    'direction',
                    'shortcode',
                    'outdent',
                    'indent',
                    '|',
                    'htmlEmbed',
                    'imageInsert',
                    'blockQuote',
                    'insertTable',
                    'mediaEmbed',
                    'undo',
                    'redo',
                    'findAndReplace',
                    'removeFormat',
                    'sourceEditing',
                    'codeBlock',
                ],
            },
            language: {
                ui: window.siteEditorLocale || 'en',

                content: window.siteEditorLocale || 'en',
            },
            image: {
                toolbar: [
                    'imageTextAlternative',
                    'imageStyle:inline',
                    'imageStyle:block',
                    'imageStyle:side',
                    'toggleImageCaption',
                    'ImageResize',
                ],
                upload: {
                    types: ['jpeg', 'png', 'gif', 'bmp', 'webp', 'tiff', 'svg+xml'],
                },
            },
            codeBlock: {
                languages: [
                    { language: 'plaintext', label: 'Plain text' },
                    { language: 'c', label: 'C' },
                    { language: 'cs', label: 'C#' },
                    { language: 'cpp', label: 'C++' },
                    { language: 'css', label: 'CSS' },
                    { language: 'diff', label: 'Diff' },
                    { language: 'html', label: 'HTML' },
                    { language: 'java', label: 'Java' },
                    { language: 'javascript', label: 'JavaScript' },
                    { language: 'php', label: 'PHP' },
                    { language: 'python', label: 'Python' },
                    { language: 'ruby', label: 'Ruby' },
                    { language: 'typescript', label: 'TypeScript' },
                    { language: 'xml', label: 'XML' },
                    { language: 'dart', label: 'Dart', class: 'language-dart' },
                ],
            },
            link: {
                defaultProtocol: 'http://',
                decorators: {
                    openInNewTab: {
                        mode: 'manual',
                        label: 'Open in a new tab',
                        attributes: {
                            target: '_blank',
                            rel: 'noopener noreferrer',
                        },
                    },
                },
            },
            table: {
                contentToolbar: [
                    'tableColumn',
                    'tableRow',
                    'mergeTableCells',
                    'tableCellProperties',
                    'tableProperties',
                ],
            },
            htmlSupport: {
                allow: [
                    {
                        name: /.*/,
                        attributes: true,
                        classes: true,
                        styles: true,
                    },
                ],
            },
            ...extraConfig,
        })
            .then((editor) => {
                editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
                    return new CKEditorUploadAdapter(loader, RV_MEDIA_URL.media_upload_from_editor, editor.t)
                }

                // create function insert html
                editor.insertHtml = (html) => {
                    const viewFragment = editor.data.processor.toView(html)
                    const modelFragment = editor.data.toModel(viewFragment)
                    editor.model.insertContent(modelFragment)
                }

                window.editor = editor

                this.CKEDITOR[element] = editor

                const minHeight = $('#' + element).prop('rows') * 90
                const className = `ckeditor-${element}-inline`
                $(editor.ui.view.editable.element).addClass(className).after(`
                    <style>
                        .ck-editor__editable_inline {
                            min-height: ${minHeight - 100}px;
                            max-height: ${minHeight + 100}px;
                        }
                    </style>
                `)

                // debounce content for ajax ne
                let timeout
                editor.model.document.on('change:data', () => {
                    clearTimeout(timeout)
                    timeout = setTimeout(() => {
                        editor.updateSourceElement()
                    }, 150)
                })

                // insert media embed
                editor.commands._commands.get('mediaEmbed').execute = (url) => {
                    editor.insertHtml(`[media url="${url}"][/media]`)
                }
            })
            .catch((error) => {
                console.error(error)
            })
    }

    getShortcodesAvailable(editor) {
        const $dropdown = $(editor).parents('.form-group').find('.add_shortcode_btn_trigger')?.next('.dropdown-menu')
        const lists = []

        if ($dropdown && $dropdown.find('> li').length) {
            $dropdown.find('> li').each(function () {
                let item = $(this).find('> a')
                lists.push({
                    key: item.data('key'),
                    hasConfig: item.data('has-admin-config'),
                    name: item.text(),
                    url: item.attr('href'),
                    description: item.data('description'),
                })
            })
        }

        this.shortcodes = lists

        return lists
    }

    uploadImageFromEditor(blobInfo, callback) {
        let formData = new FormData()
        if (typeof blobInfo.blob === 'function') {
            formData.append('upload', blobInfo.blob(), blobInfo.filename())
        } else {
            formData.append('upload', blobInfo)
        }

        $.ajax({
            type: 'POST',
            data: formData,
            url: RV_MEDIA_URL.media_upload_from_editor,
            processData: false,
            contentType: false,
            cache: false,
            success(res) {
                if (res.uploaded) {
                    callback(res.url)
                }
            },
        })
    }

    initTinyMce(element) {
        tinymce.init({
            menubar: true,
            selector: '#' + element,
            min_height: $('#' + element).prop('rows') * 110,
            resize: 'vertical',
            plugins:
                'code autolink advlist visualchars link image media table charmap hr pagebreak nonbreaking anchor insertdatetime lists wordcount imagetools visualblocks',
            extended_valid_elements: 'input[id|name|value|type|class|style|required|placeholder|autocomplete|onclick]',
            toolbar:
                'formatselect | bold italic strikethrough forecolor backcolor | link image table | alignleft aligncenter alignright alignjustify  | numlist bullist indent  |  visualblocks code',
            convert_urls: false,
            image_caption: true,
            image_advtab: true,
            image_title: true,
            placeholder: '',
            contextmenu: 'link image inserttable | cell row column deletetable',
            images_upload_url: RV_MEDIA_URL.media_upload_from_editor,
            automatic_uploads: true,
            block_unsupported_drop: false,
            file_picker_types: 'file image media',
            images_upload_handler: this.uploadImageFromEditor.bind(this),
            file_picker_callback: (callback) => {
                let $input = $('<input type="file" accept="image/*" />').click()

                $input.on('change', (e) => {
                    this.uploadImageFromEditor(e.target.files[0], callback)
                })
            },
        })
    }

    initEditor(element, extraConfig, type) {
        if (!element.length) {
            return false
        }

        let current = this
        switch (type) {
            case 'ckeditor':
                $.each(element, (index, item) => {
                    current.initCkEditor($(item).prop('id'), extraConfig)
                })
                break
            case 'tinymce':
                $.each(element, (index, item) => {
                    current.initTinyMce($(item).prop('id'))
                })
                break
        }
    }

    init() {
        let $ckEditor = $(document).find('.editor-ckeditor')
        let $tinyMce = $(document).find('.editor-tinymce')
        let current = this
        if ($ckEditor.length > 0) {
            current.initEditor($ckEditor, {}, 'ckeditor')
        }

        if ($tinyMce.length > 0) {
            current.initEditor($tinyMce, {}, 'tinymce')
        }

        $(document).on('click', '.show-hide-editor-btn', (event) => {
            event.preventDefault()
            let _self = $(event.currentTarget)
            const editorInstance = _self.data('result')

            let $result = $('#' + editorInstance)

            if ($result.hasClass('editor-ckeditor')) {
                if (this.CKEDITOR[editorInstance] && typeof this.CKEDITOR[editorInstance] !== 'undefined') {
                    this.CKEDITOR[editorInstance].destroy()
                    this.CKEDITOR[editorInstance] = null
                    $('.editor-action-item').not('.action-show-hide-editor').hide()
                } else {
                    current.initCkEditor(editorInstance, {}, 'ckeditor')
                    $('.editor-action-item').not('.action-show-hide-editor').show()
                }
            } else if ($result.hasClass('editor-tinymce')) {
                tinymce.execCommand('mceToggleEditor', false, editorInstance)
            }
        })

        this.manageShortCode()

        return this
    }

    shortcodeCallback(params = {}) {
        const { href, key, description = null, data = {}, update = false, previewImage = null } = params
        $('.short-code-admin-config').html('')

        let $addShortcodeButton = $('.short_code_modal .add_short_code_btn')

        if (update) {
            $addShortcodeButton.text($addShortcodeButton.data('update-text'))
        } else {
            $addShortcodeButton.text($addShortcodeButton.data('add-text'))
        }

        if (description != null) {
            $('.short_code_modal .modal-title strong').text(description)
        }

        if (previewImage != null && previewImage !== '') {
            $('.short_code_modal .shortcode-preview-image-link').attr('href', previewImage).show()
        } else {
            $('.short_code_modal .shortcode-preview-image-link').hide()
        }

        $('.short_code_modal').modal('show')
        $('.half-circle-spinner').show()

        $.ajax({
            type: 'POST',
            data: data,
            url: href,
            success: (res) => {
                if (res.error) {
                    Botble.showError(res.message)
                    return false
                }

                $('.short-code-data-form').trigger('reset')
                $('.short_code_input_key').val(key)
                $('.half-circle-spinner').hide()
                $('.short-code-admin-config').html(res.data)
                Botble.initResources()
                Botble.initMediaIntegrate()
            },
            error: (data) => {
                Botble.handleError(data)
            },
        })
    }

    manageShortCode() {
        const self = this
        $('.list-shortcode-items li a').on('click', function (event) {
            event.preventDefault()

            if ($(this).data('has-admin-config') == '1') {
                self.shortcodeCallback({
                    href: $(this).prop('href'),
                    key: $(this).data('key'),
                    description: $(this).data('description'),
                    previewImage: $(this).data('preview-image'),
                })
            } else {
                const editorInstance = $('.add_shortcode_btn_trigger').data('result')

                const shortcode = '[' + $(this).data('key') + '][/' + $(this).data('key') + ']'

                if ($('.editor-ckeditor').length > 0) {
                    self.CKEDITOR[editorInstance].commands.execute('shortcode', shortcode)
                } else if ($('.editor-tinymce').length > 0) {
                    tinymce.get(editorInstance).execCommand('mceInsertContent', false, shortcode)
                }
            }
        })

        $.fn.serializeObject = function () {
            let o = {}
            let a = this.serializeArray()
            $.each(a, function () {
                if (o[this.name]) {
                    if (!o[this.name].push) {
                        o[this.name] = [o[this.name]]
                    }
                    o[this.name].push(this.value || '')
                } else {
                    o[this.name] = this.value || ''
                }
            })

            return o
        }

        $('.add_short_code_btn').on('click', function (event) {
            event.preventDefault()
            let formElement = $('.short_code_modal').find('.short-code-data-form')
            let formData = formElement.serializeObject()
            let attributes = ''

            $.each(formData, function (name, value) {
                let element = formElement.find('*[name="' + name + '"]')
                let shortcodeAttribute = element.data('shortcode-attribute')
                if ((!shortcodeAttribute || shortcodeAttribute !== 'content') && value) {
                    name = name.replace('[]', '')
                    if (element.data('shortcode-attribute') !== 'content') {
                        name = name.replace('[]', '')
                        attributes += ' ' + name + '="' + value + '"'
                    }
                }
            })

            let content = ''
            let contentElement = formElement.find('*[data-shortcode-attribute=content]')
            if (contentElement != null && contentElement.val() != null && contentElement.val() !== '') {
                content = contentElement.val()
            }

            const $shortCodeKey = $(this).closest('.short_code_modal').find('.short_code_input_key').val()

            const editorInstance = $('.add_shortcode_btn_trigger').data('result')

            const shortcode = '[' + $shortCodeKey + attributes + ']' + content + '[/' + $shortCodeKey + ']'

            if ($('.editor-ckeditor').length > 0) {
                self.CKEDITOR[editorInstance].commands.execute('shortcode', shortcode)
            } else if ($('.editor-tinymce').length > 0) {
                tinymce.get(editorInstance).execCommand('mceInsertContent', false, shortcode)
            } else {
                const coreInsertShortCodeEvent = new CustomEvent('core-insert-shortcode', {
                    detail: { shortcode: shortcode },
                })
                document.dispatchEvent(coreInsertShortCodeEvent)
            }

            $(this).closest('.modal').modal('hide')
        })
    }
}

$(document).ready(() => {
    window.EDITOR = new EditorManagement().init()
    window.EditorManagement = window.EditorManagement || EditorManagement
})
