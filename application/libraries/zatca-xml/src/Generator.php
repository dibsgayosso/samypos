<?php

namespace BaseetApp\UBL;

use Sabre\Xml\Service;

class Generator
{
    public static $currencyID;

    public static function invoice(Invoice $invoice, $currencyId = 'SAR')
    {
        self::$currencyID = $currencyId;

        $xmlService = new Service();

        $xmlService->namespaceMap = [
            'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2' => '',
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2' => 'cbc',
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2' => 'cac',
            'urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2' => 'ext',
            'urn:oasis:names:specification:ubl:schema:xsd:CommonSignatureComponents-2' => 'sig',
            'urn:oasis:names:specification:ubl:schema:xsd:SignatureAggregateComponents-2' => 'sac',
            'urn:oasis:names:specification:ubl:schema:xsd:SignatureBasicComponents-2' => 'sbc'
        ];

        $w = $xmlService->getWriter();
        $w->openMemory();
        $w->contextUri = null;
        $w->setIndent(true);
        $w->startDocument('1.0', 'UTF-8');
        $w->writeElement('Invoice',[$invoice]);
        return $w->outputMemory();

    }
}
