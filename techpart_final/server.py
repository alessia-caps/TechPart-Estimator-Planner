"""
Enter [python server.py] in the terminal
"""

import threading
import re
import gradio as gr
import ollama
from flask import Flask, jsonify, request
from flask_cors import CORS  
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from webdriver_manager.chrome import ChromeDriverManager
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from chromedriver_autoinstaller import install
import time
import json
import os

# Initialize Flask app
app = Flask(__name__)
CORS(app)

# --- Web Scraping Configuration ---
CATEGORIES = {
    "motherboard": "https://pcx.com.ph/collections/motherboards",
    "processor": "https://pcx.com.ph/collections/processors",
    "graphics card": "https://pcx.com.ph/collections/graphics-cards",
    "ram": "https://pcx.com.ph/collections/memory-modules"
}

# Global variable for storing products
PRODUCTS = []

def parse_price(price_text):
    """Extracts the numerical price from text."""
    price_text = re.sub(r"[^\d.]", "", price_text)
    try:
        return float(price_text) if price_text else 0.0
    except ValueError:
        return 0.0

def get_local_image(category):
    """Return the local image path based on the product category."""
    image_filename = category.replace(" ", "_").lower() + ".png"
    return os.path.join("product_img", image_filename)

def scrape_products():
    """Scrape PC products from the given websites."""
    options = Options()
    options.add_argument("--headless")
    options.add_argument("--disable-gpu")
    options.add_argument("--no-sandbox")

    chromedriver_path = install()

    service = Service(chromedriver_path)
    global driver
    driver = webdriver.Chrome(service=service, options=options)

    products = []
    product_id = 1

    for category, url in CATEGORIES.items():
        driver.get(url)
        
        try:
            WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.CLASS_NAME, "t4s-product-title")))
        except:
            print(f"Timeout waiting for {category} products to load!")
            continue
        
        product_elements = driver.find_elements(By.CLASS_NAME, "t4s-product-title")

        for product in product_elements[:6]:
            try:
                name_element = product.find_element(By.TAG_NAME, "a")
                name = name_element.text.strip()
                brand = name.split()[0]
                link = name_element.get_attribute("href")

                price_element = product.find_element(By.XPATH, "following-sibling::div[contains(@class, 't4s-product-price')]")
                price = parse_price(price_element.text.strip())

                image_link = get_local_image(category)

                products.append({
                    "id": product_id,
                    "name": name,
                    "brand": brand.lower(),
                    "category": category.lower(),
                    "price": price,
                    "link": link,
                    "image": image_link
                })
                product_id += 1
            except Exception as e:
                print("Error extracting product:", e)

    driver.quit()
    return products

def update_product_data():
    """Periodically update the scraped product list."""
    global PRODUCTS
    while True:
        print("Scraping new product data...")
        PRODUCTS = scrape_products()
        print(f"Updated product list with {len(PRODUCTS)} items.")
        time.sleep(3600)  # Refresh every hour

# Start background thread for updating products
threading.Thread(target=update_product_data, daemon=True).start()

@app.route('/get_products', methods=['GET'])
def get_products():
    products = scrape_products()
    return jsonify(PRODUCTS)

@app.route('/get_product/<int:product_id>', methods=['GET'])
def get_product(product_id):
    products = scrape_products()
    product = next((p for p in PRODUCTS if p["id"] == product_id), None)
    return jsonify(product) if product else (jsonify({"error": "Product not found"}), 404)


# ---  Chatbot ---
SYSTEM_PROMPT = """You are a helpful and concise PC parts specialist in the Philippines.
Answer all questions in a short, friendly, and direct way.
Only respond to PC hardware topics (CPUs, GPUs, RAM, storage, motherboards, cooling, etc.).
If asked something unrelated, politely refuse."""

# Chatbot API endpoint
@app.route('/chat', methods=['POST'])
def chat():
    data = request.json
    user_message = data.get("message", "")

    if not user_message:
        return jsonify({"error": "No message provided"}), 400

    # Generate a response from Ollama
    response = ollama.chat(
        model="mistral",
        messages=[
            {"role": "system", "content": SYSTEM_PROMPT},
            {"role": "user", "content": user_message}
        ]
    )
    
    chatbot_reply = response["message"]["content"].strip()
    return jsonify({"reply": chatbot_reply})

if __name__ == '__main__':
    app.run(debug=True, port=5000)