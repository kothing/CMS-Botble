class PluginAnalytics {
    static initCharts() {
        let stats = $('div[data-stats]').data('stats')
        let country_stats = $('div[data-country-stats]').data('country-stats')
        let lang_page_views = $('div[data-lang-pageviews]').data('lang-pageviews')
        let lang_visits = $('div[data-lang-visits]').data('lang-visits')

        let statArray = []
        $.each(stats, (index, el) => {
            statArray.push({ axis: el.axis, visitors: el.visitors, pageViews: el.pageViews })
        })
        if ($('#stats-chart').length) {
            new Morris.Area({
                element: 'stats-chart',
                resize: true,
                data: statArray,
                xkey: 'axis',
                ykeys: ['visitors', 'pageViews'],
                labels: [lang_visits, lang_page_views],
                lineColors: ['#dd4d37', '#3c8dbc'],
                hideHover: 'auto',
                parseTime: false,
            })
        }

        let visitorsData = {}

        $.each(country_stats, (index, el) => {
            visitorsData[el[0]] = el[1]
        })

        $(document)
            .find('#world-map')
            .vectorMap({
                map: 'world_mill_en',
                backgroundColor: 'transparent',
                regionStyle: {
                    initial: {
                        fill: '#e4e4e4',
                        'fill-opacity': 1,
                        stroke: 'none',
                        'stroke-width': 0,
                        'stroke-opacity': 1,
                    },
                },
                series: {
                    regions: [
                        {
                            values: visitorsData,
                            scale: ['#c64333', '#dd4b39'],
                            normalizeFunction: 'polynomial',
                        },
                    ],
                },
                onRegionLabelShow: (e, el, code) => {
                    if (typeof visitorsData[code] !== 'undefined') {
                        el.html(el.html() + ': ' + visitorsData[code] + ' ' + lang_visits)
                    }
                },
            })
    }
}

$(document).ready(() => {
    BDashboard.loadWidget(
        $('#widget_analytics_general').find('.widget-content'),
        route('analytics.general'),
        null,
        () => {
            PluginAnalytics.initCharts()
        }
    )
    BDashboard.loadWidget($('#widget_analytics_page').find('.widget-content'), route('analytics.page'))
    BDashboard.loadWidget($('#widget_analytics_browser').find('.widget-content'), route('analytics.browser'))
    BDashboard.loadWidget($('#widget_analytics_referrer').find('.widget-content'), route('analytics.referrer'))
})
