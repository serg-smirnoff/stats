<?php
/**
 * PageRank Lookup (Based on Google Toolbar for Mozilla Firefox)
 *
 * @link        http://zloy.org/
 */
function GetPageRank($q,$host='toolbarqueries.google.com',$context=NULL) {
        $seed = "Mining PageRank is AGAINST GOOGLE'S TERMS OF SERVICE. Yes, I'm talking to you, scammer.";
        $result = 0x01020345;
        $len = strlen($q);
        for ($i=0; $i<$len; $i++) {
                $result ^= ord($seed{$i%strlen($seed)}) ^ ord($q{$i});
                $result = (($result >> 23) & 0x1ff) | $result << 9;
        }
    if (PHP_INT_MAX != 2147483647) { $result = -(~($result & 0xFFFFFFFF) + 1); }
        $ch=sprintf('8%x', $result);
        $url='http://%s/tbr?client=navclient-auto&ch=%s&features=Rank&q=info:%s';
        $url=sprintf($url,$host,$ch,$q);
        @$pr=file_get_contents($url,false,$context);
        return $pr?substr(strrchr($pr, ':'), 1):false;
}
?>