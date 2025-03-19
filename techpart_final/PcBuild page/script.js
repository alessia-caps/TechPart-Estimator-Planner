document.addEventListener("DOMContentLoaded", () => {
    loadDraft();
    updateWattageAndScore();
});

const sampleData = {
    cpu: [
        { product: "AMD Ryzen 5 5600X", wattage: 65, socket: "AM4" },
        { product: "Intel Core i5-12400", wattage: 65, socket: "LGA1700" },
        { product: "AMD Ryzen 7 5800X", wattage: 105, socket: "AM4" },
        { product: "Intel Core i7-11700K", wattage: 125, socket: "LGA1200" },
        { product: "AMD Ryzen 9 5900X", wattage: 105, socket: "AM4" },
        { product: "Intel Core i9-12900K", wattage: 125, socket: "LGA1700" },
        { product: "AMD Ryzen 3 3300X", wattage: 65, socket: "AM4" },
        { product: "Intel Core i3-10100F", wattage: 65, socket: "LGA1200" },
        { product: "AMD Ryzen 5 7600X", wattage: 105, socket: "AM5" },
        { product: "Intel Core i5-13600K", wattage: 125, socket: "LGA1700" }
    ],
    motherboard: [
        { product: "MSI B450 TOMAHAWK", wattage: 15, socket: "AM4", ramType: "DDR4" },
        { product: "ASUS Prime Z690-A", wattage: 20, socket: "LGA1700", ramType: "DDR5" },
        { product: "Gigabyte B560M DS3H", wattage: 15, socket: "LGA1200", ramType: "DDR4" },
        { product: "MSI X570 Gaming Plus", wattage: 18, socket: "AM4", ramType: "DDR4" },
        { product: "ASUS ROG Strix B550-F", wattage: 17, socket: "AM4", ramType: "DDR4" },
        { product: "Gigabyte A520M S2H", wattage: 15, socket: "AM4", ramType: "DDR4" },
        { product: "ASUS TUF Gaming B660M-PLUS", wattage: 20, socket: "LGA1700", ramType: "DDR4" },
        { product: "MSI PRO Z690-A", wattage: 22, socket: "LGA1700", ramType: "DDR5" },
        { product: "ASRock X670E Taichi", wattage: 25, socket: "AM5", ramType: "DDR5" },
        { product: "Gigabyte B650 AORUS Elite", wattage: 20, socket: "AM5", ramType: "DDR5" }
    ],
    ram: [
        { product: "Corsair Vengeance 16GB DDR4", wattage: 10, ramType: "DDR4" },
        { product: "G.Skill Trident Z 32GB DDR5", wattage: 12, ramType: "DDR5" },
        { product: "Kingston Fury Beast 16GB DDR4", wattage: 9, ramType: "DDR4" },
        { product: "Crucial Ballistix 32GB DDR4", wattage: 11, ramType: "DDR4" },
        { product: "Patriot Viper Steel 16GB DDR4", wattage: 10, ramType: "DDR4" },
        { product: "ADATA XPG Lancer 32GB DDR5", wattage: 13, ramType: "DDR5" },
        { product: "TeamGroup T-Force Delta RGB 16GB DDR5", wattage: 12, ramType: "DDR5" },
        { product: "Silicon Power XPOWER Turbine 16GB DDR4", wattage: 9, ramType: "DDR4" },
        { product: "PNY XLR8 Gaming 32GB DDR4", wattage: 11, ramType: "DDR4" },
        { product: "Samsung 16GB DDR5", wattage: 13, ramType: "DDR5" }
    ],
    gpu: [
        { product: "NVIDIA RTX 3060", wattage: 200 },
        { product: "AMD RX 6700 XT", wattage: 230 },
        { product: "NVIDIA RTX 3070", wattage: 220 },
        { product: "AMD RX 6800", wattage: 250 },
        { product: "NVIDIA RTX 3080", wattage: 320 },
        { product: "AMD RX 6900 XT", wattage: 300 },
        { product: "NVIDIA RTX 3090", wattage: 350 },
        { product: "AMD RX 7900 XT", wattage: 330 },
        { product: "NVIDIA RTX 4060 Ti", wattage: 160 },
        { product: "AMD RX 7600", wattage: 180 }
    ]
};

