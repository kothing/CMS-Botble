import { ActionsService } from './ActionsService'
import { Helpers } from '../Helpers/Helpers'

export class ContextMenuService {
    static initContext() {
        if (jQuery().contextMenu) {
            $.contextMenu({
                selector: '.js-context-menu[data-context="file"]',
                build: () => {
                    return {
                        items: ContextMenuService._fileContextMenu(),
                    }
                },
            })

            $.contextMenu({
                selector: '.js-context-menu[data-context="folder"]',
                build: () => {
                    return {
                        items: ContextMenuService._folderContextMenu(),
                    }
                },
            })
        }
    }

    static _fileContextMenu() {
        let items = {
            preview: {
                name: 'Preview',
                icon: (opt, $itemElement, itemKey, item) => {
                    $itemElement.html('<i class="fa fa-eye" aria-hidden="true"></i> ' + item.name)

                    return 'context-menu-icon-updated'
                },
                callback: () => {
                    ActionsService.handlePreview()
                },
            },
        }

        Helpers.each(Helpers.getConfigs().actions_list, (actionGroup, key) => {
            Helpers.each(actionGroup, (value) => {
                items[value.action] = {
                    name: value.name,
                    icon: (opt, $itemElement, itemKey, item) => {
                        $itemElement.html(
                            '<i class="' +
                                value.icon +
                                '" aria-hidden="true"></i> ' +
                                (Helpers.trans('actions_list.' + key + '.' + value.action) || item.name)
                        )

                        return 'context-menu-icon-updated'
                    },
                    callback: () => {
                        $('.js-files-action[data-action="' + value.action + '"]').trigger('click')
                    },
                }
            })
        })

        let except = []

        switch (Helpers.getRequestParams().view_in) {
            case 'all_media':
                except = ['remove_favorite', 'delete', 'restore']
                break
            case 'recent':
                except = ['remove_favorite', 'delete', 'restore', 'make_copy']
                break
            case 'favorites':
                except = ['favorite', 'delete', 'restore', 'make_copy']
                break
            case 'trash':
                items = {
                    preview: items.preview,
                    rename: items.rename,
                    download: items.download,
                    delete: items.delete,
                    restore: items.restore,
                }
                break
        }

        Helpers.each(except, (value) => {
            items[value] = undefined
        })

        let hasFolderSelected = Helpers.getSelectedFolder().length > 0

        if (hasFolderSelected) {
            items.preview = undefined
            items.crop = undefined
            items.copy_link = undefined

            if (!Helpers.hasPermission('folders.create')) {
                items.make_copy = undefined
            }

            if (!Helpers.hasPermission('folders.edit')) {
                items.rename = undefined
            }

            if (!Helpers.hasPermission('folders.trash')) {
                items.trash = undefined
                items.restore = undefined
            }

            if (!Helpers.hasPermission('folders.destroy')) {
                items.delete = undefined
            }

            if (!Helpers.hasPermission('folders.favorite')) {
                items.favorite = undefined
                items.remove_favorite = undefined
            }
        }

        let selectedFiles = Helpers.getSelectedFiles()

        if (selectedFiles.length > 0) {
            if (!Helpers.hasPermission('files.create')) {
                items.make_copy = undefined
            }

            if (!Helpers.hasPermission('files.edit')) {
                items.rename = undefined
            }

            if (!Helpers.hasPermission('files.trash')) {
                items.trash = undefined
                items.restore = undefined
            }

            if (!Helpers.hasPermission('files.destroy')) {
                items.delete = undefined
            }

            if (!Helpers.hasPermission('files.favorite')) {
                items.favorite = undefined
                items.remove_favorite = undefined
            }

            if (selectedFiles.length > 1) {
                items.crop = undefined
            }
        }

        let canPreview = Helpers.arrayFilter(selectedFiles, function (value) {
            return value.preview_url
        }).length

        if (!canPreview) {
            items.preview = undefined
        }

        let fileIsImage = Helpers.arrayFilter(selectedFiles, function (value) {
            return value.type === 'image'
        }).length

        if (!fileIsImage) {
            items.crop = undefined
            items.alt_text = undefined
        }

        return items
    }

    static _folderContextMenu() {
        let items = ContextMenuService._fileContextMenu()

        items.preview = undefined
        items.copy_link = undefined

        return items
    }

    static destroyContext() {
        if (jQuery().contextMenu) {
            $.contextMenu('destroy')
        }
    }
}
