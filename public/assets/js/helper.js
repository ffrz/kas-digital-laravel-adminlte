function localeNumberToNumber(str) {
    let num = str.replace(/\./g, '');
    return parseInt(num);
}

function ToNumber(str) {
    let num = str.replace(/\./g, '');
    return parseInt(num);
}

function toLocaleNumber(num, digit) {
    if (typeof num === 'string')
        num = Number(num);
    return num.toLocaleString('id-ID', {
        minimumFractionDigits: digit,
        maximumFractionDigits: digit
    });
}

function formatRupiah(num, digit = 0) {
    return 'Rp. ' + toLocaleNumber(num, digit);
}

function generateRandomColors(count) {
    var colors = [];
    for (var i = 0; i < count; i++) {
        var color = '#' + Math.floor(Math.random() * 16777215).toString(16);
        colors.push(color);
    }
    return colors;
}

/**
* Fungsi untuk menghasilkan warna unik berdasarkan jumlah data
*/
function generateUniqueColors(count) {
    let colors = [];
    let hueStep = 360 / count; // Pisahkan warna dalam lingkaran 360°

    for (let i = 0; i < count; i++) {
        let hue = i * hueStep; // Rotasi hue
        let saturation = 80; // Jaga agar warnanya tetap mencolok (70% - 100%)
        let lightness = 50; // Pertahankan kecerahan sedang (40% - 60%)

        colors.push(hslToHex(hue, saturation, lightness));
    }
    return colors;
}

/**
 * Konversi HSL ke HEX
 */
function hslToHex(h, s, l) {
    s /= 100;
    l /= 100;
    let c = (1 - Math.abs(2 * l - 1)) * s;
    let x = c * (1 - Math.abs((h / 60) % 2 - 1));
    let m = l - c / 2;
    let r = 0, g = 0, b = 0;

    if (h < 60) { r = c; g = x; b = 0; }
    else if (h < 120) { r = x; g = c; b = 0; }
    else if (h < 180) { r = 0; g = c; b = x; }
    else if (h < 240) { r = 0; g = x; b = c; }
    else if (h < 300) { r = x; g = 0; b = c; }
    else { r = c; g = 0; b = x; }

    r = Math.round((r + m) * 255);
    g = Math.round((g + m) * 255);
    b = Math.round((b + m) * 255);

    return `#${((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1).toUpperCase()}`;
}
