let resizeHandlers = []

export class App {
    constructor() {
        // IE mode
        this.isIE8 = false
        this.isIE9 = false
        this.isIE10 = false
        this.$body = $('body')
        this.$html = $('html')

        // Core handlers
        this.handleInit() // initialize core

        this.handleOnResize() // set and handle responsive

        // Handle group element heights
        App.addResizeHandler(this.handleHeight) // handle auto calculating height on window resize

        // UI Component handlers
        this.handleTabs() // handle tabs
        if (jQuery().tooltip) {
            this.handleTooltips() // handle bootstrap tooltips
        }
        this.handleModals() // handle modals

        // Hacks
        this.handleFixInputPlaceholderForIE() //IE8 & IE9 input placeholder issue fix
    }

    // Wrapper function to scroll(focus) to an element
    static scrollTo(el, offset) {
        let pos = el && el.length > 0 ? el.offset().top : 0

        if (el) {
            if ($('body').hasClass('page-header-fixed')) {
                pos = pos - $('.page-header').height()
            } else if ($('body').hasClass('page-header-top-fixed')) {
                pos = pos - $('.page-header-top').height()
            } else if ($('body').hasClass('page-header-menu-fixed')) {
                pos = pos - $('.page-header-menu').height()
            }
            pos = pos + (offset ? offset : -1 * el.height())
        }

        $('html,body').animate(
            {
                scrollTop: pos,
            },
            'slow'
        )
    }

    // function to scroll to the top
    static scrollTop() {
        App.scrollTo()
    }

    // To get the correct viewport width based on  http://andylangton.co.uk/articles/javascript/get-viewport-size-javascript/
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

    static getResponsiveBreakpoint(size) {
        // bootstrap responsive breakpoints
        let sizes = {
            xs: 480, // extra small
            sm: 768, // small
            md: 992, // medium
            lg: 1200, // large
        }

        return sizes[size] ? sizes[size] : 0
    }

    // initializes main settings
    handleInit() {
        this.isIE8 = !!navigator.userAgent.match(/MSIE 8.0/)
        this.isIE9 = !!navigator.userAgent.match(/MSIE 9.0/)
        this.isIE10 = !!navigator.userAgent.match(/MSIE 10.0/)

        if (this.isIE10) {
            this.$html.addClass('ie10') // detect IE10 version
        }

        if (this.isIE10 || this.isIE9 || this.isIE8) {
            this.$html.addClass('ie') // detect IE10 version
        }
    }

    handleTabs() {
        //activate tab if tab id provided in the URL
        if (encodeURI(location.hash)) {
            let tab_id = encodeURI(location.hash.substr(1))
            let $tab = $('a[href="#' + tab_id + '"]')
            $tab.parents('.tab-pane:hidden').each((index, el) => {
                $('a[href="#' + $(el).attr('id') + '"]').trigger('click')
            })
            $tab.trigger('click')
        }
    }

    handleModals() {
        let current = this
        // fix stackable modal issue: when 2 or more modals opened, closing one of modal will remove .modal-open class.
        this.$body.on('hide.bs.modal', () => {
            let $modals = $('.modal:visible')
            if ($modals.length > 1 && current.$html.hasClass('modal-open') === false) {
                current.$html.addClass('modal-open')
            } else if ($modals.length <= 1) {
                current.$html.removeClass('modal-open')
            }
        })

        // fix page scrollbars issue
        this.$body.on('show.bs.modal', '.modal', (event) => {
            if ($(event.currentTarget).hasClass('modal-scroll')) {
                current.$body.addClass('modal-open-noscroll')
            }
        })

        // fix page scrollbars issue
        this.$body.on('hidden.bs.modal', '.modal', () => {
            current.$body.removeClass('modal-open-noscroll')
        })

        // remove ajax content and remove cache on modal closed
        this.$body.on('hidden.bs.modal', '.modal:not(.modal-cached)', (event) => {
            $(event.currentTarget).removeData('bs.modal')
        })
    }

