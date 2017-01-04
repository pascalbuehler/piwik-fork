<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\GoalConversionOverview;

use Piwik\DataTable;
use Piwik\DataTable\Row;
use Piwik\Period\Factory as PeriodFactory;
use Piwik\Piwik;
use Piwik\Plugins\Goals\API as GoalsAPI;
use Piwik\Segment;
use Piwik\Site;

/**
 * API for plugin GoalConversionOverview
 *
 * @method static \Piwik\Plugins\GoalConversionOverview\API getInstance()
 */
class API extends \Piwik\Plugin\API
{

    /**
     * @param int    $idSite
     * @param string $period
     * @param string $date
     * @param bool|string $segment
     * @return DataTable
     */
    public function getGoalConversionOverview($idSite, $period, $date, $segment = false)
    {
        Piwik::checkUserHasViewAccess($idSite);

        // Init
        $goalsApi = GoalsAPI::getInstance();
        $table = new DataTable();

        // Period
        $timezone = Site::getTimezoneFor($idSite);
        $periodMeta = PeriodFactory::makePeriodFromQueryParams($timezone, $period, $date);
        $table->setMetadata('periodDateStart', $periodMeta->getDateStart());
        $table->setMetadata('periodDateEnd', $periodMeta->getDateEnd());
        $table->setMetadata('periodDatePretty', $periodMeta->getPrettyString());


        // Goals and conversions
        $goals = $goalsApi->getGoals($idSite);
        foreach($goals as $goal) {
            if($goal['deleted']) {
                continue;
            }

            $conversionData = $goalsApi->get($idSite, $period, $date, $segment, $goal['idgoal'], ['nb_visits', 'nb_conversions', 'conversion_rate']);

            $row = $conversionData->getFirstRow();
            $row->addColumn('label', $goal['name']);

            $row->setNonLoadedSubtableId($goal['idgoal']);

            $table->addRow($row);
        }

        return $table;
    }

    /**
     * @param int    $idSite
     * @param string $period
     * @param string $date
     * @param bool|string $segment
     * @return DataTable
     */
    public function getGoalConversionOverviewSubtable($idSite, $period, $date, $idSubtable, $segment = false)
    {
        Piwik::checkUserHasViewAccess($idSite);

        // Init
        $goalsApi = GoalsAPI::getInstance();
        $subTable = new DataTable();

        $conversionData = $goalsApi->get($idSite, $period, $date, $segment, $idSubtable, ['nb_visits', 'nb_conversions', 'conversion_rate']);
        $row = $conversionData->getFirstRow();

        $subTable->addRowsFromSimpleArray([
            [
                'label' => Piwik::translate('General_NewVisitor'),
                'nb_visits' => $row->getColumn('nb_visits_new_visit'),
                'nb_conversions' => $row->getColumn('nb_conversions_new_visit'),
                'conversion_rate' => $row->getColumn('conversion_rate_new_visit'),
            ],
            [
                'label' => Piwik::translate('General_ReturningVisitor'),
                'nb_visits' => $row->getColumn('nb_visits_returning_visit'),
                'nb_conversions' => $row->getColumn('nb_conversions_returning_visit'),
                'conversion_rate' => $row->getColumn('conversion_rate_returning_visit'),
            ],
        ]);

        return $subTable;

    }

    public function getGoalConversionOverviewMetadata($idSite, $period, $date, $segment = false)
    {
        Piwik::checkUserHasViewAccess($idSite);

        // Init
        $table = new DataTable();
        $row = new Row();
        $data = [];

        // Site
        $site = new Site($idSite);
        $row->addColumn('siteId', $site->getId());
        $row->addColumn('siteName', $site->getName());

        // Period
        $timezone = Site::getTimezoneFor($idSite);
        $period = PeriodFactory::makePeriodFromQueryParams($timezone, $period, $date);
        $row->addColumn('periodDateStart', $period->getDateStart()->getDatetime());
        $row->addColumn('periodDateEnd', $period->getDateEnd()->getDatetime());
        $row->addColumn('periodDatePretty', $period->getPrettyString());

        // Add row to table
        $table->addRow($row);

        return $table;
    }
}
