'use strict'

class GalleryManagement {
    init() {
        let container = document.querySelector('#list-photo')
        let masonry
        // initialize Masonry after all images have loaded
        if (container) {
            imagesLoaded(container, () => {
                masonry = new Masonry(container, {
                    isOriginLeft: $('body').prop('dir') !== 'rtl',
                })
            })
        }

        $('#list-photo').lightGallery({
            loop: true,
            thumbnail: true,
            fourceAutoply: false,
            autoplay: false,
            pager: false,
            speed: 300,
            scale: 1,
            keypress: true,
        })

        $(document).on('click', '.lg-toogle-thumb', () => {
            $(document).find('.lg-sub-html').toggleClass('inactive')
        })
    }
}

$(document).ready(() => {
    new GalleryManagement().init()
})
