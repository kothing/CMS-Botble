import { App } from './app'

class Layout {
    constructor() {
        this.resBreakpointMd = App.getResponsiveBreakpoint('md')
        this.$body = $('body')

        this.initSidebar(null)
        this.initContent()
        this.initFooter()
    }

    // Set proper height for sidebar and content. The content and sidebar height must be synced always.
    static handleSidebarAndContentHeight() {
        let content = $('.page-content')
        let sidebar = $('.page-sidebar')
        let header = $('.page-header')
        let footer = $('.page-footer')
        let body = $('body')
        let height

        if (body.hasClass('page-footer-fixed') === true && body.hasClass('page-sidebar-fixed') === false) {
            let available_height = App.getViewPort().height - footer.outerHeight() - header.outerHeight()
            let sidebar_height = sidebar.outerHeight()
            if (sidebar_height > available_height) {
                available_height = sidebar_height + footer.outerHeight()
            }
            if (content.height() < available_height) {
                content.css('min-height', available_height)
            }
        } else {
            if (body.hasClass('page-sidebar-fixed')) {
                height = Layout._calculateFixedSidebarViewportHeight()
                if (body.hasClass('page-footer-fixed') === false) {
                    height = height - footer.outerHeight()
                }
            } else {
                let headerHeight = header.outerHeight()
                let footerHeight = footer.outerHeight()

                if (App.getViewPort().width < App.getResponsiveBreakpoint('md')) {
                    height = App.getViewPort().height - headerHeight - footerHeight
                } else {
                    height = sidebar.height() + 20
                }

                if (height + headerHeight + footerHeight <= App.getViewPort().height) {
                    height = App.getViewPort().height - headerHeight - footerHeight
                }
            }
            content.css('min-height', height)
        }
    }

    // Handle sidebar menu
    handleSidebarMenu() {
        let current = this

        // offcanvas mobile menu
        $('.page-sidebar-mobile-offcanvas .responsive-toggler').on('click', (e) => {
            current.$body.toggleClass('page-sidebar-mobile-offcanvas-open')
            e.preventDefault()
            e.stopPropagation()
        })

        if (this.$body.hasClass('page-sidebar-mobile-offcanvas')) {
            $(document).on('click', (e) => {
                if (current.$body.hasClass('page-sidebar-mobile-offcanvas-open')) {
                    if (
                        $(e.target).closest('.page-sidebar-mobile-offcanvas .responsive-toggler').length === 0 &&
                        $(e.target).closest('.page-sidebar-wrapper').length === 0
                    ) {
                        current.$body.removeClass('page-sidebar-mobile-offcanvas-open')
                        e.preventDefault()
                        e.stopPropagation()
                    }
                }
            })
        }

        // handle sidebar link click
        $('.page-sidebar-menu').on('click', 'li > a.nav-toggle, li > a > span.nav-toggle', (e) => {
            let that = $(e.currentTarget).closest('.nav-item').children('.nav-link')
            let menu = $('.page-sidebar-menu')

            if (
                App.getViewPort().width >= current.resBreakpointMd &&
                !menu.attr('data-initialized') &&
                current.$body.hasClass('page-sidebar-closed') &&
                that.parent('li').parent('.page-sidebar-menu').length === 1
            ) {
                return
            }

            let hasSubMenu = that.next().hasClass('sub-menu')

            if (
                App.getViewPort().width >= current.resBreakpointMd &&
                that.parents('.page-sidebar-menu-hover-submenu').length === 1
            ) {
                // exit of hover sidebar menu
                return
            }

            if (hasSubMenu === false) {
                if (App.getViewPort().width < current.resBreakpointMd && $('.page-sidebar').hasClass('in')) {
                    // close the menu on mobile view while laoding a page
                    $('.page-header .responsive-toggler').trigger('click')
                }
                return
            }

            let parent = that.parent().parent()
            let the = that
            let sub = that.next()

            let autoScroll = menu.data('auto-scroll')
            let slideSpeed = parseInt(menu.data('slide-speed'))
            let keepExpand = menu.data('keep-expanded')

            if (!keepExpand) {
                parent.children('li.open').children('a').children('.arrow').removeClass('open')
                parent.children('li.open').children('.sub-menu:not(.always-open)').slideUp(slideSpeed)
                parent.children('li.open').removeClass('open')
            }

            let slideOffeset = -200

            if (sub.is(':visible')) {
                $('.arrow', the).removeClass('open')
                the.parent().removeClass('open')
                sub.slideUp(slideSpeed, () => {
                    if (autoScroll === true && current.$body.hasClass('page-sidebar-closed') === false) {
                        App.scrollTo(the, slideOffeset)
                    }
                    Layout.handleSidebarAndContentHeight()
                })
            } else if (hasSubMenu) {
                $('.arrow', the).addClass('open')
                the.parent().addClass('open')
                sub.slideDown(slideSpeed, () => {
                    if (autoScroll === true && current.$body.hasClass('page-sidebar-closed') === false) {
                        App.scrollTo(the, slideOffeset)
                    }
                    Layout.handleSidebarAndContentHeight()
                })
            }

            e.preventDefault()
        })

        // handle scrolling to top on responsive menu toggler click when header is fixed for mobile view
        $(document).on('click', '.page-header-fixed-mobile .page-header .responsive-toggler', () => {
            App.scrollTop()
        })

        // handle sidebar hover effect
        this.handleFixedSidebarHoverEffect()
    }

    // Helper function to calculate sidebar height for fixed sidebar layout.
    static _calculateFixedSidebarViewportHeight() {
        let sidebarHeight = App.getViewPort().height - $('.page-header').outerHeight(true)
        if ($('body').hasClass('page-footer-fixed')) {
            sidebarHeight = sidebarHeight - $('.page-footer').outerHeight()
        }

        return sidebarHeight
    }

