<?php
/**
 * Created by PhpStorm.
 * User: nterry
 * Date: 6/2/16
 * Time: 1:19 AM
 */

namespace app\models;

use Yii;
use yii\helpers\Html;

/**
 * This is the model class for collection "permit_report".
 *
 * @property \MongoId|string $_id
 */
class PermitReportRaleigh extends BaseReport
{

	use ReportTrait;

	private $count = 0;

	public function modelConstruct()
	{
		$this->pageTitle = 'Raleigh Permit Report';
		$this->messageType = 'PermitMessage';
		$this->datasetName = 'Raleigh Permit Report';
	}


	function modelViewAttributes()
	{
		return [
			'properties.originaladdressfull_address' => '',
			'properties.originaladdressfull_city' => '',
			'properties.originaladdressfull_state' => '',
			'properties.originaladdressfull_zip' => '',
			'properties.proposedworkdescription' => '',
			'properties.statuscurrentmapped' => '',
			'properties.contractorcompanyname' => '',
			'properties.estprojectcost:currency' => '',
		];
	}

	function modelAttributelabels()
	{
		return [
			'properties.originaladdressfull_address' => 'Address',
			'properties.originaladdressfull_city' => 'City',
			'properties.originaladdressfull_state' => 'State',
			'properties.originaladdressfull_zip' => 'Zip',
			'properties.proposedworkdescription' => 'Description',
			'properties.contractorcompanyname' => "Contractor",
			'properties.estprojectcost' => 'Estimated Project Cost',
			'datetime.sec' => 'Issued date / time',
			'properties.statuscurrentmapped' => 'Current Status',
		];
	}

	public function title($shortUrl)
	{
		$properties = (object)$this->properties;
		if (!isset($properties->owneraddress1)) {
			while (false) ;
		}
		return 'New Permit HERE->' . $shortUrl . ' ' .
		       date(' D M j \a\t g:ia',
			       $this->datetime->sec) .
		       '. Permit type ' .
		       ucwords(strtolower($properties->description)) .
		       (isset($properties->owneraddress1) ? (' at address ' . ucwords(strtolower($properties->owneraddress1))) : "");
	}

	public function popupContent()
	{
		$properties = (object)$this->properties;
		return Html::tag('div',
			'Permit #' . $properties->permitnum .
			date(' D M j \a\t g:ia',
				$this->datetime->sec) .
			'. Permit type ' .
			ucwords(strtolower($properties->description)) . ' ' .
			(isset($properties->owneraddress1) ? ' at address ' . ucwords(strtolower($properties->owneraddress1)) .
			                                     '. ' : ""), ['class' => 'popup-body']) .
		       $this->linkBlock();
	}

	public function datetime($record)
	{
		return new \MongoDate(strtotime($record->issueddate));
	}

	public function id($record)
	{
		return 'Raleigh_' . $record->permitnum;
	}

	public function properties($record)
	{
		$r = clone $record;
		unset($r->longitude_perm);
		unset($r->latitude_perm);
		return $r;
	}

	public function geometry($record)
	{
		if (empty($record->longitude_perm)) {
			return null;
		} else {
			$geometry = new \stdClass();
			$geometry->type = 'Point';
			$geometry->coordinates[0] = (double)$record->longitude_perm;
			$geometry->coordinates[1] = (double)$record->latitude_perm;
			return $geometry;
		}
	}

	public function other($record)
	{
		return null;
	}

	public function getData($days, $start, $rows, &$nhits)
	{
		$appToken = 'oru4D4moiJgtp0waK9fX01XtW';
		$url = 'https://data.raleighnc.gov/resource/5ccj-g2ps.json' .
		       '?$where=' . urlencode('issueddate > "' . date('Y-m-d',
					strtotime("-$days days")) . '"') .
		       '&$order=' . urlencode('issueddate') .
		       '&$offset=' . $start .
		       '&$limit=' . $rows .
		       '&$$app_token=' . $appToken;
		$data = json_decode(file_get_contents($url));
		$nhits = $nhits + count($data);
		return $data;
	}

}