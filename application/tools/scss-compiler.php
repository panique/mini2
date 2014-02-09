<?php

/**
 * Class SassWatcher
 *
 * This simple tool compiles all .scss files in folder A to .css files (with exactly the same name) into folder B.
 * To keep things as minimal as possible, this tool compiles every X seconds, regardless of changes within the files.
 * This seems weird, but makes sense as checking for changes in the files is more CPU-extensive than simply
 * re-compiling them. SassWatcher uses scssphp, the best SASS compiler in PHP available.
 *
 * SassWatcher is not a standalone compiler, it's just a little method that uses the excellent scssphp compiler written
 * by Leaf Corcoran (https://twitter.com/moonscript), which can be found here: http://leafo.net/scssphp/ and adds
 * automatic interval-compiling to it.
 *
 * The currently supported version of SCSS syntax is 3.2.12, which is the latest one.
 * To avoid confusion: SASS is the name of the language itself, and also the "name" of the "first" version of the
 * syntax (which was quite different than CSS). Then SASS's syntax was changed to "SCSS", which is more like CSS, but
 * with awesome additional possibilities and features.
 *
 * The compiler uses the SCSS syntax, which is recommened and mostly used. The old SASS syntax is not supported.
 *
 * @see SASS Wikipedia: http://en.wikipedia.org/wiki/Sass_%28stylesheet_language%29
 * @see SASS Homepage: http://sass-lang.com/
 * @see scssphp, the used compiler (in PHP): http://leafo.net/scssphp/
 *
 * How to use this tool:
 *
 * 1. Edit $sass_watcher->watch( ... ); in the last line of this file and put your stuff in here, see the parameter
 *    list below.
 * 2. Make sure PHP can write into your CSS folder.
 * 3. Run the script:
 *    a) simple way, from browser, just enter the URL to scss-compiler.php: http://127.0.0.1/folder/scss-compiler.php
 *       The script will run forever, even if you close the browser window.
 *    b) PHPStorm users can run the script by right-clicking the file and selecting "Run scss-compiler.php".
 * 4. To stop the script, stop/restart your Apache/Nginx/etc. or press the red "stop process button in PHPStorm.
 *
 * The parameters:
 *
 *  1. relative path to your SCSS folder
 *  2. relative path to your CSS folder (make sure PHP has write-rights here)
 *  3. the compiling interval (in seconds)
 *  4. relative path to the scss.inc.php file, which is the main file of the SASS compiler used
 *     here. Download the script manually from http://leafo.net/scssphp/ or "require" it via Composer:
 *     "leafo/scssphp": "0.0.9"
 *  5. optional: how the .css output should look like. See http://leafo.net/scssphp/docs/#output_formatting for more.
 *
 * How the tool works:
 *
 * Every X seconds ALL files in the scss folder will be compiled to same-name .css files in the css folder.
 * The tool does not stop when a .scss file is broken, has syntax error or similar.
 * The tool does not compile when .scss file is broken, has syntax error or similar. It will only compile next time
 * when there's a valid scss file.
 */
class SassWatcher
{
    /**
     * Watches a folder for .scss files, compiles them every X seconds
     * Re-compiling your .scss files every X seconds seems like "too much action" at first sight, but using a
     * "has-this-file-changed?"-check uses more CPU power than simply re-compiling them permanently :)
     * Beside that, we are only compiling .scss in development, for production we deploy .css, so we don't care.
     *
     * @param string $scss_folder source folder where you have your .scss files
     * @param string $css_folder destination folder where you want your .css files
     * @param int $interval interval in seconds
     * @param string $scssphp_script_path path where scss.inc.php (the scssphp script) is
     * @param string $format_style CSS output format, ee http://leafo.net/scssphp/docs/#output_formatting for more.
     */
    public function watch($scss_folder, $css_folder, $interval, $scssphp_script_path, $format_style = "scss_formatter")
    {
        // go on even if user "stops" the script by closing the browser, closing the terminal etc.
        ignore_user_abort(true);
        // set script running time to unlimited
        set_time_limit(0);

        // load the compiler script (scssphp), more here: http://www.leafo.net/scssphp
        require $scssphp_script_path;
        $scss_compiler = new scssc();
        // set css formatting (normal, nested or minimized), @see http://leafo.net/scssphp/docs/#output_formatting
        $scss_compiler->setFormatter($format_style);

        // start an infinitive loop
        while(1) {
            // get all .scss files from scss folder
            $filelist = glob($scss_folder . "*.scss");
            // step through all .scss files in that folder
            foreach ($filelist as $file_path) {
                // get path elements from that file
                $file_path_elements = pathinfo($file_path);
                // get file's name without extension
                $file_name = $file_path_elements['filename'];

                // get .scss's content, put it into $string_sass
                $string_sass = file_get_contents($scss_folder . $file_name . ".scss");

                // try/catch block to prevent script stopping when scss compiler throws an error
                try {
                    // compile this SASS code to CSS
                    $string_css = $scss_compiler->compile($string_sass);
                    // write CSS into file with the same filename, but .css extension
                    file_put_contents($css_folder . $file_name . ".css", $string_css);
                } catch (Exception $e) {
                    // here we could put the exception message, but who cares ...
                }
            }
            // wait for X seconds
            sleep($interval);
        }
    }
}

$sass_watcher = new SassWatcher();
$sass_watcher->watch("../../public/scss/", "../../public/css/", 1, "../../vendor/leafo/scssphp/scss.inc.php");
