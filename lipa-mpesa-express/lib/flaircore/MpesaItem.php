<?php
/**
 * Contains the properties of the mpesa request object body
 * sent to Safaricom mpesa api
 */


class MpesaItem
{

    private $BusinessShortCode;

    private $Password;

    private $TransactionType;

    private $Amount;

    private $PartyA;

    private $PartyB;

    private $PhoneNumber;

    private $CallBackURL;

    private $AccountReference;

    private $TransactionDesc;

    /**
     * @var MpesaConfigs
     */
    private $mpesaConfigs;

    /**
     * MpesaItem constructor.
     *
     * @param MpesaConfigs $mpesaConfigs
     */
    public function __construct(MpesaConfigs $mpesaConfigs)
    {
        $this->mpesaConfigs = $mpesaConfigs;
    }


    public function getBusinessShortCode(): string
    {
        return $this->BusinessShortCode;
    }


    public function setBusinessShortCode(?string $BusinessShortCode): void
    {
        $this->BusinessShortCode = $BusinessShortCode;
    }

    /**
     * we don't need to set this, just use the values from the MpesaConfigs
     * class and the businessShortCode set in this class(MpesaItem)
     * i.e set explicitly and return
     */
    public function getPassword(): string
    {
        return base64_encode($this->getBusinessShortCode().$this->mpesaConfigs->getPassKey().$this->mpesaConfigs->getTimestamp());
    }

    public function getTransactionType(): string
    {
        return $this->TransactionType;
    }

    public function setTransactionType(?string $TransactionType): void
    {
        $this->TransactionType = $TransactionType;
    }

    public function getAmount(): string
    {
        return $this->Amount;
    }

    public function setAmount(?string $Amount): void
    {
        $this->Amount = $Amount;
    }

    public function getPartyA(): string
    {
        return $this->PartyA;
    }

    public function setPartyA(?string $PartyA): void
    {
        $this->PartyA = $PartyA;
    }

    public function getPartyB(): string
    {
        return $this->PartyB;
    }

    public function setPartyB(?string $PartyB): void
    {
        $this->PartyB = $PartyB;
    }

    public function getPhoneNumber(): string
    {
        return $this->PhoneNumber;
    }

    public function setPhoneNumber(?string $PhoneNumber): void
    {
        $this->PhoneNumber = $PhoneNumber;
    }

    public function getCallBackURL(): string
    {
        return $this->CallBackURL;
    }

    public function setCallBackURL(?string $CallBackURL): void
    {
        $this->CallBackURL = $CallBackURL;
    }

    public function getAccountReference(): ?string
    {
        return $this->AccountReference;
    }

    public function setAccountReference(?string $AccountReference): void
    {
        $this->AccountReference = $AccountReference;
    }

    public function getTransactionDesc(): ?string
    {
        return $this->TransactionDesc;
    }

    public function setTransactionDesc(?string $TransactionDesc): void
    {
        $this->TransactionDesc = $TransactionDesc;
    }

}
