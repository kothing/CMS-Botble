;(($, DataTable) => {
    'use strict'

    let _buildParams = (dt, action) => {
        let params = dt.ajax.params()
        params.action = action
        params._token = $('meta[name="csrf-token"]').attr('content')

        return params
    }

    let _downloadFromUrl = function (url, params) {
        let postUrl = url + '/export'
        let xhr = new XMLHttpRequest()
        xhr.open('POST', postUrl, true)
        xhr.responseType = 'arraybuffer'
        xhr.onload = function () {
            if (this.status === 200) {
                let filename = ''
                let disposition = xhr.getResponseHeader('Content-Disposition')
                if (disposition && disposition.indexOf('attachment') !== -1) {
                    let filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/
                    let matches = filenameRegex.exec(disposition)
                    if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '')
                }
                let type = xhr.getResponseHeader('Content-Type')

                let blob = new Blob([this.response], { type: type })
                if (typeof window.navigator.msSaveBlob !== 'undefined') {
                    // IE workaround for "HTML7007: One or more blob URLs were revoked by closing the blob for which they were created. These URLs will no longer resolve as the data backing the URL has been freed."
                    window.navigator.msSaveBlob(blob, filename)
                } else {
                    let URL = window.URL || window.webkitURL
                    let downloadUrl = URL.createObjectURL(blob)

                    if (filename) {
                        // use HTML5 a[download] attribute to specify filename
                        let a = document.createElement('a')
                        // safari doesn't support this yet
                        if (typeof a.download === 'undefined') {
                            window.location = downloadUrl
                        } else {
                            a.href = downloadUrl
                            a.download = filename
                            document.body.appendChild(a)
                            a.trigger('click')
                        }
                    } else {
                        window.location = downloadUrl
                    }

                    setTimeout(() => {
                        URL.revokeObjectURL(downloadUrl)
                    }, 100) // cleanup
                }
            }
        }
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded')
        xhr.send($.param(params))
    }

    let _buildUrl = (dt, action) => {
        let url = dt.ajax.url() || ''
        let params = dt.ajax.params()
        params.action = action

        if (url.indexOf('?') > -1) {
            return url + '&' + $.param(params)
        }

        return url + '?' + $.param(params)
    }

    DataTable.ext.buttons.excel = {
        className: 'buttons-excel',

        text: (dt) => {
            return (
                '<i class="far fa-file-excel"></i> ' +
                dt.i18n(
                    'buttons.excel',
                    BotbleVariables.languages.tables.excel ? BotbleVariables.languages.tables.excel : 'Excel'
                )
            )
        },

        action: (e, dt) => {
            window.location = _buildUrl(dt, 'excel')
        },
    }

    DataTable.ext.buttons.postExcel = {
        className: 'buttons-excel',

        text: (dt) => {
            return (
                '<i class="far fa-file-excel"></i> ' +
                dt.i18n(
                    'buttons.excel',
                    BotbleVariables.languages.tables.excel ? BotbleVariables.languages.tables.excel : 'Excel'
                )
            )
        },

        action: (e, dt) => {
            let url = dt.ajax.url() || window.location.href
            let params = _buildParams(dt, 'excel')

            _downloadFromUrl(url, params)
        },
    }

    DataTable.ext.buttons.export = {
        extend: 'collection',

        className: 'buttons-export',

        text: (dt) => {
            return (
                '<i class="fa fa-download"></i> ' +
                dt.i18n(
                    'buttons.export',
                    BotbleVariables.languages.tables.export ? BotbleVariables.languages.tables.export : 'Export'
                ) +
                '&nbsp;<span class="caret"/>'
            )
        },

        buttons: ['csv', 'excel'],
    }

    DataTable.ext.buttons.csv = {
        className: 'buttons-csv',

        text: (dt) => {
            return (
                '<i class="fas fa-file-csv"></i> ' +
                dt.i18n(
                    'buttons.csv',
                    BotbleVariables.languages.tables.csv ? BotbleVariables.languages.tables.csv : 'CSV'
                )
            )
        },

        action: (e, dt) => {
            window.location = _buildUrl(dt, 'csv')
        },
    }

    DataTable.ext.buttons.postCsv = {
        className: 'buttons-csv',

        text: (dt) => {
            return (
                '<i class="fas fa-file-csv"></i> ' +
                dt.i18n(
                    'buttons.csv',
                    BotbleVariables.languages.tables.csv ? BotbleVariables.languages.tables.csv : 'CSV'
                )
            )
        },

        action: (e, dt) => {
            let url = dt.ajax.url() || window.location.href
            let params = _buildParams(dt, 'csv')

            _downloadFromUrl(url, params)
        },
    }

    DataTable.ext.buttons.pdf = {
        className: 'buttons-pdf',

        text: (dt) => {
            return '<i class="far fa-file-pdf"></i> ' + dt.i18n('buttons.pdf', 'PDF')
        },

        action: (e, dt) => {
            window.location = _buildUrl(dt, 'pdf')
        },
    }

    DataTable.ext.buttons.postPdf = {
        className: 'buttons-pdf',

        text: (dt) => {
            return '<i class="far fa-file-pdf"></i> ' + dt.i18n('buttons.pdf', 'PDF')
        },

        action: (e, dt) => {
            let url = dt.ajax.url() || window.location.href
            let params = _buildParams(dt, 'pdf')

            _downloadFromUrl(url, params)
        },
    }

    DataTable.ext.buttons.print = {
        className: 'buttons-print',

        text: (dt) => {
            return (
                '<i class="fa fa-print"></i> ' +
                dt.i18n(
                    'buttons.print',
                    BotbleVariables.languages.tables.print ? BotbleVariables.languages.tables.print : 'Print'
                )
            )
        },

        action: (e, dt) => {
            window.location = _buildUrl(dt, 'print')
        },
    }

    DataTable.ext.buttons.reset = {
        className: 'buttons-reset',

        text: (dt) => {
            return (
                '<i class="fa fa-undo"></i> ' +
                dt.i18n(
                    'buttons.reset',
                    BotbleVariables.languages.tables.reset ? BotbleVariables.languages.tables.reset : 'Reset'
                )
            )
        },

        action: () => {
            $('.table thead input').val('').keyup()
            $('.table thead select').val('').change()
        },
    }

    DataTable.ext.buttons.reload = {
        className: 'buttons-reload',

        text: (dt) => {
            return (
                '<i class="fas fa-sync"></i> ' +
                dt.i18n(
                    'buttons.reload',
                    BotbleVariables.languages.tables.reload ? BotbleVariables.languages.tables.reload : 'Reload'
                )
            )
        },

        action: (e, dt) => {
            dt.draw(false)
        },
    }

    DataTable.ext.buttons.create = {
        className: 'buttons-create',

        text: (dt) => {
            return '<i class="fa fa-plus"></i> ' + dt.i18n('buttons.create', 'Create')
        },

        action: () => {
            window.location = window.location.href.replace(/\/+$/, '') + '/create'
        },
    }

    if (typeof DataTable.ext.buttons.copyHtml5 !== 'undefined') {
        $.extend(DataTable.ext.buttons.copyHtml5, {
            text: (dt) => {
                return '<i class="fa fa-copy"></i> ' + dt.i18n('buttons.copy', 'Copy')
            },
        })
    }

    if (typeof DataTable.ext.buttons.colvis !== 'undefined') {
        $.extend(DataTable.ext.buttons.colvis, {
            text: (dt) => {
                return '<i class="fa fa-eye"></i> ' + dt.i18n('buttons.colvis', 'Column visibility')
            },
        })
    }

    class TableManagement {
        constructor() {
            this.init()
            this.handleActionsRow()
            this.handleActionsExport()
        }

        init() {
            $(document).on('change', '.table-check-all', (event) => {
                let _self = $(event.currentTarget)
                let set = _self.attr('data-set')
                let checked = _self.prop('checked')
                $(set).each((index, el) => {
                    if (checked) {
                        $(el).prop('checked', true)
                    } else {
                        $(el).prop('checked', false)
                    }
                })
            })

            $(document).find('.table-check-all').closest('th').removeAttr('title')

            $(document).on('change', '.checkboxes', (event) => {
                let _self = $(event.currentTarget)
                let table = _self.closest('.table-wrapper').find('.table').prop('id')

                let ids = []
                let $table = $('#' + table)
                $table.find('.checkboxes:checked').each((i, el) => {
                    ids[i] = $(el).val()
                })

                if (ids.length !== $table.find('.checkboxes').length) {
                    _self.closest('.table-wrapper').find('.table-check-all').prop('checked', false)
                } else {
                    _self.closest('.table-wrapper').find('.table-check-all').prop('checked', true)
                }
            })

            $(document).on('click', '.btn-show-table-options', (event) => {
                event.preventDefault()
                $(event.currentTarget).closest('.table-wrapper').find('.table-configuration-wrap').slideToggle(500)
            })

            $(document).on('click', '.action-item', (event) => {
                event.preventDefault()
                let span = $(event.currentTarget).find('span[data-href]')
                let action = span.data('action')
                let url = span.data('href')
                if (action && url !== '#') {
                    window.location.href = url
                }
            })
        }

        handleActionsRow() {
            let that = this
            $(document).on('click', '.deleteDialog', (event) => {
                event.preventDefault()
                let _self = $(event.currentTarget)

                $('.delete-crud-entry')
                    .data('section', _self.data('section'))
                    .data('parent-table', _self.closest('.table').prop('id'))
                $('.modal-confirm-delete').modal('show')
            })

            $('.delete-crud-entry').on('click', (event) => {
                event.preventDefault()
                let _self = $(event.currentTarget)

                _self.addClass('button-loading')

                let deleteURL = _self.data('section')

                $.ajax({
                    url: deleteURL,
                    type: 'POST',
                    data: { _method: 'DELETE' },
                    success: (data) => {
                        if (data.error) {
                            Botble.showError(data.message)
                        } else {
                            window.LaravelDataTables[_self.data('parent-table')]
                                .row($('a[data-section="' + deleteURL + '"]').closest('tr'))
                                .remove()
                                .draw()
                            Botble.showSuccess(data.message)
                        }

                        _self.closest('.modal').modal('hide')
                        _self.removeClass('button-loading')
                    },
                    error: (data) => {
                        Botble.handleError(data)
                        _self.removeClass('button-loading')
                    },
                })
            })

            $(document).on('click', '.delete-many-entry-trigger', (event) => {
                event.preventDefault()
                let _self = $(event.currentTarget)

                let table = _self.closest('.table-wrapper').find('.table').prop('id')

                let ids = []
                $('#' + table)
                    .find('.checkboxes:checked')
                    .each((i, el) => {
                        ids[i] = $(el).val()
                    })

                if (ids.length === 0) {
                    Botble.showError(
                        BotbleVariables.languages.tables.please_select_record
                            ? BotbleVariables.languages.tables.please_select_record
                            : 'Please select at least one record to perform this action!'
                    )
                    return false
                }

                $('.delete-many-entry-button')
                    .data('href', _self.prop('href'))
                    .data('parent-table', table)
                    .data('class-item', _self.data('class-item'))
                $('.delete-many-modal').modal('show')
            })

            $('.delete-many-entry-button').on('click', (event) => {
                event.preventDefault()

                let _self = $(event.currentTarget)

                _self.addClass('button-loading')

                let $table = $('#' + _self.data('parent-table'))

                let ids = []
                $table.find('.checkboxes:checked').each((i, el) => {
                    ids[i] = $(el).val()
                })

                $.ajax({
                    url: _self.data('href'),
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        ids: ids,
                        class: _self.data('class-item'),
                    },
                    success: (data) => {
                        if (data.error) {
                            Botble.showError(data.message)
                        } else {
                            Botble.showSuccess(data.message)
                        }

                        $table.find('.table-check-all').prop('checked', false)
                        window.LaravelDataTables[_self.data('parent-table')].draw()
                        _self.closest('.modal').modal('hide')
                        _self.removeClass('button-loading')
                    },
                    error: (data) => {
                        Botble.handleError(data)
                        _self.removeClass('button-loading')
                    },
                })
            })

            $(document).on('click', '.bulk-change-item', (event) => {
                event.preventDefault()
                let _self = $(event.currentTarget)

                let table = _self.closest('.table-wrapper').find('.table').prop('id')

                let ids = []
                $('#' + table)
                    .find('.checkboxes:checked')
                    .each((i, el) => {
                        ids[i] = $(el).val()
                    })

                if (ids.length === 0) {
                    Botble.showError(
                        BotbleVariables.languages.tables.please_select_record
                            ? BotbleVariables.languages.tables.please_select_record
                            : 'Please select at least one record to perform this action!'
                    )
                    return false
                }

                that.loadBulkChangeData(_self)

                $('.confirm-bulk-change-button')
                    .data('parent-table', table)
                    .data('class-item', _self.data('class-item'))
                    .data('key', _self.data('key'))
                    .data('url', _self.data('save-url'))
                $('.modal-bulk-change-items').modal('show')
            })

            $(document).on('click', '.confirm-bulk-change-button', (event) => {
                event.preventDefault()
                let _self = $(event.currentTarget)
                let value = _self.closest('.modal').find('.input-value').val()
                let inputKey = _self.data('key')

                let $table = $('#' + _self.data('parent-table'))

                let ids = []
                $table.find('.checkboxes:checked').each((i, el) => {
                    ids[i] = $(el).val()
                })

                _self.addClass('button-loading')

                $.ajax({
                    url: _self.data('url'),
                    type: 'POST',
                    data: {
                        ids: ids,
                        key: inputKey,
                        value: value,
                        class: _self.data('class-item'),
                    },
                    success: (data) => {
                        if (data.error) {
                            Botble.showError(data.message)
                        } else {
                            Botble.showSuccess(data.message)
                        }

                        $table.find('.table-check-all').prop('checked', false)
                        $.each(ids, (index, item) => {
                            window.LaravelDataTables[_self.data('parent-table')]
                                .row($table.find('.checkboxes[value="' + item + '"]').closest('tr'))
                                .remove()
                                .draw()
                        })
                        _self.closest('.modal').modal('hide')
                        _self.removeClass('button-loading')
                    },
                    error: (data) => {
                        Botble.handleError(data)
                        _self.removeClass('button-loading')
                    },
                })
            })
        }

        loadBulkChangeData($element) {
            let $modal = $('.modal-bulk-change-items')
            $.ajax({
                type: 'GET',
                url: $modal.find('.confirm-bulk-change-button').data('load-url'),
                data: {
                    class: $element.data('class-item'),
                    key: $element.data('key'),
                },
                success: (res) => {
                    let data = $.map(res.data, (value, key) => {
                        return { id: key, name: value }
                    })
                    let $parent = $('.modal-bulk-change-content')
                    $parent.html(res.html)

                    let $input = $modal.find('input[type=text].input-value')
                    if ($input.length) {
                        $input.typeahead({ source: data })
                        $input.data('typeahead').source = data
                    }

                    Botble.initResources()
                },
                error: (error) => {
                    Botble.handleError(error)
                },
            })
        }

        handleActionsExport() {
            $(document).on('click', '.export-data', (event) => {
                let _self = $(event.currentTarget)
                let table = _self.closest('.table-wrapper').find('.table').prop('id')

                let ids = []
                $('#' + table)
                    .find('.checkboxes:checked')
                    .each((i, el) => {
                        ids[i] = $(el).val()
                    })

                event.preventDefault()
                $.ajax({
                    type: 'POST',
                    url: _self.prop('href'),
                    data: {
                        'ids-checked': ids,
                    },
                    success: (response) => {
                        let a = document.createElement('a')
                        a.href = response.file
                        a.download = response.name
                        document.body.appendChild(a)
                        a.trigger('click')
                        a.remove()
                    },
                    error: (error) => {
                        Botble.handleError(error)
                    },
                })
            })
        }
    }

    $(document).ready(() => {
        new TableManagement()
    })
})(jQuery, jQuery.fn.dataTable)
