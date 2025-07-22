<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Masmerise\Toaster\Toaster;

class FirebaseStorage
{
    function uploadFile($filePath, $remoteFileName)
    {
        $bucket = env('BUCKET');

        // Abrir el archivo
        $fileData = file_get_contents($filePath);
        if ($fileData === false) {
            throw new \Exception("No se pudo leer el archivo: $filePath");
        }

        // Construir URL de Firebase Storage
        $url = "https://firebasestorage.googleapis.com/v0/b/{$bucket}/o?uploadType=media&name=products/" . urlencode($remoteFileName);

        // Iniciar cURL
        $ch = curl_init($url);

        $headers = [
            "Content-Type: " . mime_content_type($filePath)
        ];

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $fileData,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true); // Subida exitosa
        } else {
            Toaster::error("Error al subir archivo a Firebase: HTTP $httpCode - $response - $error");
            Log::info("Error al subir archivo a Firebase: HTTP $httpCode - $response - $error");
            throw new \Exception("Error al subir archivo a Firebase: HTTP $httpCode - $response - $error");
        }
    }
}
