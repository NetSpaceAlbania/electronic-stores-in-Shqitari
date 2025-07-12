<?php
error_reporting(E_ALL); 
ini_set('display_errors', 1);

// Include the Simple HTML DOM library
include('simple_html_dom/simple_html_dom.php');

// Get the search query and max price from the GET parameters
$query = isset($_GET['search']) ? $_GET['search'] : '';
$maxPrice = isset($_GET['maxPrice']) ? floatval($_GET['maxPrice']) : null; // Ensure it's float

// If no search query, return an empty array and exit
if (empty($query)) {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}

// Initialize an array to hold the products
$products = [];

/**
 * ========== MERRJEP AL: Searching for products ==========
 */
$merrjep_url = "https://www.merrjep.al/njoftime/elektronika/ne-shitje-kerkoj-te-blej-nderrohet/q-" . urlencode($query);
$html_merrjep = file_get_html($merrjep_url);

if ($html_merrjep) {
    foreach ($html_merrjep->find('.ad-item') as $product) {
        // Extract product title
        $title = $product->find('.title', 0)->plaintext ?? '';

        // Extract product link
        $link = $product->find('a', 0)->href ?? '';
        if ($link) {
            $link = 'https://www.merrjep.al' . $link; // Ensure full URL
        }

        // Extract product image
        $image = $product->find('img', 0)->src ?? '';
        if ($image) {
            // Ensure full URL if the image path is relative
            if (strpos($image, '/') === 0) {
                $image = 'https://www.merrjep.al' . $image;
            }
        }

        // Extract price (assuming price is inside a span with class 'price' or 'price-tag')
        $priceText = $product->find('.price-tag', 0)->plaintext ?? '';

        // Remove non-numeric characters and convert to float
        $price = (float) preg_replace('/[^0-9.]/', '', $priceText);

        // Check max price condition
        if ($maxPrice && $price > $maxPrice) {
            continue;
        }

        // Add to products array
        $products[] = [
            'title' => trim($title),
            'link' => $link,
            'image' => $image,
            'price' => $priceText,
            'website' => 'https://www.merrjep.al'
        ];
    }
}

// Output the JSON response
header('Content-Type: application/json');
echo json_encode($products);

?>
