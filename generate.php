<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">

<?php
require 'sys/funz.php';

$files = array_map('pathinfo', glob('img/*.*'));

$processedArray = array_map(function($file) {
    $imageName = $file['basename'];
    $nameParts = explode('_', pathinfo($file['filename'], PATHINFO_FILENAME));
    $formattedName = ucfirst($nameParts[0]) . ', ' . $nameParts[1];

    return [
        'img' => $imageName,
        'brand' => 'Hobibear',
        'name' => $formattedName,
        'price' => 49,
        'priceCents' => 99,
        'description' => "Perfect for walking, everyday life",
        'sizings' => range(41, 45),
        'rating' => number_format(rand(450, 500) / 100, 2)
    ];
}, $files);

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

foreach($userJSONs as $fileName => $userJSON){
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
    file_put_contents("assets/users/$fileName.json", json_encode($userData));
    //echo $fileName;
    vdp($userData);

}



$textarea = '';
foreach ($processedArray as $product){
    $hiddenStar = 100 - (explode('.',$product['rating'])[1]);
    //$textarea .= $product['rating'] . " => hidden = $hiddenStar";
    $textarea .= '<div class="container mb-2">
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
    </div>';

}


?>
<textarea id="copy"><?=minify_html($textarea)?></textarea>
<button onclick="copyText()">Copy Text</button>


<?php
echo $textarea;
vdp($processedArray);
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