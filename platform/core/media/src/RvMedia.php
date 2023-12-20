<?php

namespace Botble\Media;

use Botble\Base\Facades\BaseHelper;
use Botble\Media\Http\Resources\FileResource;
use Botble\Media\Models\MediaFile;
use Botble\Media\Models\MediaFolder;
use Botble\Media\Repositories\Interfaces\MediaFileInterface;
use Botble\Media\Repositories\Interfaces\MediaFolderInterface;
use Botble\Media\Services\ThumbnailService;
use Botble\Media\Services\UploadsManager;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use League\Flysystem\UnableToWriteFile;
use Mimey\MimeTypes;
use Throwable;

class RvMedia
{
    protected array $permissions = [];

    public function __construct(
        protected MediaFileInterface $fileRepository,
        protected MediaFolderInterface $folderRepository,
        protected UploadsManager $uploadManager,
        protected ThumbnailService $thumbnailService
    ) {
        $this->permissions = $this->getConfig('permissions', []);
    }

    public function renderHeader(): string
    {
        $urls = $this->getUrls();

        return view('core/media::header', compact('urls'))->render();
    }

    public function getUrls(): array
    {
        return [
            'base_url' => url(''),
            'base' => route('media.index'),
            'get_media' => route('media.list'),
            'create_folder' => route('media.folders.create'),
            'popup' => route('media.popup'),
            'download' => route('media.download'),
            'upload_file' => route('media.files.upload'),
            'get_breadcrumbs' => route('media.breadcrumbs'),
            'global_actions' => route('media.global_actions'),
            'media_upload_from_editor' => route('media.files.upload.from.editor'),
            'download_url' => route('media.download_url'),
        ];
    }

    public function renderFooter(): string
    {
        return view('core/media::footer')->render();
    }

    public function renderContent(): string
    {
        return view('core/media::content')->render();
    }

    public function responseSuccess(array $data, string|null $message = null): JsonResponse
    {
        return response()->json([
            'error' => false,
            'data' => $data,
            'message' => $message,
        ]);
    }

    public function responseError(
        string $message,
        array $data = [],
        int|null $code = null,
        int $status = 200
    ): JsonResponse {
        return response()->json([
            'error' => true,
            'message' => $message,
            'data' => $data,
            'code' => $code,
        ], $status);
    }

    public function getAllImageSizes(string|null $url): array
    {
        $images = [];
        foreach ($this->getSizes() as $size) {
            $readableSize = explode('x', $size);
            $images = $this->getImageUrl($url, $readableSize);
        }

        return $images;
    }

    public function getSizes(): array
    {
        $sizes = $this->getConfig('sizes', []);

        foreach ($sizes as $name => $size) {
            $size = explode('x', $size);

            $settingName = 'media_sizes_' . $name;

            $width = setting($settingName . '_width', $size[0]);

            $height = setting($settingName . '_height', $size[1]);

            if (! $width && ! $height) {
                continue;
            }

            if (! $width) {
                $width = 'auto';
            }

            if (! $height) {
                $height = 'auto';
            }

            $sizes[$name] = $width . 'x' . $height;
        }

        return $sizes;
    }

    public function getImageUrl(
        string|null $url,
        $size = null,
        bool $relativePath = false,
        $default = null
    ): string|null {
        if (empty($url)) {
            return $default;
        }

        $url = trim($url);

        if (empty($url)) {
            return $default;
        }

        if (empty($size) || $url == '__value__') {
            if ($relativePath) {
                return $url;
            }

            return $this->url($url);
        }

        if ($url == $this->getDefaultImage()) {
            return url($url);
        }

        if (array_key_exists($size, $this->getSizes()) &&
            $this->canGenerateThumbnails($this->getMimeType($url))
        ) {
            $url = str_replace(
                File::name($url) . '.' . File::extension($url),
                File::name($url) . '-' . $this->getSize($size) . '.' . File::extension($url),
                $url
            );
        }

        if ($relativePath) {
            return $url;
        }

        if ($url == '__image__') {
            return $this->url($default);
        }

        return $this->url($url);
    }

