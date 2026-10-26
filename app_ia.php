<?php
// app_ia.php - App Móvil Market Express Asistente
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Market Express - Escáner Móvil</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <style>
        :root {
            --bg-dark: #121624;
            --card-bg: rgba(30, 38, 56, 0.9);
            --gold-accent: #decab0;
            --green-accent: #22c55e;
            --text-light: #ffffff;
            --text-muted: #a0aec0;
        }

        * { box-sizing: border-box; font-family: 'Poppins', sans-serif; }

        body {
            background-color: var(--bg-dark);
            color: var(--text-light);
            margin: 0;
            padding: 12px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .brand-header {
            width: 100%;
            max-width: 520px;
            background: linear-gradient(135deg, rgba(35, 43, 67, 0.95), rgba(18, 22, 36, 0.95));
            border: 1px solid rgba(222, 202, 176, 0.35);
            border-radius: 18px;
            padding: 12px;
            text-align: center;
            margin-bottom: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        .brand-title { font-size: 1.15rem; font-weight: 800; margin: 0; color: #ffffff; }
        .brand-title span { color: var(--gold-accent); }

        .tab-selector {
            display: flex; gap: 8px; width: 100%; max-width: 520px; margin-bottom: 12px;
        }

        .tab-btn {
            flex: 1; padding: 12px; border: 1px solid rgba(222, 202, 176, 0.2);
            background: rgba(30, 38, 56, 0.6); color: var(--text-muted);
            border-radius: 12px; font-size: 0.85rem; font-weight: 700; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 6px;
        }

        .tab-btn.active {
            background: var(--gold-accent); color: #121624; border-color: var(--gold-accent);
        }

        .panel-container {
            width: 100%; max-width: 520px; background: var(--card-bg);
            border: 1px solid rgba(222, 202, 176, 0.25); border-radius: 18px;
            padding: 16px; display: none; box-shadow: 0 8px 25px rgba(0,0,0,0.4);
        }

        .panel-container.active { display: block; }

        /* ÁREA DEL ESCÁNER QR AMPLIADA Y PROPORCIONAL */
        .camera-viewport {
            width: 100%;
            min-height: 320px;
            max-height: 420px;
            background: #090c14;
            border: 2px dashed rgba(222, 202, 176, 0.4);
            border-radius: 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 8px;
            margin-bottom: 12px;
            position: relative;
            overflow: hidden;
        }

        #qr-live-stream-box {
            width: 100% !important;
            height: 100% !important;
            border-radius: 12px;
            overflow: hidden;
        }

        #qr-live-stream-box video {
            width: 100% !important;
            height: 100% !important;
            object-fit: contain !important; /* Mantiene la relación de aspecto sin deformar */
            background: #000;
        }

        .img-preview {
            max-width: 100%;
            max-height: 300px;
            width: auto;
            height: auto;
            object-fit: contain;
            border-radius: 10px;
            display: none;
        }

        .btn-action-cam {
            width: 100%;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff; border: none; padding: 14px; border-radius: 12px;
            font-size: 0.9rem; font-weight: 700; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 10px;
        }

        .amount-display {
            background: #090c14; border: 2px solid var(--green-accent);
            border-radius: 14px; padding: 12px; text-align: center; margin-bottom: 12px;
        }

        .amount-display-title { font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase; }
        .amount-display-val { font-size: 2.2rem; font-weight: 800; color: var(--green-accent); margin: 0; }

        .grid-title {
            font-size: 0.75rem; color: var(--gold-accent); font-weight: 700;
            text-transform: uppercase; margin: 12px 0 6px 0;
        }

        .currency-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; margin-bottom: 10px; }

        .btn-quick-add {
            background: rgba(18, 22, 36, 0.8); border: 1px solid rgba(222, 202, 176, 0.25);
            color: #ffffff; padding: 10px 2px; border-radius: 8px; font-size: 0.85rem; font-weight: 700;
            cursor: pointer; display: flex; flex-direction: column; align-items: center;
        }

        .btn-quick-add span { font-size: 0.62rem; color: var(--text-muted); font-weight: normal; }

        .footer-btns { display: flex; gap: 8px; margin-top: 14px; }

        .btn-reset {
            flex: 1; background: #ef4444; color: #fff; border: none; padding: 12px;
            border-radius: 10px; font-weight: 700; font-size: 0.85rem; cursor: pointer;
        }

        .btn-send {
            flex: 2; background: var(--gold-accent); color: #121624; border: none; padding: 12px;
            border-radius: 10px; font-weight: 800; font-size: 0.9rem; cursor: pointer;
        }

        input[type="file"] { display: none; }
    </style>
