const input = document.getElementById('imageInput');
const preview = document.getElementById('preview');
const textField = document.getElementById('ISBN');

const codeReader = new ZXing.BrowserMultiFormatReader();

input.addEventListener('change', async (e) => {
    const file = e.target.files[0];
    if (!file) return;

    const url = URL.createObjectURL(file);
    preview.src = url;
    preview.style.display = "block";

    try {
        const result = await codeReader.decodeFromImageUrl(url);
        console.log("Barcode found:", result.text);
        textField.value = result.text;
    } catch (err) {
        console.error("No barcode detected", err);
        alert("No barcode found");
    }
});