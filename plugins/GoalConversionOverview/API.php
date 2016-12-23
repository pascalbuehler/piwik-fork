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
use Piwik\Piwik;
use Piwik\Plugins\Goals\API as GoalsAPI;

/**
 * API for plugin GoalConversionOverview
 *
 * @method static \Piwik\Plugins\GoalConversionOverview\API getInstance()
 */
class API extends \Piwik\Plugin\API
{

    /**
     * Another example method that returns a data table.
     * @param int    $idSite
     * @param string $period
     * @param string $date
     * @param bool|string $segment
     * @return DataTable
     */
    public function getGoalConversionOverview($idSite, $period, $date, $segment = false)
    {
        Piwik::checkUserHasViewAccess($idSite);

        $goalsApi = GoalsAPI::getInstance();

        $goals = $goalsApi->getGoals($idSite);
var_dump($goals);

        $conversions = [];
        foreach($goals as $goal) {
            $conversion = $goalsApi->get($idSite, $period, $date, $segment, $goal['idgoal'], ['nb_conversions', 'conversion_rate']);
            $conversions[$goal['idgoal']] = $conversion;
        }
var_dump($conversions);


        $table = new DataTable();

        $table->addRowFromArray(array(Row::COLUMNS => array('nb_visits' => 5)));

        return $table;
    }
}
