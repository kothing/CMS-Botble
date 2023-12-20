export class Helpers {
    /**
     * Render a WYSIWYG editor
     * @param $elements
     * @param config
     */
    static wysiwyg($elements, config) {
        window.initializedEditor = window.initializedEditor || 0

        let editor = 'ckeditor'

        if (typeof tinymce != 'undefined') {
            editor = 'tinymce'
        }

        $elements.each((index, el) => {
            let $_self = $(el)

            $_self.attr('id', 'editor_initialized_' + window.initializedEditor)

            window.initializedEditor++

            setTimeout(() => {
                new EditorManagement().initEditor($_self, {}, editor)
            }, 100)
        })
    }

    static wysiwygGetContent($element) {
        if (typeof CKEDITOR != 'undefined') {
            return CKEDITOR[$element.attr('id')].getData()
        }

        if (typeof tinymce != 'undefined') {
            return tinymce.editors[$element.attr('id')].getContent()
        }

        return $element.val()
    }

    static arrayGet(array, key, defaultValue = null) {
        let result

        try {
            result = array[key]
        } catch (err) {
            return defaultValue
        }

        if (result === null || typeof result === 'undefined') {
            result = defaultValue
        }

        return result
    }

    static jsonEncode(object) {
        if (typeof object === 'undefined') {
            object = null
        }
        return JSON.stringify(object)
    }

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

class UseCustomFields {
    constructor() {
        this.$body = $('body')

        /**
         * Where to show the custom field elements
         */
        this.$_UPDATE_TO = $('#custom_fields_container')
        /**
         * Where to export json data when submit form
         */
        this.$_EXPORT_TO = $('#custom_fields_json')

        this.CURRENT_DATA = Helpers.jsonDecode(this.base64Helper().decode(this.$_EXPORT_TO.text()), [])

        if (this.CURRENT_DATA) {
            this.handleCustomFields()
            this.exportData()
        }
    }

    base64Helper() {
        if (!this.base64) {
            let Base64 = {
                _keyStr: 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=',
                encode: function (e) {
                    let t = ''
                    let n, r, i, s, o, u, a
                    let f = 0
                    e = Base64._utf8_encode(e)
                    while (f < e.length) {
                        n = e.charCodeAt(f++)
                        r = e.charCodeAt(f++)
                        i = e.charCodeAt(f++)
                        s = n >> 2
                        o = ((n & 3) << 4) | (r >> 4)
                        u = ((r & 15) << 2) | (i >> 6)
                        a = i & 63
                        if (isNaN(r)) {
                            u = a = 64
                        } else if (isNaN(i)) {
                            a = 64
                        }
                        t =
                            t +
                            this._keyStr.charAt(s) +
                            this._keyStr.charAt(o) +
                            this._keyStr.charAt(u) +
                            this._keyStr.charAt(a)
                    }
                    return t
                },
                decode: function (e) {
                    let t = ''
                    let n, r, i
                    let s, o, u, a
                    let f = 0
                    e = e.replace(/[^A-Za-z0-9+/=]/g, '')
                    while (f < e.length) {
                        s = this._keyStr.indexOf(e.charAt(f++))
                        o = this._keyStr.indexOf(e.charAt(f++))
                        u = this._keyStr.indexOf(e.charAt(f++))
                        a = this._keyStr.indexOf(e.charAt(f++))
                        n = (s << 2) | (o >> 4)
                        r = ((o & 15) << 4) | (u >> 2)
                        i = ((u & 3) << 6) | a
                        t = t + String.fromCharCode(n)
                        if (u != 64) {
                            t = t + String.fromCharCode(r)
                        }
                        if (a != 64) {
                            t = t + String.fromCharCode(i)
                        }
                    }
                    t = Base64._utf8_decode(t)
                    return t
                },
                _utf8_encode: (e) => {
                    e = e.replace(/rn/g, 'n')
                    let t = ''
                    for (let n = 0; n < e.length; n++) {
                        let r = e.charCodeAt(n)
                        if (r < 128) {
                            t += String.fromCharCode(r)
                        } else if (r > 127 && r < 2048) {
                            t += String.fromCharCode((r >> 6) | 192)
                            t += String.fromCharCode((r & 63) | 128)
                        } else {
                            t += String.fromCharCode((r >> 12) | 224)
                            t += String.fromCharCode(((r >> 6) & 63) | 128)
                            t += String.fromCharCode((r & 63) | 128)
                        }
                    }
                    return t
                },
                _utf8_decode: (e) => {
                    let t = ''
                    let n = 0
                    let r = 0,
                        c2 = 0
                    while (n < e.length) {
                        r = e.charCodeAt(n)
                        if (r < 128) {
                            t += String.fromCharCode(r)
                            n++
                        } else if (r > 191 && r < 224) {
                            c2 = e.charCodeAt(n + 1)
                            t += String.fromCharCode(((r & 31) << 6) | (c2 & 63))
                            n += 2
                        } else {
                            c2 = e.charCodeAt(n + 1)
                            let c3 = e.charCodeAt(n + 2)
                            t += String.fromCharCode(((r & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63))
                            n += 3
                        }
                    }
                    return t
                },
            }

            this.base64 = Base64
        }

        /**
         * @doc
         * There are 2 methods: encode & decode
         */

        return this.base64
    }

    handleCustomFields() {
        let _self = this

        let repeaterFieldAdded = 0
        /**
         * The html template of custom fields
         */
        let FIELD_TEMPLATE = {
            fieldGroup: $('#_render_custom_field_field_group_template').html(),
            globalSkeleton: $('#_render_custom_field_global_skeleton_template').html(),
            text: $('#_render_custom_field_text_template').html(),
            number: $('#_render_custom_field_number_template').html(),
            email: $('#_render_custom_field_email_template').html(),
            password: $('#_render_custom_field_password_template').html(),
            textarea: $('#_render_custom_field_textarea_template').html(),
            checkbox: $('#_render_custom_field_checkbox_template').html(),
            radio: $('#_render_custom_field_radio_template').html(),
            select: $('#_render_custom_field_select_template').html(),
            image: $('#_render_custom_field_image_template').html(),
            file: $('#_render_custom_field_file_template').html(),
            wysiwyg: $('#_render_custom_field_wysiswg_template').html(),
            repeater: $('#_render_custom_field_repeater_template').html(),
            repeaterItem: $('#_render_custom_field_repeater_item_template').html(),
            repeaterFieldLine: $('#_render_custom_field_repeater_line_template').html(),
        }

        let initWYSIWYG = ($element) => {
            Helpers.wysiwyg($element)

            return $element
        }

        let initCustomFieldsBoxes = (boxes, $appendTo) => {
            boxes.forEach((box) => {
                let skeleton = FIELD_TEMPLATE.globalSkeleton
                skeleton = skeleton.replace(/__type__/gi, box.type || '')
                skeleton = skeleton.replace(/__title__/gi, box.title || '')
                skeleton = skeleton.replace(/__instructions__/gi, box.instructions || '')

                let $skeleton = $(skeleton)
                let $data = registerLine(box)

                $skeleton.find('.meta-box-wrap').append($data)

                $skeleton.data('lcf-registered-data', box)

                $appendTo.append($skeleton)

                if (box.type === 'wysiwyg') {
                    initWYSIWYG($skeleton.find('.meta-box-wrap .wysiwyg-editor'))
                }
            })
        }

        let registerLine = (box) => {
            let result = FIELD_TEMPLATE[box.type],
                $wrapper = $('<div class="lcf-' + box.type + '-wrapper"></div>')
            $wrapper.data('lcf-registered-data', box)

            let choices = null
            let $result = null
            switch (box.type) {
                case 'text':
                case 'number':
                case 'email':
                case 'password':
                    result = result.replace(/__placeholderText__/gi, box.options.placeholderText || '')
                    result = result.replace(/__value__/gi, box.value || box.options.defaultValue || '')
                    break
                case 'textarea':
                    result = result.replace(/__rows__/gi, box.options.rows || 3)
                    result = result.replace(/__placeholderText__/gi, box.options.placeholderText || '')
                    result = result.replace(/__value__/gi, box.value || box.options.defaultValue || '')
                    break
                case 'image':
                    result = result.replace(/__value__/gi, box.value || box.options.defaultValue || '')
                    if (!box.value) {
                        let defaultImage = $(result).find('img').attr('data-default')
                        result = result
                            .replace('data-src', 'src')
                            .replace(/__image__/gi, defaultImage || box.options.defaultValue || '')
                    } else {
                        result = result
                            .replace('data-src', 'src')
                            .replace(/__image__/gi, box.thumb || box.options.defaultValue || '')
                    }
                    break
                case 'file':
                    result = result.replace(/__value__/gi, box.value || box.options.defaultValue || '')
                    result = result.replace(/__url__/gi, box.full_url || box.options.defaultValue || '')
                    break
                case 'select':
                    $result = $(result)
                    choices = parseChoices(box.options.selectChoices)
                    choices.forEach((choice) => {
                        $result.append('<option value="' + choice[0] + '">' + choice[1] + '</option>')
                    })
                    $result.val(Helpers.arrayGet(box, 'value', box.options.defaultValue))
                    $wrapper.append($result)
                    return $wrapper
                case 'checkbox':
                    choices = parseChoices(box.options.selectChoices)
                    let boxValue = Helpers.jsonDecode(box.value)
                    choices.forEach((choice) => {
                        let template = result.replace(/__value__/gi, choice[0] || '')
                        template = template.replace(/__title__/gi, choice[1] || '')
                        template = template.replace(
                            /__checked__/gi,
                            $.inArray(choice[0], boxValue) != -1 ? 'checked' : ''
                        )
                        $wrapper.append($(template))
                    })

                    return $wrapper
                case 'radio':
                    choices = parseChoices(box.options.selectChoices)
                    let isChecked = false
                    choices.forEach((choice) => {
                        let template = result.replace(/__value__/gi, choice[0] || '')
                        template = template.replace(/__id__/gi, box.id + box.slug + repeaterFieldAdded)
                        template = template.replace(/__title__/gi, choice[1] || '')
                        template = template.replace(/__checked__/gi, box.value === choice[0] ? 'checked' : '')
                        $wrapper.append($(template))

                        if (box.value === choice[0]) {
                            isChecked = true
                        }
                    })
                    if (isChecked === false) {
                        $wrapper.find('input[type=radio]:first').prop('checked', true)
                    }
                    return $wrapper
                case 'repeater':
                    $result = $(result)
                    $result.data('lcf-registered-data', box)

                    $result.find('> .repeater-add-new-field').html(box.options.buttonLabel || 'Add new item')
                    $result.find('> .sortable-wrapper').sortable({ handle: '.ui-sortable-handle' })
                    registerRepeaterItem(box.items, box.value || [], $result.find('> .field-group-items'))
                    return $result
                case 'wysiwyg':
                    result = result.replace(/__value__/gi, box.value || box.options.defaultValueTextarea || '')
                    break
            }

            $wrapper.append($(result))

            return $wrapper
        }

        let registerRepeaterItem = (items, data, $appendTo) => {
            $appendTo.data('lcf-registered-data', items)
            data.forEach((dataItem) => {
                let indexCss = $appendTo.find('> .ui-sortable-handle').length + 1
                let result = FIELD_TEMPLATE.repeaterItem
                result = result.replace(/__position__/gi, indexCss)

                let $result = $(result)
                $result.data('lcf-registered-data', items)

                registerRepeaterFieldLine(items, dataItem, $result.find('> .field-line-wrapper > .field-group'))

                $appendTo.append($result)
            })
            return $appendTo
        }

        let registerRepeaterFieldLine = (items, data, $appendTo) => {
            data.forEach((item) => {
                repeaterFieldAdded++

                let result = FIELD_TEMPLATE.repeaterFieldLine
                result = result.replace(/__title__/gi, item.title || '')
                result = result.replace(/__instructions__/gi, item.instructions || '')

                let $result = $(result)
                let $data = registerLine(item)
                $result.data('lcf-registered-data', item)
                $result.find('> .repeater-item-input').append($data)

                $appendTo.append($result)

                if (item.type === 'wysiwyg') {
                    initWYSIWYG($result.find('> .repeater-item-input .wysiwyg-editor'))
                }
            })
            return $appendTo
        }

        let parseChoices = (choiceString) => {
            if (!choiceString) {
                return []
            }

            let choices = []
            choiceString.split('\n').forEach((item) => {
                let currentChoice = item.split(':')
                if (currentChoice[0] && currentChoice[1]) {
                    currentChoice[0] = currentChoice[0].trim()
                    currentChoice[1] = currentChoice[1].trim()
                }
                choices.push(currentChoice)
            })
            return choices
        }

        /**
         * Remove field item
         */
        this.$body.on('click', '.remove-field-line', (event) => {
            event.preventDefault()
            let current = $(event.currentTarget)
            current.parent().animate(
                {
                    opacity: 0.1,
                },
                300,
                () => {
                    current.parent().remove()
                }
            )
        })

        /**
         * Collapse field item
         */
        this.$body.on('click', '.collapse-field-line', (event) => {
            event.preventDefault()
            let current = $(event.currentTarget)
            current.toggleClass('collapsed-line')
        })

        /**
         * Add new repeater line
         */
        this.$body.on('click', '.repeater-add-new-field', (event) => {
            event.preventDefault()
            let $groupWrapper = $.extend(true, {}, $(event.currentTarget).prev('.field-group-items'))
            let registeredData = $groupWrapper.data('lcf-registered-data')

            repeaterFieldAdded++

            registerRepeaterItem(registeredData, [registeredData], $groupWrapper)

            Botble.initMediaIntegrate()
        })

        /**
         * Init data when page loaded
         */
        this.CURRENT_DATA.forEach((group) => {
            let groupTemplate = FIELD_TEMPLATE.fieldGroup
            groupTemplate = groupTemplate.replace(/__title__/gi, group.title || '')

            let $groupTemplate = $(groupTemplate)

            initCustomFieldsBoxes(group.items, $groupTemplate.find('.meta-boxes-body'))

            $groupTemplate.data('lcf-field-group', group)

            _self.$_UPDATE_TO.append($groupTemplate)
        })

        Botble.initMediaIntegrate()
    }

    exportData() {
        let _self = this

        let getFieldGroups = () => {
            let fieldGroups = []

            $('#custom_fields_container')
                .find('> .meta-boxes')
                .each((index, el) => {
                    let $current = $(el)
                    let currentData = $current.data('lcf-field-group')
                    let $items = $current.find('> .meta-boxes-body > .meta-box')
                    currentData.items = getFieldItems($items)
                    fieldGroups.push(currentData)
                })
            return fieldGroups
        }

        let getFieldItems = ($items) => {
            let items = []
            $items.each((index, el) => {
                items.push(getFieldItemValue($(el)))
            })
            return items
        }

        let getFieldItemValue = ($item) => {
            let customFieldData = $.extend(true, {}, $item.data('lcf-registered-data'))
            switch (customFieldData.type) {
                case 'text':
                case 'number':
                case 'email':
                case 'password':
                case 'image':
                case 'file':
                    customFieldData.value = $item.find('> .meta-box-wrap input').val()
                    break
                case 'wysiwyg':
                    customFieldData.value = Helpers.wysiwygGetContent($item.find('> .meta-box-wrap textarea'))
                    break
                case 'textarea':
                    customFieldData.value = $item.find('> .meta-box-wrap textarea').val()
                    break
                case 'checkbox':
                    customFieldData.value = []
                    $item.find('> .meta-box-wrap input:checked').each((index, el) => {
                        customFieldData.value.push($(el).val())
                    })
                    break
                case 'radio':
                    customFieldData.value = $item.find('> .meta-box-wrap input:checked').val()
                    break
                case 'select':
                    customFieldData.value = $item.find('> .meta-box-wrap select').val()
                    break
                case 'repeater':
                    customFieldData.value = []
                    let $repeaterItems = $item.find('> .meta-box-wrap > .lcf-repeater > .field-group-items > li')
                    $repeaterItems.each((index, el) => {
                        let $current = $(el)
                        let fieldGroup = $current.find('> .field-line-wrapper > .field-group')
                        customFieldData.value.push(getRepeaterItemData(fieldGroup.find('> li')))
                    })
                    break
                default:
                    customFieldData = null
                    break
            }
            return customFieldData
        }

        let getRepeaterItemData = ($where) => {
            let data = []
            $where.each((index, el) => {
                let $current = $(el)
                data.push(getRepeaterItemValue($current))
            })

            return data
        }

        let getRepeaterItemValue = ($item) => {
            let customFieldData = $.extend(true, {}, $item.data('lcf-registered-data'))
            switch (customFieldData.type) {
                case 'text':
                case 'number':
                case 'email':
                case 'password':
                case 'image':
                case 'file':
                    customFieldData.value = $item.find('> .repeater-item-input input').val()
                    break
                case 'wysiwyg':
                    customFieldData.value = Helpers.wysiwygGetContent(
                        $item.find('> .repeater-item-input > .lcf-wysiwyg-wrapper > .wysiwyg-editor')
                    )
                    break
                case 'textarea':
                    customFieldData.value = $item.find('> .repeater-item-input textarea').val()
                    break
                case 'checkbox':
                    customFieldData.value = []
                    $item.find('> .repeater-item-input input:checked').each((index, el) => {
                        customFieldData.value.push($(el).val())
                    })
                    break
                case 'radio':
                    customFieldData.value = $item.find('> .repeater-item-input input:checked').val()
                    break
                case 'select':
                    customFieldData.value = $item.find('> .repeater-item-input select').val()
                    break
                case 'repeater':
                    customFieldData.value = []
                    let $repeaterItems = $item.find('> .repeater-item-input > .lcf-repeater > .field-group-items > li')
                    $repeaterItems.each((index, el) => {
                        let $current = $(el)
                        let fieldGroup = $current.find('> .field-line-wrapper > .field-group')
                        customFieldData.value.push(getRepeaterItemData(fieldGroup.find('> li')))
                    })
                    break
                default:
                    customFieldData = null
                    break
            }
            return customFieldData
        }

        _self.$_EXPORT_TO.closest('form').on('submit', () => {
            _self.$_EXPORT_TO.val(Helpers.jsonEncode(getFieldGroups()))
        })
    }
}

;(($) => {
    $(document).ready(() => {
        new UseCustomFields()

        document.addEventListener('core-init-resources', function () {
            new UseCustomFields()
        })
    })
})(jQuery)
