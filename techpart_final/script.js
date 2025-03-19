document.addEventListener("DOMContentLoaded", () => {
    loadDraft();
});

function saveDraft() {
    const build = {
        cpu: document.getElementById("cpu-store").value,
        motherboard: document.getElementById("motherboard-store").value,
        ram: document.getElementById("ram-store").value,
        gpu: document.getElementById("gpu-store").value
    };
    const blob = new Blob([JSON.stringify(build, null, 2)], { type: "application/json" });
    const a = document.createElement("a");
    a.href = URL.createObjectURL(blob);
    a.download = "pcBuildDraft.csv";
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    alert("Build draft saved locally!");
}

function loadDraft() {
    const input = document.createElement("input");
    input.type = "file";
    input.accept = "application/json";
    input.addEventListener("change", (event) => {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const build = JSON.parse(e.target.result);
                document.getElementById("cpu-store").value = build.cpu;
                document.getElementById("motherboard-store").value = build.motherboard;
                document.getElementById("ram-store").value = build.ram;
                document.getElementById("gpu-store").value = build.gpu;
            };
            reader.readAsText(file);
        }
    });
    input.click();
}

function clearBuild() {
    document.getElementById("cpu-store").value = "";
    document.getElementById("motherboard-store").value = "";
    document.getElementById("ram-store").value = "";
    document.getElementById("gpu-store").value = "";
    alert("Build cleared!");
}

function removeComponent(component) {
    document.getElementById(`${component}-store`).value = "";
}

function searchComponent(component) {
    alert(`Search function for ${component} will be implemented later.`);
}