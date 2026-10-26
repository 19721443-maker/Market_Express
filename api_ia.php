<?php
// api_ia.php - Backend Inteligente con API Gemini Vision Real
header('Content-Type: application/json');

// 🔑 PEGA AQUÍ TU API KEY DE GOOGLE AI STUDIO (empieza por AIzaSy...)
$geminiApiKey = 'AQ.Ab8RN6JQIZXtySlSIAqREzJRq418c1kZG_PFXxXAbETkwhPRfw';

$archivoEstado = __DIR__ . '/estado_ia.json';

$estado = [
    'monto_efectivo' => 0.00,
    'nuevo_qr' => null,
    'timestamp' => time()
];

if (file_exists($archivoEstado)) {
    $dataJson = json_decode(file_get_contents($archivoEstado), true);
    if (is_array($dataJson)) {
        $estado = array_merge($estado, $dataJson);
    }
}

$action = $_REQUEST['action'] ?? '';

// 1. RECONOCIMIENTO DE DINERO USD CON API GEMINI VISION REAL
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'analizar_efectivo_ia') {
    if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'mensaje' => 'No se recibió la foto del dinero']);
        exit;
    }

    if (empty($geminiApiKey) || $geminiApiKey === 'TU_API_KEY_DE_GEMINI_AQUI') {
        echo json_encode(['success' => false, 'mensaje' => 'Por favor pega tu API Key en api_ia.php']);
        exit;
    }

    $tmpPath = $_FILES['imagen']['tmp_name'];
    $mimeType = mime_content_type($tmpPath);
    $base64Image = base64_encode(file_get_contents($tmpPath));

    $prompt = "Analiza minuciosamente esta imagen de dinero en Dólares Estadounidenses (USD). "
            . "Identifica cada moneda (1c Penny, 5c Nickel, 10c Dime, 25c Quarter, 50c Half Dollar, $1 Moneda) "
            . "y cada billete ($1, $2, $5, $10, $20, $50, $100). Suma los valores y devuelve ÚNICAMENTE "
            . "un objeto JSON válido sin bloques markdown ni texto adicional con la estructura exactas: "
            . "{\"monto\": 1.35, \"descripcion\": \"Moneda de $1, Quarter (25c) y Nickel (5c)\"}";

    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . trim($geminiApiKey);

    $payload = [
        "contents" => [
            [
                "parts" => [
                    ["text" => $prompt],
                    [
                        "inline_data" => [
                            "mime_type" => $mimeType,
                            "data" => $base64Image
                        ]
                    ]
                ]
            ]
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_error($ch);
    curl_close($ch);

    if ($httpCode === 200 && !empty($response)) {
        $result = json_decode($response, true);
        $responseText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
        
        $cleanJsonText = preg_replace('/```json\s*|\s*```/', '', trim($responseText));
        $parsed = json_decode($cleanJsonText, true);

        if ($parsed && isset($parsed['monto'])) {
            echo json_encode([
                'success' => true,
                'monto' => floatval($parsed['monto']),
                'descripcion' => $parsed['descripcion'] ?? 'Efectivo USD Detectado por IA'
            ]);
            exit;
        }
    }

    echo json_encode([
        'success' => false, 
        'mensaje' => "Error HTTP $httpCode al consultar la IA. Verifica tu API Key o conexión."
    ]);
    exit;
}

// 2. REGISTRAR CÓDIGO QR / BARRAS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'registrar_qr') {
    $codigo = trim($_POST['codigo'] ?? '');
    if (!empty($codigo)) {
        $estado['nuevo_qr'] = $codigo;
        $estado['timestamp'] = time();
        file_put_contents($archivoEstado, json_encode($estado));
        echo json_encode(['success' => true, 'codigo' => $codigo]);
        exit;
    }
}

// 3. REGISTRAR O REINICIAR MONTO DE EFECTIVO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'registrar_efectivo') {
    $monto = floatval($_POST['monto'] ?? 0);
    $estado['monto_efectivo'] = $monto;
    $estado['timestamp'] = time();
    file_put_contents($archivoEstado, json_encode($estado));
    echo json_encode(['success' => true, 'monto' => $monto]);
    exit;
}

// 4. LECTURA EN TIEMPO REAL DESDE LA LAPTOP (market_express.php)
if ($action === 'obtener_estado') {
    echo json_encode([
        'success' => true,
        'monto_efectivo' => $estado['monto_efectivo'],
        'nuevo_qr' => $estado['nuevo_qr']
    ]);
    if (!empty($estado['nuevo_qr'])) {
        $estado['nuevo_qr'] = null;
        file_put_contents($archivoEstado, json_encode($estado));
    }
    exit;
}

echo json_encode(['success' => false, 'mensaje' => 'Acción no válida']);
?>