import { MediaService } from './MediaService'
import { MessageService } from './MessageService'
import { Helpers } from '../Helpers/Helpers'

export class DownloadService {
    constructor() {
        this.MediaService = new MediaService()

        $(document).on('shown.bs.modal', '#modal_download_url', (event) => {
            $(event.currentTarget).find('.form-download-url input[type=text]').focus()
        })
    }

    async download(urls, onProgress) {
        let _self = this

        urls = $.trim(urls).split(/\r?\n/)

        let index = 0
        let hasError = false
        for (const url of urls) {
            let filename = ''
            try {
                filename = new URL(url).pathname.split('/').pop()
            } catch (e) {
                filename = url
            }
            let ok = onProgress(`${index} / ${urls.length}`, filename, url)
            await new Promise((resolve) => {
                $.ajax({
                    url: RV_MEDIA_URL.download_url,
                    type: 'POST',
                    data: {
                        folderId: Helpers.getRequestParams().folder_id,
                        url,
                    },
                    dataType: 'json',
                    success: (res) => {
                        if (res.error) {
                            hasError = true
                            ok(false, res.message ?? res.data?.message)
                        } else {
                            ok(true, res.message ?? res.data?.message)
                        }
                        resolve()
                    },
                    error: (data) => {
                        MessageService.handleError(data)
                    },
                })
            })

            index += 1
        }

        Helpers.resetPagination()
        _self.MediaService.getMedia(true)
        if (!hasError) {
            DownloadService.closeModal()
            MessageService.showMessage('success', Helpers.trans('message.success_header'))
        }
    }

    static closeModal() {
        $(document).find('#modal_download_url').modal('hide')
    }
}
