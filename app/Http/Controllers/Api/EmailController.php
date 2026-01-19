<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Throwable;

class EmailController extends Controller
{
    public function send(Request $request)
    {
        $data = $request->validate([
            'recipients' => 'required|array|min:1',
            'recipients.*' => 'email',

            'subject' => 'required|string',
            'htmlBody' => 'required|string',

            'attachments' => 'nullable|array',
            'attachments.*.name' => 'required_with:attachments|string',
            'attachments.*.type' => 'required_with:attachments|string',
            'attachments.*.data' => 'required_with:attachments|string',
        ]);

        // Extract FROM NAME from HTML
        $fromName = $this->extractSocietyName($data['htmlBody'])
            ?? config('mail.from.name');

        $fromAddress = config('mail.from.address');

        // 🔹 Create email log (PENDING)
        $log = $this->createEmailLog(
            request: $request,
            data: $data,
            fromName: $fromName,
            fromAddress: $fromAddress
        );

        try {
            Mail::send([], [], function ($message) use ($data, $fromName, $fromAddress) {

                $message->from($fromAddress, $fromName)
                        ->to($data['recipients'])
                        ->subject($data['subject'])
                        ->html($data['htmlBody']);
              
                 // Add CC if provided
                if (!empty($data['cc'])) {
                  $message->cc($data['cc']);
                }

                foreach ($data['attachments'] ?? [] as $file) {
                    $decoded = base64_decode($file['data'], true);

                    if ($decoded === false) {
                        throw new \InvalidArgumentException('Invalid base64 attachment');
                    }

                    $message->attachData(
                        $decoded,
                        $file['name'],
                        ['mime' => $file['type']]
                    );
                }
            });

            // ✅ Mark as sent
            $log->update([
                'status' => 'sent',
            ]);

            return response()->json([
                'status' => 'success',
                'fromName' => $fromName,
            ]);

        } catch (Throwable $e) {

            // ❌ Mark as failed
            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e; // let Laravel handle the error response
        }
    }

    /**
     * Create Email Log Entry
     */
    private function createEmailLog(
        Request $request,
        array $data,
        string $fromName,
        string $fromAddress
    ): EmailLog {
        return EmailLog::create([
            'from_email' => $fromAddress,
            'from_name' => $fromName,

            'to_emails' => $data['recipients'],
            'cc_emails' => $data['cc'] ?? null,
            'subject' => $data['subject'],
            'html_body' => $data['htmlBody'],

            // Store attachment metadata only
            'attachments' => collect($data['attachments'] ?? [])
                ->map(fn ($a) => [
                    'name' => $a['name'],
                    'type' => $a['type'],
                    'size_kb' => round(strlen($a['data']) / 1024, 2),
                ]),

            'status' => 'pending',

            // Execution context
            'executed_domain' => $request->getHost(),
            'request_url' => $request->fullUrl(),
            'protocol' => $request->isSecure() ? 'https' : 'http',
            'request_ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    /**
     * Extract and sanitize Society Name from HTML body
     * - DOM-safe
     * - Stops at correct node
     * - Enforces length limit
     */
    private function extractSocietyName(string $html, int $maxLength = 60): ?string
    {
        libxml_use_internal_errors(true);

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);

        $xpath = new \DOMXPath($dom);

        $strongNodes = $xpath->query(
            "//strong[contains(translate(normalize-space(.),
            'abcdefghijklmnopqrstuvwxyz', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'),
            'SOCIETY NAME')]"
        );

        if ($strongNodes->length === 0) {
            return null;
        }

        foreach ($strongNodes as $strong) {
            $sibling = $strong->nextSibling;

            if ($sibling && $sibling->nodeType === XML_TEXT_NODE) {
                $name = trim($sibling->nodeValue);

                if ($name === '') {
                    continue;
                }

                $name = preg_replace('/[^A-Z0-9._\- ]/i', '', $name);

                if (mb_strlen($name) > $maxLength) {
                    $name = mb_substr($name, 0, $maxLength);
                }

                return $name;
            }
        }

        return null;
    }
}
