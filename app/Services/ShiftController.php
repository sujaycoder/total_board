<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\Batch;
use App\Models\Production;
use App\Models\downtime;

class ShiftController
{
	public function Getshiftstatus($runnig_batch)
	{
		//get the runnint_batch details
		$batch_id = $runnig_batch[0]['batch_id'];

		///set the timezone now is dubai
		$timezone = timezone_name_from_abbr("", (240*60), false);
		date_default_timezone_set($timezone);


		$datetime = strtotime(date('H:i:s'));//current time

		//get shift details
		$shifdetails = shift::GetDetails()->get();
		$row = count($shifdetails);

		$shift = array();//store the curent shift start and end time
		$id = 0;//store the shift id

		$t = new \App\Services\datetimechecker();//to get the start and end date


			//to get the shift id start time and end time
			for ($i=0; $i < $row; $i++) {

					$start = $shifdetails[$i]['start_time']; //shift start time
					$end = $shifdetails[$i]['end_time']; //shift end time

					if($datetime>=strtotime($start)){

						if($datetime<=strtotime($end)){
							//if time within the current day
							$shift = $t->check(date('y-m-d'), $start, $end);
							$id = $shifdetails[$i]["shift_id"];
							break;
						}else{
							if(strtotime($start)-strtotime($end)>0){
								//if time within the current day
								$shift = $t->check(date('y-m-d'), $start, $end);
								$id = $shifdetails[$i]["shift_id"];
								break;
							}
						}
					}else{
							if(strtotime($start)-strtotime($end)>0){
								//time after 12 am means new day start
								$shift = $t->checkpre(date('y-m-d'), $start, $end);
								$id = $shifdetails[$i]["shift_id"];
								break;
							}
					}

				}


		if(count($shift)>0){

			$data = Production::GetShiftWiseData($shift[0], 
				$shift[1], 
				$batch_id)->get();//get the this shift carton data

			$downtime = downtime::Getdowntime($shift[0], 
				$shift[1])->get();//this shift downtime

			$data[0]['shift_down_time'] = gmdate('H:i:s', 
				$downtime[0]['shift_down_time']);//dwontime convert into h:m:s

			//calculate the total bottle produce in this shift
			$data[0]['shiftwise_bottle_produced'] = 
			$data[0]['shiftwise_carton_produced']*$runnig_batch[0]['no_of_bottle'];

			//curent shift id
			$data[0]['shift_id'] = $id;
			//current shift start time
			$data[0]['start'] = $shifdetails[$id-1]['start_time'];
			//current shift end time
			$data[0]['end'] = $shifdetails[$id-1]['end_time'];

			//return the data
			return $data;
		}

		else{
			$data = array(array(
				"shiftwise_carton_produced"=>0,
				"shiftwise_bottle_produced"=>0,
				'shift_id'=>-5,
				'start'=>'00:00:00',
				'end'=>'00:00:00',
				'shift_down_time'=>'00:00:00'
			));
			return $data;
		}

	}
    //
}