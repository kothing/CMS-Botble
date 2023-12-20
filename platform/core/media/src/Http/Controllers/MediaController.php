<?php

namespace Botble\Media\Http\Controllers;

use Botble\Base\Facades\PageTitle;
use Botble\Media\Facades\RvMedia;
use Botble\Media\Http\Resources\FileResource;
use Botble\Media\Http\Resources\FolderResource;
use Botble\Media\Models\MediaFile;
use Botble\Media\Models\MediaFolder;
use Botble\Media\Repositories\Interfaces\MediaFileInterface;
use Botble\Media\Repositories\Interfaces\MediaFolderInterface;
use Botble\Media\Repositories\Interfaces\MediaSettingInterface;
use Botble\Media\Services\ThumbnailService;
use Botble\Media\Services\UploadsManager;
use Botble\Media\Supports\Zipper;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * @since 19/08/2015 08:05 AM
 */
class MediaController extends Controller
{
    public function __construct(
        protected MediaFileInterface $fileRepository,
        protected MediaFolderInterface $folderRepository,
        protected MediaSettingInterface $mediaSettingRepository,
        protected UploadsManager $uploadManager
    ) {
    }

    public function getMedia()
    {
        PageTitle::setTitle(trans('core/media::media.menu_name'));

        return view('core/media::index');
    }

    public function getPopup()
    {
        return view('core/media::popup')->render();
    }

    public function getList(Request $request)
    {
        $files = [];
        $folders = [];
        $breadcrumbs = [];

        if ($request->has('is_popup')
            && $request->has('selected_file_id')
            && $request->input('selected_file_id') != null
        ) {
            $currentFile = $this->fileRepository->getFirstBy(
                ['id' => $request->input('selected_file_id')],
                ['folder_id']
            );
            if ($currentFile) {
                $request->merge(['folder_id' => $currentFile->folder_id]);
            }
        }

        $paramsFolder = [];

        $paramsFile = [
            'order_by' => [
                'is_folder' => 'DESC',
            ],
            'paginate' => [
                'per_page' => $request->integer('posts_per_page', 30),
                'current_paged' => $request->integer('paged', 1),
            ],
            'selected_file_id' => $request->input('selected_file_id'),
            'is_popup' => $request->input('is_popup'),
            'filter' => $request->input('filter'),
        ];

        $orderBy = $this->transformOrderBy($request->input('sort_by'));

        if (count($orderBy) > 1) {
            $paramsFile['order_by'][$orderBy[0]] = $orderBy[1];
        }

        $search = $request->input('search');

        if ($search) {
            $paramsFolder['condition'] = [
                ['media_folders.name', 'LIKE', '%' . $search . '%'],
            ];

            $paramsFile['condition'] = [
                ['media_files.name', 'LIKE', '%' . $search . '%'],
            ];
        }

        $folderId = $request->input('folder_id');

        switch ($request->input('view_in')) {
            case 'all_media':
                $breadcrumbs = [
                    [
                        'id' => 0,
                        'name' => trans('core/media::media.all_media'),
                        'icon' => 'fa fa-user-secret',
                    ],
                ];

                $queried = $this->fileRepository->getFilesByFolderId($folderId, $paramsFile, true, $paramsFolder);

                $folders = FolderResource::collection($queried->where('is_folder', 1));

                $files = FileResource::collection($queried->where('is_folder', 0));

                break;

            case 'trash':
                $breadcrumbs = [
                    [
                        'id' => 0,
                        'name' => trans('core/media::media.trash'),
                        'icon' => 'fa fa-trash',
                    ],
                ];

                $queried = $this->fileRepository->getTrashed(
                    $folderId,
                    $paramsFile,
                    true,
                    $paramsFolder
                );

                $folders = FolderResource::collection($queried->where('is_folder', 1));

                $files = FileResource::collection($queried->where('is_folder', 0));

                break;

            case 'recent':
                $breadcrumbs = [
                    [
                        'id' => 0,
                        'name' => trans('core/media::media.recent'),
                        'icon' => 'fa fa-clock',
                    ],
                ];

                if (! count($request->input('recent_items', []))) {
                    break;
                }

                $queried = $this->fileRepository->getFilesByFolderId(
                    0,
                    array_merge($paramsFile, ['recent_items' => $request->input('recent_items', [])]),
                    false,
                    $paramsFolder
                );

                $files = FileResource::collection($queried);

                break;

            case 'favorites':
                $breadcrumbs = [
                    [
                        'id' => 0,
                        'name' => trans('core/media::media.favorites'),
                        'icon' => 'fa fa-star',
                    ],
                ];

                $favoriteItems = $this->mediaSettingRepository
                    ->getFirstBy([
                        'key' => 'favorites',
                        'user_id' => Auth::id(),
                    ]);

                if (! empty($favoriteItems)) {
                    $fileIds = collect($favoriteItems->value)
                        ->where('is_folder', 'false')
                        ->pluck('id')
                        ->all();

                    $folderIds = collect($favoriteItems->value)
                        ->where('is_folder', 'true')
                        ->pluck('id')
                        ->all();

                    $paramsFile = array_merge_recursive($paramsFile, [
                        'condition' => [
                            ['media_files.id', 'IN', $fileIds],
                        ],
                    ]);

                    $paramsFolder = array_merge_recursive($paramsFolder, [
                        'condition' => [
                            ['media_folders.id', 'IN', $folderIds],
                        ],
                    ]);

                    $queried = $this->fileRepository->getFilesByFolderId(
                        $folderId,
                        $paramsFile,
                        true,
                        $paramsFolder
                    );

                    $folders = FolderResource::collection($queried->where('is_folder', 1));

                    $files = FileResource::collection($queried->where('is_folder', 0));
                }

                break;
        }

        $breadcrumbs = array_merge($breadcrumbs, $this->getBreadcrumbs($request));
        $selectedFileId = $request->input('selected_file_id');

        return RvMedia::responseSuccess([
            'files' => $files,
            'folders' => $folders,
            'breadcrumbs' => $breadcrumbs,
            'selected_file_id' => $selectedFileId,
        ]);
    }

