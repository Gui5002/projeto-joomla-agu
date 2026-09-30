<?php
/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:		Buruj Solutions
  + Contact:		www.burujsolutions.com , info@burujsolutions.com
 * Created on:	May 03, 2012
  ^
  + Project: 	JS Tickets
  ^
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
$document = Factory::getDocument();
$document->addStyleSheet('components/com_jssupportticket/include/css/circle.css');
$document->addScript('components/com_jssupportticket/include/js/circle.js');
?>

<?php
/*
 * Google Charts loader.
 *
 * This page used to pull https://www.google.com/jsapi, the legacy loader that
 * Google has retired. Everything hanging off it - google.load() and the
 * top-level google.setOnLoadCallback() - went with it, so the very first call
 * threw and not one chart on the page was ever drawn. The current entry point
 * is gstatic.com/charts/loader.js, which exposes google.charts.load() and
 * google.charts.setOnLoadCallback().
 */
?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
    });

    // Loaded once for the whole page; every chart below registers its own
    // callback against this single load.
    google.charts.load('current', {packages: ['corechart']});

    google.charts.setOnLoadCallback(drawBarChart);
    function drawBarChart() {
        var data = google.visualization.arrayToDataTable([
         ['<?php echo Text::_('Status'); ?>', '<?php echo Text::_('Tickets By Status'); ?>', { role: 'style' }],
         <?php echo $this->result['bar_chart']; ?>
      ]);
     var view = new google.visualization.DataView(data);
      view.setColumns([0, 1,
                       { calc: "stringify",
                         sourceColumn: 1,
                         type: "string",
                         role: "annotation" },
                       2]);

      var options = {
        //title: "Density of Precious Metals, in g/cm^3",
        width: '95%',
        bar: {groupWidth: "95%"},        
        legend: { position: "none" },
      };
      var chart = new google.visualization.ColumnChart(document.getElementById("bar_chart"));
      chart.draw(view, options);        
    }
    google.charts.setOnLoadCallback(drawStackChart);
    function drawStackChart() {
      var data = google.visualization.arrayToDataTable([
        ['<?php echo Text::_('Tickets'); ?>', '<?php echo Text::_('Direct'); ?>', '<?php echo Text::_('Email'); ?>', { role: 'annotation' } ],
        <?php echo $this->result['stack_data']; ?>
      ]);

      var view = new google.visualization.DataView(data);
      var options = {
        width: '95%',
        //height: 400,
        legend: { position: 'top', maxLines: 3 },
        bar: { groupWidth: '75%' },
        isStacked: true,
      };
      var chart = new google.visualization.ColumnChart(document.getElementById("stack_chart"));
      chart.draw(view, options);
    }   
    google.charts.setOnLoadCallback(drawPie3d1Chart);
    function drawPie3d1Chart() {
        var data = google.visualization.arrayToDataTable([
          ['<?php echo Text::_('Departments'); ?>', '<?php echo Text::_('Tickets By Department'); ?>'],
          <?php echo $this->result['pie3d_chart1']; ?>
        ]);

        var options = {
          title: '<?php echo Text::_('Ticket by departments'); ?>',
          chartArea :{width:450,height:350,top:80,left:80},
          is3D: true,
        };

        var chart = new google.visualization.PieChart(document.getElementById('pie3d_chart1'));
        chart.draw(data, options);
    }   
    google.charts.setOnLoadCallback(drawPie3d2Chart);
    function drawPie3d2Chart() {
        var data = google.visualization.arrayToDataTable([
          ['<?php echo Text::_('Priorities'); ?>', '<?php echo Text::_('Tickets By Priority'); ?>'],
          <?php echo $this->result['pie3d_chart2']; ?>
        ]);

        var options = {
          title: '<?php echo Text::_('Tickets By Priorities'); ?>',
          chartArea :{width:450,height:350,top:80,left:80},
          is3D: true,
          colors:<?php echo $this->result['priorityColorList'] ?>
        };

        var chart = new google.visualization.PieChart(document.getElementById('pie3d_chart2'));
        chart.draw(data, options);
    }   
    google.charts.setOnLoadCallback(drawStackChartHorizontal);
    function drawStackChartHorizontal() {
      var horizontalEl = document.getElementById('stack_chart_horizontal');
      var hasPriorityStatusData = <?php echo !empty($this->result['has_priority_status_data']) ? 'true' : 'false'; ?>;
      if (!hasPriorityStatusData) {
        if (horizontalEl) {
          if (horizontalEl.className.indexOf('jsst-report-empty-chart') === -1) {
            horizontalEl.className = (horizontalEl.className ? horizontalEl.className + ' ' : '') + 'jsst-report-empty-chart';
          }
          horizontalEl.innerHTML = '<div class="jsst-report-empty-state"><strong><?php echo addslashes(Text::_('No priority status data available')); ?></strong><span><?php echo addslashes(Text::_('There is no priority status data for the selected report range.')); ?></span></div>';
        }
        return;
      }

      var data = google.visualization.arrayToDataTable([
        <?php
            echo $this->result['stack_chart_horizontal']['title'].',';
            echo $this->result['stack_chart_horizontal']['data'];
        ?>
      ]);

      var view = new google.visualization.DataView(data);

      var options = {
        legend: { position: 'top', maxLines: 3 },
        bar: { groupWidth: '75%' },
        isStacked: true,
        colors:<?php echo $this->result['priorityColorList'] ?>
      };
      var chart = new google.visualization.BarChart(document.getElementById("stack_chart_horizontal"));
      chart.draw(view, options);
    }
