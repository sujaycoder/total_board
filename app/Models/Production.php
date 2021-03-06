<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Production extends Model
{
	protected $table = "production_count";

	public function scopeGetProducedQtyDetails($query, $id, $starttime)
	{
		$query
		->select(DB::raw('sum(count) as carton_produced'))
		->where([['batch_id','=', $id],['machine_index', '=', '11'], ['created_time', '>', $starttime]]);
		# code...
	}
	public function scopeGetShiftWiseData($query, $start, $end, $id)
	{		
		$query
		->select(DB::raw('sum(count) as shiftwise_carton_produced'))
		->where([['batch_id', '=', $id],['machine_index', '=', '11'], ['created_time', '>', $start], ['created_time', "<", $end]]);
	}

	public function scopeGetcarton($query, $id, $index, $start, $end)
	{
		$query
		->select(DB::raw('sum(count) as carton_produced'))
		->where([['batch_id','=', $id],['machine_index', '=', $index], ['created_time', '>=', $end], ['created_time', '<=', $start]]);
	}


	///get production count machine wise
	public function scopeMprod($query, $id, $starttime, $m_id)
	{
		$query
		->select(DB::raw('sum(count) as production'))
		->where([['batch_id','=', $id],['machine_index', '=', $m_id], ['created_time', '>', $starttime]]);
		# code...
	}


	///get shitwise data by machine index
	public function scopeGetShiftWiseDataM($query, $start, $end, $id, $m_id)
	{		
		$query
		->select(DB::raw('sum(count) as production'))
		->where([['batch_id','=', $id],['machine_index', '=', $m_id], ['created_time', '>=', $end], ['created_time', '<=', $start]]);
	}




}
