$(document).ready(() => {
    BDashboard.loadWidget(
        $('#widget_request_errors').find('.widget-content'),
        route('request-log.widget.request-errors')
    )
})
