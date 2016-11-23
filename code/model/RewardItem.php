<?php

class RewardItem extends DataObject
{
    public static $db = array(
        'Quantity' => 'Int',
        'Points' => 'Currency'
    );

    public static $has_one = array(
        'Product' => 'Product',
        'Order' => 'Order'
    );

    public function QuantityField()
    {
        return new NumericField('Quantity['.$this->ProductID.']', '', $this->Quantity);
    }

    public function TotalPoints()
    {
        if ($this->Points > 0) {
            return $this->Points * $this->Quantity;
        } else {
            return $this->Product()->PointsPrice * $this->Quantity;
        }
    }

    public function AddRewardItemLink()
    {
        return $this->Product()->AddRewardItemLink();
    }

    public function RemoveRewardItemLink()
    {
        return $this->Product()->RemoveRewardItemLink();
    }

    public function ChangeRewardItemLink()
    {
        return $this->Product()->ChangeRewardItemLink();
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        if (!$this->Points) {
            if ($this->ProductID && $this->Product()) {
                $this->Points = $this->Product()->PointsPrice;
            }
        }
    }
}
