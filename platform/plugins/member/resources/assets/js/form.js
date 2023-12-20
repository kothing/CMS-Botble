$(document).ready(() => {
    $('.custom-select-image').on('click', function (event) {
        event.preventDefault()
        $(this).closest('.image-box').find('.image_input').trigger('click')
    })

    function readURL(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader()
            reader.onload = function (e) {
                $(input)
                    .closest('.image-box')
                    .find('.preview_image')
                    .prop('src', e.target.result)
            }

            reader.readAsDataURL(input.files[0])
        }
    }

    $('.image_input').on('change', function () {
        readURL(this)
    })
})
