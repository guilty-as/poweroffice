<?php


namespace Guilty\Poweroffice\Requests;


class ExternallyDeliverableInvoiceDeliveredRequest
{
    protected $externallyDeliverableInvoiceId;
    protected $fileBase64;
    protected $fileName;
    protected $comment;

    public function __construct($externallyDeliverableInvoiceId, $comment = null)
    {
        $this->externallyDeliverableInvoiceId = $externallyDeliverableInvoiceId;
        $this->comment = $comment;
    }

    public function withComment($comment)
    {
        $this->comment = $comment;
    }

    public function withFile($filePath, $filename = null)
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("File does not exist");
        }

        if (!$filename) {
            $filename = basename($filePath);
        }

        $data = file_get_contents($filePath);
        $this->fileBase64 = base64_encode($data);
        $this->fileName = $filename;

        return $this;
    }

    public function toArray()
    {
        return [
            "Comment" => $this->comment,
            "ExternallyDeliverableInvoiceId" => $this->externallyDeliverableInvoiceId,
            "File" => [
                "Base64EncodedData" => $this->fileBase64,
                "FileName" => $this->fileName,
            ],
        ];
    }
}