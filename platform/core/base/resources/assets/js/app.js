import Axios from 'axios'

window._ = require('lodash')

const axios = Axios.create({
    baseURL: window.location.origin,
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
    },
    withCredentials: true,
})

window.axios = axios