    public function url(string|null $path): string
    {
        $path = trim($path);

        if (Str::contains($path, ['http://', 'https://'])) {
            return $path;
        }

        if (config('filesystems.default') === 'do_spaces' && (int)setting('media_do_spaces_cdn_enabled')) {
            $customDomain = setting('media_do_spaces_cdn_custom_domain');

            if ($customDomain) {
                return $customDomain . '/' . ltrim($path, '/');
            }

            return str_replace('.digitaloceanspaces.com', '.cdn.digitaloceanspaces.com', Storage::url($path));
        }

        return Storage::url($path);
    }

    public function getDefaultImage(bool $relative = false): string
    {
        $default = $this->getConfig('default_image');

        if (setting('media_default_placeholder_image')) {
            $default = $this->url(setting('media_default_placeholder_image'));
        }

        if ($relative) {
            return $default;
        }

        return $default ? url($default) : $default;
    }

    public function getSize(string $name): string|null
    {
        return Arr::get($this->getSizes(), $name);
    }

    public function deleteFile(MediaFile $file): bool
    {
        $this->deleteThumbnails($file);

        return Storage::delete($file->url);
    }

    public function deleteThumbnails(MediaFile $file): bool
    {
        if (! $file->canGenerateThumbnails()) {
            return false;
        }

        $filename = pathinfo($file->url, PATHINFO_FILENAME);

        $files = [];
        foreach ($this->getSizes() as $size) {
            $files[] = str_replace($filename, $filename . '-' . $size, $file->url);
        }

        return Storage::delete($files);
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function setPermissions(array $permissions): void
    {
        $this->permissions = $permissions;
    }

    public function removePermission(string $permission): void
    {
        Arr::forget($this->permissions, $permission);
    }

    public function addPermission(string $permission): void
    {
        $this->permissions[] = $permission;
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions);
    }

    public function hasAnyPermission(array $permissions): bool
    {
        $hasPermission = false;
        foreach ($permissions as $permission) {
            if (in_array($permission, $this->permissions)) {
                $hasPermission = true;

                break;
            }
        }

        return $hasPermission;
    }

    public function addSize(string $name, int|string $width, int|string $height = 'auto'): self
    {
        if (! $width) {
            $width = 'auto';
        }

        if (! $height) {
            $height = 'auto';
        }

        config(['core.media.media.sizes.' . $name => $width . 'x' . $height]);

        return $this;
    }

    public function removeSize(string $name): self
    {
        $sizes = $this->getSizes();
        Arr::forget($sizes, $name);

        config(['core.media.media.sizes' => $sizes]);

        return $this;
    }

    public function uploadFromEditor(
        Request $request,
        int|string|null $folderId = 0,
        $folderName = null,
        string $fileInput = 'upload'
    ) {
        $validator = Validator::make($request->all(), [
            'upload' => $this->imageValidationRule(),
        ]);

        if ($validator->fails()) {
            return response('<script>alert("' . trans('core/media::media.can_not_detect_file_type') . '")</script>')
                ->header('Content-Type', 'text/html');
        }

        $folderName = $folderName ?: $request->input('upload_type');

        $result = $this->handleUpload($request->file($fileInput), $folderId, $folderName);

        if (! $result['error']) {
            $file = $result['data'];
            if (! $request->input('CKEditorFuncNum')) {
                return response()->json([
                    'fileName' => File::name($this->url($file->url)),
                    'uploaded' => 1,
                    'url' => $this->url($file->url),
                ]);
            }

            return response(
                '<script>window.parent.CKEDITOR.tools.callFunction("' . $request->input('CKEditorFuncNum') .
                '", "' . $this->url($file->url) . '", "");</script>'
            )
                ->header('Content-Type', 'text/html');
        }

        return response('<script>alert("' . Arr::get($result, 'message') . '")</script>')
            ->header('Content-Type', 'text/html');
    }

