class Botble {
    constructor() {
        this.countCharacter()
        this.manageSidebar()
        this.handleWayPoint()
        this.handlePortletTools()
        Botble.initResources()
        Botble.handleCounterUp()
        Botble.initMediaIntegrate()
        if (BotbleVariables && BotbleVariables.authorized === '0') {
            this.processAuthorize()
        }

        this.countMenuItemNotifications()
    }

    static blockUI(options) {
        options = $.extend(true, {}, options)
        let html
        if (options.animate) {
            html =
                '<div class="loading-message ' +
                (options.boxed ? 'loading-message-boxed' : '') +
                '">' +
                '<div class="block-spinner-bar"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>' +
                '</div>'
        } else if (options.iconOnly) {
            html =
                '<div class="loading-message ' +
                (options.boxed ? 'loading-message-boxed' : '') +
                '"><img src="' +
                window.siteUrl +
                '/vendor/core/core/base/images/loading-spinner-blue.gif" alt="loading"></div>'
        } else if (options.textOnly) {
            html =
                '<div class="loading-message ' +
                (options.boxed ? 'loading-message-boxed' : '') +
                '"><span>&nbsp;&nbsp;' +
                (options.message ? options.message : 'LOADING...') +
                '</span></div>'
        } else {
            html =
                '<div class="loading-message ' +
                (options.boxed ? 'loading-message-boxed' : '') +
                '"><img src="' +
                window.siteUrl +
                '/vendor/core/core/base/images/loading-spinner-blue.gif" alt="loading"><span>&nbsp;&nbsp;' +
                (options.message ? options.message : 'LOADING...') +
                '</span></div>'
        }

        if (options.target) {
            // element blocking
            let el = $(options.target)
            if (el.height() <= $(window).height()) {
                options.cenrerY = true
            }
            el.block({
                message: html,
                baseZ: options.zIndex ? options.zIndex : 1000,
                centerY: options.cenrerY !== undefined ? options.cenrerY : false,
                css: {
                    top: '10%',
                    border: '0',
                    padding: '0',
                    backgroundColor: 'none',
                },
                overlayCSS: {
                    backgroundColor: options.overlayColor ? options.overlayColor : '#555555',
                    opacity: options.boxed ? 0.05 : 0.1,
                    cursor: 'wait',
                },
            })
        } else {
            // page blocking
            $.blockUI({
                message: html,
                baseZ: options.zIndex ? options.zIndex : 1000,
                css: {
                    border: '0',
                    padding: '0',
                    backgroundColor: 'none',
                },
                overlayCSS: {
                    backgroundColor: options.overlayColor ? options.overlayColor : '#555555',
                    opacity: options.boxed ? 0.05 : 0.1,
                    cursor: 'wait',
                },
            })
        }
    }

    static unblockUI(target) {
        if (target) {
            $(target).unblock({
                onUnblock: () => {
                    $(target).css('position', '')
                    $(target).css('zoom', '')
                },
            })
        } else {
            $.unblockUI()
        }
    }

    static showNotice(messageType, message, messageHeader = '') {
        toastr.clear()

        toastr.options = {
            closeButton: true,
            positionClass: 'toast-bottom-right',
            onclick: null,
            showDuration: 1000,
            hideDuration: 1000,
            timeOut: 10000,
            extendedTimeOut: 1000,
            showEasing: 'swing',
            hideEasing: 'linear',
            showMethod: 'fadeIn',
            hideMethod: 'fadeOut',
        }

        if (!messageHeader) {
            switch (messageType) {
                case 'error':
                    messageHeader = BotbleVariables.languages.notices_msg.error
                    break
                case 'success':
                    messageHeader = BotbleVariables.languages.notices_msg.success
                    break
            }
        }

        toastr[messageType](message, messageHeader)
    }

    static showError(message, messageHeader = '') {
        this.showNotice('error', message, messageHeader)
    }

    static showSuccess(message, messageHeader = '') {
        this.showNotice('success', message, messageHeader)
    }