function updateWattageAndScore() {
    let totalWattage = 0;
    let issues = [];

    let cpuElement = document.getElementById("cpu-product");
    let motherboardElement = document.getElementById("motherboard-product");
    let ramElement = document.getElementById("ram-product");
    let gpuElement = document.getElementById("gpu-product");

    // Sum up wattage
    ["cpu", "motherboard", "ram", "gpu"].forEach(component => {
        let wattageText = document.getElementById(`${component}-wattage`).textContent;
        totalWattage += parseInt(wattageText) || 0;
    });

    // Retrieve compatibility-related attributes
    let cpuSocket = cpuElement.dataset.socket;
    let motherboardSocket = motherboardElement.dataset.socket;
    let motherboardRamType = motherboardElement.dataset.ramType;
    let ramType = ramElement.dataset.ramType;
    let cpuWattage = parseInt(cpuElement.dataset.wattage) || 0;
    let gpuWattage = parseInt(gpuElement.dataset.wattage) || 0;

    // Compatibility checks
    if (cpuElement.textContent !== "-" && motherboardElement.textContent !== "-") {
        if (cpuSocket !== motherboardSocket) {
            issues.push("CPU & Motherboard socket mismatch");
        }
    }

    if (motherboardElement.textContent !== "-" && ramElement.textContent !== "-") {
        if (motherboardRamType !== ramType) {
            issues.push("RAM & Motherboard mismatch");
        }
    }

    if (cpuElement.textContent !== "-" && gpuElement.textContent !== "-") {
        if (cpuWattage < 70 && gpuWattage > 250) {
            issues.push("Weak CPU & High-end GPU");
        }
    }

    if (totalWattage > 850) {
        issues.push("Total wattage exceeds PSU limits");
    }

    // Determine compatibility status
    let compatibilityStatus = issues.length > 0 ? `No (${issues.join(", ")})` : "Yes";

    document.getElementById("total-wattage").textContent = totalWattage;
    document.getElementById("compatibility-status").textContent = compatibilityStatus;
}


function searchComponent(component) {
    let options = sampleData[component];
    let dropdown = document.getElementById(`${component}-dropdown`);
    
    if (!dropdown) {
        console.error(`Dropdown for ${component} not found.`);
        return;
    }

    dropdown.innerHTML = `<option value="">Select ${component.toUpperCase()}</option>` + 
        options.map(opt => `<option value='${JSON.stringify(opt)}'>${opt.product}</option>`).join("");

    // Make dropdown visible when clicking search
    dropdown.style.display = "inline-block";
}

function selectComponent(component, event) {
    if (!event.target.value) return; // Prevent selecting empty option

    let selected = JSON.parse(event.target.value);
    
    // Assign values
    let productElement = document.getElementById(`${component}-product`);
    let wattageElement = document.getElementById(`${component}-wattage`);
    let storeElement = document.getElementById(`${component}-store`);

    productElement.textContent = selected.product;
    wattageElement.textContent = selected.wattage;
    storeElement.textContent = "PC Express";

    // Set dataset attributes (ensure they are accessible)
    productElement.dataset.socket = selected.socket || "";
    productElement.dataset.ramType = selected.ramType || "";
    productElement.dataset.ramType = selected.ramType || "";
    productElement.dataset.wattage = selected.wattage || "";

    updateWattageAndScore();
}


function saveDraft() {
    const build = {
        cpu: document.getElementById("cpu-product").textContent,
        motherboard: document.getElementById("motherboard-product").textContent,
        ram: document.getElementById("ram-product").textContent,
        gpu: document.getElementById("gpu-product").textContent
    };
    const csvContent = Object.keys(build).map(key => `${key},${build[key]}`).join("\n");
    const blob = new Blob([csvContent], { type: "text/csv" });
    const a = document.createElement("a");
    a.href = URL.createObjectURL(blob);
    a.download = "pcBuildDraft.csv";
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

function clearBuild() {
    ["cpu", "motherboard", "ram", "gpu"].forEach(component => {
        document.getElementById(`${component}-product`).textContent = "-";
        document.getElementById(`${component}-wattage`).textContent = "-";
        document.getElementById(`${component}-store`).textContent = "-";
    });
    updateWattageAndScore();
}

function removeComponent(component) {
    document.getElementById(`${component}-product`).textContent = "-";
    document.getElementById(`${component}-wattage`).textContent = "-";
    document.getElementById(`${component}-store`).textContent = "-";
    updateWattageAndScore();
}