</head>
<body>

    <div class="brand-header">
        <h1 class="brand-title">Market Express <span>Móvil</span></h1>
        <div style="font-size: 0.7rem; color: var(--text-muted); margin-top:2px;">ESCÁNER LOCAL DE CÓDIGOS & COBRO EN CAJA</div>
    </div>

    <div class="tab-selector">
        <button class="tab-btn active" id="tab-qr" onclick="cambiarModo('qr')">
            <i class="fa-solid fa-qrcode"></i> Escáner QR / Barras
        </button>
        <button class="tab-btn" id="tab-cash" onclick="cambiarModo('cash')">
            <i class="fa-solid fa-money-bill-wave"></i> Registro Efectivo
        </button>
    </div>

    <!-- PESTAÑA 1: ESCÁNER QR -->
    <div class="panel-container active" id="panel-qr">
        <label for="input-cam-qr-file" class="btn-action-cam" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
            <i class="fa-solid fa-camera"></i> Escanear con Foto HD
        </label>
        <input type="file" id="input-cam-qr-file" accept="image/*" capture="environment" onchange="procesarFotoQR(this)">

        <button type="button" class="btn-action-cam" id="btn-toggle-live-qr" onclick="toggleCamaraLiveQR()">
            <i class="fa-solid fa-video"></i> Transmisión Cámara en Vivo
        </button>

        <div class="camera-viewport">
            <img id="preview-qr-img" class="img-preview" src="" alt="Vista Previa QR">
            <div id="qr-live-stream-box"></div>
            <p id="qr-status-text" style="font-size: 0.75rem; color: var(--text-muted); text-align: center; position: absolute; bottom: 8px; margin: 0; background: rgba(0,0,0,0.6); padding: 4px 8px; border-radius: 6px;">
                Selecciona una opción de escaneo.
            </p>
        </div>

        <div style="background: rgba(18, 22, 36, 0.6); padding: 12px; border-radius: 12px; text-align: center; border: 1px solid rgba(222, 202, 176, 0.15);">
            <span style="font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase;">Último Código Leído:</span>
            <div id="last-code-display" style="font-size: 1.2rem; font-weight: 800; color: var(--gold-accent); margin-top: 2px;">Ninguno</div>
        </div>
    </div>

    <!-- PESTAÑA 2: REGISTRO DE EFECTIVO MANUAL -->
    <div class="panel-container" id="panel-cash">
        <div class="amount-display">
            <div class="amount-display-title">Total Calculado en Teléfono</div>
            <div class="amount-display-val" id="cash-total-val">$0.00</div>
        </div>

        <div class="grid-title"><i class="fa-solid fa-coins"></i> Monedas USD</div>
        <div class="currency-grid">
            <button type="button" class="btn-quick-add" onclick="sumarMonto(0.01)">+$0.01 <span>1¢ Penny</span></button>
            <button type="button" class="btn-quick-add" onclick="sumarMonto(0.05)">+$0.05 <span>5¢ Nickel</span></button>
            <button type="button" class="btn-quick-add" onclick="sumarMonto(0.10)">+$0.10 <span>10¢ Dime</span></button>
            <button type="button" class="btn-quick-add" onclick="sumarMonto(0.25)">+$0.25 <span>25¢ Quarter</span></button>
            <button type="button" class="btn-quick-add" onclick="sumarMonto(0.50)">+$0.50 <span>50¢ Half D.</span></button>
            <button type="button" class="btn-quick-add" onclick="sumarMonto(1.00)">+$1.00 <span>Moneda $1</span></button>
        </div>

        <div class="grid-title"><i class="fa-solid fa-money-bill"></i> Billetes USD</div>
        <div class="currency-grid">
            <button type="button" class="btn-quick-add" onclick="sumarMonto(1.00)">+$1.00 <span>Billete $1</span></button>
            <button type="button" class="btn-quick-add" onclick="sumarMonto(5.00)">+$5.00 <span>Billete $5</span></button>
            <button type="button" class="btn-quick-add" onclick="sumarMonto(10.00)">+$10.00 <span>Billete $10</span></button>
            <button type="button" class="btn-quick-add" onclick="sumarMonto(20.00)">+$20.00 <span>Billete $20</span></button>
            <button type="button" class="btn-quick-add" onclick="sumarMonto(50.00)">+$50.00 <span>Billete $50</span></button>
            <button type="button" class="btn-quick-add" onclick="sumarMonto(100.00)">+$100.00 <span>Billete $100</span></button>
        </div>

        <div class="footer-btns">
            <button type="button" class="btn-reset" onclick="reiniciarTotal()"><i class="fa-solid fa-rotate-left"></i> Borrar</button>
            <button type="button" class="btn-send" onclick="enviarACajaPC()"><i class="fa-solid fa-paper-plane"></i> Enviar a Caja PC</button>
        </div>
    </div>

    <script>
        function id(e) { return document.getElementById(e); }

        let totalCalculado = 0.00;
        let html5QrScannerLive = null;
        let camaraActiva = false;

        function cambiarModo(modo) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.panel-container').forEach(p => p.classList.remove('active'));

            if (modo === 'qr') {
                id('tab-qr').classList.add('active');
                id('panel-qr').classList.add('active');
            } else {
                if (camaraActiva) detenerCamaraLiveQR();
                id('tab-cash').classList.add('active');
                id('panel-cash').classList.add('active');
            }
        }

        function procesarFotoQR(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const previewImg = id('preview-qr-img');
                
                // Mostrar vista previa escalada proporcionalmente
                previewImg.src = URL.createObjectURL(file);
                previewImg.style.display = 'block';
                id('qr-live-stream-box').style.display = 'none';

                id('qr-status-text').innerText = "Procesando imagen...";

                const html5QrCode = new Html5Qrcode("qr-live-stream-box");
                html5QrCode.scanFile(file, true)
                    .then(decodedText => {
                        id('last-code-display').innerText = decodedText;
                        id('qr-status-text').innerText = "¡Código leído exitosamente!";
                        reproducirBeep();
                        enviarCodigoServidor(decodedText);
                    })
                    .catch(() => {
                        id('qr-status-text').innerText = "No se detectó un código nítido. Intenta enfocar más cerca.";
                    });
            }
        }

        function toggleCamaraLiveQR() {
            if (camaraActiva) {
                detenerCamaraLiveQR();
            } else {
                iniciarCamaraLiveQR();
            }
        }

        function iniciarCamaraLiveQR() {
            id('preview-qr-img').style.display = 'none';
            id('qr-live-stream-box').style.display = 'block';
            id('qr-status-text').innerText = "Abriendo cámara en vivo...";
            
            html5QrScannerLive = new Html5Qrcode("qr-live-stream-box");

            // Configuración con cuadro amplio de lectura
            const config = { fps: 15, qrbox: { width: 260, height: 260 } };

            html5QrScannerLive.start({ facingMode: "environment" }, config, (decodedText) => {
                id('last-code-display').innerText = decodedText;
                id('qr-status-text').innerText = "¡Código leído y enviado!";
                reproducirBeep();
                enviarCodigoServidor(decodedText);
            }, (error) => {})
            .then(() => {
                camaraActiva = true;
                id('btn-toggle-live-qr').innerHTML = '<i class="fa-solid fa-stop"></i> Detener Cámara';
                id('btn-toggle-live-qr').style.background = '#ef4444';
                id('qr-status-text').innerText = "Apunta al código QR o de barras.";
            })
            .catch(err => {
                alert("Usa el botón 'Escanear con Foto HD' para escanear localmente.");
            });
        }

        function detenerCamaraLiveQR() {
            if (html5QrScannerLive && camaraActiva) {
                html5QrScannerLive.stop().then(() => {
                    camaraActiva = false;
                    id('btn-toggle-live-qr').innerHTML = '<i class="fa-solid fa-video"></i> Transmisión Cámara en Vivo';
                    id('btn-toggle-live-qr').style.background = 'linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)';
                    id('qr-status-text').innerText = "Cámara detenida.";
                });
            }
        }

        function enviarCodigoServidor(codigo) {
            const formData = new FormData();
            formData.append('action', 'registrar_qr');
            formData.append('codigo', codigo);

            fetch('api_ia.php', { method: 'POST', body: formData });
        }

        function sumarMonto(val) {
            totalCalculado += parseFloat(val);
            id('cash-total-val').innerText = '$' + totalCalculado.toFixed(2);
            if (navigator.vibrate) navigator.vibrate(35);
        }

        function reiniciarTotal() {
            totalCalculado = 0.00;
            id('cash-total-val').innerText = '$0.00';

            const formData = new FormData();
            formData.append('action', 'registrar_efectivo');
            formData.append('monto', 0);
            fetch('api_ia.php', { method: 'POST', body: formData });
        }

        function enviarACajaPC() {
            if (totalCalculado <= 0) {
                alert('Ingresa un monto mayor a $0.00');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'registrar_efectivo');
            formData.append('monto', totalCalculado);

            fetch('api_ia.php', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        alert('¡Monto de $' + totalCalculado.toFixed(2) + ' enviado a la laptop!');
                        reproducirBeep();
                    }
                });
        }

        function reproducirBeep() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                osc.type = 'sine';
                osc.frequency.value = 1000;
                osc.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.12);
            } catch(e) {}
        }
    </script>
</body>
</html>