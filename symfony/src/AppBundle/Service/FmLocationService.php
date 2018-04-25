<?php
/**
 * Created by PhpStorm.
 * User: eskil.saatvedt
 * Date: 26.02.2018
 * Time: 15:07
 */

namespace AppBundle\Service;

use AppBundle\Entity\FmBuildingExportView;
use AppBundle\Entity\FmLocation1 as FmLocation1;
use AppBundle\Entity\HmManagerForBuildingView;
use Doctrine\ORM\ArrayCollection as ArrayCollection;
use Doctrine\ORM\EntityManager as EntityManager;
use AppBundle\Entity\GwApplication;
use AppBundle\Entity\CustAttribute;

class FmLocationService
{
    /* @var $em EntityManager */
    private  $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return \Doctrine\ORM\Persisters\Collection $customAttributes
     */
    private function getCustomAttributesForProperties(){
        $appForProperties = $this->em->getRepository(GwApplication::class)->findAppForProperties();
        $gwLocationId = $appForProperties->getLocations()->first()->getId();
        return $this->em->getRepository(CustAttribute::class)->findProperties($gwLocationId);
    }

    private function minifyObjectArrayOfProperties(array $customAttributes ){
        $result = array();
        /* @var $customAttribute CustAttribute */
        foreach($customAttributes as $customAttribute){
            $result = array_merge($result, $customAttribute->getMinfiedArray());
        }
        return $result;
    }

    public function addCustomFieldsForProperties($locations)
    {
        $customAttributes = $this->getCustomAttributesForProperties();
        $minifiedCA = $this->minifyObjectArrayOfProperties($customAttributes);
        /* @var $location FmLocation1 */
        foreach ($locations as $location){
            $location->setCustomAttributes($minifiedCA);
        }
    }

    /**
     * Return the value(s) for a given property, if the property is in both arrays it will look itself up in custom attributes and return its representation
     *
     * @param string $property
     * @param array $objectVars
     * @param array $customAttributes
     * @return mixed
     */
    public static function getValue(string $property, array $objectVars, array $customAttributes)
    {
        if (!array_key_exists($property, $objectVars)) {
            return null;
        }
        if (!array_key_exists($property, $customAttributes)) {
            return $objectVars[$property];
        }

        $index = $objectVars[$property];
        $type = $customAttributes[$property]['type'];
        if (!$index){
            return CustAttribute::getDefaultValue($type);
        }

        switch ($type){
            case 'LB':
                return $customAttributes[$property]['values'][$index];
                break;
            case 'R':
            case 'CH':
                if(!is_array($index)){
                    $index = array_filter(explode(',', $index));
                }
                return array_intersect_key($customAttributes[$property]['values'],array_flip($index));
                break;
            default:
                return CustAttribute::getDefaultValue($type);
        }
    }


	public function getBuildings(): array
	{
		$buildings = $this->em->getRepository('AppBundle:FmBuildingExportView')->findAll();
		$managers = $this->em->getRepository('AppBundle:HmManagerForBuildingView')->findAllIncludingAccount();
		$this->addAgressoIDToManager($managers);
		/* @var FmBuildingExportView $building */
		foreach ($buildings as $building) {
			$this->addManagerDataToBuilding($building, $managers);
		}
		return $buildings;
	}

	private function addManagerDataToBuilding(FmBuildingExportView &$building, array $managers)
	{
		/* @var HmManagerForBuildingView $manager */
		foreach ($managers as $manager) {
			if (empty($manager->getLocationCode())) {
				continue;
			}
			if (empty($manager->getAgressoId())) {
				continue;
			}
			if (empty($manager->getAccount())) {
				continue;
			}
			if ($building->getLoc1() == $manager->getLocationCode()) {
				$building->setManagerAgressoId($manager->getAgressoId() ?? '');
				$building->setManagerUserId($manager->getContactId() ?? '');
				$name = Trim(($manager->getFirstName() ?? '') . ' ' . ($manager->getLastName() ?? ''));
				$building->setManagerName($name);
				$building->setManagerAccountId($manager->getAccount()->getAccountId());
			}
		}
	}

	private function addAgressoIDToManager(array &$managers)
	{
		$users_with_agresso_id = $this->em->getRepository('AppBundle:GwPreference')->findUsersWithPropertyResourceNr();
		/* @var HmManagerForBuildingView $manager */
		foreach ($managers as $key=>&$manager) {
			if(empty($manager->getContactId())){
				unset($managers[$key]);
				continue;
			}

			/* @var GwPreference $pref_user */
			foreach ($users_with_agresso_id as $pref_user) {
				if ($pref_user->getPreferenceOwner() == $manager->getAccount()->getAccountId()) {
					$manager->setAgressoId($pref_user->getResourceNumber());
				}
			}
			if(empty($manager->getAgressoId())){
				unset($managers[$key]);
			}
		}
	}
}