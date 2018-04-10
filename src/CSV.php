<?php

namespace Jtn\CSV;

class CSV
{

    /**
     * Given the path of a csv file, extract it to an associative array with
     * the headers as keys
     *
     * @param string $path
     * @param array $headers    An array of headers can be provided if the csv does not contain a header row
     * @return array
     */
    public static function toAssociativeArray($path = '', $headers = [])
    {
        ini_set('auto_detect_line_endings', true);

        $array = [];
        $file = fopen($path, 'r');
        $keys = count($headers) === 0 ? fgetcsv($file) : $headers;
        while (!feof($file)) {
            $row = fgetcsv($file);
            if(!$row || count($keys) !== count($row)) {
                continue;
            }

            foreach($row as $key => &$value) {
                $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            }
            $array[] = array_combine($keys, $row);
        }

        return $array;
    }

    /**
     * Return a CSV file as a string for the given array
     *
     * @param array $array
     * @return string
     */
    public static function fromAssociativeArray($array = [])
    {
        $handle = tmpfile();
        $meta = stream_get_meta_data($handle);
        $first = $array[0];

        if(is_array($first)) {
            fputcsv($handle, array_keys($first));
        }

        foreach($array as $item) {
            foreach($item as $k => $v) {
                if(is_array($v)) $item[$k] = json_encode($v);
            }

            fputcsv($handle, $item);
        }

        $string = file_get_contents($meta['uri']);
        fclose($handle);

        return $string;
    }

}
