<?php

namespace BaseetApp\UBL;

use Sabre\Xml\Writer;
use Sabre\Xml\XmlSerializable;

class TaxScheme implements XmlSerializable
{
    private $id;
    private $taxTypeCode;
    private $name;

    private $idAttributes;
    //  = [
    //     'schemeID' => "UN/ECE 5153", 
    //     'schemeAgencyID' => "6"
    // ];

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return TaxScheme
     */
    public function setId(string $id, $attributes = null): TaxScheme
    {
        $this->id = $id;
        if(isset($attributes)){
            $this->idAttributes = $attributes;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getTaxTypeCode(): ?string
    {
        return $this->taxTypeCode;
    }

    /**
     * @param string $taxTypeCode
     * @return TaxScheme
     */
    public function setTaxTypeCode(?string $taxTypeCode)
    {
        $this->taxTypeCode = $taxTypeCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return TaxScheme
     */
    public function setName(?string $name)
    {
        $this->name = $name;
        return $this;
    }

    public function xmlSerialize(Writer $writer)
    {
        if($this->id){
            $writer->write([
                'name' => Schema::CBC . 'ID',
                'value' => $this->getId(),
                'attributes' => $this->idAttributes,
            ]);
        }

        if ($this->taxTypeCode !== null) {
            $writer->write([
                Schema::CBC . 'TaxTypeCode' => $this->taxTypeCode
            ]);
        }

        if ($this->name !== null) {
            $writer->write([
                Schema::CBC . 'Name' => $this->name
            ]);
        }
    }
}
