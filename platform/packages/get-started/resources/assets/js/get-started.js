window.getStartedCookie = (() => {
    const COOKIE_VALUE = 1
    const COOKIE_NAME = 'get_started_cookie'
    const COOKIE_DOMAIN = window.location.hostname

    function setupCookie() {
        setCookie(COOKIE_NAME, COOKIE_VALUE, 1)
    }

    function cookieExists() {
        return document.cookie.split('; ').indexOf(COOKIE_NAME + '=' + COOKIE_VALUE) !== -1
    }

    function setCookie(name, value, expirationInDays) {
        const date = new Date()
        date.setTime(date.getTime() + expirationInDays * 24 * 60 * 60 * 1000)
        document.cookie = name + '=' + value + ';expires=' + date.toUTCString() + ';domain=' + COOKIE_DOMAIN + ';path=/'
    }

    return {
        setupCookie: setupCookie,
        cookieExists: cookieExists,
    }
})()

$(document).ready(function () {
    if (!window.getStartedCookie.cookieExists()) {
        $('.get-started-modal[data-step="1"]').modal('show')
    }

    $(document).on('click', '.get-started-modal button[type=submit]', function (event) {
        event.preventDefault()
        event.stopPropagation()

        let _self = $(this)

        _self.addClass('button-loading')

        $.ajax({
            method: 'POST',
            cache: false,
            url: _self.closest('form').prop('action'),
            data: new FormData(_self.closest('form')[0]),
            contentType: false,
            processData: false,
            success: (res) => {
                if (res.error) {
                    _self.removeClass('button-loading')
                    Botble.showError(res.message)
                    return false
                }

                _self.removeClass('button-loading')
                _self.closest('.get-started-modal').modal('hide')
                $('.get-started-modal[data-step="' + res.data.step + '"]').modal('show')
            },
            error: (error) => {
                _self.removeClass('button-loading')
                Botble.handleError(error)
            },
        })
    })

    $('.get-started-modal .btn-close').on('click', function (event) {
        event.preventDefault()
        let step = $(this).closest('.get-started-modal').data('step')
        $('.js-back-to-wizard').data('step', step)
        $(this).closest('.get-started-modal').modal('hide')

        $('.close-get-started-modal').modal('show')
    })

    $(document).on('click', '.js-back-to-wizard', function (event) {
        event.preventDefault()
        $(this).closest('.close-get-started-modal').modal('hide')
        $('.get-started-modal[data-step="' + $(this).data('step') + '"]').modal('show')
    })

    $(document).on('click', '.js-close-wizard', function (event) {
        event.preventDefault()
        window.getStartedCookie.setupCookie()
        $(this).closest('.close-get-started-modal').modal('hide')
    })

    $(document).on('click', '.resume-setup-wizard', function (event) {
        event.preventDefault()
        $('.get-started-modal[data-step="1"]').modal('show')
    })
})
