<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="indexstyles.css">
    <title>TechPart - Products</title>
</head>
<body>
<div class="header">
    <div class="logo">
        <img src="logo.png" alt="Web Name">
    </div>
    <nav>
        <a href="index.php" class="active">Products</a>
        <a href="PcBuild page/pcbuild.php">PC Builds</a>
        <div class="auth-section">
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Display the logged-in user's first and last name -->
                <span>Welcome, <a href="profile.php"><?php echo htmlspecialchars($_SESSION['first_name'] . " " . $_SESSION['last_name']); ?></a>!</span>
                <a href="logout.php">Sign Out</a>
            <?php else: ?>
                <!-- If not logged in, show Sign In link -->
                <a href="login.html">Sign In</a>
            <?php endif; ?>
        </div>
    </nav>
</div>
    

    <div class="title-section">Products</div>

    <div class="container">
        <div class="left-section">
            <h3>Sort by</h3>
            <select id="sort-options" onchange="applyFilters()">
                <option value="none">None</option>
                <option value="name-asc">Alphabetically (A to Z)</option>
                <option value="name-desc">Alphabetically (Z to A)</option>
                <option value="price-asc">Price (Low to High)</option>
                <option value="price-desc">Price (High to Low)</option>
            </select>

            <h3>Filter by Brand</h3>
            <div id="brand-filters"></div>

            <h3>Filter by Category</h3>
            <div id="category-filters"></div>

            <h3>Filter by Price</h3>
            <label class="priceee">From: <input type="number" id="min-price" placeholder="0"></label>
            <label class="priceee">To: <input type="number" id="max-price" placeholder="10000"></label>

            <button onclick="applyFilters()">Apply Filters</button>
        </div>

        <div class="right-section" id="product-list">Loading...</div>
    </div>

    <div class="chatbot-container">
    <button id="chatbot-button" class="chatbot-link">Ask Our Chatbot!</button>
    
    <div id="chat-window" class="chat-window">
        <div class="chat-header">
            <span>TechPart Chatbot</span>
            <button id="close-chat">✖</button>
        </div>
        <div class="chat-body" id="chat-body">
            <p class="bot-message">Hello! Ask me anything about PC parts.</p>
        </div>
        <div class="chat-footer">
            <input type="text" id="user-input" placeholder="Type a message..." />
            <button id="send-button">Send</button>
        </div>
    </div>
</div>



    <script>
        let products = [];

        async function loadProducts() {
            try {
                const response = await fetch("http://127.0.0.1:5000/get_products");
                products = await response.json();
                displayProducts(products);
                populateFilters();
            } catch (error) {
                console.error("Error loading products:", error);
                document.getElementById("product-list").innerText = "Failed to load products.";
            }
        }

        function populateFilters() {
            const brands = [...new Set(products.map(p => p.brand))];
            const categories = [...new Set(products.map(p => p.category))];

            document.getElementById("brand-filters").innerHTML = brands.map(b => `<label><input type="checkbox" class="brand-filter" value="${b}">${b}</label><br>`).join('');
            document.getElementById("category-filters").innerHTML = categories.map(c => `<label><input type="checkbox" class="category-filter" value="${c}">${c}</label><br>`).join('');
        }

        function applyFilters() {
            const selectedBrands = Array.from(document.querySelectorAll(".brand-filter:checked")).map(cb => cb.value);
            const selectedCategories = Array.from(document.querySelectorAll(".category-filter:checked")).map(cb => cb.value);
            const minPrice = parseFloat(document.getElementById("min-price").value) || 0;
            const maxPrice = parseFloat(document.getElementById("max-price").value) || Infinity;
            const selectedSort = document.getElementById("sort-options").value;

            let filteredProducts = products.filter(product => 
                (selectedBrands.length === 0 || selectedBrands.includes(product.brand)) &&
                (selectedCategories.length === 0 || selectedCategories.includes(product.category)) &&
                (product.price >= minPrice && product.price <= maxPrice)
            );

        switch (selectedSort) {
            case "name-asc":
                filteredProducts.sort((a, b) => a.name.localeCompare(b.name));
                break;
            case "name-desc":
                filteredProducts.sort((a, b) => b.name.localeCompare(a.name));
                break;
            case "price-asc":
                filteredProducts.sort((a, b) => a.price - b.price);
                break;
            case "price-desc":
                filteredProducts.sort((a, b) => b.price - a.price);
                break;
        }

        displayProducts(filteredProducts);
    }

        function displayProducts(productsToDisplay) {
            document.getElementById("product-list").innerHTML = productsToDisplay.length ? 
            productsToDisplay.map(p => `
                <a href="product.html?id=${p.id}" class="product-link">
                    <div class="product">
                        <img src="${p.image}" alt="${p.name}">
                        <h2>${p.name}</h2>
                        <p><strong>Brand:</strong> ${p.brand}</p>
                        <p><strong>Category:</strong> ${p.category}</p>
                        <p><strong>Price:</strong> ₱${p.price.toLocaleString()}</p>
                    </div>
                </a>
            `).join('') 
            : "No products match your filters.";
        }

        products.forEach(product => {
            const productCard = document.createElement("div");
            productCard.classList.add("product-card");
            productCard.innerHTML = `
                <a href="product.html?id=${product.id}" class="product-link">
                    <img src="${product.image}" alt="${product.name}">
                    <h3>${product.name}</h3>
                    <p><strong>Price:</strong> ₱${product.price.toLocaleString()}</p>
                </a>
            `;
            productList.appendChild(productCard);
        });

        loadProducts();

    document.getElementById("chatbot-button").addEventListener("click", function () {
        let chatWindow = document.getElementById("chat-window");
        chatWindow.style.display = chatWindow.style.display === "block" ? "none" : "block";
    });

    document.getElementById("close-chat").addEventListener("click", function () {
        document.getElementById("chat-window").style.display = "none";
    });

    document.getElementById("send-button").addEventListener("click", sendMessage);
    document.getElementById("user-input").addEventListener("keypress", function(event) {
        if (event.key === "Enter") {
            sendMessage();
        }
    });

    async function sendMessage() {
    const userInput = document.getElementById("user-input").value.trim();
    if (!userInput) return;

    const chatBody = document.getElementById("chat-body");

    // Display user message
    const userMessage = document.createElement("p");
    userMessage.classList.add("user-message");
    userMessage.textContent = userInput;
    chatBody.appendChild(userMessage);

    document.getElementById("user-input").value = "";

    try {
        const response = await fetch("http://127.0.0.1:5000/chat", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ message: userInput })
        });

        const data = await response.json();

        // Display chatbot response with formatted text
        const botMessage = document.createElement("p");
        botMessage.classList.add("bot-message");
        botMessage.innerHTML = data.reply || "Sorry, I couldn't understand that."; // Use innerHTML to support line breaks
        chatBody.appendChild(botMessage);
    } catch (error) {
        console.error("Error:", error);
        const errorMessage = document.createElement("p");
        errorMessage.classList.add("bot-message");
        errorMessage.textContent = "Error connecting to chatbot.";
        chatBody.appendChild(errorMessage);
    }

    chatBody.scrollTop = chatBody.scrollHeight;
}
    </script>
</body>
</html>
