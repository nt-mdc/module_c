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
    //todo: regex para separar o arquvio em array

    private function verifyDateFilename($filename)
    {

        if (strlen($filename) < 11 || $filename[10] !== '-') {
            return null;
        }
        // Original: $firstTenCarac = substr($nameWithoutExtension, 0, 10);
        $datePart = substr($filename, 0, 10);
        // Suggestion: Renamed $firstTenCarac to $datePart for clarity.

        // Original: $isDate = Carbon::canBeCreatedFromFormat($firstTenCarac, 'Y-m-d');
        return Carbon::canBeCreatedFromFormat($datePart, 'Y-m-d');
    }

    /**
     * Verifies if a file meets specific requirements based on its name and content (front matter).
     *
     * @param string $path The path to the file.
     * @return array|null An array with file details if it meets requirements, otherwise null.
     */
    private function verifyFileRequirements(string $path): ?array // Corrected: Renamed for clarity and added type hinting
    {
        // Original: function verifyIfTheFileMeetsTheRequirements($path)

        if (!Storage::exists($path) || File::isDirectory($path)) { // Added: Basic check
            return null;
        }

        $rawContent = Storage::get($path);
        // Original: $raw = Storage::get($path);

        // Original: $nameWithoutExtension = explode('.', basename($path))[0];
        $filenameWithoutExtension = pathinfo(basename($path), PATHINFO_FILENAME);

        // Correction: Used pathinfo() for a more robust way to get the filename without the extension.
        // It handles filenames with multiple dots better (e.g., "my.file.name.md").

        if (!$this->verifyDateFilename($filenameWithoutExtension)) {
            return null;
        }

        // Original: $fileTitle = ucwords(implode(" ", explode('-', substr($nameWithoutExtension, 11))));
        // Assumes filename structure is YYYY-MM-DD-the-rest-of-the-title
        $titleSlug = substr($filenameWithoutExtension, 11); // Assuming date is 10 chars + 1 hyphen
        $fileTitle = Str::title(str_replace('-', ' ', $titleSlug)); // Laravel's Str::title or ucwords()
        // Correction/Suggestion: Used str_replace for directness and Str::title (similar to ucwords).
        // This is cleaner than exploding and imploding for simple character replacement.

        $lines = preg_split("/\r\n|\r|\n/", $rawContent);
        // Original: $lines = preg_split("/\r\n|\r|\n/", $raw);

        $content = ['title' => trim($fileTitle)]; // Initialize with title
        // Original: $content = []; $content['title'] = trim($fileTitle);
        // Suggestion: Combined initialization.

        $isFrontMatterSection = false;
        // Original: $isFrontMatter = false;

        foreach ($lines as $line) {
            $trimmedLine = trim($line);

            if ($trimmedLine === '---') {
                // Original: if (trim($line) == '---')
                // Suggestion: Used strict comparison (===) and pre-trimmed line.
                if (!$isFrontMatterSection) {
                    $isFrontMatterSection = true;
                } else {
                    $isFrontMatterSection = false;
                    // Suggestion: If no content after the second '---' is needed for metadata,
                    // you could break the loop here for a minor optimization.
                    break;
                }
                continue;
            }

            if ($isFrontMatterSection) {
                // Original: [$key, $value] = explode(': ', $line, 2);
                // This would throw a notice if ':' is not found or no space after it.
                if (strpos($line, ':') === false) { // Check if colon exists
                    continue; // Skip malformed lines in front matter
                }

                list($key, $value) = array_map('trim', explode(':', $line, 2));
                // Correction: Ensured both key and value are trimmed and handles cases without a space after ':'.
                // Using list() is a common way to destructure.

                // Original: if ($key == 'draft' && $value == true || $value == 'true')
                if ($key === 'draft' && in_array(strtolower($value), ['true', '1'], true)) {
                    // Correction: More robust boolean check for draft status.
                    // Checks for 'true' (case-insensitive) or '1'.
                    return null; // File is a draft, skip it.
                }

                if ($key === 'summary') {
                    // Original: $content[trim($key)] = trim($value);
                    // $key and $value are already trimmed.
                    $content['summary'] = $value;
                }
                // You can add more front matter keys here as needed, e.g.:
                // if ($key === 'tags') {
                //     $content['tags'] = array_map('trim', explode(',', $value));
                // }
            }
        }

        $filePath = substr($path, strpos($path, '/') + 1);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        // Original: $content['link'] = route('list.pages', ['path' => $nameWithoutExtension]);
        $content['link'] = route('list.pages', ['path' => substr($filePath, 0, - (strlen($extension) + 1))]);
        // Ensures the same filename source is used.

        return $content;
    }

    /**
     * Retrieves and formats files and folders from a given path.
     *
     * @param string $path The directory path to scan.
     * @return array A list of formatted file and folder data.
     */
    private function retrieveAndFormatFilesAndFolders(string $path): array // Corrected: Renamed and added type hinting
    {
        // dd($path);
        // Original: private function retriveFilesAndFolderFormmated($path)
        // Correction: Typo in function name "retrive" and "Formmated" fixed to "retrieve" and "Formatted".

        $files = Storage::files($path);
        $directories = Storage::directories($path); // Original: $dirs = Storage::directories($path);
        // Suggestion: More descriptive variable name $directories.

        $items = []; // Combined list for files and folders
        // Original: $filesAndFolders = [];
        // Original: $finalFiles = []; // This variable was unused, so it has been removed.

        foreach ($files as $file) {
            // Original: array_push($filesAndFolders, $this->verifyIfTheFileMeetsTheRequirements($item));
            $fileData = $this->verifyFileRequirements($file); // Corrected: Called renamed function
            if ($fileData !== null) { // Only add valid files that meet requirements
                $items[] = $fileData; // Suggestion: Use [] for cleaner array append.
            }
        }

        foreach ($directories as $directory) {
            // Original: $content['title'] = basename($item); // This was problematic and likely a bug.
            // It would attempt to use $content from the previous scope or overwrite it.

            // Original: array_push($filesAndFolders, [ ... ]);
            $items[] = [ // Suggestion: Use [] for cleaner array append.
                'title' => basename($directory),
                'link' => route('list.pages', ['path' => substr($directory, strpos($directory, '/') + 1)]),
                // Suggestion: Consider adding a 'type' to distinguish items in the view, e.g.:
                // 'type' => 'folder',
                // 'is_folder' => true,
            ];
        }
        // Original: return $filesAndFolders;
        return $items;
    }

    /**
     * Displays a list of content pages from a given path.
     *
     * @param string $relativePath The relative path from the base content directory.
     * @return \Illuminate\View\View
     */
    public function index(string $relativePath = ''): \Illuminate\View\View // Added type hinting
    {


        // Original: public function index($path = '')
        // Suggestion: Renamed $path to $relativePath for clarity.
        $basePath = 'content-pages';

        // Original: $dynamicPath = $basePath . '/' . $path;
        // This could lead to 'content-pages//folder' or 'content-pages/'
        $dynamicPath = rtrim($basePath, '/'); // Ensure $basePath has no trailing slash
        if (!empty(trim($relativePath, '/'))) { // Ensure $relativePath is not empty or just slashes
            $dynamicPath .= '/' . trim($relativePath, '/'); // Add $relativePath, ensuring no leading/trailing slashes
        }
        // Correction: More robust path concatenation to avoid double slashes
        // and handle empty $relativePath correctly.

        $filename = basename($dynamicPath);
        $dirname = dirname($dynamicPath);

        if ($this->verifyDateFilename($filename)) {
            $arquivos = Storage::files($dirname);
            dd(
                Arr::where(
                    $arquivos,
                    function ($item) use ($filename) {
                        return Str::contains($item, $filename);
                    }
                )

            );
        } else {
            # code...
        }

        // Original: return view('home', ['data' => $this->retriveFilesAndFolderFormmated($dynamicPath)]);
        return view('home', [
            'data' => $this->retrieveAndFormatFilesAndFolders($dynamicPath) // Corrected: Called renamed function
        ]);
    }
}