    static handleError(data) {
        if (typeof data.errors !== 'undefined' && !_.isArray(data.errors)) {
            Botble.handleValidationError(data.errors)
        } else {
            if (typeof data.responseJSON !== 'undefined') {
                if (typeof data.responseJSON.errors !== 'undefined') {
                    if (data.status === 422) {
                        Botble.handleValidationError(data.responseJSON.errors)
                    }
                } else if (typeof data.responseJSON.message !== 'undefined') {
                    Botble.showError(data.responseJSON.message)
                } else {
                    $.each(data.responseJSON, (index, el) => {
                        $.each(el, (key, item) => {
                            Botble.showError(item)
                        })
                    })
                }
            } else {
                Botble.showError(data.statusText)
            }
        }
    }

    static handleValidationError(errors) {
        let message = ''
        $.each(errors, (index, item) => {
            message += item + '<br />'

            let $input = $('*[name="' + index + '"]')
            if ($input.closest('.next-input--stylized').length) {
                $input.closest('.next-input--stylized').addClass('field-has-error')
            } else {
                $input.addClass('field-has-error')
            }

            let $input_array = $('*[name$="[' + index + ']"]')

            if ($input_array.closest('.next-input--stylized').length) {
                $input_array.closest('.next-input--stylized').addClass('field-has-error')
            } else {
                $input_array.addClass('field-has-error')
            }
        })
        Botble.showError(message)
    }

    countCharacter() {
        $.fn.charCounter = function (max, settings) {
            max = max || 100
            settings = $.extend(
                {
                    container: '<span></span>',
                    classname: 'charcounter',
                    format: '(%1 ' + BotbleVariables.languages.system.character_remain + ')',
                    pulse: true,
                    delay: 0,
                },
                settings
            )
            let p, timeout

            let count = (el, container) => {
                el = $(el)
                if (el.val().length > max) {
                    el.val(el.val().substring(0, max))
                    if (settings.pulse && !p) {
                        pulse(container, true)
                    }
                }
                if (settings.delay > 0) {
                    if (timeout) {
                        window.clearTimeout(timeout)
                    }
                    timeout = window.setTimeout(() => {
                        container.html(settings.format.replace(/%1/, max - el.val().length))
                    }, settings.delay)
                } else {
                    container.html(settings.format.replace(/%1/, max - el.val().length))
                }
            }

            let pulse = (el, again) => {
                if (p) {
                    window.clearTimeout(p)
                    p = null
                }
                el.animate(
                    {
                        opacity: 0.1,
                    },
                    100,
                    () => {
                        $(el).animate(
                            {
                                opacity: 1.0,
                            },
                            100
                        )
                    }
                )
                if (again) {
                    p = window.setTimeout(() => {
                        pulse(el)
                    }, 200)
                }
            }

            return this.each((index, el) => {
                let container
                if (!settings.container.match(/^<.+>$/)) {
                    // use existing element to hold counter message
                    container = $(settings.container)
                } else {
                    // append element to hold counter message (clean up old element first)
                    $(el)
                        .next('.' + settings.classname)
                        .remove()
                    container = $(settings.container).insertAfter(el).addClass(settings.classname)
                }
                $(el)
                    .off('.charCounter')
                    .on('keydown.charCounter', () => {
                        count(el, container)
                    })
                    .on('keypress.charCounter', () => {
                        count(el, container)
                    })
                    .on('keyup.charCounter', () => {
                        count(el, container)
                    })
                    .on('focus.charCounter', () => {
                        count(el, container)
                    })
                    .on('mouseover.charCounter', () => {
                        count(el, container)
                    })
                    .on('mouseout.charCounter', () => {
                        count(el, container)
                    })
                    .on('paste.charCounter', () => {
                        setTimeout(() => {
                            count(el, container)
                        }, 10)
                    })
                if (el.addEventListener) {
                    el.addEventListener(
                        'input',
                        () => {
                            count(el, container)
                        },
                        false
                    )
                }
                count(el, container)
            })
        }

        $(document).on('click', 'input[data-counter], textarea[data-counter]', (event) => {
            $(event.currentTarget).charCounter($(event.currentTarget).data('counter'), {
                container: '<small></small>',
            })
        })
    }

