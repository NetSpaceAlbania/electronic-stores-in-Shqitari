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
 * ========== FIRST WEBSITE: crs.al ==========
 */
$crs_url = "https://crs.al/?s=" . urlencode($query) . "&post_type=product";
$html_crs = file_get_html($crs_url);

if ($html_crs) {
    foreach ($html_crs->find('.list-col-item .item-product') as $product) {
        $title = $product->find('.product-title', 0)->plaintext ?? '';
        $link = $product->find('a', 0)->href ?? '';
        $image = $product->find('img', 0)->src ?? '';
        $priceText = $product->find('.product-price', 0)->plaintext ?? '';

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
            'website' => 'https://crs.al'
        ];
    }
}

/**
 * ========== SECOND WEBSITE: germancomputers.al ==========
 */
$url2 = "https://www.germancomputers.al/sq/shop?search=" . urlencode($query) . "&order=name+asc";
$html2 = file_get_html($url2);

if ($html2) {
    foreach ($html2->find('.col.tp-product-item') as $product) {
        $title = $product->find('.tp-product-title', 0)->plaintext ?? '';
        $link = $product->find('a', 0)->href ?? '';
        
        // Fix image fetching logic
        $imageTag = $product->find('img', 0);
        $image = $imageTag->src ?: $imageTag->getAttribute('data-src');

        // Fix relative URLs
        if (strpos($image, '/') === 0) {
            $image = 'https://www.germancomputers.al' . $image;
        }

        $priceText = $product->find('.oe_currency_value', 0)->plaintext ?? '';

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
            'website' => 'https://www.germancomputers.al'
        ];
    }
}

/**
 * ========== THIRD WEBSITE: gjirafa50.al ==========
 */
$gjirafa_url3 = "https://gjirafa50.al/search?q=" . urlencode($query);
$html_gjirafa3 = file_get_html($gjirafa_url3);

if ($html_gjirafa3) {
    foreach ($html_gjirafa3->find('.product-item') as $product) {
        // Extract product title
        $title = $product->find('.product-title', 0)->plaintext ?? '';

        // Extract product link
        $link = $product->find('a', 0)->href ?? '';
        if ($link) {
            $link = 'https://gjirafa50.al' . $link; // Ensure full URL
        }

        // Extract product image
        $image = $product->find('img', 0)->src ?? '';
        if ($image) {
            // Ensure full URL if the image path is relative
            if (strpos($image, '/') === 0) {
                $image = 'https://gjirafa50.al' . $image;
            }
        }

        // Extract price (assuming price is inside a span with class 'price')
        $priceText = $product->find('.price', 0)->plaintext ?? '';

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
            'website' => 'https://gjirafa50.al'
        ];
    }
}

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


// ✅ Send the final combined array as valid JSON
header('Content-Type: application/json');
echo json_encode($products);
exit;
?>
