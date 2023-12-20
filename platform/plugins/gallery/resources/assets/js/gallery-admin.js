'use strict'

$(document).ready(function () {
    $('.btn_select_gallery').rvMedia({
        filter: 'image',
        view_in: 'all_media',
        onSelectFiles: function (files) {
            var last_index = $('.list-photos-gallery .photo-gallery-item:last-child').data('id') + 1
            $.each(files, function (index, file) {
                $('.list-photos-gallery .row').append(
                    '<div class="col-md-2 col-sm-3 col-4 photo-gallery-item" data-id="' +
                        (last_index + index) +
                        '" data-img="' +
                        file.url +
                        '" data-description=""><div class="gallery_image_wrapper"><img src="' +
                        file.thumb +
                        '" alt="image" loading="lazy"/></div></div>'
                )
            })
            initSortable()
            updateItems()
            $('.reset-gallery').removeClass('hidden')
        },
    })

    let initSortable = function () {
        let el = document.getElementById('list-photos-items')
        if (el) {
            Sortable.create(el, {
                group: 'galleries', // or { name: "...", pull: [true, false, clone], put: [true, false, array] }
                sort: true, // sorting inside list
                delay: 0, // time in milliseconds to define when the sorting should start
                disabled: false, // Disables the sortable if set to true.
                store: null, // @see Store
                animation: 150, // ms, animation speed moving items when sorting, `0` — without animation
                handle: '.photo-gallery-item',
                ghostClass: 'sortable-ghost', // Class name for the drop placeholder
                chosenClass: 'sortable-chosen', // Class name for the chosen item
                dataIdAttr: 'data-id',

                forceFallback: false, // ignore the HTML5 DnD behaviour and force the fallback to kick in
                fallbackClass: 'sortable-fallback', // Class name for the cloned DOM Element when using forceFallback
                fallbackOnBody: false, // Appends the cloned DOM Element into the Document's Body

                scroll: true, // or HTMLElement
                scrollSensitivity: 30, // px, how near the mouse must be to an edge to start scrolling.
                scrollSpeed: 10, // px

                // dragging ended
                onEnd: () => {
                    updateItems()
                },
            })
        }
    }

    initSortable()

    let updateItems = function () {
        let items = []
        $.each($('.photo-gallery-item'), (index, widget) => {
            $(widget).data('id', index)
            items.push({ img: $(widget).data('img'), description: $(widget).data('description') })
        })

        $('#gallery-data').val(JSON.stringify(items))
    }

    let $listPhotos = $('.list-photos-gallery')
    let $editGalleryItem = $('#edit-gallery-item')

    $('.reset-gallery').on('click', function (event) {
        event.preventDefault()
        $('.list-photos-gallery .photo-gallery-item').remove()
        updateItems()
        $(this).addClass('hidden')
    })

    $listPhotos.on('click', '.photo-gallery-item', function () {
        let id = $(this).data('id')
        $('#delete-gallery-item').data('id', id)
        $('#update-gallery-item').data('id', id)
        $('#gallery-item-description').val($(this).data('description'))
        $editGalleryItem.modal('show')
    })

    $editGalleryItem.on('click', '#delete-gallery-item', function (event) {
        event.preventDefault()
        $editGalleryItem.modal('hide')
        $listPhotos.find('.photo-gallery-item[data-id=' + $(this).data('id') + ']').remove()
        updateItems()
        if ($listPhotos.find('.photo-gallery-item').length === 0) {
            $('.reset-gallery').addClass('hidden')
        }
    })

    $editGalleryItem.on('click', '#update-gallery-item', function (event) {
        event.preventDefault()
        $editGalleryItem.modal('hide')
        $listPhotos
            .find('.photo-gallery-item[data-id=' + $(this).data('id') + ']')
            .data('description', $('#gallery-item-description').val())
        updateItems()
    })
})