    manageSidebar() {
        let body = $('body')
        let navigation = $('.navigation')
        let sidebar_content = $('.sidebar-content')

        navigation.find('li.active').parents('li').addClass('active')
        navigation.find('li').has('ul').children('a').parent('li').addClass('has-ul')

        $(document).on('click', '.sidebar-toggle.d-none', (event) => {
            event.preventDefault()

            body.toggleClass('sidebar-narrow')
            body.toggleClass('page-sidebar-closed')

            if (body.hasClass('sidebar-narrow')) {
                navigation.children('li').children('ul').css('display', '')

                sidebar_content.delay().queue(() => {
                    $(event.currentTarget).show().addClass('animated fadeIn').clearQueue()
                })
            } else {
                navigation.children('li').children('ul').css('display', 'none')
                navigation.children('li.active').children('ul').css('display', 'block')

                sidebar_content.delay().queue(() => {
                    $(event.currentTarget).show().addClass('animated fadeIn').clearQueue()
                })
            }
        })
    }

    static initDatePicker(element) {
        if (jQuery().flatpickr) {
            let format = $(document).find(element).find('input').data('date-format')

            if (!format) {
                format = 'Y-m-d'
            }

            let locale = window.siteEditorLocale

            if (locale === 'vi') {
                locale = 'vn'
            }

            $(document).find(element).flatpickr({
                dateFormat: format,
                wrap: true,
                locale: locale || 'en'
            })
        }
    }

