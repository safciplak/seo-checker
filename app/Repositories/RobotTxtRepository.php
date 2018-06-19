<?php

namespace App\Http\Repositories;


/**
 * Class RobotTxtRepository
 * @package App\Http\Repositories
 */
class RobotTxtRepository
{
    /**
     * @var
     */
    private $array;
    /**
     * @var
     */
    private $siteAddress;

    /**
     * Check sitemap
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function check()
    {
        $message = null;
        $array = null;

        if (substr(request('siteAddress'), 0, 3) != 'www') {
            $address = 'https://www.' . request('siteAddress');
        }

        if (substr(request('siteAddress'), 0, 3) == 'www') {
            $address = 'https://' . request('siteAddress');
        }

        if (stristr(request('siteAddress'), 'http')) {
            $address = str_replace('http://', 'https://', request('siteAddress'));
        }

        if (stristr(request('siteAddress'), 'https')) {
            $address = str_replace('https://', 'https://', request('siteAddress'));
        }

        $this->siteAddress = $address;


        $fullPath = $address . "/robots.txt";
        $ch = curl_init($fullPath);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);

        $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

//        $array['siteAddress'] = request('siteAddress');

        if ($retcode == 200) {
            $this->array = null;
            $array['no'] = 1;
            $array['testName'] = 'Проверка наличия файла robots.txt';
            $array['status'] = 'Ок';
            $array['currentState'] = 'Доработки не требуются';

            $this->array = $array;
            $this->saveToExcel();

            $this->array = null;
            $array['no'] = 12;
            $array['testName'] = 'Проверка кода ответа сервера для файла robots.txt';
            $array['status'] = 'Ок';
            $array['currentState'] = 'Доработки не требуются';

            $this->array = null;
            $this->array = $array;
            $this->saveToExcel();

            $_SESSION['allow'] = 1;
        }

        if (in_array($retcode, [400, 404, 501])) {
            $this->array = null;
            $array['no'] = 1;
            $array['testName'] = 'Проверка наличия файла robots.txt';
            $array['status'] = 'Ошибка';
            $array['currentState'] = 'Создать файл robots.txt и разместить его на сайте.';

            $this->array = $array;
            $this->saveToExcel();
            $this->array = null;

            $array['no'] = 12;
            $array['testName'] = 'Проверка кода ответа сервера для файла robots.txt';
            $array['status'] = 'Ок';
            $array['currentState'] = 'Файл robots.txt должны отдавать код ответа 200, иначе файл не будет обрабатываться. Необходимо настроить сайт таким образом, чтобы при обращении к файлу robots.txt сервер возвращает код ответа 200';

            $this->array = null;
            $this->array = $array;
            $this->saveToExcel();

            $_SESSION['allow'] = 0;
        }


        $fileSize = $this->getRemoteFilesize($fullPath);
        $array['responseCode'] = $retcode;
        $array['fileSize'] = $fileSize;


        curl_close($ch);

        return view('welcome', compact('message', 'array'));
    }

    /**
     * Calculating sitemap size
     *
     * @param $file_url
     * @param bool $formatSize
     * @return int|string
     */
    function getRemoteFilesize($file_url, $formatSize = true)
    {
        $head = array_change_key_case(get_headers($file_url, 1));
        // content-length of download (in bytes), read from Content-Length: field

        $clen = isset($head['content-length']) ? $head['content-length'] : 0;

        // cannot retrieve file size, return "-1"
        if (!$clen) {
            return -1;
        }

        if (!$formatSize) {
            return $clen;
            // return size in bytes
        }

        $size = $clen;
        switch ($clen) {
            case $clen < 1024:
                $size = $clen . ' B';
                $sizeType = 'B';
                break;
            case $clen < 1048576:
                $size = round($clen / 1024, 2) . ' KB';
                $sizeType = 'KB';
                break;
            case $clen < 1073741824:
                $size = round($clen / 1048576, 2) . ' MB';
                $sizeType = 'MB';
                break;
            case $clen < 1099511627776:
                $size = round($clen / 1073741824, 2) . ' GB';
                $sizeType = 'GB';
                break;
        }

        $this->array = null;

        if ($size < 32 && $sizeType == 'KB') {
            $array['no'] = 10;
            $array['testName'] = 'Проверка размера файла robots.txt';
            $array['status'] = 'Ок';
            $array['currentState'] = 'Доработки не требуются';
        } else {
            $array['no'] = 10;
            $array['testName'] = 'Проверка размера файла robots.txt';
            $array['status'] = 'Ошибка';
            $array['currentState'] = 'Максимально допустимый размер файла robots.txt составляем 32 кб. Необходимо отредактировть файл robots.txt таким образом, чтобы его размер не превышал 32 Кб';
        }

        $this->array = $array;

        $this->saveToExcel();

        return $size;
        // return formatted size
    }