    // Handles Bootstrap Tooltips.
    handleTooltips() {
        // global tooltips
        $('.tooltips').tooltip()

        // portlet tooltips
        $('.portlet > .portlet-title .fullscreen').tooltip({
            trigger: 'hover',
            container: 'body',
            title: 'Fullscreen',
        })
        $('.portlet > .portlet-title > .tools > .reload').tooltip({
            trigger: 'hover',
            container: 'body',
            title: 'Reload',
        })
        $('.portlet > .portlet-title > .tools > .remove').tooltip({
            trigger: 'hover',
            container: 'body',
            title: 'Remove',
        })
        $('.portlet > .portlet-title > .tools > .config').tooltip({
            trigger: 'hover',
            container: 'body',
            title: 'Settings',
        })
        $('.portlet > .portlet-title > .tools > .collapse, .portlet > .portlet-title > .tools > .expand').tooltip({
            trigger: 'hover',
            container: 'body',
            title: 'Collapse/Expand',
        })
    }

    // Fix input placeholder issue for IE8 and IE9
    handleFixInputPlaceholderForIE() {
        //fix html5 placeholder attribute for ie7 & ie8
        if (this.isIE8 || this.isIE9) {
            // ie8 & ie9
            // this is html5 placeholder fix for inputs, inputs with placeholder-no-fix class will be skipped(e.g: we need this for password fields)
            $('input[placeholder]:not(.placeholder-no-fix), textarea[placeholder]:not(.placeholder-no-fix)').each(
                (index, el) => {
                    let input = $(el)

                    if (input.val() === '' && input.attr('placeholder') !== '') {
                        input.addClass('placeholder').val(input.attr('placeholder'))
                    }

                    input.focus(() => {
                        if (input.val() === input.attr('placeholder')) {
                            input.val('')
                        }
                    })

                    input.blur(() => {
                        if (input.val() === '' || input.val() === input.attr('placeholder')) {
                            input.val(input.attr('placeholder'))
                        }
                    })
                }
            )
        }
    }

    // handle group element heights
    handleHeight() {
        $('[data-auto-height]').each((index, el) => {
            let parent = $(el)
            let items = $('[data-height]', parent)
            let height = 0
            let mode = parent.attr('data-mode')
            let offset = parseInt(parent.attr('data-offset') ? parent.attr('data-offset') : 0)

            items.each((key, sub) => {
                if ($(sub).attr('data-height') === 'height') {
                    $(sub).css('height', '')
                } else {
                    $(sub).css('min-height', '')
                }

                let height_ = mode === 'base-height' ? $(sub).outerHeight() : $(sub).outerHeight(true)
                if (height_ > height) {
                    height = height_
                }
            })

            height = height + offset

            items.each((key, sub) => {
                if ($(sub).attr('data-height') === 'height') {
                    $(sub).css('height', height)
                } else {
                    $(sub).css('min-height', height)
                }
            })

            if (parent.attr('data-related')) {
                $(parent.attr('data-related')).css('height', parent.height())
            }
        })
    }

    //public function to add callback a function which will be called on window resize
    static addResizeHandler(func) {
        resizeHandlers.push(func)
    }

    // runs callback functions set by App.addResponsiveHandler().
    static runResizeHandlers() {
        // reinitialize other subscribed elements
        for (let i = 0; i < resizeHandlers.length; i++) {
            let each = resizeHandlers[i]
            each.call()
        }
    }

    handleOnResize() {
        let windowWidth = $(window).width()
        let resize
        if (this.isIE8) {
            let currentHeight
            $(window).resize(() => {
                if (currentHeight === document.documentElement.clientHeight) {
                    return //quite event since only body resized not window.
                }
                if (resize) {
                    clearTimeout(resize)
                }
                resize = setTimeout(() => {
                    App.runResizeHandlers()
                }, 50) // wait 50ms until window resize finishes.
                currentHeight = document.documentElement.clientHeight // store last body client height
            })
        } else {
            $(window).resize(() => {
                if ($(window).width() !== windowWidth) {
                    windowWidth = $(window).width()
                    if (resize) {
                        clearTimeout(resize)
                    }
                    resize = setTimeout(() => {
                        App.runResizeHandlers()
                    }, 50) // wait 50ms until window resize finishes.
                }
            })
        }
    }
}

$(document).ready(() => {
    new App()
    window.App = App
})
