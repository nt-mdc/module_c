<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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

        //limpa as quebras de linha e separa em array
        $contentRaw = array_filter(preg_split("/\r\n|\r|\n/", str_replace($frontMatter[0], '', $rawContent)));
        $htmlArray = [];
        if ($pathExt !== "html") { //se n for html, converte em html
            foreach ($contentRaw as $key => $value) {
                $pattern = '/\b([\w\-]+\.(jpg|jpeg|png|gif|webp))\b/i';
                $base = asset("storage/images/");
                $base .= '/${1}';
                $replacement = '![Image]('.$base.')';
                $convertedText = preg_replace($pattern, $replacement, $value);
                $htmlArray[] = Str::markdown($convertedText);
            }
        } else { // se for html, reseta os indices do array original e salva no novo array
            $htmlArray = array_values($contentRaw);
        }

        // //pega a primeira string
        // $rawString = $htmlArray[0];
        // $htmlStripped = strip_tags($rawString); // tira as tags html
        // $firstLetter = substr($htmlStripped, 0, 1); // pega a primeira letra

        // $arrayFromRawString = str_split($rawString); // transforma a string em array

        // $firstLetterKey = array_search($firstLetter, $arrayFromRawString); //procura o indice da primeira letra
        // unset($arrayFromRawString[$firstLetterKey]); // exclui ela
        // $rawString = implode("", $arrayFromRawString); // monta a string dnv
        // $htmlArray[0] = $rawString; // atualiza a string para ficar sem a primeira letra


        // dd($firstLetter,$firstLetterKey, $rawString);
        // dd($contentRaw);





        if (!array_key_exists('title', $parsedFrontMatter)) {
            preg_match('/<h1>\s*(.*?)\s*<\/h1>/s', $rawContent, $h1Title);
            if (!$h1Title) {
                $parsedFrontMatter['title'] = Str::title(str_replace('-', ' ', substr($pathName, 11)));
            } else {
                $parsedFrontMatter['title'] = $h1Title[1];
            }
        }

        if (!array_key_exists('cover', $parsedFrontMatter)) {
            $coverPath = Arr::where(Storage::disk('public')->allFiles(), function ($item) use ($pathName) {
                return Str::contains($item, $pathName);
            });
            $coverPath = explode("/", reset($coverPath))[1];
            $parsedFrontMatter['cover'] = $coverPath;
        }

        $firstString = $htmlArray[0];
        unset($htmlArray[0]);


        $formattedContent = [
            'frontMatter' => $parsedFrontMatter,
            'content' => $htmlArray,
            'first' => $firstString
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
