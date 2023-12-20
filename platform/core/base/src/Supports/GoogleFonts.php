<?php

namespace Botble\Base\Supports;

use Exception;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GoogleFonts
{
    public function __construct(
        protected Filesystem $filesystem,
        protected string $path,
        protected bool $inline,
        protected string $userAgent,
    ) {
    }

    public function load(string $font, string|null $nonce = null, bool $forceDownload = false): Fonts|null
    {
        ['font' => $font, 'nonce' => $nonce] = $this->parseOptions($font, $nonce);

        $url = $font;

        try {
            if ($forceDownload) {
                return $this->fetch($url, $nonce);
            }

            $fonts = $this->loadLocal($url, $nonce);

            if (! $fonts) {
                return $this->fetch($url, $nonce);
            }

            return $fonts;
        } catch (Exception $exception) {
            if (App::hasDebugModeEnabled()) {
                throw $exception;
            }

            return new Fonts(googleFontsUrl: $url, nonce: $nonce);
        }
    }

    protected function loadLocal(string $url, string|null $nonce): ?Fonts
    {
        if (! $this->filesystem->exists($this->path($url, 'fonts.css'))) {
            return null;
        }

        $fontCssPath = $this->path($url, 'fonts.css');

        $localizedCss = $this->filesystem->get($fontCssPath);

        if (str_contains($localizedCss, '<!DOCTYPE html>')) {
            $this->filesystem->delete($fontCssPath);

            return null;
        }

        if (! str_contains($localizedCss, Storage::disk('public')->url('fonts'))) {
            $localizedCss = preg_replace('/(http|https):\/\/.*?\/storage\/fonts\//i', Storage::disk('public')->url('fonts/'), $localizedCss);
            $this->filesystem->put($fontCssPath, $localizedCss);
        }

        return new Fonts(
            googleFontsUrl: $url,
            localizedUrl: $this->filesystem->url($fontCssPath),
            localizedCss: $localizedCss,
            nonce: $nonce,
            preferInline: $this->inline,
        );
    }

    protected function fetch(string $url, string|null $nonce): Fonts|null
    {
        $response = Http::withHeaders(['User-Agent' => $this->userAgent])
            ->timeout(300)
            ->withoutVerifying()
            ->get($url);

        if ($response->failed()) {
            return null;
        }

        $localizedCss = $response->body();

        foreach ($this->extractFontUrls($response) as $fontUrl) {
            $localizedFontUrl = $this->localizeFontUrl($fontUrl);

            $this->filesystem->put(
                $this->path($url, $localizedFontUrl),
                Http::withoutVerifying()->get($fontUrl)->body(),
            );

            $localizedCss = str_replace(
                $fontUrl,
                $this->filesystem->url($this->path($url, $localizedFontUrl)),
                $localizedCss,
            );
        }

        $this->filesystem->put($this->path($url, 'fonts.css'), $localizedCss);

        return new Fonts(
            googleFontsUrl: $url,
            localizedUrl: $this->filesystem->url($this->path($url, 'fonts.css')),
            localizedCss: $localizedCss,
            nonce: $nonce,
            preferInline: $this->inline,
        );
    }

    protected function extractFontUrls(string $css): array
    {
        $matches = [];
        preg_match_all('/url\((https:\/\/fonts.gstatic.com\/[^)]+)\)/', $css, $matches);

        return $matches[1] ?? [];
    }

    protected function localizeFontUrl(string $path): string
    {
        [$path, $extension] = explode('.', str_replace('https://fonts.gstatic.com/', '', $path));

        return implode('.', [Str::slug($path), $extension]);
    }

    protected function path(string $url, string $path = ''): string
    {
        $segments = collect([
            $this->path,
            substr(md5($url), 0, 10),
            $path,
        ]);

        return $segments->filter()->join('/');
    }

    protected function parseOptions(string $font, string|null $nonce = null): array
    {
        return [
            'font' => $font,
            'nonce' => $nonce,
        ];
    }
}
