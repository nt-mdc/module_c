<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class LisitingPageController extends Controller
{
    private function verifyDateFilename($filename)
    {
        if (strlen($filename) < 11 || $filename[10] !== '-') {
            return null;
        }

        $datePart = substr($filename, 0, 10);
        return Carbon::canBeCreatedFromFormat($datePart, 'Y-m-d');
    }

    private function verifyFileRequirements(string $path): ?array
    {
        if (!Storage::exists($path) || File::isDirectory($path)) {
            return null;
        }

        $rawContent = Storage::get($path);
        $filenameWithoutExtension = pathinfo(basename($path), PATHINFO_FILENAME);

        if (!$this->verifyDateFilename($filenameWithoutExtension)) {
            return null;
        }

        $titleSlug = substr($filenameWithoutExtension, 11);
        $fileTitle = Str::title(str_replace('-', ' ', $titleSlug));

        $content = ['title' => trim($fileTitle)];

        preg_match('/---\s*(.*?)\s*---/s', $rawContent, $frontMatter);

        if ($frontMatter && count($frontMatter) == 2) {

            $lines = preg_split("/\r\n|\r|\n/", $frontMatter[1]);

            foreach ($lines as $line) {
                if (strpos($line, ':') === false) {
                    continue;
                }

                list($key, $value) = array_map('trim', explode(':', $line, 2));

                if ($key === 'draft' && in_array(strtolower($value), ['true', '1'], true)) {
                    return null;
                }

                if ($key === 'summary') {
                    $content['summary'] = $value;
                }
            }
        }

        $filePath = substr($path, strpos($path, '/') + 1);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        $content['link'] = route('list.pages', ['path' => substr($filePath, 0, - (strlen($extension) + 1))]);

        return $content;
    }

    private function retrieveAndFormatFilesAndFolders(string $path): array
    {
        $files = Storage::files($path);
        $directories = Storage::directories($path);

        $items = [];

        foreach ($files as $file) {
            $fileData = $this->verifyFileRequirements($file);
            if ($fileData !== null) {
                $items[] = $fileData;
            }
        }

        foreach ($directories as $directory) {
            $items[] = [
                'title' => basename($directory),
                'link' => route('list.pages', ['path' => substr($directory, strpos($directory, '/') + 1)]),
            ];
        }

        return $items;
    }

    public function index(string $relativePath = ''): \Illuminate\View\View
    {
        $basePath = 'content-pages';

        $dynamicPath = rtrim($basePath, '/');
        if (!empty(trim($relativePath, '/'))) {
            $dynamicPath .= '/' . trim($relativePath, '/');
        }

        $filename = basename($dynamicPath);
        $dirname = dirname($dynamicPath);

        if ($this->verifyDateFilename($filename)) {
            $arquivos = Storage::files($dirname);
            $filePath = Arr::where(
                $arquivos,
                function ($item) use ($filename) {
                    return Str::contains($item, $filename);
                }
            );

            $filePath = reset($filePath);
            
            if ($filePath) {
                return SinglePageController::index($filePath);
            }
        }

        return view('home', [
            'data' => $this->retrieveAndFormatFilesAndFolders($dynamicPath)
        ]);
    }
}
