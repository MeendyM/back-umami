<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Masmerise\Toaster\Toaster;

class FirebaseStorage
{
    function uploadFile($filePath, $remoteFileName)
    {
        $bucket = 'tria-126f1.firebasestorage.app';

        // Abrir el archivo
        $fileData = file_get_contents($filePath);
        if ($fileData === false) {
            throw new \Exception("No se pudo leer el archivo: $filePath");
        }

        // Construir URL de Firebase Storage
        $url = "https://firebasestorage.googleapis.com/v0/b/{$bucket}/o?uploadType=media&name=products/" . urlencode($remoteFileName);

        // Log the upload URL
        Log::info("Uploading file to Firebase Storage: $url");

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

    function uploadFileToFolder($filePath, $remoteFileName, $folder = 'products')
    {
        $bucket = env('BUCKET');

        // Abrir el archivo
        $fileData = file_get_contents($filePath);
        if ($fileData === false) {
            throw new \Exception("No se pudo leer el archivo: $filePath");
        }

        // Construir URL de Firebase Storage con folder dinámico
        $url = "https://firebasestorage.googleapis.com/v0/b/{$bucket}/o?uploadType=media&name={$folder}/" . urlencode($remoteFileName);

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

    function deleteFile($filePath)
    {
        $bucket = config('services.firebase.bucket');

        // Construir URL de Firebase Storage para eliminar
        $url = "https://firebasestorage.googleapis.com/v0/b/{$bucket}/o/" . urlencode($filePath);

        // Iniciar cURL
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return true; // Eliminación exitosa
        } else {
            Log::warning("Error al eliminar archivo de Firebase: HTTP $httpCode - $response - $error");
            throw new \Exception("Error al eliminar archivo de Firebase: HTTP $httpCode - $response - $error");
        }
    }
}
