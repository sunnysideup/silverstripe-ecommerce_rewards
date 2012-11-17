<?php


class CheckMemberRewardPoints extends BuildTask {

	function getTitle() {
		return 'Check points for all members';
	}

	function getDescription() {
		return 'Goes through all customers and checks their orders and the points gained / points used.';
	}

	function run($request){
		if($request->Param("ID") =="reset") {
			$reset = true;
			echo "<h1 style=\"color: red\">RESETTING MEMBER POINTS!</h1>";
		}
		else {
			$reset = false;
			echo "<h1>MORE OPTIONS</h1>";
			echo "<a href=\"reset/\">Reset points for all members</a>";
		}
		$members = DataObject::get("Member", "", "Member.Email", "INNER JOIN \"Order\" ON \"Order\".\"MemberID\" = \"Member\".\"ID\"");
		foreach($members as $member) {
			echo "
			<h3>$member->FirstName $member->Surname, $member->Email: $member->PointsBalance</h3>
			<table border=\"1\">
				<thead>
					<tr>
						<th></th>
						<th>USED</th>
						<th>GAINED</th>
						<th>CHANGE</th>
						<th>RUNNING TOTAL</th>
						<th>Note</th>
					</tr>
				</thead>
				<tbody>";
			$orders = DataObject::get(
				"Order",
				"\"MemberID\" = ".$member->ID." AND \"CancelledByID\" = 0 OR \"CancelledByID\" IS NULL",
				" \"Order\".\"ID\" ASC"
			);
			$memberTotal = 0;
			if($reset) {
				$member->PointsBalance = 0;
				$member->write();
			}
			if($orders) {
				foreach($orders as $order) {
					if($order->IsSubmitted()) {
						$note = "";
						if($order->PointsTotal != $order->CalculatePointsTotal()) {
							if($order->CalculatePointsTotal() > 0 && $order->PointsTotal == 0) {
								$order->PointsTotal = $order->CalculatePointsTotal();
								//$order->write();
							}
							$note .= "ERROR, calculated points added: ".$order->CalculatePointsTotal().", difference: ".($order->PointsTotal - $order->CalculatePointsTotal());
						}
						if($order->RewardsTotal != $order->CalculateRewardsTotal()) {
							if($order->CalculateRewardsTotal() > 0 && $order->RewardsTotal == 0) {
								$order->RewardsTotal = $order->CalculateRewardsTotal();
								//$order->write();
							}
							$note .= "ERROR, calculated points added: ".$order->CalculateRewardsTotal().", difference: ".($order->RewardsTotal - $order->CalculateRewardsTotal());
						}
						$change = $order->PointsTotal - $order->RewardsTotal;
						$memberTotal += $change;
						echo "
					<tr>
						<td>Order #".$order->ID."</td>
						<td>".$order->RewardsTotal."</td>
						<td>".$order->PointsTotal."</td>
						<td>".$change."</td>
						<td>".$memberTotal."</td>
					</tr>";
					}
				}
				echo "
					<tr>
						<th colspan=\"3\">CALCULATED BALANCE</th>
						<td>$memberTotal</td>
					</tr>";
			}
			echo "</tbody></table>";
			if($reset) {
				$member->PointsBalance = $memberTotal;
				$member->write();
			}
		}
	}

}



class CheckMemberRewardPoints_AdminEXT extends Extension {

	static $allowed_actions = array('checkmemberrewardpoints' => true);

	function updateEcommerceDevMenuRegularMaintenance($tasks) {
		$tasks[] = 'checkmemberrewardpoints';
		return $tasks;
	}

	function checkmemberrewardpoints($request) {
		$this->owner->runTask("CheckMemberRewardPoints", $request);
	}

}
