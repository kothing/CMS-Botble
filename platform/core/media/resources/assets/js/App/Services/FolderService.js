import { MediaConfig } from '../Config/MediaConfig'
import { MediaService } from './MediaService'
import { MessageService } from './MessageService'
import { Helpers } from '../Helpers/Helpers'

export class FolderService {
    constructor() {
        this.MediaService = new MediaService()

        $(document).on('shown.bs.modal', '#modal_add_folder', (event) => {
            $(event.currentTarget).find('.form-add-folder input[type=text]').focus()
        })
    }

    create(folderName) {
        let _self = this

        $.ajax({
            url: RV_MEDIA_URL.create_folder,
            type: 'POST',
            data: {
                parent_id: Helpers.getRequestParams().folder_id,
                name: folderName,
            },
            dataType: 'json',
            beforeSend: () => {
                Helpers.showAjaxLoading()
            },
            success: (res) => {
                if (res.error) {
                    MessageService.showMessage('error', res.message, Helpers.trans('message.error_header'))
                } else {
                    MessageService.showMessage(
                        'success',
                        res.message,
                        Helpers.trans('message.success_header')
                    )
                    Helpers.resetPagination()
                    _self.MediaService.getMedia(true)
                    FolderService.closeModal()
                }
            },
            complete: () => {
                Helpers.hideAjaxLoading()
            },
            error: (data) => {
                MessageService.handleError(data)
            },
        })
    }

    changeFolder(folderId) {
        MediaConfig.request_params.folder_id = folderId
        Helpers.storeConfig()
        this.MediaService.getMedia(true)
    }

    static closeModal() {
        $(document).find('#modal_add_folder').modal('hide')
    }
}