    /**
     *
     */
    public function saveToExcel()
    {
        if ($this->array != null) {
            $handle = fopen("test.csv", "a");
            $line = $this->array;
            fputcsv($handle, $line, ";"); # $line is an array of string values here
            fclose($handle);
        }
    }

    /**
     * Find Sitemap Directive
     *
     * @return mixed
     */
    public function findDirectiveSiteMap()
    {

        $file = $this->siteAddress . '/robots.txt';

// the following line prevents the browser from parsing this as HTML.
        header('Content-Type: text/plain');

// get the file contents, assuming the file to be readable (and exist)
        $contents = file_get_contents($file);
// escape special characters in the query
        $pattern = preg_quote('Sitemap', '/');
// finalise the regular expression, matching the whole line
        $pattern = "/^.*$pattern.*\$/m";
// search, and store all matching occurences in $matches

        $this->array = null;
        if (preg_match_all($pattern, $contents, $matches)) {
            if (isset($matches[0])) {
                $array['no'] = 11;
                $array['testName'] = 'Проверка указания директивы Sitemap';
                $array['status'] = 'Ок';
                $array['currentState'] = 'Доработки не требуются';

            }
        } else {
//            echo "No matches found";
            $array['no'] = 11;
            $array['testName'] = 'Проверка указания директивы Sitemap';
            $array['status'] = 'Ошибка';
            $array['currentState'] = 'Добавить в файл robots.txt директиву Sitemap';
        }

        $this->array = $array;

        $this->saveToExcel();

        return $this->array;
    }

    /**
     * Find Host Directive
     *
     * @return mixed
     */
    public function findDirectiveHost()
    {

        $file = $this->siteAddress . '/robots.txt';

// the following line prevents the browser from parsing this as HTML.
        header('Content-Type: text/plain');

// get the file contents, assuming the file to be readable (and exist)
        $contents = file_get_contents($file);
// escape special characters in the query
        $pattern = preg_quote('Host', '/');
// finalise the regular expression, matching the whole line
        $pattern = "/^.*$pattern.*\$/m";
// search, and store all matching occurences in $matches

        if (preg_match_all($pattern, $contents, $matches)) {

            $this->array = null;
            $array['no'] = 6;
            $array['testName'] = 'Проверка указания директивы Host';
            $array['status'] = 'Ок';
            $array['currentState'] = 'Доработки не требуются';
            $this->array = $array;
            $this->saveToExcel();

//            $this->array['host'] = implode("\n", $matches[0]);

            $hostDirectiveCount = count($matches[0]);
            $this->array = null;
            if ($hostDirectiveCount == 1) {
                $array['no'] = 8;
                $array['testName'] = 'Проверка количества директив Host, прописанных в файле';
                $array['status'] = 'Ок';
                $array['currentState'] = 'Доработки не требуются';
            } else if ($hostDirectiveCount > 1) {
                $array['no'] = 8;
                $array['testName'] = 'Проверка количества директив Host, прописанных в файле';
                $array['status'] = 'Ошибка';
                $array['currentState'] = 'Директива Host должна быть указана в файле толоко 1 раз. Необходимо удалить все дополнительные директивы Host и оставить только 1, корректную и соответствующую основному зеркалу сайта';
            }
            $this->array = $array;
            $this->saveToExcel();
            $this->array = null;

        } else {
            $this->array = null;
            $array['no'] = 6;
            $array['testName'] = 'Проверка указания директивы Host';
            $array['status'] = 'Ошибка';
            $array['currentState'] = 'Для того, чтобы поисковые системы знали, какая версия сайта является основных зеркалом, необходимо прописать адрес основного зеркала в директиве Host. В данный момент это не прописано. Необходимо добавить в файл robots.txt директиву Host. Директива Host задётся в файле 1 раз, после всех правил.';
            $this->array = $array;
            $this->saveToExcel();
        }


        return $this->array;
    }
}
