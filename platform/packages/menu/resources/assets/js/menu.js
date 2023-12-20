import sanitizeHTML from 'sanitize-html'

class MenuNestable {
    constructor() {
        this.$nestable = $('#nestable')
    }

    updatePositionForSerializedObj(arrayObject) {
        let result = arrayObject
        let that = this

        $.each(result, (index, val) => {
            val = val.menuItem
            val.position = index
            if (typeof val.children == 'undefined') {
                val.children = []
            }
            that.updatePositionForSerializedObj(val.children)
        })

        return result
    }

    // Main function to initiate the module
    init() {
        let depth = parseInt(this.$nestable.attr('data-depth'))
        if (depth < 1) {
            depth = 5
        }
        $('.nestable-menu').nestable({
            group: 1,
            maxDepth: depth,
            expandBtnHTML: '',
            collapseBtnHTML: '',
        })

        this.handleNestableMenu()
    }

    handleNestableMenu() {
        let that = this
        // Show node details
        $(document).on('click', '.dd-item .dd3-content a.show-item-details', (e) => {
            e.preventDefault()
            let parent = $(e.currentTarget).parent().parent()
            $(e.currentTarget).toggleClass('active')
            parent.toggleClass('active')
        })

        // Edit attributes
        $(document).on(
            'change blur keyup',
            '.nestable-menu .item-details input, .nestable-menu .item-details select',
            (e) => {
                e.preventDefault()
                let current = $(e.currentTarget)

                let parent = current.closest('li.dd-item')
                let value = sanitizeHTML(current.val())
                let name = sanitizeHTML(current.attr('name'))
                let old = sanitizeHTML(current.attr('data-old'))
                let currentInfo = $.parseJSON(JSON.stringify(parent.data('menu-item')))

                currentInfo[name] = value

                parent.data('menu-item', currentInfo)
                parent.find('> .dd3-content .text[data-update="' + name + '"]').text(value)
                if (value.trim() === '') {
                    parent.find('> .dd3-content .text[data-update="' + name + '"]').text(old)
                }
            }
        )

        // Add nodes
        $(document).on('click', '.box-links-for-menu .btn-add-to-menu', (e) => {
            e.preventDefault()
            let current = $(e.currentTarget)
            let parent = current.parents('.the-box')
            let params = {}

            if (parent.attr('id') === 'external_link') {
                $('#menu-node-create-form')
                    .find('input, textarea, select')
                    .serializeArray()
                    .map(function (x) {
                        params[x.name] = sanitizeHTML(x.value)
                    })

                params.position = $('#nestable .dd-list .dd-item').length + 1

                createMenuNode(params, that, parent)
            } else {
                parent.find('.list-item li.active').each((index, el) => {
                    let find_in = $(el).find('> label')

                    params.reference_type = sanitizeHTML(find_in.data('reference-type'))
                    params.reference_id = sanitizeHTML(find_in.data('reference-id'))
                    params.title = sanitizeHTML(find_in.data('title'))
                    params.menu_id = sanitizeHTML(find_in.data('menu-id'))

                    params.position = $('#nestable .dd-list .dd-item').length + 1 + index

                    createMenuNode(params, that, parent)
                })
            }
        })

        let createMenuNode = (params, current, parent) => {
            $.ajax({
                url: route('menus.get-node'),
                type: 'GET',
                data: { data: params },
                async: false,
                success: (res) => {
                    if (!res.error) {
                        current.appendMenuNode(res.data.html, parent)
                    } else {
                        Botble.showError(res.message)
                    }
                },
                error: (res) => {
                    Botble.handleError(res)
                },
            })
        }

        // Remove nodes
        $('.form-save-menu input[name="deleted_nodes"]').val('')
        $(document).on('click', '.nestable-menu .item-details .btn-remove', (e) => {
            e.preventDefault()
            let $this = $(e.currentTarget)
            let dd_item = $this.parents('.item-details').parent()

            let $elm = $('.form-save-menu input[name="deleted_nodes"]')
            // Add id of deleted nodes to delete in controller
            $elm.val($elm.val() + ' ' + dd_item.data('menu-item').id)
            let children = dd_item.find('> .dd-list').html()
            if (children !== '' && children != null) {
                dd_item.before(children.replace('<script>', '').replace('<\\/script>', ''))
            }
            dd_item.remove()
        })

        $(document).on('click', '.nestable-menu .item-details .btn-cancel', (e) => {
            e.preventDefault()
            const $this = $(e.currentTarget)
            const parent = $this.parents('.item-details').parent()
            parent.find('input, textarea, select').each((index, el) => {
                $(el).val($(el).attr('data-old')).trigger('change')
            })

            parent.removeClass('active')
        })

        $(document).on('change', '.box-links-for-menu .list-item li input[type=checkbox]', (event) => {
            $(event.currentTarget).closest('li').toggleClass('active')
        })

        $(document).on('submit', '.form-save-menu', () => {
            if (that.$nestable.length < 1) {
                $('#nestable-output').val('[]')
            } else {
                let nestable_obj_returned = that.$nestable.nestable('serialize')
                let the_obj = that.updatePositionForSerializedObj(nestable_obj_returned)
                $('#nestable-output').val(JSON.stringify(the_obj))
            }
        })

        let accordion = $('#accordion')

        let toggleChevron = (event) => {
            $(event.target).prev('.widget-heading').find('.narrow-icon').toggleClass('fa-angle-down fa-angle-up')
        }

        accordion.on('hidden.bs.collapse', toggleChevron)
        accordion.on('shown.bs.collapse', toggleChevron)

        Botble.callScroll($('.list-item'))
    }

    appendMenuNode(html, parent) {
        $('.nestable-menu > ol.dd-list').append(html.replace('<script>', '').replace('<\\/script>', ''))

        $('.nestable-menu').find('.select-full').select2({
            width: '100%',
            minimumResultsForSearch: -1,
        })

        if (parent.attr('id') === 'external_link') {
            parent.find('input:not(.menu_id), textarea, select').val('').trigger('change')
        }

        parent.find('.list-item li.active').removeClass('active').find('input[type=checkbox]').prop('checked', false)

        parent.find('.btn_remove_image').trigger('click')

        Botble.initResources()
        Botble.initMediaIntegrate()
    }
}

$(document).ready(() => {
    new MenuNestable().init()
})
