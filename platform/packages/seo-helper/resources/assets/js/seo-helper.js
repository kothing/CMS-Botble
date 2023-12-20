class SEOHelperManagement {
    constructor() {
        this.$document = $(document)
    }

    static updateSEOTitle(value) {
        if (value) {
            if (!$('#seo_title').val()) {
                $('.page-title-seo').text(value)
            }
            $('.default-seo-description').addClass('hidden')
            $('.existed-seo-meta').removeClass('hidden')
        } else {
            $('.default-seo-description').removeClass('hidden')
            $('.existed-seo-meta').addClass('hidden')
        }
    }

    static updateSEODescription(value) {
        if (value) {
            if (!$('#seo_description').val()) {
                $('.page-description-seo').text(value)
            }
        }
    }

    handleMetaBox() {
        let permalink = this.$document.find('#sample-permalink a')

        if (permalink.length) {
            $('.page-url-seo p').text(permalink.prop('href').replace('?preview=true', ''))
        }

        this.$document.on('click', '.btn-trigger-show-seo-detail', (event) => {
            event.preventDefault()
            $('.seo-edit-section').toggleClass('hidden')
        })

        this.$document.on('keyup', 'input[name=name]', (event) => {
            SEOHelperManagement.updateSEOTitle($(event.currentTarget).val())
        })

        this.$document.on('keyup', 'input[name=title]', (event) => {
            SEOHelperManagement.updateSEOTitle($(event.currentTarget).val())
        })

        this.$document.on('keyup', 'textarea[name=description]', (event) => {
            SEOHelperManagement.updateSEODescription($(event.currentTarget).val())
        })

        this.$document.on('keyup', '#seo_title', (event) => {
            if ($(event.currentTarget).val()) {
                $('.page-title-seo').text($(event.currentTarget).val())
                $('.default-seo-description').addClass('hidden')
                $('.existed-seo-meta').removeClass('hidden')
            } else {
                let $inputName = $('input[name=name]')
                if ($inputName.val()) {
                    $('.page-title-seo').text($inputName.val())
                } else {
                    $('.page-title-seo').text($('input[name=title]').val())
                }
            }
        })

        this.$document.on('keyup', '#seo_description', (event) => {
            if ($(event.currentTarget).val()) {
                $('.page-description-seo').text($(event.currentTarget).val())
            } else {
                $('.page-description-seo').text($('textarea[name=description]').val())
            }
        })
    }
}

$(document).ready(() => {
    new SEOHelperManagement().handleMetaBox()
})
