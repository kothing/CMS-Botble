!(function ($) {
    $.fn.filetree = function (i) {
        const options = {
            animationSpeed: 'slow',
            console: false,
        }

        function init(i) {
            i = $.extend(options, i)
            return this.each(function () {
                $(this)
                    .find('li')
                    .on('click', '.file-opener-i', function (e) {
                        return (
                            e.preventDefault(),
                            $(this).hasClass('fa-plus-square')
                                ? ($(this).addClass('fa-minus-square'), $(this).removeClass('fa-plus-square'))
                                : ($(this).addClass('fa-plus-square'), $(this).removeClass('fa-minus-square')),
                            $(this).parent().toggleClass('closed').toggleClass('open'),
                            !1
                        )
                    })
            })
        }

        if ('object' == typeof i || !i) {
            return init.apply(this, arguments)
        }
    }
})(jQuery)

;(function ($) {
    $.fn.dragScroll = function (options) {
        function init() {
            const $el = $(this)
            let settings = $.extend(
                {
                    scrollVertical: false,
                    scrollHorizontal: true,
                    cursor: null,
                },
                options
            )

            let clicked = false,
                clickY,
                clickX

            let getCursor = function () {
                if (settings.cursor) return settings.cursor
                if (settings.scrollVertical && settings.scrollHorizontal) return 'move'
                if (settings.scrollVertical) return 'row-resize'
                if (settings.scrollHorizontal) return 'col-resize'
            }

            let updateScrollPos = function (e, el) {
                let $el = $(el)
                settings.scrollVertical && $el.scrollTop($el.scrollTop() + (clickY - e.pageY))
                settings.scrollHorizontal && $el.scrollLeft($el.scrollLeft() + (clickX - e.pageX))
            }

            $el.on({
                mousemove: function (e) {
                    clicked && updateScrollPos(e, this)
                },
                mousedown: function (e) {
                    $el.css('cursor', getCursor())
                    clicked = true
                    clickY = e.pageY
                    clickX = e.pageX
                },
                mouseup: function () {
                    clicked = false
                    $el.css('cursor', 'auto')
                },
                mouseleave: function () {
                    clicked = false
                    $el.css('cursor', 'auto')
                },
            })
        }

        if ('object' == typeof options || !options) {
            return init.apply(this, arguments)
        }
    }
})(jQuery)

