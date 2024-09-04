<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code PDF</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .qr-code { text-align: center; margin-top: 50px; }
    </style>
</head>
<body>
    <h1>Voici votre QR Code</h1>
    <div class="qr-code">
        <img src="{{ $qrCodeBase64 }}" alt="QR Code" style="width: 200px; height: 200px;" />
    </div>
</body>
</html>
