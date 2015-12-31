<?php

class EcommerceRewardsMemberDecorator extends DataObjectDecorator
{
    public function extraStatics()
    {
        return array(
            'db' => array(
                'PointsBalance' => 'Currency'
            )
        );
    }

    public function updateCMSFields(&$fields)
    {
        $fields->addFieldToTab("Root.Points", new ReadonlyField("PointsBalance"));
    }

    private $lastPoints = 0;

    public function onBeforeWrite()
    {
        $this->lastPoints = $this->owner->PointsBalance;
    }

    public function onAfterWrite()
    {
        if (!$this->lastPoints != $this->owner->PointsBalance) {
            $obj = new EcommerceRewardsMemberDecorator_Log();
            $obj->PreviousValue = $this->lastPoints;
            $obj->CurrentValue = $this->owner->PointsBalance;
            $obj->write();
        }
    }
}


class EcommerceRewardsMemberDecorator_Log extends DataObject
{

    public static $db = array(
        "PreviousValue" => "Currency",
        "CurrentValue" => "Currency"
    );
}