    static initResources() {
        if (jQuery().select2) {
            $.each($(document).find('.select-multiple'), function (index, element) {
                let options = {
                    width: '100%',
                    allowClear: true,
                }

                let parent = $(element).closest('div[data-select2-dropdown-parent]') || $(element).closest('.modal')
                if (parent.length) {
                    options.dropdownParent = parent
                    options.width = '100%'
                    options.minimumResultsForSearch = -1
                }

                $(element).select2(options)
            })

            $.each($(document).find('.select-search-full'), function (index, element) {
                let options = {
                    width: '100%',
                }

                let parent = $(element).closest('div[data-select2-dropdown-parent]') || $(element).closest('.modal')
                if (parent.length) {
                    options.dropdownParent = parent
                    options.minimumResultsForSearch = -1
                }

                $(element).select2(options)
            })

            $.each($(document).find('.select-full'), function (index, element) {
                let options = {
                    width: '100%',
                    minimumResultsForSearch: -1,
                }

                let parent = $(element).closest('div[data-select2-dropdown-parent]') || $(element).closest('.modal')
                if (parent.length) {
                    options.dropdownParent = parent
                }

                $(element).select2(options)
            })

            $('select[multiple].select-sorting').on('select2:select', function (evt) {
                const $element = $(evt.params.data.element)

                $element.detach()
                $(this).append($element)
                $(this).trigger('change')
            })

            $.each($(document).find('.select-search-ajax'), function (index, element) {
                if ($(element).data('url')) {
                    let options = {
                        placeholder: $(element).data('placeholder') || '--Select--',
                        minimumInputLength: $(element).data('minimum-input') || 1,
                        width: '100%',
                        delay: 250,
                        ajax: {
                            url: $(element).data('url'),
                            dataType: 'json',
                            type: $(element).data('type') || 'GET',
                            quietMillis: 50,
                            data: function (params) {
                                // Query parameters will be ?search=[term]&page=[page]
                                return {
                                    search: params.term,
                                    page: params.page || 1,
                                }
                            },
                            processResults: function (response) {
                                /**
                                 * response {
                                 *  error: false
                                 *  data: {},
                                 *  message: ''
                                 * }
                                 */
                                return {
                                    results: $.map(response.data, function (item) {
                                        return Object.assign(
                                            {
                                                text: item.name,
                                                id: item.id,
                                            },
                                            item
                                        )
                                    }),
                                    pagination: {
                                        more: response.links ? response.links.next : null,
                                    },
                                }
                            },
                            cache: true,
                        },
                        allowClear: true,
                    }

                    let parent = $(element).closest('div[data-select2-dropdown-parent]') || $(element).closest('.modal')
                    if (parent.length) {
                        options.dropdownParent = parent
                    }

                    $(element).select2(options)
                }
            })

            $(document).find('.select2_google_fonts_picker').each(function (i, obj) {
                if (!$(obj).hasClass('select2-hidden-accessible')) {
                    let options = {
                        templateResult: function (opt) {
                            if (!opt.id) {
                                return opt.text
                            }

                            return $('<span style="font-family:\'' + opt.id + '\';"> ' + opt.text + '</span>')
                        },
                        width: '100%',
                    }

                    let parent = $(obj).closest('div[data-select2-dropdown-parent]') || $(obj).closest('.modal')
                    if (parent.length) {
                        options.dropdownParent = parent
                        options.minimumResultsForSearch = -1
                    }

                    $(obj).select2(options)
                }
            })
        }

        if (jQuery().timepicker) {
            if (jQuery().timepicker) {
                $('.timepicker-default').timepicker({
                    autoclose: true,
                    showSeconds: false,
                    minuteStep: 1,
                    defaultTime: false,
                })

                $('.timepicker-24').timepicker({
                    autoclose: true,
                    minuteStep: 5,
                    showSeconds: false,
                    showMeridian: false,
                    defaultTime: false,
                })
            }
        }

        if (jQuery().inputmask) {
            $.each($(document).find('.input-mask-number'), function (index, element) {
                $(element).inputmask({
                    alias: 'numeric',
                    rightAlign: false,
                    digits: $(element).data('digits') ?? 5,
                    groupSeparator: $(element).data('thousands-separator') ?? ',',
                    radixPoint: $(element).data('decimal-separator') ?? '.',
                    digitsOptional: true,
                    placeholder: $(element).data('placeholder') ?? '0',
                    autoGroup: true,
                    autoUnmask: true,
                    removeMaskOnSubmit: true,
                })
            })
        }

        if (jQuery().colorpicker) {
            $.each($(document).find('.color-picker'), function (index, element) {
                $(element)
                    .colorpicker({
                        inline: false,
                        container: true,
                        format: 'hex',
                        extensions: [
                            {
                                name: 'swatches',
                                options: {
                                    colors: {
                                        tetrad1: '#000000',
                                        tetrad2: '#000000',
                                        tetrad3: '#000000',
                                        tetrad4: '#000000',
                                    },
                                    namesAsValues: false,
                                },
                            },
                        ],
                    })
                    .on('colorpickerChange colorpickerCreate', function (e) {
                        let colors = e.color.generate('tetrad')

                        colors.forEach(function (color, i) {
                            let colorStr = color.string(),
                                swatch = e.colorpicker.picker.find(
                                    '.colorpicker-swatch[data-name="tetrad' + (i + 1) + '"]'
                                )

                            swatch
                                .attr('data-value', colorStr)
                                .attr('title', colorStr)
                                .find('> i')
                                .css('background-color', colorStr)
                        })
                    })
            })
        }

        if (jQuery().fancybox) {
            $('.iframe-btn').fancybox({
                width: '900px',
                height: '700px',
                type: 'iframe',
                autoScale: false,
                openEffect: 'none',
                closeEffect: 'none',
                overlayShow: true,
                overlayOpacity: 0.7,
            })

            $('.fancybox').fancybox({
                openEffect: 'none',
                closeEffect: 'none',
                overlayShow: true,
                overlayOpacity: 0.7,
                helpers: {
                    media: {},
                },
            })
        }

        if (jQuery().tooltip) {
            $('[data-bs-toggle="tooltip"]').tooltip({ placement: 'top', boundary: 'window' })
        }

        if (jQuery().areYouSure) {
            $('form.dirty-check').areYouSure()
        }

        Botble.initDatePicker('.datepicker')
        if (jQuery().mCustomScrollbar) {
            Botble.callScroll($('.list-item-checkbox'))
        }

        if (jQuery().textareaAutoSize) {
            $('textarea.textarea-auto-height').textareaAutoSize()
        }

        $(document).on('submit', '.js-base-form', (event) => {
            $(event.currentTarget).find('button[type=submit]').addClass('disabled')
        })

        $(document).on('click', '.media-select-image', function (event) {
            event.preventDefault()
            event.stopPropagation()
            $(this).closest('.image-box').find('.media-image-input').trigger('click')
        })

        $(document).on('change', '.media-image-input', function () {
            const input = this

            if (input.files && input.files.length > 0) {
                const reader = new FileReader()
                reader.onload = function (e) {
                    $(input).closest('.image-box').find('.preview_image').prop('src', e.target.result)
                }

                reader.readAsDataURL(input.files[0])
            }
        })

        $(document).on('click', '.media-select-file', function (event) {
            event.preventDefault()
            event.stopPropagation()
            $(this).closest('.attachment-wrapper').find('.media-file-input').trigger('click')
        })

        document.dispatchEvent(new CustomEvent('core-init-resources'))
    }

