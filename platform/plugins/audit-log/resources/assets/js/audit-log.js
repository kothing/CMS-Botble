$(document).ready(() => {
    BDashboard.loadWidget($('#widget_audit_logs').find('.widget-content'), route('audit-log.widget.activities'))
})
