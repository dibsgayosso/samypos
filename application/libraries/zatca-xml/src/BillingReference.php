<?php

namespace BaseetApp\UBL;

use Sabre\Xml\Writer;
use Sabre\Xml\XmlSerializable;

class BillingReference implements XmlSerializable
{
    private $invoiceDocumentReference;

    /**
     * @return InvoiceDocumentReference
     */
    public function getInvoiceDocumentReference(): string
    {
        return $this->invoiceDocumentReference;
    }

    /**
     * @param string $id
     * @return BillingReference
     */
    public function setInvoiceDocumentReference(InvoiceDocumentReference $invoiceDocumentReference): BillingReference
    {
        $this->invoiceDocumentReference = $invoiceDocumentReference;
        return $this;
    }

    /**
     * The xmlSerialize method is called during xml writing.
     *
     * @param Writer $writer
     * @return void
     */
    public function xmlSerialize(Writer $writer)
    {
        if ($this->invoiceDocumentReference !== null) {
            $writer->write([ Schema::CAC . 'InvoiceDocumentReference' => $this->invoiceDocumentReference ]);
        }
    }
}
