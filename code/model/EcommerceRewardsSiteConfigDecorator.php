<?php

/**
 *@author nicolaas [at] sunnysideup.co.nz
 *
 *
 **/

class EcommerceRewardsSiteConfigDecorator extends DataObjectDecorator
{
    public function extraStatics()
    {
        return array(
            'db' => array(
                'PointsExchangeRate' => 'Currency',
            ),
        );
    }


    public function updateCMSFields(FieldSet &$fields)
    {
        $fields->addFieldToTab('Root.RewardPoints', new NumericField('PointsExchangeRate', 'The number of dollars a customer has to spend to receive one reward point.'));
        return $fields;
    }

    private $lastPointsExchangeRate = 0;

    public function onBeforeWrite()
    {
        $this->lastPointsExchangeRate = $this->owner->PointsExchangeRate;
    }

    public function onAfterWrite()
    {
        if (!$this->lastPointsExchangeRate != $this->owner->PointsExchangeRate) {
            $obj = new EcommerceRewardsSiteConfigDecorator_Log();
            $obj->PreviousValue = $this->lastPointsExchangeRate;
            $obj->CurrentValue = $this->owner->PointsExchangeRate;
            $obj->write();
        }
    }
}


class EcommerceRewardsSiteConfigDecorator_Log extends DataObject
{
    public static $db = array(
        "PreviousValue" => "Currency",
        "CurrentValue" => "Currency"
    );
}
