class SystemInformationManagement {
    init() {
        let s = document.getElementById('txt-report').value
        s = s.replace(/(^\s*)|(\s*$)/gi, '')
        s = s.replace(/[ ]{2,}/gi, ' ')
        s = s.replace(/\n /, '\n')
        document.getElementById('txt-report').value = s

        $('#btn-report').on('click', () => {
            $('#report-wrapper').slideToggle()
        })

        $('#copy-report').on('click', () => {
            $('#txt-report').select()
            document.execCommand('copy')
        })
    }
}

$(document).ready(() => {
    new SystemInformationManagement().init()
})
