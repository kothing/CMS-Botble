class SocialLoginManagement {
    init() {
        $('#social_login_enable').on('change', (event) => {
            if ($(event.currentTarget).prop('checked')) {
                $('.wrapper-list-social-login-options').show()
            } else {
                $('.wrapper-list-social-login-options').hide()
            }
        })

        $('.enable-social-login-option').on('change', (event) => {
            let _self = $(event.currentTarget)
            if (_self.prop('checked')) {
                _self.closest('.wrapper-content').find('.enable-social-login-option-wrapper').show()
                _self.closest('.form-group').removeClass('mb-0')
            } else {
                _self.closest('.wrapper-content').find('.enable-social-login-option-wrapper').hide()
                _self.closest('.form-group').addClass('mb-0')
            }
        })
    }
}

$(document).ready(() => {
    new SocialLoginManagement().init()
})
