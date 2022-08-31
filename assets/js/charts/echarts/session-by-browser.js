import utils from '../../utils';
import { getPosition, echartSetOption } from './echarts-utils';
import $ from 'jquery';
var echarts = require('echarts');
/* -------------------------------------------------------------------------- */
/*                                Session By Device                           */
/* -------------------------------------------------------------------------- */

const sessionByBrowserChartInit = () => {
  const $sessionByBroswser = document.querySelector(
    '.echart-session-by-browser'
  );

  if ($sessionByBroswser) {
    const userOptions = utils.getData($sessionByBroswser, 'options');
    const chart = echarts.init($sessionByBroswser);
    var dataset;
    $.ajax({
      method:"post",
      data:"RepartitionDepReussi",
      url:"/admin/statistics/data/"+ $sessionByBroswser.id,
      success: function(data){
        
        dataset=data["dataset"];
    
        const getDefaultOptions = () => ({
          color: [
            utils.getColors().primary,
            utils.getColors().success,
            utils.getColors().info,
            utils.getColors().danger,
          ],
          tooltip: {
            trigger: 'item',
            padding: [7, 10],
            backgroundColor: utils.getGrays()['100'],
            borderColor: utils.getGrays()['300'],
            textStyle: { color: utils.getColors().dark },
            borderWidth: 1,
            transitionDuration: 0,
            formatter: params =>
              `<strong>${params.data.name}:</strong> ${params.data.value}%`,
            position(pos, params, dom, rect, size) {
              return getPosition(pos, params, dom, rect, size);
            },
          },
    
          legend: { show: false },
          series: [
            {
              type: 'pie',
              radius: ['100%', '65%'],
              avoidLabelOverlap: false,
              hoverAnimation: false,
              itemStyle: {
                borderWidth: 2,
                borderColor: utils.getColor('card-bg'),
              },
              label: {
                normal: {
                  show: true,
                },
                emphasis: {
                  show: true,
                },
              },
              labelLine: { normal: { show: true } },
              data: dataset.all,
            },
          ],
        });
    
        echartSetOption(chart, userOptions, getDefaultOptions);
        const selectMenu = document.querySelector(
          "[data-target='.echart-session-by-browser']"
        );
    
        if (selectMenu) {
          selectMenu.addEventListener('change', e => {
            const value = e.currentTarget.value;
            chart.setOption({
              series: [{ data: dataset[value] }],
            });
          });
        }
      },
      error:function(data){
        console.log("error");
      }
    });
  }
};

export default sessionByBrowserChartInit;
