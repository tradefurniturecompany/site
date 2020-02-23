<?php
namespace Hotlink\Framework\Helper\Report;

class Format
{

    function htmlXml( $xml )
    {
        // add marker linefeeds to aid the pretty-tokeniser (adds a linefeed between all tag-end boundaries)
        $xml = preg_replace('/(>)(<)(\/*)/', "$1\n$2$3", $xml);

        // now indent the tags
        $token      = strtok($xml, "\n");
        $pad        = 0; // initial indent
        $matches    = array(); // returns from preg_matches()
        $result     = array();

        // scan each line and adjust indent based on opening/closing tags
        while ($token !== false) {
            // test for the various tag states
            // 1. open and closing tags on same line - no change
            if (preg_match('/.+<\/\w[^>]*>$/', $token, $matches)) {
                $indent=0;
            }
            // 2. closing tag - outdent now
            elseif (preg_match('/^<\/\w/', $token, $matches)) {
                $indent=-1;//$pad--;
            }
            // 3. opening tag - don't pad this one, only subsequent tags
            elseif (preg_match('/^<\w[^>]*[^\/]>.*$/', $token, $matches)) {
                $indent=1;
            }
            // 4. no indentation needed
            else {
                $indent = 0;
            }

            // pad the line with the required number of leading spaces
            if ($indent<0) {
                $line    = str_pad($token, strlen($token)+$pad-1, ' ', STR_PAD_LEFT);
            } else {
                $line    = str_pad($token, strlen($token)+$pad, ' ', STR_PAD_LEFT);
            }
            $result[] = $line; // add to the cumulative result
            $token   = strtok("\n"); // get the next token
            $pad    += $indent; // update the pad size for subsequent lines
        }

        // compacts empty tags and creates output xml
        $xml = '';
        for ($i=0; $i<count($result)-1; $i++) {
            $tag1 = $tag2 = '';
            if (preg_match('/<([^ \t\r\/>]+)/', $result[$i], $matches) && isset($matches[1])) {
                $tag1 = $matches[1];
            }
            if (preg_match('/<\/([^ \t\r\/>]+)>/', $result[$i+1], $matches) && isset($matches[1])) {
                $tag2 = $matches[1];
            }
            if ($tag1 == $tag2 && $tag1!='') {
                $result[$i] .= $result[$i+1];
                $result[$i] = preg_replace('/>\s+</', '><', $result[$i]);
                $result[$i+1] = '';
                $i++;
            }
        }
        $xml = implode("\n", $result);
        // takes off empty lines
        $xml = preg_replace('/[\n]{2,}/', "\n", $xml);

        return $xml;
    }

    function htmlPrintR( $arg )
    {
        $out = '';
        ob_start();
        print_r( $arg );
        $out = ob_get_contents();
        ob_end_clean();
        return $out;
    }

    function htmlVarDump( $arg )
    {
        $out = '';
        ob_start();
        var_dump( $arg );
        $out = ob_get_contents();
        ob_end_clean();
        return $out;
    }

}