$(() => {
    $treeWrapper = $('.file-tree-wrapper')

    $treeWrapper.dragScroll()

    const $formLoading = $('.tree-form-container').find('.tree-loading')
    const $treeLoading = $('.tree-categories-container').find('.tree-loading')

    function loadTree(activeId) {
        $treeLoading.removeClass('d-none')
        $treeWrapper.filetree().removeClass('d-none').hide().slideDown('slow')
        $treeLoading.addClass('d-none')

        if (activeId) {
            $treeWrapper.find('li[data-id="' + activeId + '"] .category-name:first').addClass('active')
        }
    }

    loadTree()

    function reloadForm(data) {
        $('.tree-form-body').html(data)
        Botble.initResources()
        Botble.handleCounterUp()
        Botble.initMediaIntegrate()
        if (window.EditorManagement) {
            new EditorManagement().init()
        }
    }

    $(document).on('click', '.tree-categories-container .toggle-tree', function (e) {
        const $this = $(e.currentTarget)
        if ($this.hasClass('open-tree')) {
            $this.text($this.data('collapse'))
            $('.tree-categories-container').find('.folder-root.closed').removeClass('closed').addClass('open')
        } else {
            $this.text($this.data('expand'))
            $('.tree-categories-container').find('.folder-root.open').removeClass('open').addClass('closed')
        }
        $this.toggleClass('open-tree')
    })

    function clearRefSetupDefault() {
        let data = $.ajaxSetup().data
        if (data) {
            delete data.ref_from
            delete data.ref_lang
        }
    }

    function fetchData(url, $el) {
        clearRefSetupDefault()

        $.ajax({
            url: url,
            type: 'GET',
            beforeSend: () => {
                $formLoading.removeClass('d-none')
                $treeWrapper.find('a.active').removeClass('active')
                if ($el) {
                    $el.addClass('active')
                }
            },
            success: (data) => {
                if (data.error) {
                    Botble.showError(data.message)
                } else {
                    reloadForm(data.data)
                }
            },
            error: (data) => {
                Botble.handleError(data)
            },
            complete: () => {
                $formLoading.addClass('d-none')
            },
        })
    }

    $treeWrapper.on('click', '.fetch-data', (event) => {
        event.preventDefault()
        const $this = $(event.currentTarget)
        if ($this.attr('href')) {
            fetchData($this.attr('href'), $this)
        } else {
            $treeWrapper.find('a.active').removeClass('active')
            $this.addClass('active')
        }
    })

    $(document).on('click', '.tree-categories-create', (event) => {
        event.preventDefault()
        const $this = $(event.currentTarget)
        loadCreateForm($this.attr('href'))
    })

    let searchParams = new URLSearchParams(window.location.search)

    function loadCreateForm(url) {
        let data = {}
        if (searchParams.get('ref_lang')) {
            data.ref_lang = searchParams.get('ref_lang')
        }
        $.ajax({
            url: url,
            type: 'GET',
            data: data,
            beforeSend: () => {
                $formLoading.removeClass('d-none')
            },
            success: (data) => {
                if (data.error) {
                    Botble.showError(data.message)
                } else {
                    reloadForm(data.data)
                }
            },
            error: (data) => {
                Botble.handleError(data)
            },
            complete: () => {
                $formLoading.addClass('d-none')
            },
        })
    }

    function reloadTree(activeId, callback) {
        $.ajax({
            url: $treeWrapper.data('url'),
            type: 'GET',
            success: (data) => {
                if (data.error) {
                    Botble.showError(data.message)
                } else {
                    $treeWrapper.html(data.data)
                    loadTree(activeId)

                    if (jQuery().tooltip) {
                        $('[data-bs-toggle="tooltip"]').tooltip({
                            placement: 'top',
                            boundary: 'window',
                        })
                    }

                    if (callback) {
                        callback()
                    }
                }
            },
            error: (data) => {
                Botble.handleError(data)
            },
        })
    }

    $(document).on('click', '#list-others-language a', (event) => {
        event.preventDefault()
        fetchData($(event.currentTarget).prop('href'))
    })

    $(document).on('submit', '.tree-form-container form', (event) => {
        event.preventDefault()
        const $form = $(event.currentTarget)
        const formData = $form.serializeArray()
        const submitter = event.originalEvent?.submitter
        let saveAndEdit = false

        if (submitter && submitter.name) {
            saveAndEdit = submitter.value === 'apply'
            formData.push({ name: submitter.name, value: submitter.value })
        }

        $.ajax({
            url: $form.attr('action'),
            type: $form.attr('method') || 'POST',
            data: formData,
            beforeSend: () => {
                $formLoading.removeClass('d-none')
            },
            success: (data) => {
                if (data.error) {
                    Botble.showError(data.message)
                } else {
                    Botble.showSuccess(data.message)
                    $formLoading.addClass('d-none')

                    const activeId = saveAndEdit && data.data.model ? data.data.model.id : null
                    reloadTree(activeId, function () {
                        if (activeId) {
                            let fetchDataButton = $('.folder-root[data-id="' + activeId + '"] a.fetch-data')
                            if (fetchDataButton.length) {
                                fetchDataButton.trigger('click')
                            } else {
                                location.reload()
                            }
                        } else if ($('.tree-categories-create').length) {
                            $('.tree-categories-create').trigger('click')
                        } else {
                            reloadForm(data.data?.form)
                            $formLoading.addClass('d-none')
                        }
                    })
                }
            },
            error: (data) => {
                Botble.handleError(data)
                $formLoading.addClass('d-none')
            },
        })
    })

    $(document).on('click', '.deleteDialog', (event) => {
        event.preventDefault()
        let _self = $(event.currentTarget)

        $('.delete-crud-entry').data('section', _self.data('section'))
        $('.modal-confirm-delete').modal('show')
    })

    $('.delete-crud-entry').on('click', (event) => {
        event.preventDefault()
        let _self = $(event.currentTarget)

        _self.addClass('button-loading')

        let deleteURL = _self.data('section')

        $.ajax({
            url: deleteURL,
            type: 'POST',
            data: { _method: 'DELETE' },
            success: (data) => {
                if (data.error) {
                    Botble.showError(data.message)
                } else {
                    Botble.showSuccess(data.message)
                    reloadTree()
                    let $createButton = $('.tree-categories-create')
                    if ($createButton.length) {
                        $createButton.trigger('click')
                    } else {
                        reloadForm('')
                    }
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
})
