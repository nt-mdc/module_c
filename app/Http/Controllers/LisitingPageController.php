<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class LisitingPageController extends Controller
{


    function verifyIfTheFileMeetsTheRequirements($path)
    {
        $raw = Storage::get($path);
        $nameWithoutExtension = explode('.', basename($path))[0];
        $firstTenCarac = substr($nameWithoutExtension, 0, 10);
        $isDate = Carbon::canBeCreatedFromFormat($firstTenCarac, 'Y-m-d');

        if (!$isDate) {
            return null;
        }

        $fileTitle = ucwords(implode(" ", explode('-', substr($nameWithoutExtension, 11))));
        $lines = preg_split("/\r\n|\r|\n/", $raw);
        $content = [];
        $content['title'] = trim($fileTitle);
        $isFrontMatter = false;
        foreach ($lines as $line) {
            if (trim($line) == '---') {
                if (!$isFrontMatter) {
                    $isFrontMatter = true;
                } else {
                    $isFrontMatter = false;
                }
                continue;
            }

            if ($isFrontMatter) {
                [$key, $value] = explode(': ', $line, 2);

                if ($key == 'draft' && $value == true || $value == 'true') {
                    return null;
                }

                if ($key == 'summary') {
                    $content[trim($key)] = trim($value);
                }
            }
        }

        $content['link'] = route('list.pages', ['path' => $nameWithoutExtension]);

        return $content;
    }

    private function retriveFilesAndFolderFormmated($path)
    {
        $files = Storage::files($path);
        $dirs = Storage::directories($path);
        $filesAndFolders = [];
        $finalFiles = [];
        foreach ($files as $item) {
            array_push($filesAndFolders, $this->verifyIfTheFileMeetsTheRequirements($item));
        }

        foreach ($dirs as $item) {
            $content['title'] = basename($item);
            array_push($filesAndFolders, [
                'title' => basename($item),
                'link' => route('list.pages', ['path' => basename($item)])
            ]);
        }
        return $filesAndFolders;
    }

    public function index($path = '')
    {
        $basePath = 'content-pages';
        $dynamicPath = $basePath . '/' . $path;


        return view('home', ['data' => $this->retriveFilesAndFolderFormmated($dynamicPath)]);
    }
}
