import Botble from './utils'

require('./bootstrap')
require('./form')
require('./avatar')

$(document).ready(() => {
    if (window.noticeMessages) {
        window.noticeMessages.forEach((message) => {
            Botble.showNotice(
                message.type,
                message.message,
                message.type === 'error'
                    ? _.get(window.trans, 'notices.error')
                    : _.get(window.trans, 'notices.success')
            )
        })
    }
})
