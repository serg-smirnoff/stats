<?php
function GoogleIndex($url_) //  оличество проиндексированных страниц в Google
{ 
	error_reporting(0);
	$content_ = file_get_contents('http://ajax.googleapis.com/ajax/services/search/web?v=1.0&filter=0&q=site:' .urlencode($url_));
	$data_ = json_decode($content_); 
	return intval($data_->responseData->cursor->estimatedResultCount);
}
?>