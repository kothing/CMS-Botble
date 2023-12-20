import { MediaConfig, RecentItems } from '../Config/MediaConfig'

export class Helpers {
    static getUrlParam(paramName, url = null) {
        if (!url) {
            url = window.location.search
        }
        let reParam = new RegExp('(?:[?&]|&)' + paramName + '=([^&]+)', 'i')
        let match = url.match(reParam)
        return match && match.length > 1 ? match[1] : null
    }

    static asset(url) {
        if (url.substring(0, 2) === '//' || url.substring(0, 7) === 'http://' || url.substring(0, 8) === 'https://') {
            return url
        }

        let baseUrl = RV_MEDIA_URL.base_url.substr(-1, 1) !== '/' ? RV_MEDIA_URL.base_url + '/' : RV_MEDIA_URL.base_url

        if (url.substring(0, 1) === '/') {
            return baseUrl + url.substring(1)
        }

        return baseUrl + url
    }

    static showAjaxLoading($element = $('.rv-media-main')) {
        $element.addClass('on-loading').append($('#rv_media_loading').html())
    }

    static hideAjaxLoading($element = $('.rv-media-main')) {
        $element.removeClass('on-loading').find('.loading-wrapper').remove()
    }

    static isOnAjaxLoading($element = $('.rv-media-items')) {
        return $element.hasClass('on-loading')
    }

    static jsonEncode(object) {
        if (typeof object === 'undefined') {
            object = null
        }
        return JSON.stringify(object)
    }

    static jsonDecode(jsonString, defaultValue) {
        if (!jsonString) {
            return defaultValue
        }
        if (typeof jsonString === 'string') {
            let result
            try {
                result = $.parseJSON(jsonString)
            } catch (err) {
                result = defaultValue
            }
            return result
        }
        return jsonString
    }

    static getRequestParams() {
        if (window.rvMedia.options && window.rvMedia.options.open_in === 'modal') {
            return $.extend(true, MediaConfig.request_params, window.rvMedia.options || {})
        }
        return MediaConfig.request_params
    }

    static setSelectedFile(fileId) {
        if (typeof window.rvMedia.options !== 'undefined') {
            window.rvMedia.options.selected_file_id = fileId
        } else {
            MediaConfig.request_params.selected_file_id = fileId
        }
    }

    static getConfigs() {
        return MediaConfig
    }

    static storeConfig() {
        localStorage.setItem('MediaConfig', Helpers.jsonEncode(MediaConfig))
    }

    static storeRecentItems() {
        localStorage.setItem('RecentItems', Helpers.jsonEncode(RecentItems))
    }

    static addToRecent(id) {
        if (id instanceof Array) {
            Helpers.each(id, (value) => {
                RecentItems.push(value)
            })
        } else {
            RecentItems.push(id)
            this.storeRecentItems()
        }
    }

    static getItems() {
        let items = []
        $('.js-media-list-title').each((index, el) => {
            let $box = $(el)
            let data = $box.data() || {}
            data.index_key = $box.index()
            items.push(data)
        })
        return items
    }

    static getSelectedItems() {
        let selected = []
        $('.js-media-list-title input[type=checkbox]:checked').each((index, el) => {
            let $box = $(el).closest('.js-media-list-title')
            let data = $box.data() || {}
            data.index_key = $box.index()
            selected.push(data)
        })
        return selected
    }

    static getSelectedFiles() {
        let selected = []
        $('.js-media-list-title[data-context=file] input[type=checkbox]:checked').each((index, el) => {
            let $box = $(el).closest('.js-media-list-title')
            let data = $box.data() || {}
            data.index_key = $box.index()
            selected.push(data)
        })
        return selected
    }

    static getSelectedFolder() {
        let selected = []
        $('.js-media-list-title[data-context=folder] input[type=checkbox]:checked').each((index, el) => {
            let $box = $(el).closest('.js-media-list-title')
            let data = $box.data() || {}
            data.index_key = $box.index()
            selected.push(data)
        })
        return selected
    }

    static isUseInModal() {
        return window.rvMedia && window.rvMedia.options && window.rvMedia.options.open_in === 'modal'
    }

    static resetPagination() {
        RV_MEDIA_CONFIG.pagination = { paged: 1, posts_per_page: 40, in_process_get_media: false, has_more: true }
    }

    static trans(key) {
        return _.get(RV_MEDIA_CONFIG.translations, key, key)
    }

    static config(key, defaultValue = null) {
        return _.get(RV_MEDIA_CONFIG, key, defaultValue)
    }

    static hasPermission(key) {
        return Helpers.inArray(Helpers.config('permissions', []), key)
    }

    static inArray(array, value) {
        return _.includes(array, value)
    }

    static each(array, callback) {
        return _.each(array, callback);
    }

    static forEach(array, callback) {
        return _.forEach(array, callback)
    }

    static arrayReject(array, callback) {
        return _.reject(array, callback)
    }

    static arrayFilter(array, callback) {
        return _.filter(array, callback)
    }

    static arrayFirst(array) {
        return _.first(array)
    }

    static isArray(value) {
        return _.isArray(value)
    }

    static isEmpty(value) {
        return _.isEmpty(value)
    }

    static size(item) {
        return _.size(item);
    }
}
