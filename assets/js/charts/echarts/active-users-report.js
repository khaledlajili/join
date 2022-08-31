import utils from '../../utils';
import { getPosition, echartSetOption } from './echarts-utils';
import dayjs from 'dayjs';
var echarts = require('echarts');
import $ from 'jquery';

/* -------------------------------------------------------------------------- */
/*                             Echarts Active Users                           */
/* -------------------------------------------------------------------------- */

const activeUsersChartReportInit = () => {
  const $echartsActiveUsersChart = document.querySelector(
    '.echart-active-users-report'
  );
    var deps=[];
  if ($echartsActiveUsersChart) {
    const userOptions = utils.getData($echartsActiveUsersChart, 'options');
    const chart = echarts.init($echartsActiveUsersChart);

    $.ajax({
      method:"post",
      data:"cycleDeVieCandidat",
      url:"/admin/statistics/data/"+ $echartsActiveUsersChart.id,
      success: function(data){
        // console.log(data["dataset"]);
        var series=[];
        var colors=[utils.getColor('primary'),
              utils.getColor('success'),
              utils.getColor('info'),
              utils.getColor('danger')];

        Object.keys(data["dataset"]).forEach(key =>{
          var color=colors[Math.floor(Math.random()*colors.length)]
          var temp={
            type: 'line',
            data:data["dataset"][key],
            showSymbol: false,
            symbol: 'circle',
            itemStyle: {
              borderColor: color,
              borderWidth: 2,
            },
            lineStyle: {
              color: color,
            },
            symbolSize: 2,
          }
          // console.log(temp);
          deps.push(key);
          series.push(temp);
        });
        // console.log(series);
        const getDefaultOptions = () => ({
          color: [
            utils.getColor('primary'),
            utils.getColor('success'),
            utils.getColor('info'),
            utils.getColor('danger'),
          ],
          tooltip: {
            trigger: 'axis',
            padding: [7, 10],
            backgroundColor: utils.getGrays()['100'],
            borderColor: utils.getGrays()['300'],
            textStyle: { color: utils.getColors().dark },
            borderWidth: 1,
            transitionDuration: 0,
            position(pos, params, dom, rect, size) {
              return getPosition(pos, params, dom, rect, size);
            },
            formatter: tooltipFormatter,
          },
          xAxis: {
            type: 'category',
            data: ["All","PreReg","Collective","Tech","Interview","Trial"],
            boundaryGap: true,
            silent: true,
            axisPointer: {
              lineStyle: {
                color: utils.getGrays()['300'],
              },
            },
            splitLine: { show: false },
            axisLine: {
              lineStyle: {
                color: utils.getGrays()['300'],
              },
            },
            axisTick: {
              show: true,
              length: 20,
              lineStyle: {
                color: utils.getGrays()['200'],
              },
    
              interval: 6,
            },
            axisLabel: {
              color: utils.getGrays()['600'],
              formatter: value => value,
              align: 'left',
              fontSize: 11,
              padding: [0, 0, 0, 5],
              interval: 0,
            },
          },
          yAxis: {
            type: 'value',
            position: 'right',
            axisPointer: { show: false },
            splitLine: {
              lineStyle: {
                color: utils.getGrays()['200'],
              },
            },
            axisLabel: {
              show: true,
              color: utils.getGrays()['600'],
              formatter: value => value,
            },
            axisTick: { show: false },
            axisLine: { show: false },
          },
          series:series,
          grid: { right: '0px', left: '20px', bottom: '20px', top: '0px' },
        });
    
        echartSetOption(chart, userOptions, getDefaultOptions);
         
        
      },
      error: function(data){
        console.log("error")
      }
    });
    const tooltipFormatter = params => {
      // console.log(params);
      var output= `
      <div>
        <p class='mb-2 text-600'>${params[0].name}</p>
        <div class='ms-1'>`;
      for(var i=0;i<deps.length;i++){
        output=output+` <h6 class="fs--1 text-700"><span class="fas fa-circle text-primary me-2"></span>${
          deps[i]
        }: ${
          params[i].value
        }</h6>`;
      }
      output=output+ `</div>;
      </div>`;
      return output;
    };
    // const getDefaultOptions = () => ({
    //   color: [
    //     utils.getColor('primary'),
    //     utils.getColor('success'),
    //     utils.getColor('info'),
    //   ],
    //   tooltip: {
    //     trigger: 'axis',
    //     padding: [7, 10],
    //     backgroundColor: utils.getGrays()['100'],
    //     borderColor: utils.getGrays()['300'],
    //     textStyle: { color: utils.getColors().dark },
    //     borderWidth: 1,
    //     transitionDuration: 0,
    //     position(pos, params, dom, rect, size) {
    //       return getPosition(pos, params, dom, rect, size);
    //     },
    //     formatter: tooltipFormatter,
    //   },
    //   xAxis: {
    //     type: 'category',
    //     data: ["All","PreReg","Collective","Tech","Interview","Trial"],
    //     boundaryGap: false,
    //     silent: true,
    //     axisPointer: {
    //       lineStyle: {
    //         color: utils.getGrays()['300'],
    //       },
    //     },
    //     splitLine: { show: false },
    //     axisLine: {
    //       lineStyle: {
    //         color: utils.getGrays()['300'],
    //       },
    //     },
    //     axisTick: {
    //       show: true,
    //       length: 20,
    //       lineStyle: {
    //         color: utils.getGrays()['200'],
    //       },

    //       interval: 5,
    //     },
    //     axisLabel: {
    //       color: utils.getGrays()['600'],
    //       formatter: value => value,
    //       align: 'left',
    //       fontSize: 11,
    //       padding: [0, 0, 0, 5],
    //       interval: 1,
    //     },
    //   },
    //   yAxis: {
    //     type: 'value',
    //     position: 'right',
    //     axisPointer: { show: false },
    //     splitLine: {
    //       lineStyle: {
    //         color: utils.getGrays()['200'],
    //       },
    //     },
    //     axisLabel: {
    //       show: true,
    //       color: utils.getGrays()['600'],
    //       formatter: value => `${Math.round((value / 1000) * 10) / 10}k`,
    //     },
    //     axisTick: { show: false },
    //     axisLine: { show: false },
    //   },
    //   series: [
    //     {
    //       type: 'line',
    //       data: [
    //         4164, 5486, 6146,
    //         8411, 9273, 9430,
    //       ],
    //       showSymbol: false,
    //       symbol: 'circle',
    //       itemStyle: {
    //         borderColor: utils.getColors().primary,
    //         borderWidth: 2,
    //       },
    //       lineStyle: {
    //         color: utils.getColor('primary'),
    //       },
    //       symbolSize: 2,
    //     },
    //     {
    //       type: 'line',
    //       data: [
    //         2164, 3688,
    //         3840, 5267,
    //         5566, 6000,
    //       ],
    //       showSymbol: false,
    //       symbol: 'circle',
    //       itemStyle: {
    //         borderColor: utils.getColors().success,
    //         borderWidth: 2,
    //       },
    //       lineStyle: {
    //         color: utils.getColor('success'),
    //       },
    //       symbolSize: 2,
    //     },
    //     {
    //       type: 'line',
    //       data: [
    //         1069,  1346,
    //         1395,  2179,
    //         2264,  2480,
    //       ],
    //       showSymbol: false,
    //       symbol: 'circle',
    //       itemStyle: {
    //         borderColor: utils.getColors().info,
    //         borderWidth: 2,
    //       },
    //       lineStyle: {
    //         color: utils.getColor('info'),
    //       },
    //       symbolSize: 2,
    //     },
    //   ],
    //   grid: { right: '30px', left: '5px', bottom: '20px', top: '20px' },
    // });

    // echartSetOption(chart, userOptions, getDefaultOptions);
  }
};

export default activeUsersChartReportInit;
