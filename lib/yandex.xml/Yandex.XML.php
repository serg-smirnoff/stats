<?php
session_start();
if (!isset($_SESSION['ttl'])) {
    $_SESSION['ttl'] = microtime(true);
}

require_once 'Yandex.php';

// get "query" and "page" from request

$query = 'создание сайта';
$geo = '15';

//$query = isset($_REQUEST['query'])?$_REQUEST['query']:null;
$page  = isset($_REQUEST['page']) ?$_REQUEST['page']:0;
$host  = isset($_REQUEST['host']) ?$_REQUEST['host']:null;

//$geo   = isset($_REQUEST['geo']) ?$_REQUEST['geo']:null;
$cat   = isset($_REQUEST['cat']) ?$_REQUEST['cat']:null;
$theme = isset($_REQUEST['theme']) ?$_REQUEST['theme']:null;

// small protection for example script
// only 2 seconds
if ($query && (microtime(true) - $_SESSION['ttl']) > 2) {
    // Your data http://xmlsearch.yandex.ru/xmlsearch?user=AntonShevchuk&key=03.28303679:b340c90e875df328e6e120986c837284
    //http://xmlsearch.yandex.ru/xmlsearch?user=programmatore&key=03.29915828:0e54bb50b2061a2a18038bb37ff306cb
	$user = 'programmatore';
    $key  = '03.29915828:0e54bb50b2061a2a18038bb37ff306cb';

    // Create new instance of Yandex class
    $Yandex = new Yandex($user, $key);
    
    // Set Query
    $Yandex -> query($query)
            -> host($host)                      // set one host or multihost
            //-> host(array('anton.shevchuk.name','cotoha.info')) 
            //-> site(array('anton.shevchuk.name','cotoha.info')) 
            //-> domain(array('ru','org'))
            -> page($page)                      // set current page
            -> limit(10)                        // set page limit
            -> geo($geo)                        // set geo region - http://search.yaca.yandex.ru/geo.c2n
            -> cat($cat)                        // set category - http://search.yaca.yandex.ru/cat.c2n
            -> theme($theme)                    // set theme - http://help.yandex.ru/site/?id=1111797
            -> sortby(Yandex::SORT_RLV)
            -> groupby(Yandex::GROUP_DEFAULT)           
            -> set('max-title-length',   160)   // set some options
            -> set('max-passage-length', 200)
            -> request()                        // send request
            ;

    // Debug request
    $request = $Yandex -> getRequest()->asXml();
}

// current URL
$server = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
$server = substr($server, 0, strpos($server, '?'));
$url = $server .'?query='.urlencode($query)
               .'&host='.urlencode($host)
               .'&geo='.urlencode($geo)
               .'&cat='.urlencode($cat)
               .'&theme='.urlencode($theme)
               ;



			   
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>Статистика поисковых запросов по собственным проектам</title>
    <link rel="stylesheet" href="styles.css" type="text/css" />
</head>
<body>

        <?php 
            // if $Yandex exists and don't have errors in response
            if (isset($Yandex) && empty($Yandex->error)) : 
        ?>
            <div class="result box">
                <p><?php echo ($Yandex->totalHuman()); ?></p>
            </div>
            <ol start="<?php echo $Yandex->getLimit()*$Yandex->getPage() + 1;?>">
            <?php foreach ($Yandex->results() as $result) :?>
                <?php
                    /*
                    $result is Object with next properties:
                        ->url
                        ->domain
                        ->title
                        ->headline
                        ->passages // array
                        ->sitelinks // array
                    */
                ?>
                <li class="box"><a href="<?php echo $result->url; ?>" title="<?php echo $result->url; ?>" class="title"><?php Yandex::highlight($result->title); ?></a>
                    <?php if ($result->headline) : ?>
                    <div class="headline">
                        <?php echo $result->headline; ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($result->passages) : ?>
                    <ul class="passages">
                        <?php foreach ($result->passages as $passage) :?>
                        <li><?php Yandex::highlight($passage);?></li>                    
                        <?php endforeach;?>
                    </ul>
                    <?php endif; ?>
                    <a href="<?php echo $result->url; ?>" class="host" title="<?php echo $result->url; ?>"><?php echo $result->domain; ?></a> 
                    <a href="<?php echo $server .'?query='.urlencode($query).'&host='. urlencode($result->domain)?>" class="host" title="Поиск на сайте <?php echo $result->domain; ?>">ещё</a>
                </li>
            <?php endforeach;?>
            </ol>
            <div class="pagebar box">
            <p>
            <?php foreach ($Yandex->pageBar() as $page => $value) : ;?>
                <?php // switch statement for $value['type']
                switch ($value['type']) {
                	case 'link':
                		echo '<a href="'. $url .'&page='. $page .'" title="Page '. ($page+1) .'">'. sprintf($value['text'], $page+1) .'</a> | ';
                		break;
                	case 'current':
                		echo sprintf($value['text'], $page+1) .' | ';
                		break;
                	case 'text':
                		echo $value['text'] .' | ';
                		break;
                
                	default:
                		break;
                }
                ?>
            <?php endforeach;?>
            <?php /*if ($Yandex->pages() > 1 && $Yandex->getPage() != $Yandex->pages()-1) : ?>
                <?php if ($Yandex->getPage() == $Yandex->pages() - 2):?>
                    <a href="<?php echo $url;?>&page=<?php echo $Yandex->getPage()+1;?>" title="Next Page"><?php echo $Yandex->getPage()+2;?></a> 
                <?php elseif ($Yandex->getPage() < $Yandex->pages()):?> .. |
                    <a href="<?php echo $url;?>&page=<?php echo $Yandex->getPage()+1;?>" title="Next Page">&raquo;</a>
                <?php endif; ?>            
            <?php endif;*/ ?>            
            </p>
            </div>
        <?php 
            // Error in response
            elseif(isset($Yandex) && isset($Yandex->error)):
        ?>
            <div class="error"><?php echo $Yandex->error; ?></div>
        <?php endif; ?>
    </div>
    <!--div class="request">
        <pre>
        <?php //echo htmlentities($request, ENT_QUOTES, "UTF-8") ?>
        </pre>
    </div>-->
</div>
</body>
</html>