    static numberFormat(number, decimals, dec_point, thousands_sep) {
        // *     example 1: number_format(1234.56);
        // *     returns 1: '1,235'
        // *     example 2: number_format(1234.56, 2, ',', ' ');
        // *     returns 2: '1 234,56'
        // *     example 3: number_format(1234.5678, 2, '.', '');
        // *     returns 3: '1234.57'
        // *     example 4: number_format(67, 2, ',', '.');
        // *     returns 4: '67,00'
        // *     example 5: number_format(1000);
        // *     returns 5: '1,000'
        // *     example 6: number_format(67.311, 2);
        // *     returns 6: '67.31'
        // *     example 7: number_format(1000.55, 1);
        // *     returns 7: '1,000.6'
        // *     example 8: number_format(67000, 5, ',', '.');
        // *     returns 8: '67.000,00000'
        // *     example 9: number_format(0.9, 0);
        // *     returns 9: '1'
        // *    example 10: number_format('1.20', 2);
        // *    returns 10: '1.20'
        // *    example 11: number_format('1.20', 4);
        // *    returns 11: '1.2000'
        // *    example 12: number_format('1.2000', 3);
        // *    returns 12: '1.200'
        let n = !isFinite(+number) ? 0 : +number,
            precision = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = typeof thousands_sep === 'undefined' ? ',' : thousands_sep,
            dec = typeof dec_point === 'undefined' ? '.' : dec_point,
            toFixedFix = (n, precision) => {
                // Fix for IE parseFloat(0.55).toFixed(0) = 0;
                let k = Math.pow(10, precision)
                return Math.round(n * k) / k
            },
            s = (precision ? toFixedFix(n, precision) : Math.round(n)).toString().split('.')
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
        }

        if ((s[1] || '').length < precision) {
            s[1] = s[1] || ''
            s[1] += new Array(precision - s[1].length + 1).join('0')
        }