</script>
<div id="js-tk-admin-wrapper" class="jsst-screen-report-v73">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
      <?php
$jsstPageTitle = 'Overall Statistics';
$jsstBreadcrumb = array(
    array('label' => 'Dashboard', 'link' => 'index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel'),
    array('label_raw' => Text::_('Overall Statistics'), 'link' => null),
);
include_once('components/com_jssupportticket/views/partials/pageheader.php');
?>
        <?php 
        $open_percentage = 0;
        $close_percentage = 0;
        $answered_percentage = 0;
        $overdue_percentage = 0; 
        $allticket_percentage = 0;

        if($this->result['alltickets'] != 0){
          $open_percentage  = getJSTicketPHPFunctionsClass()->jsticket_round(($this->result['openticket'] / $this->result['alltickets']) * 100);
          $close_percentage  = getJSTicketPHPFunctionsClass()->jsticket_round(($this->result['closeticket'] / $this->result['alltickets']) * 100);
          $answered_percentage = getJSTicketPHPFunctionsClass()->jsticket_round(($this->result['answeredticket'] / $this->result['alltickets']) * 100);
          $overdue_percentage = getJSTicketPHPFunctionsClass()->jsticket_round(($this->result['overdueticket'] / $this->result['alltickets']) * 100);
        }
        $allticket_percentage = 100;
        ?>
        <div id="jsstadmin-data-wrp" class="js-bg-null js-padding-all-null">
            <div class="js-row js-ticket-top-cirlce-count-wrp">
                <div class="js-col-xs-12 js-col-md-2 js-myticket-link js-ticket-myticket-link-myticket js-ticket-open">
                      <a class="js-ticket-green js-myticket-link" href="javascript:void(0);">
                            <div class="js-ticket-cricle-wrp ">
                                <div class="circlebar" data-circle-startTime=0 data-circle-maxValue="<?php echo $open_percentage; ?>" data-circle-dialWidth=15 data-circle-size="100px" data-circle-type="progress">
                                    <div class="loader-bg"></div>
                                </div>
                            </div>
                            <div class="js-ticket-circle-count-text">
                                <?php 
                                      echo Text::_('Open');
                                      if($this->config['show_count_tickets'] == 1)
                                      echo " ( " . $this->result['openticket'] ." ) "; 
                                ?>
                            </div>
                      </a>
                </div>
                <div class="js-col-xs-12 js-col-md-2 js-myticket-link js-ticket-myticket-link-myticket js-ticket-answer">
                    <a class="js-ticket-pink js-myticket-link" href="javascript:void(0);">
                        <div class="js-ticket-cricle-wrp ">
                            <div class="circlebar" data-circle-startTime=0 data-circle-maxValue="<?php echo $answered_percentage; ?>" data-circle-dialWidth=15 data-circle-size="100px" data-circle-type="progress">
                                <div class="loader-bg"></div>
                            </div>
                        </div>
                        <div class="js-ticket-circle-count-text">
                            <?php 
                                echo Text::_('Answered');
                                if($this->config['show_count_tickets'] == 1)
                                echo " ( " . $this->result['answeredticket'] . " ) "; 
                            ?>
                        </div>
                    </a>
                </div>
                <div class="js-col-xs-12 js-col-md-2 js-myticket-link js-ticket-myticket-link-myticket js-ticket-overdue">
                    <a class="js-ticket-orange js-myticket-link" href="javascript:void(0);">
                        <div class="js-ticket-cricle-wrp ">
                            <div class="circlebar" data-circle-startTime=0 data-circle-maxValue="<?php echo $overdue_percentage; ?>" data-circle-dialWidth=15 data-circle-size="100px" data-circle-type="progress">
                                <div class="loader-bg"></div>
                            </div>
                        </div>
                        <div class="js-ticket-circle-count-text">
                            <?php 
                                echo Text::_('Overdue');
                                if($this->config['show_count_tickets'] == 1)
                                echo " ( ". $this->result['overdueticket'] ." ) "; 
                            ?>
                        </div>
                    </a>
                </div>
                <div class="js-col-xs-12 js-col-md-2 js-myticket-link js-ticket-myticket-link-myticket js-ticket-close">
                    <a class="js-ticket-red js-myticket-link" href="javascript:void(0);">
                        <div class="js-ticket-cricle-wrp ">
                            <div class="circlebar" data-circle-startTime=0 data-circle-maxValue="<?php echo $close_percentage; ?>" data-circle-dialWidth=15 data-circle-size="100px" data-circle-type="progress">
                                <div class="loader-bg"></div>
                            </div>
                        </div>
                        <div class="js-ticket-circle-count-text">
                            <?php 
                                echo Text::_('Closed');
                                if($this->config['show_count_tickets'] == 1)
                                echo " ( ". $this->result['closeticket'] ." ) "; 
                            ?>
                        </div>
                    </a>
                </div>
                <div class="js-col-xs-12 js-col-md-2 js-myticket-link js-ticket-myticket-link-myticket js-ticket-allticket">
                    <a class="js-ticket-blue js-myticket-link" href="javascript:void(0);">
                        <div class="js-ticket-cricle-wrp ">
                            <div class="circlebar" data-circle-startTime=0 data-circle-maxValue="<?php echo $allticket_percentage; ?>" data-circle-dialWidth=15 data-circle-size="100px" data-circle-type="progress">
                                <div class="loader-bg"></div>
                            </div>
                        </div>
                        <div class="js-ticket-circle-count-text js-ticket-blue">
                            <?php 
                                echo Text::_('All Tickets');
                                if($this->config['show_count_tickets'] == 1)
                                echo " ( " . $this->result['alltickets'] ." ) "; 
                            ?>
                        </div>
                    </a>
                </div>
            </div>
            <div class="js-admin-report">
                <span class="js-admin-subtitle"><?php echo Text::_('Tickets By Status'); ?></span>
                <div id="bar_chart" style="height:500px;width:100%; "></div>
            </div>
            <div class="js-admin-report halfwidth">
                <span class="js-admin-subtitle"><?php echo Text::_('Tickets By Departments'); ?></span>
                <div id="pie3d_chart1" style="height:400px;width:100%;"></div>
            </div>
            <div class="js-admin-report halfwidth">
                <span class="js-admin-subtitle"><?php echo Text::_('Tickets By Priorities'); ?></span>
                <div id="pie3d_chart2" style="height:400px;width:100%;"></div>
            </div>
            <div class="js-admin-report halfwidth">
                <span class="js-admin-subtitle"><?php echo Text::_('Tickets By Priority And Status'); ?></span>
                <div id="stack_chart_horizontal" class="<?php echo empty($this->result['has_priority_status_data']) ? 'jsst-report-empty-chart' : ''; ?>" style="height:400px;width:100%;">
                    <?php if(empty($this->result['has_priority_status_data'])){ ?>
                        <div class="jsst-report-empty-state">
                            <strong><?php echo Text::_('No priority status data available'); ?></strong>
                            <span><?php echo Text::_('There is no priority status data for the selected report range.'); ?></span>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="js-admin-report halfwidth">
              <span class="js-admin-subtitle"><?php echo Text::_('Tickets By Channel'); ?></span>
              <div id="stack_chart" style="height:400px;width:100%;"></div>
            </div>
            <div class="js-admin-report">
                <span class="js-admin-subtitle"><?php echo Text::_('Tickets By Staff'); ?></span>
                <div id="slice_chart" class="<?php echo empty($this->result['has_staff_ticket_data']) ? 'jsst-report-empty-chart' : ''; ?>" style="height:400px;width:100%;">
                    <?php if(empty($this->result['has_staff_ticket_data'])){ ?>
                        <div class="jsst-report-empty-state">
                            <strong><?php echo Text::_('No staff ticket data available'); ?></strong>
                            <span><?php echo Text::_('There is no staff ticket data for the selected report range.'); ?></span>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once('components/com_jssupportticket/views/partials/pagefooter.php'); ?>