    protected function transformOrderBy(string|null $orderBy): array
    {
        $result = explode('-', $orderBy);
        if (! count($result) == 2) {
            return ['name', 'asc'];
        }

        return $result;
    }

    protected function getBreadcrumbs(Request $request): array
    {
        $folderId = $request->input('folder_id');

        if (! $folderId) {
            return [];
        }

        if ($request->input('view_in') == 'trash') {
            $folder = $this->folderRepository->getFirstByWithTrash(['id' => $folderId]);
        } else {
            $folder = $this->folderRepository->getFirstBy(['id' => $folderId]);
        }

        if (empty($folder)) {
            return [];
        }

        $breadcrumbs = [
            [
                'name' => $folder->name,
                'id' => $folder->id,
            ],
        ];

        $child = $this->folderRepository->getBreadcrumbs($folder->parent_id);
        if (! empty($child)) {
            return array_merge($child, $breadcrumbs);
        }

        return $breadcrumbs;
    }

    public function postGlobalActions(Request $request, ThumbnailService $thumbnailService)
    {
        $response = RvMedia::responseError(trans('core/media::media.invalid_action'));

        $type = $request->input('action');

        switch ($type) {
            case 'trash':
                $error = false;
                foreach ($request->input('selected') as $item) {
                    $id = $item['id'];
                    if ($item['is_folder'] == 'false') {
                        try {
                            $this->fileRepository->deleteBy(['id' => $id]);
                        } catch (Exception $exception) {
                            info($exception->getMessage());
                            $error = true;
                        }
                    } else {
                        $this->folderRepository->deleteFolder($id);
                    }
                }

                if ($error) {
                    $response = RvMedia::responseError(trans('core/media::media.trash_error'));

                    break;
                }

                $response = RvMedia::responseSuccess([], trans('core/media::media.trash_success'));

                break;

            case 'restore':
                $error = false;
                foreach ($request->input('selected') as $item) {
                    $id = $item['id'];
                    if ($item['is_folder'] == 'false') {
                        try {
                            $this->fileRepository->restoreBy(['id' => $id]);
                        } catch (Exception $exception) {
                            info($exception->getMessage());
                            $error = true;
                        }
                    } else {
                        $this->folderRepository->restoreFolder($id);
                    }
                }

                if ($error) {
                    $response = RvMedia::responseError(trans('core/media::media.restore_error'));

                    break;
                }

                $response = RvMedia::responseSuccess([], trans('core/media::media.restore_success'));

                break;

            case 'make_copy':
                foreach ($request->input('selected', []) as $item) {
                    $id = $item['id'];
                    if ($item['is_folder'] == 'false') {
                        $file = $this->fileRepository->getFirstBy(['id' => $id]);

                        if (! $file) {
                            break;
                        }

                        $this->copyFile($file);
                    } else {
                        $oldFolder = $this->folderRepository->getFirstBy(['id' => $id]);

                        if (! $oldFolder) {
                            break;
                        }

                        $folderData = $oldFolder->replicate()->toArray();

                        $folderData['slug'] = $this->folderRepository->createSlug(
                            $oldFolder->name,
                            $oldFolder->parent_id
                        );
                        $folderData['name'] = $oldFolder->name . '-(copy)';
                        $folderData['user_id'] = Auth::id();
                        $folder = $this->folderRepository->create($folderData);

                        $files = $this->fileRepository->getFilesByFolderId($id, [], false);
                        foreach ($files as $file) {
                            $this->copyFile($file, $folder->id);
                        }

                        $children = $this->folderRepository->getAllChildFolders($id);
                        foreach ($children as $parentId => $child) {
                            if ($parentId != $oldFolder->id) {
                                /**
                                 * @var MediaFolder $child
                                 */
                                $folder = $this->folderRepository->getFirstBy(['id' => $parentId]);

                                if (! $folder) {
                                    break;
                                }

                                $folderData = $folder->replicate()->toArray();

                                $folderData['slug'] = $this->folderRepository->createSlug(
                                    $oldFolder->name,
                                    $oldFolder->parent_id
                                );
                                $folderData['name'] = $oldFolder->name . '-(copy)';
                                $folderData['user_id'] = Auth::id();
                                $folderData['parent_id'] = $folder->id;
                                $folder = $this->folderRepository->create($folderData);

                                $parentFiles = $this->fileRepository->getFilesByFolderId($parentId, [], false);
                                foreach ($parentFiles as $parentFile) {
                                    $this->copyFile($parentFile, $folder->id);
                                }
                            }

                            foreach ($child as $sub) {
                                /**
                                 * @var MediaFolder $sub
                                 */
                                $subFiles = $this->fileRepository->getFilesByFolderId($sub->id, [], false);

                                $subFolderData = $sub->replicate()->toArray();

                                $subFolderData['user_id'] = Auth::id();
                                $subFolderData['parent_id'] = $folder->id;

                                $sub = $this->folderRepository->create($subFolderData);

                                foreach ($subFiles as $subFile) {
                                    $this->copyFile($subFile, $sub->id);
                                }
                            }
                        }

                        $allFiles = Storage::allFiles($this->folderRepository->getFullPath($oldFolder->id));
                        foreach ($allFiles as $file) {
                            Storage::copy($file, str_replace($oldFolder->slug, $folder->slug, $file));
                        }
                    }
                }

                $response = RvMedia::responseSuccess([], trans('core/media::media.copy_success'));

                break;

            case 'delete':
                foreach ($request->input('selected') as $item) {
                    $id = $item['id'];
                    if ($item['is_folder'] == 'false') {
                        try {
                            $this->fileRepository->forceDelete(['id' => $id]);
                        } catch (Exception $exception) {
                            info($exception->getMessage());
                        }
                    } else {
                        $this->folderRepository->deleteFolder($id, true);
                    }
                }

                $response = RvMedia::responseSuccess([], trans('core/media::media.delete_success'));

                break;

            case 'favorite':
                $meta = $this->mediaSettingRepository->firstOrCreate([
                    'key' => 'favorites',
                    'user_id' => Auth::id(),
                ]);

                if (! empty($meta->value)) {
                    $meta->value = array_merge($meta->value, $request->input('selected', []));
                } else {
                    $meta->value = $request->input('selected', []);
                }

                $meta->save();

                $response = RvMedia::responseSuccess([], trans('core/media::media.favorite_success'));

                break;

            case 'remove_favorite':
                $meta = $this->mediaSettingRepository->firstOrCreate([
                    'key' => 'favorites',
                    'user_id' => Auth::id(),
                ]);

                if (! empty($meta)) {
                    $value = $meta->value;
                    if (! empty($value)) {
                        foreach ($value as $key => $item) {
                            foreach ($request->input('selected') as $selectedItem) {
                                if ($item['is_folder'] == $selectedItem['is_folder'] && $item['id'] == $selectedItem['id']) {
                                    unset($value[$key]);
                                }
                            }
                        }

                        $meta->value = $value;

                        $meta->save();
                    }
                }

                $response = RvMedia::responseSuccess([], trans('core/media::media.remove_favorite_success'));

                break;

            case 'crop':
                $validated = Validator::validate($request->input(), [
                    'imageId' => ['required', 'string', 'exists:media_files,id'],
                    'cropData' => ['required', 'json'],
                ]);

                $data = json_decode($validated['cropData'], true);

                $cropData = Validator::validate($data, [
                    'x' => ['required', 'numeric'],
                    'y' => ['required', 'numeric'],
                    'width' => ['required', 'numeric'],
                    'height' => ['required', 'numeric'],
                ]);

                $file = $this->fileRepository->findOrFail($validated['imageId']);

                if (! $file->canGenerateThumbnails()) {
                    $response = RvMedia::responseError(trans('core/media::media.failed_to_crop_image'));

                    break;
                }

                $fileUrl = $file->url;
                $parsedUrl = parse_url($fileUrl);

                if (isset($parsedUrl['query'])) {
                    $fileUrl = str_replace('?' . $parsedUrl['query'], '', $fileUrl);
                }

                $thumbnailService
                    ->setImage(RvMedia::getRealPath($fileUrl))
                    ->setSize((int)$cropData['width'], (int)$cropData['height'])
                    ->setCoordinates((int)$cropData['x'], (int)$cropData['y'])
                    ->setDestinationPath(File::dirname($fileUrl))
                    ->setFileName(File::name($fileUrl) . '.' . File::extension($fileUrl))
                    ->save('crop');

                $file->url = $fileUrl . '?v=' . time();
                $file->save();

                RvMedia::generateThumbnails($file);

                $response = RvMedia::responseSuccess([], trans('core/media::media.crop_success'));

                break;

            case 'rename':
                foreach ($request->input('selected') as $item) {
                    if (! $item['id'] || empty($item['name'])) {
                        continue;
                    }

                    $id = $item['id'];
                    if ($item['is_folder'] == 'false') {
                        $file = $this->fileRepository->getFirstBy(['id' => $id]);

                        if (! empty($file)) {
                            $file->name = $this->fileRepository->createName($item['name'], $file->folder_id);
                            $file->save();
                        }
                    } else {
                        $name = $item['name'];
                        $folder = $this->folderRepository->getFirstBy(['id' => $id]);

                        if (! empty($folder)) {
                            $folder->name = $this->folderRepository->createName($name, $folder->parent_id);
                            $folder->save();
                        }
                    }
                }

                $response = RvMedia::responseSuccess([], trans('core/media::media.rename_success'));

                break;

            case 'alt_text':
                foreach ($request->input('selected') as $item) {
                    if (! $item['id']) {
                        continue;
                    }

                    $file = $this->fileRepository->getFirstBy(['id' => $item['id']]);

                    if ($file) {
                        $file->alt = $item['alt'];
                        $file->save();
                    }
                }

                $response = RvMedia::responseSuccess([], trans('core/media::media.update_alt_text_success'));

                break;
            case 'empty_trash':
                $this->folderRepository->emptyTrash();
                $this->fileRepository->emptyTrash();

                $response = RvMedia::responseSuccess([], trans('core/media::media.empty_trash_success'));

                break;
        }

        return $response;
    }