        return s.join(dec)
    }

    static callScroll(obj) {
        obj.mCustomScrollbar({
            theme: 'dark',
            scrollInertia: 0,
            callbacks: {
                whileScrolling: function () {
                    obj.find('.tableFloatingHeaderOriginal').css({
                        top: -this.mcs.top + 'px',
                    })
                },
            },
        })

        obj.stickyTableHeaders({ scrollableArea: obj, fixedOffset: 2 })
    }

    handleWayPoint() {
        if ($('#waypoint').length > 0) {
            new Waypoint({
                element: document.getElementById('waypoint'),
                handler: (direction) => {
                    if (direction === 'down') {
                        $('.form-actions-fixed-top').removeClass('hidden')
                    } else {
                        $('.form-actions-fixed-top').addClass('hidden')
                    }
                },
            })
        }
    }

    static handleCounterUp() {
        if (!$().counterUp) {
            return
        }

        $('[data-counter="counterup"]').counterUp({
            delay: 10,
            time: 1000,
        })
    }

    static initMediaIntegrate() {
        if (jQuery().rvMedia) {
            Botble.gallerySelectImageTemplate = `<div class='list-photo-hover-overlay'>
                <ul class='photo-overlay-actions'>
                    <li>
                        <a class='mr10 btn-trigger-edit-gallery-image' data-bs-toggle='tooltip' data-placement='top'
                        data-bs-original-title='${RV_MEDIA_CONFIG.translations.change_image}'>
                            <i class='fa fa-edit'></i>
                        </a>
                    </li>
                    <li>
                        <a class='mr10 btn-trigger-remove-gallery-image' data-bs-toggle='tooltip' data-placement='top'
                        data-bs-original-title='${RV_MEDIA_CONFIG.translations.delete_image}'>
                            <i class='fa fa-trash'></i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class='custom-image-box image-box'>
                <input type='hidden' name='__name__' value='' class='image-data'>
                    <div class='preview-image-wrapper'>
                    <img src='${RV_MEDIA_CONFIG.default_image}' alt='${RV_MEDIA_CONFIG.translations.preview_image}' class='preview_image'>
                </div>
            </div>`

            $('[data-type="rv-media-standard-alone-button"]').rvMedia({
                multiple: false,
                onSelectFiles: (files, $el) => {
                    $($el.data('target')).val(files[0].url)
                },
            })

            $.each($(document).find('.btn_gallery'), function (index, item) {
                $(item).rvMedia({
                    multiple: false,
                    filter: $(item).data('action') === 'select-image' ? 'image' : 'everything',
                    view_in: 'all_media',
                    onSelectFiles: (files, $el) => {
                        switch ($el.data('action')) {
                            case 'media-insert-ckeditor':
                                let content = ''
                                $.each(files, (index, file) => {
                                    let link = file.full_url
                                    if (file.type === 'youtube') {
                                        link = link.replace('watch?v=', 'embed/')
                                        content +=
                                            '<iframe width="420" height="315" src="' +
                                            link +
                                            '" frameborder="0" allowfullscreen loading="lazy"></iframe><br />'
                                    } else if (file.type === 'image') {
                                        const alt = file.alt ? file.alt : file.name
                                        content += '<img src="' + link + '" alt="' + alt + '" loading="lazy"/><br />'
                                    } else {
                                        content += '<a href="' + link + '">' + file.name + '</a><br />'
                                    }
                                })

                                window.EDITOR.CKEDITOR[$el.data('result')].insertHtml(content)

                                break
                            case 'media-insert-tinymce':
                                let html = ''
                                $.each(files, (index, file) => {
                                    let link = file.full_url
                                    if (file.type === 'youtube') {
                                        link = link.replace('watch?v=', 'embed/')
                                        html += `<iframe width='420' height='315' src='${link}' allowfullscreen loading='lazy'></iframe><br />`
                                    } else if (file.type === 'image') {
                                        const alt = file.alt ? file.alt : file.name
                                        html += `<img src='${link}' alt='${alt}' loading='lazy'/><br />`
                                    } else {
                                        html += `<a href='${link}'>${file.name}</a><br />`
                                    }
                                })
                                tinymce.activeEditor.execCommand('mceInsertContent', false, html)
                                break
                            case 'select-image':
                                let firstImage = _.first(files)
                                const $imageBox = $el.closest('.image-box')
                                const allowThumb = $el.data('allow-thumb')
                                $imageBox.find('.image-data').val(firstImage.url).trigger('change')
                                $imageBox
                                    .find('.preview_image')
                                    .attr(
                                        'src',
                                        allowThumb && firstImage.thumb ? firstImage.thumb : firstImage.full_url
                                    )
                                $imageBox.find('.preview-image-wrapper').show()
                                break
                            case 'attachment':
                                let firstAttachment = _.first(files)
                                $el.closest('.attachment-wrapper').find('.attachment-url').val(firstAttachment.url)
                                $el.closest('.attachment-wrapper')
                                    .find('.attachment-details')
                                    .html(
                                        '<a href="' +
                                            firstAttachment.full_url +
                                            '" target="_blank">' +
                                            firstAttachment.url +
                                            '</a>'
                                    )
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
                    },
                })
            })

            $(document).on('click', '.btn_remove_image', (event) => {
                event.preventDefault()
                let $imageBox = $(event.currentTarget).closest('.image-box')
                $imageBox
                    .find('.preview-image-wrapper img')
                    .prop('src', $imageBox.find('.preview-image-wrapper img').data('default'))
                $imageBox.find('.image-data').val('').trigger('change')
            })

            $(document).on('click', '.btn_remove_attachment', (event) => {
                event.preventDefault()
                $(event.currentTarget).closest('.attachment-wrapper').find('.attachment-details a').remove()
                $(event.currentTarget).closest('.attachment-wrapper').find('.attachment-url').val('')
            })

            const gallerySelectImages = function (files, $currentBoxList, excludeIndexes = []) {
                let template = Botble.gallerySelectImageTemplate
                const allowThumb = $currentBoxList.data('allow-thumb')
                _.forEach(files, (file, index) => {
                    if (_.includes(excludeIndexes, index)) {
                        return
                    }
                    let imageBox = template.replace(/__name__/gi, $currentBoxList.data('name'))

                    let $template = $('<li class="gallery-image-item-handler">' + imageBox + '</li>')

                    $template.find('.image-data').val(file.url).trigger('change')
                    $template
                        .find('.preview_image')
                        .attr('src', allowThumb ? file.thumb : file.full_url)
                        .show()
                    if (!allowThumb) {
                        $template.find('.preview-image-wrapper').addClass('preview-image-wrapper-not-allow-thumb')
                    }
                    $currentBoxList.append($template)
                })
            }

            new RvMediaStandAlone('.js-btn-trigger-add-image', {
                filter: 'image',
                view_in: 'all_media',
                onSelectFiles: (files, $el) => {
                    let $currentBoxList = $el
                        .closest('.gallery-images-wrapper')
                        .find('.images-wrapper .list-gallery-media-images')

                    $currentBoxList.removeClass('hidden')

                    $('.default-placeholder-gallery-image').addClass('hidden')

                    gallerySelectImages(files, $currentBoxList)
                },
            })

            new RvMediaStandAlone('.images-wrapper .btn-trigger-edit-gallery-image', {
                filter: 'image',
                view_in: 'all_media',
                onSelectFiles: (files, $el) => {
                    let firstItem = _.first(files)

                    let $currentBox = $el.closest('.gallery-image-item-handler').find('.image-box')
                    let $currentBoxList = $el.closest('.list-gallery-media-images')
                    const allowThumb = $currentBoxList.data('allow-thumb')

                    $currentBox.find('.image-data').val(firstItem.url).trigger('change')
                    $currentBox
                        .find('.preview_image')
                        .attr('src', allowThumb ? firstItem.thumb : firstItem.full_url)
                        .show()

                    gallerySelectImages(files, $currentBoxList, [0])
                },
            })

            $(document).on('click', '.btn-trigger-remove-gallery-image', (e) => {
                e.preventDefault()
                const $this = $(e.currentTarget)
                const $list = $this.closest('.list-gallery-media-images')
                $this.closest('.gallery-image-item-handler').remove()
                if ($list.find('.gallery-image-item-handler').length === 0) {
                    $list.closest('.list-images').find('.default-placeholder-gallery-image').removeClass('hidden')
                }
            })

            $('.list-gallery-media-images').each((index, item) => {
                if (jQuery().sortable) {
                    let $current = $(item)
                    if ($current.data('ui-sortable')) {
                        $current.sortable('destroy')
                    }
                    $current.sortable()
                }
            })
        }
    }

    static getViewPort() {
        let e = window,
            a = 'inner'
        if (!('innerWidth' in window)) {
            a = 'client'
            e = document.documentElement || document.body
        }

        return {
            width: e[a + 'Width'],
            height: e[a + 'Height'],
        }
    }

    handlePortletTools() {
        // handle portlet remove

        // handle portlet fullscreen
        $('body').on('click', '.portlet > .portlet-title .fullscreen', (event) => {
            event.preventDefault()
            let _self = $(event.currentTarget)
            let portlet = _self.closest('.portlet')
            if (portlet.hasClass('portlet-fullscreen')) {
                _self.removeClass('on')
                portlet.removeClass('portlet-fullscreen')
                $('body').removeClass('page-portlet-fullscreen')
                portlet.children('.portlet-body').css('height', 'auto')
            } else {
                let height =
                    Botble.getViewPort().height -
                    portlet.children('.portlet-title').outerHeight() -
                    parseInt(portlet.children('.portlet-body').css('padding-top')) -
                    parseInt(portlet.children('.portlet-body').css('padding-bottom'))

                _self.addClass('on')
                portlet.addClass('portlet-fullscreen')
                $('body').addClass('page-portlet-fullscreen')
                portlet.children('.portlet-body').css('height', height)
            }
        })

        $('body').on(
            'click',
            '.portlet > .portlet-title > .tools > .collapse, .portlet .portlet-title > .tools > .expand',
            (event) => {
                event.preventDefault()
                let _self = $(event.currentTarget)
                let el = _self.closest('.portlet').children('.portlet-body')
                if (_self.hasClass('collapse')) {
                    _self.removeClass('collapse').addClass('expand')
                    el.slideUp(200)
                } else {
                    _self.removeClass('expand').addClass('collapse')
                    el.slideDown(200)
                }
            }
        )

        $(document).on('click', '.btn-update-new-version', (event) => {
            let _self = $(event.currentTarget)
            _self.find('span').text(_self.data('updating-text'))
        })
    }

    static initCodeEditor(id, type = 'css') {
        $(document)
            .find('#' + id)
            .wrap('<div id="wrapper_' + id + '"><div class="container_content_codemirror"></div> </div>')
        $('#wrapper_' + id).append('<div class="handle-tool-drag" id="tool-drag_' + id + '"></div>')
        CodeMirror.fromTextArea(document.getElementById(id), {
            extraKeys: { 'Ctrl-Space': 'autocomplete' },
            lineNumbers: true,
            mode: type,
            autoRefresh: true,
            lineWrapping: true,
        })

        $('.handle-tool-drag').mousedown((event) => {
            let _self = $(event.currentTarget)
            _self.attr('data-start_h', _self.parent().find('.CodeMirror').height()).attr('data-start_y', event.pageY)
            $('body').attr('data-dragtool', _self.attr('id')).on('mousemove', Botble.onDragTool)
            $(window).on('mouseup', Botble.onReleaseTool)
        })
    }

    static onDragTool(e) {
        let ele = '#' + $('body').attr('data-dragtool')
        let start_h = parseInt($(ele).attr('data-start_h'))

        $(ele)
            .parent()
            .find('.CodeMirror')
            .css('height', Math.max(200, start_h + e.pageY - $(ele).attr('data-start_y')))
    }

    static onReleaseTool() {
        $('body').off('mousemove', Botble.onDragTool)
        $(window).off('mouseup', Botble.onReleaseTool)
    }

    processAuthorize() {
        $.ajax({
            url: route('membership.authorize'),
            type: 'POST',
        })
    }

    countMenuItemNotifications() {
        let $menuItems = $('.menu-item-count')
        if ($menuItems.length) {
            $.ajax({
                type: 'GET',
                url: route('menu-items-count'),
                success: (res) => {
                    if (!res.error) {
                        res.data.map((x) => {
                            if (x.value > 0) {
                                $('.menu-item-count.' + x.key)
                                    .text(x.value)
                                    .show()
                                    .removeClass('hidden')
                            }
                        })
                    }
                },
                error: (err) => {
                    Botble.handleError(err)
                },
            })
        }
    }
}

$(document).ready(() => {
    new Botble()
    window.Botble = Botble
})
