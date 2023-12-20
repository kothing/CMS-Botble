let AdminNotification = function () {
    const adminNotification = $('#admin-notification')
    $(document).on('click', '#open-notification', function (e) {
        e.preventDefault()

        $('#notification-sidebar').addClass('active')
        adminNotification.find('.current-page').val(1)
        $('#sidebar-notification-backdrop').addClass('sidebar-backdrop')
        if ($('.list-item-notification').html() === '') {
            $('#loading-notification').show()
        }

        $.ajax({
            url: $(this).attr('href'),
            method: 'GET',
            success: (res) => {
                if ($(res).hasClass('no-data-notification')) {
                    adminNotification.find('.action-notification').hide()
                    adminNotification.find('.title-notification-heading').hide()
                }
                $('#sidebar-notification-backdrop').addClass('sidebar-backdrop')
                $('.list-item-notification').html(res)
            },
            complete: () => {
                $('#loading-notification').hide()
            },
        })
    })

    $(document).on('click', '#sidebar-notification-backdrop', function (e) {
        const notificationSidebar = document.getElementById('notification-sidebar')
        const openNotificationSidebar = document.getElementById('open-notification')
        const adminNotification = document.getElementById('admin-notification')

        let targetEl = e.target
        if (targetEl.parentNode !== openNotificationSidebar && targetEl.parentNode !== adminNotification) {
            do {
                if (targetEl === notificationSidebar) {
                    return
                }
                targetEl = targetEl.parentNode
            } while (targetEl)
            $('#sidebar-notification-backdrop').removeClass('sidebar-backdrop')
            $('#notification-sidebar').removeClass('active')
        }
    })

    $(adminNotification).on('click', '#close-notification', function (e) {
        e.preventDefault()

        $('#sidebar-notification-backdrop').removeClass('sidebar-backdrop')
        $('#notification-sidebar').removeClass('active')
    })

    $(adminNotification).on('click', '.mark-read-all', function (e) {
        e.preventDefault()

        $.ajax({
            url: $(this).attr('href'),
            method: 'POST',
            data: {
                _method: 'PUT',
            },
            beforeSend: () => {
                $('#loading-notification').show()
            },
            success: () => {
                $('.list-group-item').addClass('read')
                updateNotificationsCount()
            },
            complete: () => {
                $('#loading-notification').hide()
            },
        })
    })

    $(adminNotification).on('click', '.delete-all-notifications', function (e) {
        e.preventDefault()

        $.ajax({
            url: $(this).attr('href'),
            method: 'POST',
            data: {
                _method: 'DELETE',
            },
            beforeSend: () => {
                $('#loading-notification').show()
            },
            success: (res) => {
                $('#notification-sidebar').html(res)
                adminNotification.find('.action-notification').hide()
                adminNotification.find('.title-notification-heading').hide()
                updateNotificationsCount()
            },
            complete: () => {
                $('#loading-notification').hide()
            },
        })
    })

    $(adminNotification).on('click', '.view-more-notification', function (e) {
        e.preventDefault()

        const pageNow = adminNotification.find('.current-page').val()
        let nextPage = parseInt(pageNow) + 1
        $(this).hide()

        $.ajax({
            url: $('#open-notification').attr('href') + '?page=' + nextPage,
            beforeSend: () => {
                $('#loading-notification').show()
            },
            success: (res) => {
                adminNotification.find('.current-page').val(nextPage++)
                $('.list-item-notification').append(res)
            },
            complete: () => {
                $('#loading-notification').hide()
            },
        })
    })

    $(adminNotification).on('click', '.btn-delete-notification', function (e) {
        e.preventDefault()

        $.ajax({
            url: $(this).data('href'),
            method: 'POST',
            data: {
                _method: 'DELETE',
            },
            beforeSend: () => {
                $('#loading-notification').show()
            },
            success: (res) => {
                $(this).closest('li.list-group-item').fadeOut(500).remove()
                updateItems()
                updateNotificationsCount()
                if (res.view) {
                    $('#notification-sidebar').html(res.view)
                    $('p.action-notification').hide()
                    $('h2.title-notification-heading').hide()
                }
            },
            complete: () => {
                $('#loading-notification').hide()
            },
        })
    })

    $(adminNotification).on('click', '.show-more-description', function (e) {
        e.preventDefault()

        $(`.show-less-${$(this).data('id')}`).show()
        $(this).hide()
        $(`.${$(this).data('class')}`).text($(this).data('description'))
    })

    $(adminNotification).on('click', '.show-less-description', function (e) {
        e.preventDefault()

        $(`.show-more-${$(this).data('id')}`).show()
        $(this).hide()
        $(`.${$(this).data('class')}`).text($(this).data('description'))
    })

    function updateNotificationsCount() {
        const countNotifications = $('#open-notification')

        $.ajax({
            url: countNotifications.data('href'),
            method: 'GET',
            success: (res) => {
                countNotifications.html(res)
            },
        })
    }

    function updateItems() {
        const pageNow = adminNotification.find('.current-page').val()

        $.ajax({
            url: $('#open-notification').attr('href') + '?page=' + pageNow,
            success: (res) => {
                $('.list-item-notification').html(res)
            },
        })
    }
}

$(document).ready(function () {
    AdminNotification()
})