    protected function copyFile(MediaFile $file, int|string|null $newFolderId = null)
    {
        $file = $file->replicate();
        $file->user_id = Auth::id();

        if ($newFolderId == null) {
            $file->name = $file->name . '-(copy)';

            $path = '';

            $folderPath = File::dirname($file->url);
            if ($folderPath) {
                $path = $folderPath . '/' . $path;
            }

            $path = $path . File::name($file->url) . '-(copy)' . '.' . File::extension($file->url);

            $filePath = RvMedia::getRealPath($file->url);
            if (Storage::exists($filePath)) {
                $content = File::get($filePath);

                $this->uploadManager->saveFile($path, $content);
                $file->url = $path;

                RvMedia::generateThumbnails($file);
            }
        } else {
            $file->url = str_replace(
                RvMedia::getRealPath(File::dirname($file->url)),
                RvMedia::getRealPath($this->folderRepository->getFullPath($newFolderId)),
                $file->url
            );

            $file->folder_id = $newFolderId;
        }

        unset($file->is_folder);
        unset($file->slug);
        unset($file->parent_id);

        return $file->save();
    }

    public function download(Request $request)
    {
        $items = $request->input('selected', []);

        if (count($items) == 1 && $items['0']['is_folder'] == 'false') {
            $file = $this->fileRepository->getFirstByWithTrash(['id' => $items[0]['id']]);
            if (! empty($file) && $file->type != 'video') {
                $filePath = RvMedia::getRealPath($file->url);

                if (! RvMedia::isUsingCloud()) {
                    if (! File::exists($filePath)) {
                        return RvMedia::responseError(trans('core/media::media.file_not_exists'));
                    }

                    return response()->download($filePath, Str::slug($file->name));
                }

                return response()->make(file_get_contents(str_replace('https://', 'http://', $filePath)), 200, [
                    'Content-type' => $file->mime_type,
                    'Content-Disposition' => 'attachment; filename="' . $file->name . '.' . File::extension($file->url) . '"',
                ]);
            }
        } else {
            $fileName = Storage::disk('local')->path('download-' . Carbon::now()->format('Y-m-d-h-i-s') . '.zip');
            $zip = new Zipper();
            $zip->make($fileName);
            foreach ($items as $item) {
                $id = $item['id'];
                if ($item['is_folder'] == 'false') {
                    $file = $this->fileRepository->getFirstByWithTrash(['id' => $id]);
                    if (! empty($file) && $file->type != 'video') {
                        $filePath = RvMedia::getRealPath($file->url);
                        if (! RvMedia::isUsingCloud()) {
                            if (File::exists($filePath)) {
                                $zip->add($filePath);
                            }
                        } else {
                            $zip->addString(
                                File::basename($file),
                                file_get_contents(str_replace('https://', 'http://', $filePath))
                            );
                        }
                    }
                } else {
                    $folder = $this->folderRepository->getFirstByWithTrash(['id' => $id]);
                    if (! empty($folder)) {
                        if (! RvMedia::isUsingCloud()) {
                            $folderPath = RvMedia::getRealPath($this->folderRepository->getFullPath($folder->id));
                            if (File::isDirectory($folderPath)) {
                                $zip->add($folderPath);
                            }
                        } else {
                            $allFiles = Storage::allFiles($this->folderRepository->getFullPath($folder->id));
                            foreach ($allFiles as $file) {
                                $zip->addString(
                                    File::basename($file),
                                    file_get_contents(str_replace('https://', 'http://', RvMedia::getRealPath($file)))
                                );
                            }
                        }
                    }
                }
            }

            if (version_compare(phpversion(), '8.0') >= 0) {
                $zip = null;
            } else {
                $zip->close();
            }

            if (File::exists($fileName)) {
                return response()
                    ->download($fileName, File::name($fileName))
                    ->deleteFileAfterSend();
            }

            return RvMedia::responseError(trans('core/media::media.download_file_error'));
        }

        return RvMedia::responseError(trans('core/media::media.can_not_download_file'));
    }
}
