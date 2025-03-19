# Project Setup

## Prerequisites
Make sure you have the following installed:
- [XAMPP](https://www.apachefriends.org/index.html)
- [Python](https://www.python.org/downloads/)
- Required Python libraries (see below)
- [Ollama](https://ollama.ai/)
- [Google Chrome](https://www.google.com/chrome/)
- [ChromeDriver](https://sites.google.com/chromium.org/driver/)

## Installation and Setup

### 1. Start XAMPP
Start XAMPP and ensure that Apache and MySQL are running.

### 2. Import Database
Import the `user_database.sql` file into **phpMyAdmin**.

### 3. Install Required Python Libraries
Run the following command to install the necessary Python dependencies:
```bash
pip install flask flask-cors selenium webdriver-manager chromedriver-autoinstaller ollama
```

### 4. Download ChromeDriver
Ensure you have ChromeDriver installed and properly set up:
```bash
python -c "import chromedriver_autoinstaller; chromedriver_autoinstaller.install()"
```

### 5. Download Ollama Mistral Model
Run the following command to download the **Mistral** model for Ollama:
```bash
ollama pull mistral
```

### 6. Run the Backend Server
Execute the `server.py` script in the terminal:
```bash
python server.py
```

### 7. Run the Website
Open a web browser and navigate to:
```
http://localhost/techpart/login.php
```

You're all set! Your website should now be running locally.

