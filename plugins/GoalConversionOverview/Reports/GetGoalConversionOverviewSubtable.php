<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\GoalConversionOverview\Reports;

use Piwik\Piwik;
use Piwik\Plugin\Report;
use Piwik\Plugin\ViewDataTable;

use Piwik\View;


class GetGoalConversionOverviewSubtable extends Base
{
    protected function init()
    {
        parent::init();

        $this->dimension     = null;
        $this->isSubtableReport = true;

        $this->dimension     = null;

        $this->defaultSortColumn = 'conversion_rate';
        $this->defaultSortOrderDesc = true;

        $this->metrics = array('nb_visits', 'nb_conversions', 'conversion_rate');
    }

    public function configureView(ViewDataTable $view)
    {
        $view->config->addTranslations(array('label' => Piwik::translate('General_Visitor')));
        $view->config->columns_to_display = array_merge(array('label'), $this->metrics);

        $view->config->show_search = false;
        $view->config->show_exclude_low_population = false;
        $view->config->show_limit_control = false;
        $view->config->show_all_views_icons = false;
        $view->config->show_offset_information = false;
        $view->config->show_table = false;
    }
}
