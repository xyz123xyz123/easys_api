<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use PDF;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;

class PdfController extends Controller
{


public function generatePdf($htmlFile = '', $data = [], $saveLocation = '')
{
    // Normalize base path (Windows + Linux safe)
    $basePath = rtrim(config('constants.PDF_LOCAL_PATH'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

    try {
        // Full directory path (without filename)
        $directory = $basePath . dirname($saveLocation);

        // 🔍 LOG: base & directory paths
        Log::info('PDF Base Path', ['base_path' => $basePath]);
        Log::info('PDF Target Directory', ['directory' => $directory]);

        // Ensure directory exists
        if (!is_dir($directory)) {
            Log::info('PDF Directory missing, creating...', ['directory' => $directory]);
            mkdir($directory, 0775, true);
        }

        // Full file path
        $fullPath = $basePath . $saveLocation;

        // 🔍 LOG: final save path
        Log::info('PDF Full Save Path', ['full_path' => $fullPath]);

        // Generate & save PDF
        PDF::loadView($htmlFile, $data)
            ->setPaper('A4', 'portrait')
            ->save($fullPath);

        // 🔍 LOG: confirm file exists
        $exists = file_exists($fullPath);
        Log::info('PDF Save Result', [
            'exists' => $exists,
            'size'   => $exists ? filesize($fullPath) : 0
        ]);

        return $exists;

    } catch (Exception $e) {

        // 🔥 LOG: exception details
        Log::error('PDF Generation Failed', [
            'view' => $htmlFile,
            'save_location' => $saveLocation,
            'error' => $e->getMessage()
        ]);

        return false;
    }
}


    public function generateLedgerPdf($ledgerData)
    {
        $fileData = [];

        $memberId = $ledgerData['member_data']['member_id'];
        $fileName = "ledger_{$memberId}_" . date('mdyHis') . ".pdf";
        $path = "ledgers/{$fileName}";

        if ($this->generatePdf('pdf.ledger', $ledgerData, $path)) {
            $fileData['file_path'] = config('constants.PDF_DOCUMENT_URL') . $path;
            $fileData['file_name'] = $fileName;
        }

        return $fileData;
    }

    public function generateBillPdf($billData)
    {
        $fileData = [];

        $memberId = $billData['member_data']['member_id'];
        $fileName = "bill_{$memberId}_" . date('mdyHis') . ".pdf";
        $path = "bills/{$fileName}";

        if ($this->generatePdf('pdf.bill', $billData, $path)) {
            $fileData['file_path'] = config('constants.PDF_DOCUMENT_URL') . $path;
            $fileData['file_name'] = $fileName;
        }

        return $fileData;
    }

    public function generateReciptPdf($receiptData)
    {
        $fileData = [];

        $memberId = $receiptData['member_id'];
        $fileName = "receipt_{$memberId}_" . date('mdyHis') . ".pdf";
        $path = "receipts/{$fileName}";

        if ($this->generatePdf('pdf.receipt', $receiptData, $path)) {
            $fileData['file_path'] = config('constants.PDF_DOCUMENT_URL') . $path;
            $fileData['file_name'] = $fileName;
        }

        return $fileData;
    }


public function show(string $type, string $filename)
{
    $path = storage_path("app/pdfs/{$type}/{$filename}");

    if (!file_exists($path)) {
        abort(404);
    }

    return Response::file($path, [
        'Content-Type' => 'application/pdf'
    ]);
}
}