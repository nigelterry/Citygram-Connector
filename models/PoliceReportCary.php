<?php

namespace app\models;

use Yii;
use \yii\helpers\Html;

/**
 * Class PoliceReportCary
 * @package app\models
 *
 * This class handles the pulling of data from the Town of Cary OpenData endpoint and building the documents in
 * the police_report_cary and crime_report collections in the Mongodb database.
 *
 */
class PoliceReportCary extends BaseReport  // All REPORT Models extend from BaseReport 
{

    use ReportTrait;  // All REPORT Models use ReportTrait which adds much of the required code

    /**
     *
     * Sets several required parameters as model is instantiated.
     *
     * @param  string $username
     *
     * @return static|null
     */
    public function modelConstruct()
    {
        $this->pageTitle   = 'Cary Police Report';     // This is the title line used on view and index pages
        $this->messageType = 'CrimeMessage';         // This is the Model Name of the related MESSAGE model
        $this->datasetName = 'Cary Police Report';   // This is a string used in the database to identify the source
    }

    /**
     * The following attribute functions define information that is used on view, index, item pages.
     * view attributes are used on the item pages which and linked from the popup on the map. Hence they provide
     * user information and need to be carefully defined.
     */

    /**
     * function modelAttributes
     *
     * @return array of attributes for the REPORT model. These are merged with others defined at the REPORT and App
     * level
     *
     */
    public function modelAttributes()
    {
        return [

        ];
    }

    /**
     *
     * function modelAttributeLabels
     *
     * @return associative array of user friendly labels for attributes
     *
     */
    public function modelAttributeLabels()
    {
        return [
            'dataset'                            => 'Source',
            'properties.id'                      => 'Cary Police ID #',
            'properties.crime_category'          => 'Crime Category',
            'properties.crime_type'              => 'Crime Type',
            'properties.crimeday'                => 'Day of week',
            'properties.date_from'               => 'Start Date',
            'properties.from_time'               => 'Start Time',
            'properties.date_to'                 => 'End Date',
            'properties.to_time'                 => 'End Time',
            'properties.lat'                     => 'Latitude',
            'properties.lon'                     => 'Longitude',
            'properties.residential_subdivision' => 'Subdivision',
        ];
    }

    /**
     *
     * function modelViewAttributes
     *
     * @return associative array of REPORT attributes for the item and view pages. Keys are the attribute names.
     * values are format strings. See Yii2 docs for full details, or use ':date', ':datetime', ':time'
     */
    public function modelViewAttributes()
    {
        return [
            'dataset'                            => '',
            'properties.id'                      => '',
            'properties.crime_category'          => '',
            'properties.crime_type'              => '',
            'properties.crimeday'                => '',
            'properties.date_from'               => ':date',
            'properties.from_time'               => ':time',
            'properties.date_to'                 => ':date',
            'properties.to_time'                 => ':time',
            'properties.lat'                     => '',
            'properties.lon'                     => '',
            'properties.residential_subdivision' => '',
        ];
    }

    /**
     *
     * function modelViewAttributes
     *
     * @return associative array of REPORT attributes for the item and view pages. Keys are the attribute names.
     * values are format strings. See Yii2 docs for full details, or use ':date', ':datetime', ':time'
     */
    public function modelIndexAttributes()
    {
        return [
            'dataset'                            => '',
            'properties.id'                      => '',
            'properties.crime_category'          => '',
            'properties.crime_type'              => '',
            'properties.crimeday'                => '',
            'properties.date_from'               => ':date',
            'properties.from_time'               => ':time',
            'properties.date_to'                 => ':date',
            'properties.to_time'                 => ':time',
            'properties.lat'                     => '',
            'properties.lon'                     => '',
            'properties.residential_subdivision' => '',
        ];
    }

    /**
     *
     * The following functions take the record returned from the api (or elsewhere) and return information
     * used in building the MESSAGE documents.
     */

    /**
     * @param $shortUrl - The shortened Url for inclusion in the SMS message
     *
     * this - The REPORT model (PoliceReportCary)
     *
     * @return string - The text for the SMS message
     *
     */
    public function title($shortUrl)
    {
        $properties = (object)$this->properties;

        return 'Crime incident HERE->' . $shortUrl . ' ' .
               $this->datetime->toDateTime()->format('D M j \a\t g:ia') .
               '. Cary Police described as ' .
               (isset($properties->crime_category) ?
                   (ucwords(strtolower($properties->crime_category)) . ' ')
                   :
                   '') . ucwords(strtolower($properties->crime_type));
    }

    /**
     *
     * this - The REPORT model (PoliceReportCary
     *
     * @return string - The content of the popup for the map
     */
    public function popupContent()
    {
        $properties = (object)$this->properties;

        return Html::tag('div',
            'Crime incident #' . $properties->incident_number .
            $this->datetime->toDateTime()->format('D M j \a\t g:ia') .
            '. Cary Police described as ' .
            (isset($properties->crime_category) ?
                (ucwords(strtolower($properties->crime_category)) . ' ') :
                '') .
            ucwords(strtolower($properties->crime_type)), ['class' => 'popup-body']) .
               $this->linkBlock();
    }

    /**
     * @param $record - the raw record from the api or elsewhere
     *
     * @return \MongoDB\BSON\UTCDateTime - returns the primary date/time for the REPORT  & MESSAGE
     */
    public function datetime($record)
    {
        $properties = $record->fields;

        return new \MongoDB\BSON\UTCDateTime(strtotime($properties->date_to) * 1000);
    }

    /**
     * @param $record - the raw record from the api or elsewhere
     *
     * @return string - a UNIQUE identifier for the REPORT and MESSAGE. http://citygram.org uses this to de-duplicate
     *
     */
    public function id($record)
    {
        return 'Cary_' . $record->fields->id;
    }

    /**
     * @param $record - the raw record from the api or elsewhere
     *
     * @return object - an object containing all the fields from the api related to the REPORT. Typically these are
     * presented by the api as one object. It is simpler to store these in the mongodb document as a sub-document
     * object as this removes the need to define each one as a seperate attribute
     *
     */
    public function properties($record)
    {
        return (object)$record->fields;
    }

    /**
     * @param $record - the raw record from the api or elsewhere
     *
     * @return object
     */
    public function geometry($record)
    {
        return (object)(isset($record->geometry) ? $record->geometry : null);
    }

    /**
     * @param $record - the raw record from the api or elsewhere
     *
     * @return \stdClass - an object containing each of the fields related to the database storage, not associated with
     * the actual record information
     */
    public function other($record)
    {
        $other                   = new \stdClass();
        $other->datasetid        = $record->datasetid;
        $other->recordid         = $record->recordid;
        $other->record_timestamp = $record->record_timestamp;

        return $other;
    }

    /**
     * @param $days - Number of days to retrieve from the api
     * @param $start - start record for pagination - typically 0 and not used
     * @param $rows - number of rows for paginantion - typically 10000 and not used
     * @param $nhits - Passed by reference, return the number of records retrieved
     *
     * @return mixed - an array of records to be processed
     */
    public function getData($days, $start, $rows, &$nhits)
    {
        $url   = 'https://data.townofcary.org/api/records/1.0/search/?dataset=cpd-incidents' .
                 '&q=' . urlencode("date_from > #now(days=-$days)") .
                 '&sort=date_from' .
                 '&start=' . $start . '&rows=' . $rows;
        $data  = json_decode(file_get_contents($url));
        $nhits = $data->nhits;

        return $data->records;
    }

}
