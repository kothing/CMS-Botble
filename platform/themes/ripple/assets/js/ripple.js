let searchInput = $('.search-input')
let superSearch = $('.super-search')
let closeSearch = $('.close-search')
let searchResult = $('.search-result')
let timeoutID = null

export class Ripple {
    searchFunction(keyword) {
        clearTimeout(timeoutID)
        timeoutID = setTimeout(() => {
            superSearch.removeClass('search-finished')
            searchResult.fadeOut()
            $.ajax({
                type: 'GET',
                cache: false,
                url: superSearch.data('search-url'),
                data: {
                    q: keyword,
                },
                success: (res) => {
                    if (!res.error) {
                        searchResult.html(res.data.items)
                        superSearch.addClass('search-finished')
                    } else {
                        searchResult.html(res.message)
                    }
                    searchResult.fadeIn(500)
                },
                error: (res) => {
                    searchResult.html(res.responseText)
                    searchResult.fadeIn(500)
                },
            })
        }, 500)
    }

    bindActionToElement() {
        closeSearch.on('click', (event) => {
            event.preventDefault()
            if (closeSearch.hasClass('active')) {
                superSearch.removeClass('active')
                searchResult.hide()
                closeSearch.removeClass('active')
                $('body').removeClass('overflow')
                $('.quick-search > .form-control').focus()
            } else {
                superSearch.addClass('active')
                if (searchInput.val() !== '') {
                    this.searchFunction(searchInput.val())
                }
                $('body').addClass('overflow')
                closeSearch.addClass('active')
            }
        })

        searchInput.keyup((e) => {
            searchInput.val(e.target.value)
            this.searchFunction(e.target.value)
        })
    }
}

$(document).ready(function () {
    new Ripple().bindActionToElement()
})
