
<title>GENERATE, UPDATE PAGES</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">

<?php
require 'sys/funz.php';
require 'sys/content.php';

$imageFiles = array_map('pathinfo', glob('img/*.*'));

$reviewsArray = array_map('trim', explode(PHP_EOL, file_get_contents('assets/reviews.txt')));
//vdp($reviewsArray);

$processedArray = array_map(function($file) {
    $imageName = $file['basename'];
    $code = pathinfo($file['filename'], PATHINFO_FILENAME);
    $nameParts = explode('_', $code);
    $formattedName = ucfirst($nameParts[0]) . ', ' . $nameParts[1];

    return [
        'code' => $code,
        'img' => $imageName,
        'brand' => 'Hobibear',
        'name' => $formattedName,
        'price' => 49,
        'priceCents' => 99,
        'description' => "Perfect for walking, everyday life",
        'sizings' => range(41, 45),
        'rating' => number_format(rand(450, 500) / 100, 2)
    ];
}, $imageFiles);

$usersDirectory = 'assets/users/';
$fileCount = count(glob($usersDirectory . '*.json'));
$requiredUsers = count($processedArray)*4;

if ($fileCount < $requiredUsers) {
    $neededUsers = $requiredUsers - $fileCount;
    for ($i = 0; $i < $neededUsers; $i++) {
        cachejson($fileCount + $i , 'https://randomuser.me/api/', $usersDirectory);
    }
}


$userJSONs = glob($usersDirectory . '*.json');
$userJSONsOutput = [];
foreach($userJSONs as $userJSON){
    $fileName = pathinfo($userJSON, PATHINFO_FILENAME);

    $userData = json_decode(file_get_contents($userJSON), true);
    if(isset($userData['results'][0]['location'])){
        unset(
            $userData['results'][0]['location'],
            $userData['results'][0]['login'],
            $userData['results'][0]['email'],
            $userData['results'][0]['dob'],
            $userData['results'][0]['registered'],
            $userData['results'][0]['phone'],
            $userData['results'][0]['cell'],
            $userData['results'][0]['id'],
            $userData['info']
        );
    }
    $userData['review'] = [
        'text' => $reviewsArray[$fileName],
        'rating' => number_format((rand(1, 10) > 2 ? 5 : (rand(1, 10) > 2 ? 4.5 : 4)), 1),
        'date' => date('d M Y', mt_rand(strtotime('-1 year'), time()))
    ];
    file_put_contents("assets/users/$fileName.json", json_encode($userData));
    //echo $fileName; // no extension :))
    $userJSONsOutput[] = $userData;
}
//vdp($userJSONsOutput);


