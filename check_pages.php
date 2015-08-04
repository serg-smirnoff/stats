<?php
class method_curl
{
	// Инициализация curl
	function curl_start($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
		curl_setopt($ch, CURLOPT_ENCODING,'gzip,deflate');
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; ru; ru:1");
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	// Получаем количество страниц в индексе Яндекса
	public function yandex_index($site_url)
	{
		$content = $this->curl_start('http://yandex.ru/yandsearch?date=&text= &spcctx=notfar&zone=all& wordforms=all&lang=all&within=0&from_day=&from_month= &from_year= &to_day=21 &to_month=7&to_year=2009&mime=all &site='.urlencode($site_url).'&rstr=&ds=&numdoc=10');
		$content = str_replace('&nbsp;тыс.','000',$content);
		$content = str_replace('&nbsp;млн','000000',$content);
		$content = str_replace('ничего не найдено','0',$content);
		preg_match('~<title>[^\d]*(\d+)[^\d]*</title>~',$content,$match);
		return $match[1];
	}
	// Получаем количество страниц в индексе Google
	public function google_index($site_url)
	{
		$content =  str_replace(',','',$this->curl_start('http://www.google.com/search?hl=en&safe=off&q=site:'.$site_url.'&btnG=Search'));
		preg_match('/<b>.+?<\/b>\s*-\s*<b>.+?<\/b>.+?<b>(.+?)<\/b>/',$content,$a);
		$count = str_replace(',','',htmlspecialchars_decode($a[1]));
		return $count;
	}
}

// Для вызовы и выполнения кода пишем
$analiz = new method_curl();
// Адрес сайта
$url = "onlysites.ru";

// Вывод
echo "Анализ сайта: <b>".$url."</b><br />";
echo "Google: ".$analiz->google_index($url)."<br />";
echo "Яндекс: ".$analiz->yandex_index($url);
?>