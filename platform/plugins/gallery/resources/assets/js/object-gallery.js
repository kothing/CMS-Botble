'use strict'

class ObjectGalleryManagement {
    init() {
        $('[data-slider="owl"] .owl-carousel').each((index, el) => {
            let parent = $(el).parent()
            let items
            let itemsDesktop
            let itemsDesktopSmall
            let itemsTablet
            let itemsTabletSmall
            let itemsMobile

            if (parent.data('single-item') === 'true') {
                items = 1
                itemsDesktop = 1
                itemsDesktopSmall = 1
                itemsTablet = 1
                itemsTabletSmall = 1
                itemsMobile = 1
            } else {
                items = parent.data('items')
                itemsDesktop = [1199, parent.data('desktop-items') ? parent.data('desktop-items') : items]
                itemsDesktopSmall = [979, parent.data('desktop-small-items') ? parent.data('desktop-small-items') : 3]
                itemsTablet = [768, parent.data('tablet-items') ? parent.data('tablet-items') : 2]
                itemsMobile = [479, parent.data('mobile-items') ? parent.data('mobile-items') : 1]
            }

            $(el).owlCarousel({
                items: items,
                itemsDesktop: itemsDesktop,
                itemsDesktopSmall: itemsDesktopSmall,
                itemsTablet: itemsTablet,
                itemsTabletSmall: itemsTabletSmall,
                itemsMobile: itemsMobile,
                navigation: !!parent.data('navigation'),
                navigationText: false,
                slideSpeed: parent.data('slide-speed'),
                paginationSpeed: parent.data('pagination-speed'),
                singleItem: !!parent.data('single-item'),
                autoPlay: parent.data('auto-play'),
            })
        })
    }
}

$(document).ready(() => {
    new ObjectGalleryManagement().init()
})