$textarea = '';
foreach ($processedArray as $id => $product){
    $hiddenStar = 100 - (explode('.',$product['rating'])[1]);
    $urlProductPage = 'items/'.$product['code'].'.html';
    //$textarea .= $product['rating'] . " => hidden = $hiddenStar";
    $textarea .= '<div class="container mb-2">
        <a href="'.$urlProductPage.'" class="text-decoration-none text-reset">
            <div class="d-md-flex bg-secondary bg-opacity-50 p-4 position-relative rounded">
                <div class="me-4 mb-4 mb-md-0">
                    <img src="img/'.$product['img'].'" alt="'.$product['name'].'" height="180" class="rounded">
                    <div class="position-absolute price">
                        <span class="">'.$product['price'].'</span><span class="fs-4 fraction">'.$product['priceCents'].'€</span>
                    </div>
                </div>
                <div>
                    <h2>'.$product['name'].'</h2>
                    <p class="fw-bold fs-5 mb-0 mb-md-2">'.$product['description'].'</p>
                    <div class="stars">
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning" style="mask-image: linear-gradient(to left, transparent '.$hiddenStar.'%, black '.$hiddenStar.'%, black 100%)"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>';

    $sizings = '';
    foreach ($product['sizings'] as $size){
        $sizings .= '<input type="radio" id="radio'.$size.'" name="number" value="'.$size.'">'.
                    '<label for="radio'.$size.'" class="text-bg-danger rounded">'.$size.'</label>';
    }

    $template = file_get_contents('items/_item_template.html');
    $fullContent = str_replace('{brand}', $product['brand'], $template);
    $fullContent = str_replace('{name}', $product['name'], $fullContent);
    $fullContent = str_replace('{title}', $product['name']. " by Bare Shoes Shopping", $fullContent);
    $fullContent = str_replace('{img}', $product['img'], $fullContent);
    $fullContent = str_replace('{sizings}', $sizings, $fullContent);
    $fullContent = str_replace('{price}', $product['price'], $fullContent);
    $fullContent = str_replace('{priceCents}', $product['priceCents'], $fullContent);

    $reviews = ''; // 4
    $startIndex = $id * 4;
    $selectedJSONs = array_slice($userJSONsOutput, $startIndex, 4);
    //echo "ID: $id, startIndex: $startIndex<br>";

    foreach($selectedJSONs as $index => $userContent){
    //vdp($userContent);break(2);
        $firstName = $userContent['results'][0]['name']['first'];
        //echo "$firstName<br>";
        $rating = $userContent['review']['rating'];
       // echo "RATING: $rating";
        if($rating == "4"){
            $classStar = 'star';
        }elseif($rating = "4.5"){
            $classStar = 'star-half';
        }else{
            $classStar = 'star-fill';
        }

        $reviews .= '<section class="row">
        <div class="col-auto">
            <img src="'.$userContent['results'][0]['picture']['thumbnail'].'" alt="Avatar '.$firstName.'"
                class="rounded-circle pe-none user-select-none">
        </div>
        <div class="col">
            <div class="fw-400">'.$firstName.' '.$userContent['results'][0]['name']['last'].'</div>
            <div class="fw-300">'.$userContent['review']['date'].'</div>
            <div class="stars small">
                <i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i
                    class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i
                    class="bi bi-'.$classStar.' text-warning"></i>
            </div>
            <div>' . $userContent['review']['text'] . '</div>';
        $reviews .= ($index < 3) ? '<hr>' : '';
        $reviews .= '</div></section>';
    }
    $fullContent = str_replace('{reviews}', $reviews, $fullContent);

    file_put_contents($urlProductPage, $fullContent);



}

$filename = 'index.html';
$tinyHTML = minify_html($textarea);
file_put_contents($filename, preg_replace('/<!-- content -->(.*?)<!-- \/content -->/s', '<!-- content -->' . $tinyHTML . '<!-- /content -->', file_get_contents($filename)));

echo 'Content inserted successfully.';



?>



<?php
//echo $textarea;
//vdp($processedArray);
?>
<script>
function copyText() {
    var copyText = document.getElementById("copy");
    copyText.select();
    document.execCommand("copy");
    
    var button = document.querySelector("button");
    button.innerText = "Copied";
    
    setTimeout(function() {
        button.innerText = "Copy Text";
    }, 3000);
}
</script>


<?php

// LEGAL


preg_match('/<!-- page top -->(.*?)<!-- \/page top -->/s', file_get_contents('index.html'), $matches);

$pageTop = '';
if (isset($matches[1])) {
    $pageTop = $matches[1];
} else {
    echo "<h1>Custom tags not found or content is empty!<h1>";
}


$files = glob('legal/*.html');

$indexName = 'legal-pages-index';
$templateIndexFile = "legal/$indexName.bak";
$fileExtended = array_merge($files, [$templateIndexFile]);
//vdp($fileExtended);
$links = '';
foreach ($fileExtended as $file) {
    $content = file_get_contents($file);
    $fileName = pathinfo($file, PATHINFO_FILENAME);
    $niceTitle = titleCase(str_replace('-', ' ', $fileName));
    $titleHtml = "<title>$niceTitle - Bare Shoes Shopping</title>";
    //echo "$niceTitle - $fileName<br>";
    $content = str_replace('{head}', $headText.$titleHtml, $content);

    $content = str_replace('{scripts}', $scriptsText, $content);
    $content = str_replace('{pagetop}', $pageTop, $content);
    $content = str_replace('".', '"..', $content);

    file_put_contents("legal/$fileName", $content);
    if($fileName == $indexName){
        continue;
    }
    $links.= "<a class=\"list-group-item list-group-item-action\" href=\"$fileName\">$niceTitle</a>";

}

//echo $links;

$content = file_get_contents("legal/$indexName");
$content = str_replace('{content}', "$links", $content);
// $content = str_replace('Template_index', "Legal Pages Index", $content);
file_put_contents("legal/$fileName", $content);

rename("legal/$indexName", "legal/index.html");