<?php
/**
 * Created by PhpStorm.
 * User: eskil.saatvedt
 * Date: 27.02.2018
 * Time: 13:39
 */

namespace AppBundle\XmlModels;

use AppBundle\Entity\FmLocation1;
use AppBundle\Entity\FmLocation2;
use AppBundle\Entity\FmStreetaddress;
use AppBundle\Entity\FmBuildingExportView;

class HmAddressXMLModel
{
    /**
     * @var string
     */
    protected $Address1 = '';
    /**
     * @var string
     */
    protected $StreetNo = '';
    /**
     * @var string
     */
    protected $Address2 = '';
    /**
     * @var string
     */
    protected $PostalCode = '';
    /**
     * @var string
     */
    protected $PostalArea = '';
    /**
     * @var string
     */
    protected $AddressName = '';
//    /**
//     * @var int
//     */
//    protected $Latitude = 0;
//    /**
//     * @var int
//     */
//    protected $Longitude = 0;
//    /**
//     * @var string
//     */
//    protected $Address3 = '';
//    /**
//     * @var string
//     */
//    protected $Address4 = '';
    /**
     * @var string
     */
    protected $Country = 'NO';

    /**
     * HmAddressXMLModel constructor.
     */
    public function __construct()
    {
        // Stuff
    }

	/**
	 * @var $building FmBuildingExportView
	 * @return HmAddressXMLModel
	 */
	public static function constructBuildingExport(FmBuildingExportView $building): HmAddressXMLModel
	{
		$instance = new self();
		$instance->Address1 = $building->getAddress() ?? '' ;
		$instance->PostalArea = $building->getPoststed() ?? '';
		$instance->PostalCode = $building->getPostnummer() ?? '';
		$instance->AddressName = $building->getLoc2Name() ?? '';
		$instance->StreetNo = $building->getStreetNumber() ?? '';
		return $instance;
	}


    /**
     * @return string
     */
    public function getAddress1(): string
    {
        return $this->Address1;
    }

    /**
     * @return string
     */
    public function getStreetNo(): string
    {
        return $this->StreetNo;
    }

    /**
     * @return string
     */
    public function getAddress2(): string
    {
        return $this->Address2;
    }

    /**
     * @return string
     */
    public function getPostalCode(): string
    {
        return $this->PostalCode;
    }

    /**
     * @return string
     */
    public function getPostalArea(): string
    {
        return $this->PostalArea;
    }

    /**
     * @return string
     */
    public function getAddressName(): string
    {
        return $this->AddressName;
    }
//
//    /**
//     * @return int
//     */
//    public function getLatitude(): int
//    {
//        return $this->Latitude;
//    }
//
//    /**
//     * @return int
//     */
//    public function getLongitude(): int
//    {
//        return $this->Longitude;
//    }
//
//    /**
//     * @return string
//     */
//    public function getAddress3(): string
//    {
//        return $this->Address3;
//    }
//
//    /**
//     * @return string
//     */
//    public function getAddress4(): string
//    {
//        return $this->Address4;
//    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->Country;
    }
}