    public function handleUpload(
        ?UploadedFile $fileUpload,
        int|string|null $folderId = 0,
        string|null $folderSlug = null,
        bool $skipValidation = false
    ): array {
        $request = request();

        if ($uploadPath = $request->input('path')) {
            $folderId = $this->handleTargetFolder($folderId, $uploadPath);
        }

        if (! $fileUpload) {
            return [
                'error' => true,
                'message' => trans('core/media::media.can_not_detect_file_type'),
            ];
        }

        $allowedMimeTypes = $this->getConfig('allowed_mime_types');

        if (! $this->isChunkUploadEnabled()) {
            if (! $skipValidation) {
                $validator = Validator::make(['uploaded_file' => $fileUpload], [
                    'uploaded_file' => 'required|mimes:' . $allowedMimeTypes,
                ]);

                if ($validator->fails()) {
                    return [
                        'error' => true,
                        'message' => $validator->getMessageBag()->first(),
                    ];
                }
            }

            $maxUploadFilesizeAllowed = setting('max_upload_filesize');

            if (
                $maxUploadFilesizeAllowed
                && ($fileUpload->getSize() / 1024) / 1024 > (float)$maxUploadFilesizeAllowed
            ) {
                return [
                    'error' => true,
                    'message' => trans('core/media::media.file_too_big_readable_size', [
                        'size' => BaseHelper::humanFilesize($maxUploadFilesizeAllowed * 1024 * 1024),
                    ]),
                ];
            }

            $maxSize = $this->getServerConfigMaxUploadFileSize();

            if ($fileUpload->getSize() / 1024 > (int)$maxSize) {
                return [
                    'error' => true,
                    'message' => trans('core/media::media.file_too_big_readable_size', [
                        'size' => BaseHelper::humanFilesize($maxSize),
                    ]),
                ];
            }
        }

        try {
            $file = $this->fileRepository->getModel();

            $fileExtension = $fileUpload->getClientOriginalExtension();

            if (! $skipValidation && ! in_array(strtolower($fileExtension), explode(',', $allowedMimeTypes))) {
                return [
                    'error' => true,
                    'message' => trans('core/media::media.can_not_detect_file_type'),
                ];
            }

            if ($folderId == 0 && ! empty($folderSlug)) {
                if (str_contains($folderSlug, '/')) {
                    $paths = array_filter(explode('/', $folderSlug));
                    foreach ($paths as $folder) {
                        $folderId = $this->createFolder($folder, $folderId, true);
                    }
                } else {
                    $folderId = $this->createFolder($folderSlug, $folderId, true);
                }
            }

            $file->name = $this->fileRepository->createName(
                File::name($fileUpload->getClientOriginalName()),
                $folderId
            );

            $folderPath = $this->folderRepository->getFullPath($folderId);

            $fileName = $this->fileRepository->createSlug(
                $file->name,
                $fileExtension,
                Storage::path($folderPath ?: '')
            );

            $filePath = $fileName;

            if ($folderPath) {
                $filePath = $folderPath . '/' . $filePath;
            }

            $content = File::get($fileUpload->getRealPath());

            $this->uploadManager->saveFile($filePath, $content, $fileUpload);

            $data = $this->uploadManager->fileDetails($filePath);

            if (! $skipValidation && empty($data['mime_type'])) {
                return [
                    'error' => true,
                    'message' => trans('core/media::media.can_not_detect_file_type'),
                ];
            }

            $file->url = $data['url'];
            $file->alt = $file->name;
            $file->size = $data['size'];
            $file->mime_type = $data['mime_type'];
            $file->folder_id = $folderId;
            $file->user_id = Auth::check() ? Auth::id() : 0;
            $file->options = $request->input('options', []);
            $file->save();

            $this->generateThumbnails($file, $fileUpload);

            return [
                'error' => false,
                'data' => new FileResource($file),
            ];
        } catch (UnableToWriteFile $exception) {
            $message = $exception->getMessage();

            if (! $this->isUsingCloud()) {
                $message = trans('core/media::media.unable_to_write', ['folder' => $this->getUploadPath()]);
            }

            return [
                'error' => true,
                'message' => $message,
            ];
        } catch (Throwable $exception) {
            return [
                'error' => true,
                'message' => $exception->getMessage(),
            ];
        }
    }

