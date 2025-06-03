<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HeritagePages extends Controller
{
    public function index()
    {
        $raw = Storage::get('content-pages/2023-12-28-tips-of-using-lyon-city-card.txt');

        $lines = preg_split("/\r\n|\r|\n/", $raw);
        $frontMatter = [];
        $content = [];
        $isFrontMatter = false;
        $frontMatterDone = false;
        foreach ($lines as $line) {
            if (trim($line) == '---') {
                if(!$isFrontMatter){
                    $isFrontMatter = true;
                } else {
                    $isFrontMatter = false;
                    $frontMatterDone = true;
                }
                continue;
            }

            if($isFrontMatter) {
                [$key, $value] = explode(': ', $line, 2);
                $frontMatter[trim($key)] = trim($value);
            } elseif ($frontMatterDone) {
                $content[] = $line;
            }
        }

        $content = implode("\n", $content);

        return view('heritage', [
            'meta' => $frontMatter,
            'content' => Str::markdown($content)
        ]);
    }
}
