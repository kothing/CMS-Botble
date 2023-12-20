let MediaConfig = $.parseJSON(localStorage.getItem('MediaConfig')) || {}

let defaultConfig = {
    app_key: RV_MEDIA_CONFIG.random_hash ? RV_MEDIA_CONFIG.random_hash : '21d06709fe1d3abdf0e35ddda89c4b279',
    request_params: {
        view_type: 'tiles',
        filter: 'everything',
        view_in: 'all_media',
        sort_by: 'created_at-desc',
        folder_id: 0,
    },
    hide_details_pane: false,
    icons: {
        folder: 'fa fa-folder',
    },
    actions_list: {
        basic: [
            {
                icon: 'fa fa-eye',
                name: 'Preview',
                action: 'preview',
                order: 0,
                class: 'rv-action-preview',
            },
            {
                icon: 'fa fa-crop',
                name: 'Crop',
                action: 'crop',
                order: 1,
                class: 'rv-action-crop',
            },
        ],
        file: [
            {
                icon: 'fa fa-link',
                name: 'Copy link',
                action: 'copy_link',
                order: 0,
                class: 'rv-action-copy-link',
            },
            {
                icon: 'far fa-edit',
                name: 'Rename',
                action: 'rename',
                order: 1,
                class: 'rv-action-rename',
            },
            {
                icon: 'fa fa-copy',
                name: 'Make a copy',
                action: 'make_copy',
                order: 2,
                class: 'rv-action-make-copy',
            },
            {
                icon: 'fas fa-file-signature',
                name: 'Alt text',
                action: 'alt_text',
                order: 3,
                class: 'rv-action-alt-text',
            },
        ],
        user: [
            {
                icon: 'fa fa-star',
                name: 'Favorite',
                action: 'favorite',
                order: 2,
                class: 'rv-action-favorite',
            },
            {
                icon: 'fa fa-star',
                name: 'Remove favorite',
                action: 'remove_favorite',
                order: 3,
                class: 'rv-action-favorite',
            },
        ],
        other: [
            {
                icon: 'fa fa-download',
                name: 'Download',
                action: 'download',
                order: 0,
                class: 'rv-action-download',
            },
            {
                icon: 'fa fa-trash',
                name: 'Move to trash',
                action: 'trash',
                order: 1,
                class: 'rv-action-trash',
            },
            {
                icon: 'fa fa-eraser',
                name: 'Delete permanently',
                action: 'delete',
                order: 2,
                class: 'rv-action-delete',
            },
            {
                icon: 'fa fa-undo',
                name: 'Restore',
                action: 'restore',
                order: 3,
                class: 'rv-action-restore',
            },
        ],
    },
}

if (!MediaConfig.app_key || MediaConfig.app_key !== defaultConfig.app_key) {
    MediaConfig = defaultConfig
}

MediaConfig.request_params.search = ''

let RecentItems = $.parseJSON(localStorage.getItem('RecentItems')) || []

export { MediaConfig, RecentItems }
