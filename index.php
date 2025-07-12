<?php
// Ensure you have error reporting on for debugging
error_reporting(E_ALL); 
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Search</title>
    <!-- Add Bootstrap 4 from a CDN -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Optional custom styles -->
    <style>
        .product {
            margin-bottom: 30px;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .product img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }
        .product h3 {
            font-size: 1.25rem;
            color: #333;
        }
        .product p {
            font-size: 1rem;
            color: #555;
        }
        .product-price {
            font-size: 1.2rem;
            font-weight: bold;
            color: green;
        }
        /* Spinner Styles */
        #loadingSpinner {
            display: none;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <!-- Container for the page content -->
    <div class="container mt-5">
        <!-- Search Form -->
        <div class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search Products">
                </div>
                <div class="col-md-4">
                    <input type="number" id="maxPrice" class="form-control" placeholder="Max Price (e.g., 999)">
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary btn-block" onclick="searchProducts()">Search</button>
                </div>
            </div>
        </div>

        <!-- Loading Spinner -->
        <div id="loadingSpinner">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p>Loading results...</p>
        </div>

        <!-- Container to display search results -->
        <div id="product-list" class="row"></div>
    </div>

    <script>
        // Function to fetch and display products based on the search query and max price
        function searchProducts() {
            const query = document.getElementById('searchInput').value.trim();  // Get the search query
            const maxPrice = document.getElementById('maxPrice').value.trim();  // Get the max price value

            // Check if search query is empty
            if (!query) {
                alert("Please enter a search query.");
                return;
            }

            // Validate max price (it should be a positive number)
            if (maxPrice && (isNaN(maxPrice) || parseFloat(maxPrice) < 0)) {
                alert("Please enter a valid maximum price.");
                return;
            }

            // Show the loading spinner while waiting for results
            document.getElementById('loadingSpinner').style.display = 'block';

            // Make the AJAX request to search.php with search query and max price
            fetch('search.php?search=' + encodeURIComponent(query) + '&maxPrice=' + encodeURIComponent(maxPrice))
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to fetch data');
                    }
                    return response.json();  // Parse the JSON response
                })
                .then(data => {
                    displayProducts(data);  // Call the function to display products
                })
                .catch(error => {
                    console.error('Error fetching products:', error);
                    alert("There was an error fetching the products.");
                })
                .finally(() => {
                    // Hide the loading spinner once the search is complete
                    document.getElementById('loadingSpinner').style.display = 'none';
                });
        }

        // Function to display the fetched products in the product list
        function displayProducts(products) {
            const productList = document.getElementById('product-list');
            productList.innerHTML = '';  // Clear any previous search results

            // Check if products are found
            if (products.length === 0) {
                productList.innerHTML = '<p>No products found.</p>';
                return;
            }

            // Loop through the products and add them to the HTML
            products.forEach(product => {
                const productHTML = `
                    <div class="col-md-4">
                        <div class="product">
                            <img src="${product.image}" alt="${product.title}">
                            <h3><a href="${product.link}">${product.title}</a></h3>
                            <p><strong>Website:</strong> <a href="${product.website}" target="_blank">${product.website}</a></p> <!-- Display Website URL -->
                            <p class="product-price">${product.price}</p>
                        </div>
                    </div>
                `;
                productList.innerHTML += productHTML;
            });
        }
    </script>

    <!-- Add Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
