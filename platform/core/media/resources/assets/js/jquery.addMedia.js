/* ========================================================================
 * AddMedia.js v1.0
 * Requires Botble Media
 * ======================================================================== */

;+(function ($) {
    'use strict'

    /**
     * @param element
     * @param options
     * @constructor
     */
    let AddMedia = function (element, options) {
        this.options = options
        $(element).rvMedia({
            multiple: true,
            onSelectFiles: function (files, $el) {
                if (typeof files !== 'undefined') {
                    switch ($el.data('editor')) {
                        case 'summernote':
                            handleInsertImagesForSummerNote($el, files)
                            break
                        case 'wysihtml5':
                            let editor = $(options.target).data('wysihtml5').editor
                            handleInsertImagesForWysihtml5Editor(editor, files)
                            break
                        case 'ckeditor':
                            handleForCkeditor($el, files)
                            break
                        case 'tinymce':
                            handleForTinyMce(files)
                            break
                        default:
                            const coreInsertMediaEvent = new CustomEvent('core-insert-media', {
                                detail: {
                                    files: files,
                                    element: $el,
                                },
                            })
                            document.dispatchEvent(coreInsertMediaEvent)
                    }
                }
            },
        })
    }

    AddMedia.VERSION = '1.1.0'

    /**
     * Insert images to summernote editor
     * @param $el
     * @param files
     */
    function handleInsertImagesForSummerNote($el, files) {
        if (files.length === 0) {
            return
        }

        let instance = $el.data('target')
        for (let i = 0; i < files.length; i++) {
            if (files[i].type === 'image') {
                $(instance).summernote('insertImage', files[i].full_url, files[i].basename)
            } else {
                $(instance).summernote('pasteHTML', '<a href="' + files[i].full_url + '">' + files[i].full_url + '</a>')
            }
        }
    }

    /**
     * Insert images to Wysihtml5 editor
     * @param editor
     * @param files
     */
    function handleInsertImagesForWysihtml5Editor(editor, files) {
        if (files.length === 0) {
            return
        }

        // insert images for the wysihtml5 editor
        let s = ''
        for (let i = 0; i < files.length; i++) {
            if (files[i].type === 'image') {
                s += '<img src="' + files[i].full_url + '" alt="' + files[i].name + '" loading="lazy">'
            } else {
                s += '<a href="' + files[i].full_url + '">' + files[i].full_url + '</a>'
            }
        }

        if (editor.getValue().length > 0) {
            let length = editor.getValue()
            editor.composer.commands.exec('insertHTML', s)
            if (editor.getValue() === length) {
                editor.setValue(editor.getValue() + s)
            }
        } else {
            editor.setValue(editor.getValue() + s)
        }
    }

    /**
     * @param $el
     * @param files
     */
    function handleForCkeditor($el, files) {
        let instance = $el.data('target').replace('#', '')
        let content = ''
        $.each(files, (index, file) => {
            let link = file.full_url
            if (file.type === 'image') {
                content += '<img src="' + link + '" alt="' + file.name + '" loading="lazy"/><br />'
            } else {
                content += '<a href="' + link + '">' + file.name + '</a><br />'
            }
        })

        CKEDITOR.instances[instance].insertHtml(content)
    }

    /**
     * @param files
     */
    function handleForTinyMce(files) {
        let html = ''
        $.each(files, (index, file) => {
            let link = file.full_url
            if (file.type === 'image') {
                html += '<img src="' + link + '" alt="' + file.name + '" loading="lazy" /><br />'
            } else {
                html += '<a href="' + link + '">' + file.name + '</a><br />'
            }
        })
        tinymce.activeEditor.execCommand('mceInsertContent', false, html)
    }

    /**
     * @param option
     */
    function callAction(option) {
        return this.each(function () {
            let $this = $(this)
            let data = $this.data('bs.media')
            let options = $.extend({}, $this.data(), typeof option === 'object' && option)
            if (!data) {
                $this.data('bs.media', new AddMedia(this, options))
            }
        })
    }

    $.fn.addMedia = callAction
    $.fn.addMedia.Constructor = AddMedia

    $(window).on('load', function () {
        $('[data-type="rv-media"]').each(function () {
            let $addMedia = $(this)
            callAction.call($addMedia, $addMedia.data())
        })
    })
})(jQuery)
