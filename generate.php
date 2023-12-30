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
        'sizings' => range(41, 45)
    ];
}, $files);

$textarea = '';

foreach ($processedArray as $product){
    
    $textarea .= '
    <div class="container mb-2">
    <div class="d-md-flex bg-secondary bg-opacity-50 p-4 position-relative">
        <div class="me-4 mb-4 mb-md-0">
            <img src="img/'.$product['img'].'" alt="'.$product['name'].'" height="180">
            <div class="position-absolute price">
                <span class="">'.$product['price'].'</span><span class="fs-4 fraction">'.$product['priceCents'].'€</span>
            </div>
        </div>
        <div>
            <h2>'.$product['name'].'</h2>
            <p class="fw-bold fs-5 mb-0 mb-md-2">'.$product['description'].'</p>
            <div class="stars">
                <i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i>
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