    /**
     * Returns a file size limit in bytes based on the PHP upload_max_filesize and post_max_size
     */
    public function getServerConfigMaxUploadFileSize(): float
    {
        // Start with post_max_size.
        $maxSize = $this->parseSize(@ini_get('post_max_size'));

        // If upload_max_size is less, then reduce. Except if upload_max_size is
        // zero, which indicates no limit.
        $uploadMax = $this->parseSize(@ini_get('upload_max_filesize'));
        if ($uploadMax > 0 && $uploadMax < $maxSize) {
            $maxSize = $uploadMax;
        }

        return $maxSize;
    }

    public function parseSize(int|string $size): float
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $size = (int)preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
        if ($unit) {
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }

        return round($size);
    }

    public function generateThumbnails(MediaFile $file, UploadedFile $fileUpload = null): bool
    {
        if (! $file->canGenerateThumbnails()) {
            return false;
        }

        if (! $this->isUsingCloud() && ! File::exists($this->getRealPath($file->url))) {
            return false;
        }

        $folderIds = json_decode(setting('media_folders_can_add_watermark', ''), true);

        if (empty($folderIds) ||
            in_array($file->folder_id, $folderIds) ||
            ! empty(array_intersect($file->folder->parents->pluck('id')->all(), $folderIds))
        ) {
            $this->insertWatermark($file->url);
        }

        foreach ($this->getSizes() as $size) {
            $readableSize = explode('x', $size);

            if (! $fileUpload || $this->isChunkUploadEnabled()) {
                $fileUpload = $this->getRealPath($file->url);
            }

            $this->thumbnailService
                ->setImage($fileUpload)
                ->setSize($readableSize[0], $readableSize[1])
                ->setDestinationPath(File::dirname($file->url))
                ->setFileName(File::name($file->url) . '-' . $size . '.' . File::extension($file->url))
                ->save();
        }

        return true;
    }

    public function insertWatermark(string $image): bool
    {
        if (! $image || ! setting('media_watermark_enabled', $this->getConfig('watermark.enabled'))) {
            return false;
        }

        $watermarkImage = setting('media_watermark_source', $this->getConfig('watermark.source'));

        if (! $watermarkImage) {
            return true;
        }

        $watermarkPath = $this->getRealPath($watermarkImage);

        if (! File::exists($watermarkPath)) {
            return true;
        }

        $watermark = Image::make($watermarkPath);

        $imageSource = Image::make($this->getRealPath($image));

        // 10% less than an actual image (play with this value)
        // Watermark will be 10 less than the actual width of the image
        $watermarkSize = (int)round(
            $imageSource->width() * ((int)setting(
                'media_watermark_size',
                $this->getConfig('watermark.size')
            ) / 100),
            2
        );

        // Resize watermark width keep height auto
        $watermark
            ->resize($watermarkSize, null, function ($constraint) {
                $constraint->aspectRatio();
            })
            ->opacity((int)setting('media_watermark_opacity', $this->getConfig('watermark.opacity')));

        $imageSource->insert(
            $watermark,
            setting('media_watermark_position', $this->getConfig('watermark.position')),
            (int)setting('watermark_position_x', $this->getConfig('watermark.x')),
            (int)setting('watermark_position_y', $this->getConfig('watermark.y'))
        );

        $destinationPath = sprintf(
            '%s/%s',
            trim(File::dirname($image), '/'),
            File::name($image) . '.' . File::extension($image)
        );

        $this->uploadManager->saveFile($destinationPath, $imageSource->stream()->__toString());

        return true;
    }

    public function getRealPath(string|null $url): string
    {
        $path = match (config('filesystems.default')) {
            'local', 'public' => Storage::path($url),
            default => Storage::url($url),
        };

        return Arr::first(explode('?v=', $path));
    }

    public function isImage(string $mimeType): bool
    {
        return Str::startsWith($mimeType, 'image/');
    }

    public function isUsingCloud(): bool
    {
        return ! in_array(config('filesystems.default'), ['local', 'public']);
    }

    public function uploadFromUrl(
        string $url,
        int|string $folderId = 0,
        string|null $folderSlug = null,
        string|null $defaultMimetype = null
    ): array|null {
        if (empty($url)) {
            return [
                'error' => true,
                'message' => trans('core/media::media.url_invalid'),
            ];
        }

        $info = pathinfo($url);

        try {
            $contents = file_get_contents($url);
        } catch (Exception $exception) {
            return [
                'error' => true,
                'message' => $exception->getMessage(),
            ];
        }

        if (empty($contents)) {
            return null;
        }

        $path = '/tmp';
        if (! File::isDirectory($path)) {
            File::makeDirectory($path);
        }

        $path = $path . '/' . $info['basename'];
        file_put_contents($path, $contents);

        $mimeType = $this->getMimeType($url);

        if (empty($mimeType)) {
            $mimeType = $defaultMimetype;
        }

        $fileName = File::name($info['basename']);
        $fileExtension = File::extension($info['basename']);
        if (empty($fileExtension)) {
            $mimeTypeDetection = new MimeTypes();

            $fileExtension = $mimeTypeDetection->getExtension($mimeType);
        }

        $fileUpload = new UploadedFile($path, $fileName . '.' . $fileExtension, $mimeType, null, true);

        $result = $this->handleUpload($fileUpload, $folderId, $folderSlug);

        File::delete($path);

        return $result;
    }

    public function uploadFromPath(
        string $path,
        int|string $folderId = 0,
        string|null $folderSlug = null,
        string|null $defaultMimetype = null
    ): array {
        if (empty($path)) {
            return [
                'error' => true,
                'message' => trans('core/media::media.path_invalid'),
            ];
        }

        $mimeType = $this->getMimeType($path);

        if (empty($mimeType)) {
            $mimeType = $defaultMimetype;
        }

        $fileName = File::name($path);
        $fileExtension = File::extension($path);
        if (empty($fileExtension)) {
            $mimeTypeDetection = new MimeTypes();

            $fileExtension = $mimeTypeDetection->getExtension($mimeType);
        }

        $fileUpload = new UploadedFile($path, $fileName . '.' . $fileExtension, $mimeType, null, true);

        return $this->handleUpload($fileUpload, $folderId, $folderSlug);
    }

    public function getUploadPath(): string
    {
        return is_link(public_path('storage')) ? storage_path('app/public') : public_path('storage');
    }

    public function getUploadURL(): string
    {
        return str_replace('/index.php', '', $this->getConfig('default_upload_url'));
    }

    public function setUploadPathAndURLToPublic(): void
    {
        add_action('init', function () {
            config([
                'filesystems.disks.public.root' => $this->getUploadPath(),
                'filesystems.disks.public.url' => $this->getUploadURL(),
            ]);
        }, 124);
    }

    public function getMimeType(string $url): string|null
    {
        if (! $url) {
            return null;
        }

        $mimeTypeDetection = new MimeTypes();

        return $mimeTypeDetection->getMimeType(File::extension($url));
    }

    public function canGenerateThumbnails(string|null $mimeType): bool
    {
        if (! $this->getConfig('generate_thumbnails_enabled')) {
            return false;
        }

        if (! $mimeType) {
            return false;
        }

        return RvMedia::isImage($mimeType) && ! in_array($mimeType, ['image/svg+xml', 'image/x-icon']);
    }

    public function createFolder(string $folderSlug, int|string|null $parentId = 0, bool $force = false): int|string
    {
        $folder = $this->folderRepository->getFirstBy([
            'slug' => $folderSlug,
            'parent_id' => $parentId,
        ]);

        if (! $folder) {
            if ($force) {
                $this->folderRepository->forceDelete([
                    'slug' => $folderSlug,
                    'parent_id' => $parentId,
                ]);
            }

            $folder = MediaFolder::query()->create([
                'user_id' => Auth::check() ? Auth::id() : 0,
                'name' => $this->folderRepository->createName($folderSlug, 0),
                'slug' => $this->folderRepository->createSlug($folderSlug, 0),
                'parent_id' => $parentId,
            ]);
        }

        return $folder->id;
    }

    public function handleTargetFolder(int|string|null $folderId = 0, string $filePath = ''): string
    {
        if (str_contains($filePath, '/')) {
            $paths = array_filter(explode('/', $filePath));
            array_pop($paths);
            foreach ($paths as $folder) {
                $folderId = $this->createFolder($folder, $folderId, true);
            }
        }

        return $folderId;
    }

    public function isChunkUploadEnabled(): bool
    {
        return (int)$this->getConfig('chunk.enabled') == 1;
    }

    public function getConfig(string|null $key = null, string|null|array $default = null)
    {
        $configs = config('core.media.media');

        if (! $key) {
            return $configs;
        }

        return Arr::get($configs, $key, $default);
    }

    public function imageValidationRule(): string
    {
        return 'required|image|mimes:jpg,jpeg,png,webp,gif,bmp';
    }

    public function turnOffAutomaticUrlTranslationIntoLatin(): bool
    {
        return (int)setting('media_turn_off_automatic_url_translation_into_latin', 0) == 1;
    }

    public function getImageProcessingLibrary(): string
    {
        return setting('media_image_processing_library') ?: 'gd';
    }

    public function getMediaDriver(): string
    {
        return setting('media_driver', 'public');
    }

    public function setS3Disk(array $config): void
    {
        if (
            ! $config['key'] ||
            ! $config['secret'] ||
            ! $config['region'] ||
            ! $config['bucket'] ||
            ! $config['url']
        ) {
            return;
        }

        config()->set([
            'filesystems.disks.s3' => [
                'driver' => 's3',
                'visibility' => 'public',
                'throw' => true,
                'key' => $config['key'],
                'secret' => $config['secret'],
                'region' => $config['region'],
                'bucket' => $config['bucket'],
                'url' => $config['url'],
                'endpoint' => $config['endpoint'],
                'use_path_style_endpoint' => $config['use_path_style_endpoint'],
            ],
        ]);
    }

    public function setR2Disk(array $config): void
    {
        if (
            ! $config['key'] ||
            ! $config['secret'] ||
            ! $config['bucket'] ||
            ! $config['endpoint']
        ) {
            return;
        }

        config()->set([
            'filesystems.disks.r2' => [
                'driver' => 's3',
                'visibility' => 'public',
                'throw' => true,
                'key' => $config['key'],
                'secret' => $config['secret'],
                'region' => 'auto',
                'bucket' => $config['bucket'],
                'url' => $config['url'],
                'endpoint' => $config['endpoint'],
                'use_path_style_endpoint' => true,
            ],
        ]);
    }

    public function setDoSpacesDisk(array $config): void
    {
        if (
            ! $config['key'] ||
            ! $config['secret'] ||
            ! $config['region'] ||
            ! $config['bucket'] ||
            ! $config['endpoint']
        ) {
            return;
        }

        config()->set([
            'filesystems.disks.do_spaces' => [
                'driver' => 's3',
                'visibility' => 'public',
                'throw' => true,
                'key' => $config['key'],
                'secret' => $config['secret'],
                'region' => $config['region'],
                'bucket' => $config['bucket'],
                'endpoint' => $config['endpoint'],
            ],
        ]);
    }

    public function setWasabiDisk(array $config): void
    {
        if (
            ! $config['key'] ||
            ! $config['secret'] ||
            ! $config['region'] ||
            ! $config['bucket']
        ) {
            return;
        }

        config()->set([
            'filesystems.disks.wasabi' => [
                'driver' => 'wasabi',
                'visibility' => 'public',
                'throw' => true,
                'key' => $config['key'],
                'secret' => $config['secret'],
                'region' => $config['region'],
                'bucket' => $config['bucket'],
                'root' => $config['root'] ?: '/',
            ],
        ]);
    }

    public function setBunnyCdnDisk(array $config): void
    {
        if (
            ! $config['hostname'] ||
            ! $config['storage_zone'] ||
            ! $config['api_key'] ||
            ! $config['region']
        ) {
            return;
        }

        config()->set([
            'filesystems.disks.bunnycdn' => [
                'driver' => 'bunnycdn',
                'visibility' => 'public',
                'throw' => true,
                'hostname' => $config['hostname'],
                'storage_zone' => $config['storage_zone'],
                'api_key' => $config['api_key'],
                'region' => $config['region'],
            ],
        ]);
    }
}