    // Handles fixed sidebar
    handleFixedSidebar() {
        let menu = $('.page-sidebar-menu')

        Layout.handleSidebarAndContentHeight()

        if (
            App.getViewPort().width >= App.getResponsiveBreakpoint('md') &&
            !$('body').hasClass('page-sidebar-menu-not-fixed')
        ) {
            menu.attr('data-height', Layout._calculateFixedSidebarViewportHeight())
            Layout.handleSidebarAndContentHeight()
        }
    }

    // Handles sidebar toggler to close/hide the sidebar.
    handleFixedSidebarHoverEffect() {
        let current = this
        if (this.$body.hasClass('page-sidebar-fixed')) {
            $('.page-sidebar')
                .on('mouseenter', (event) => {
                    if (current.$body.hasClass('page-sidebar-closed')) {
                        $(event.currentTarget).find('.page-sidebar-menu').removeClass('page-sidebar-menu-closed')
                    }
                })
                .on('mouseleave', (event) => {
                    if (current.$body.hasClass('page-sidebar-closed')) {
                        $(event.currentTarget).find('.page-sidebar-menu').addClass('page-sidebar-menu-closed')
                    }
                })
        }
    }

    // Handles sidebar toggler
    handleSidebarToggler() {
        // handle sidebar show/hide
        let body = this.$body
        const _self = this
        this.$body.on('click', '.sidebar-toggler', (event) => {
            event.preventDefault()
            let sidebarMenu = $('.page-sidebar-menu')

            if (body.hasClass('page-sidebar-closed')) {
                body.removeClass('page-sidebar-closed')
                sidebarMenu.removeClass('page-sidebar-menu-closed')
                _self._toggleSidebarMenu()
            } else {
                body.addClass('page-sidebar-closed')
                sidebarMenu.addClass('page-sidebar-menu-closed')
                if (body.hasClass('page-sidebar-fixed')) {
                    sidebarMenu.trigger('mouseleave')
                }
                _self._toggleSidebarMenu(true)
            }

            $(window).trigger('resize')
        })
    }

    _toggleSidebarMenu(status = false) {
        $.ajax({
            url: route('admin.sidebar-menu.toggle'),
            type: 'POST',
            dataType: 'json',
            data: { status },
            error: (data) => {
                Botble.handleError(data)
            },
        })
    }

    // Handles Bootstrap Tabs.
    handleTabs() {
        // fix content height on tab click
        this.$body.on('shown.bs.tab', 'a[data-bs-toggle="tab"]', () => {
            Layout.handleSidebarAndContentHeight()
        })
    }

    // Handles the go to top button at the footer
    handleGoTop() {
        let offset = 300
        let duration = 500

        if (navigator.userAgent.match(/iPhone|iPad|iPod/i)) {
            // ios supported
            $(window).bind('touchend touchcancel touchleave', (event) => {
                if ($(event.currentTarget).scrollTop() > offset) {
                    $('.scroll-to-top').fadeIn(duration)
                } else {
                    $('.scroll-to-top').fadeOut(duration)
                }
            })
        } else {
            // general
            $(window).scroll((event) => {
                if ($(event.currentTarget).scrollTop() > offset) {
                    $('.scroll-to-top').fadeIn(duration)
                } else {
                    $('.scroll-to-top').fadeOut(duration)
                }
            })
        }

        $('.scroll-to-top').on('click', (e) => {
            e.preventDefault()
            $('html, body').animate({ scrollTop: 0 }, duration)
            return false
        })
    }

    // Handle 100% height elements(block, portlet, etc)
    handle100HeightContent() {
        let current = this
        $('.full-height-content').each((index, el) => {
            let target = $(el)
            let height

            height =
                App.getViewPort().height -
                $('.page-header').outerHeight(true) -
                $('.page-footer').outerHeight(true) -
                $('.page-title').outerHeight(true)

            if (target.hasClass('portlet')) {
                let portletBody = target.find('.portlet-body')

                height =
                    height -
                    target.find('.portlet-title').outerHeight(true) -
                    parseInt(target.find('.portlet-body').css('padding-top')) -
                    parseInt(target.find('.portlet-body').css('padding-bottom')) -
                    5

                if (
                    App.getViewPort().width >= current.resBreakpointMd &&
                    target.hasClass('full-height-content-scrollable')
                ) {
                    height = height - 35
                    portletBody.find('.full-height-content-body').css('height', height)
                } else {
                    portletBody.css('min-height', height)
                }
            } else {
                if (
                    App.getViewPort().width >= current.resBreakpointMd &&
                    target.hasClass('full-height-content-scrollable')
                ) {
                    height = height - 35
                    target.find('.full-height-content-body').css('height', height)
                } else {
                    target.css('min-height', height)
                }
            }
        })
    }

    initSidebar() {
        this.handleFixedSidebar() // handles fixed sidebar menu
        this.handleSidebarMenu() // handles main menu
        this.handleSidebarToggler() // handles sidebar hide/show

        App.addResizeHandler(this.handleFixedSidebar) // reinitialize fixed sidebar on window resize
    }

    initContent() {
        this.handle100HeightContent() // handles 100% height elements(block, portlet, etc)
        this.handleTabs() // handle bootstrap tabs

        App.addResizeHandler(Layout.handleSidebarAndContentHeight) // recalculate sidebar & content height on window resize
        App.addResizeHandler(this.handle100HeightContent) // reinitialize content height on window resize
    }

    initFooter() {
        this.handleGoTop() //handles scroll to top functionality in the footer
    }
}

$(document).ready(() => {
    new Layout()
    window.Layout = Layout
})
