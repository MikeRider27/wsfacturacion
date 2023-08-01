<?php

interface XmlgenConfig {
    public function getDefaultValue(): bool;
    public function getArrayValuesSeparator(): ?string;
    public function getErrorSeparator(): ?string;
    public function getErrorLimit(): ?int;
    public function getRedondeoSedeco(): bool;
    public function getDecimals(): ?int;
    public function getTaxDecimals(): ?int;
    public function getPygDecimals(): ?int;
    public function getPygTaxDecimals(): ?int;
    public function getUserObjectRemove(): bool;
    public function isTest(): bool;
}

class XmlgenConfigImpl implements XmlgenConfig {
    private $defaultValues;
    private $arrayValuesSeparator;
    private $errorSeparator;
    private $errorLimit;
    private $redondeoSedeco;
    private $decimals;
    private $taxDecimals;
    private $pygDecimals;
    private $pygTaxDecimals;
    private $userObjectRemove;
    private $test;

    public function __construct(
        bool $defaultValues = true,
        ?string $arrayValuesSeparator = ',',
        ?string $errorSeparator = ';',
        ?int $errorLimit = 100,
        bool $redondeoSedeco = true,
        ?int $decimals = 2,
        ?int $taxDecimals = 2,
        ?int $pygDecimals = 2,
        ?int $pygTaxDecimals = 2,
        bool $userObjectRemove = true,
        bool $test = false
    ) {
        $this->defaultValues = $defaultValues;
        $this->arrayValuesSeparator = $arrayValuesSeparator;
        $this->errorSeparator = $errorSeparator;
        $this->errorLimit = $errorLimit;
        $this->redondeoSedeco = $redondeoSedeco;
        $this->decimals = $decimals;
        $this->taxDecimals = $taxDecimals;
        $this->pygDecimals = $pygDecimals;
        $this->pygTaxDecimals = $pygTaxDecimals;
        $this->userObjectRemove = $userObjectRemove;
        $this->test = $test;
    }

    public function getDefaultValue(): bool
    {
        return $this->defaultValues;
    }

    public function getArrayValuesSeparator(): ?string
    {
        return $this->arrayValuesSeparator;
    }

    public function getErrorSeparator(): ?string
    {
        return $this->errorSeparator;
    }

    public function getErrorLimit(): ?int
    {
        return $this->errorLimit;
    }

    public function getRedondeoSedeco(): bool
    {
        return $this->redondeoSedeco;
    }

    public function getDecimals(): ?int
    {
        return $this->decimals;
    }

    public function getTaxDecimals(): ?int
    {
        return $this->taxDecimals;
    }

    public function getPygDecimals(): ?int
    {
        return $this->pygDecimals;
    }

    public function getPygTaxDecimals(): ?int
    {
        return $this->pygTaxDecimals;
    }

    public function getUserObjectRemove(): bool
    {
        return $this->userObjectRemove;
    }

    public function isTest(): bool
    {
        return $this->test;
    }
}