<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SinglePageController extends Controller
{
    public static function index($path)
    {
        $rawContent = Storage::get($path);
        $pathExt = pathinfo($path, PATHINFO_EXTENSION);
        $pathName = pathinfo($path, PATHINFO_FILENAME);

        preg_match('/---\s*(.*?)\s*---/s', $rawContent, $frontMatter);

        // $lines = preg_split("/\r\n|\r|\n/", $rawContent);
        $frontMatterLines = preg_split("/\r\n|\r|\n/", $frontMatter[1]);
        $parsedFrontMatter = ['date' => substr($pathName, 0, 10)];
        foreach ($frontMatterLines as $line) {
            if (strpos($line, ':') === false) {
                continue;
            }

            list($key, $value) = array_map('trim', explode(':', $line, 2));

            $parsedFrontMatter[$key] = $value;
        }

        $contentRaw = array_filter(preg_split("/\r\n|\r|\n/", str_replace($frontMatter[0], '', $rawContent)));
        $htmlArray = [];
        foreach ($contentRaw as $key => $value) {
            $htmlString = $value;

            

            if (preg_match('/^\*\s+(.*?)/', $htmlString, $matches)) {
                $htmlString = '<li>' . $matches[1] . '</li>';
            }

            $htmlString = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $htmlString);




            $htmlArray[] = $htmlString;
        }


        if (!array_key_exists('title', $parsedFrontMatter)) {
            preg_match('/<h1>\s*(.*?)\s*<\/h1>/s', $rawContent, $h1Title);
            if (!$h1Title) {
                $parsedFrontMatter['title'] = Str::title(str_replace('-', ' ', substr($pathName, 11)));
            } else {
                $parsedFrontMatter['title'] = $h1Title[1];
            }
        }




        $formattedContent = [
            'frontMatter' => $parsedFrontMatter,
            'content' => $htmlArray
        ];

        return view('heritage', [
            'data' => $formattedContent
        ]);


        // $rawContent = Storage::get($path);
        // $filenameWithoutExtension = pathinfo(basename($path), PATHINFO_FILENAME);

        // if (!$this->verifyDateFilename($filenameWithoutExtension)) {
        //     return null;
        // }

        // $titleSlug = substr($filenameWithoutExtension, 11);
        // $fileTitle = Str::title(str_replace('-', ' ', $titleSlug));

        // $content = ['title' => trim($fileTitle)];


        // if ($frontMatter && count($frontMatter) == 2) {

        //     $lines = preg_split("/\r\n|\r|\n/", $frontMatter[1]);

        //     foreach ($lines as $line) {
        //         if (strpos($line, ':') === false) {
        //             continue;
        //         }

        //         list($key, $value) = array_map('trim', explode(':', $line, 2));

        //         if ($key === 'draft' && in_array(strtolower($value), ['true', '1'], true)) {
        //             return null;
        //         }

        //         if ($key === 'summary') {
        //             $content['summary'] = $value;
        //         }
        //     }
        // }
    }
}
