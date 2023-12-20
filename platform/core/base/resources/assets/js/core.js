require('./base/app')
require('./base/layout')
require('./script')
require('./notification')

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    },
})
