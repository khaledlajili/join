import utils from '../../utils';
import { getPosition, echartSetOption, tooltipFormatter } from './echarts-utils';
import dayjs from 'dayjs';
var echarts = require('echarts');
import $ from 'jquery';

/* -------------------------------------------------------------------------- */
/*                                Traffic Channels                           */
/* -------------------------------------------------------------------------- */

var label;var fields;
//var label=["Math","Physique","Geo","Bio","Info","SVT","Prepa"]; 
const tooltipFormatterCustom = params => {
  let tooltipItem = ``;
  params.forEach(el => {
    tooltipItem =
      tooltipItem +
      `<div class='ms-1'> 
        <h6 class="text-700"><span class="fas fa-circle me-1 fs--2" style="color:${
          el.borderColor ? el.borderColor : el.color
        }"></span>
          ${el.seriesName} : ${typeof el.value === 'object' ? el.value[1] : el.value}
        </h6>
      </div>`;
  });
  return `<div>
            <p class='mb-2 text-600'>
              ${
                fields[params[0].axisValue]
              }
            </p>
            ${tooltipItem}
          </div>`;
};

const trafficChannelChartInit = () => {
  const $trafficChannels = document.querySelector('.echart-traffic-channels');

  if ($trafficChannels) {
    const userOptions = utils.getData($trafficChannels, 'options');
    const chart = echarts.init($trafficChannels);

    $.ajax({
      method:"post",
      data:"demandeDepartment",
      url:"/admin/statistics/data/"+ $trafficChannels.id,
      success: function(data){
       // console.log(data["dataset"],data["label"],data["fieldNames"]);
        label=data["label"];fields=data["fieldNames"]
        const getDefaultOptions = () => ({
          color: [
            utils.getColors().primary,
            utils.rgbaColor(utils.getColors().primary, 0.8),
            utils.rgbaColor(utils.getColors().primary, 0.6),
            utils.rgbaColor(utils.getColors().primary, 0.4),
            utils.rgbaColor(utils.getColors().primary, 0.2)
          ],
          legend: {
            data:label,
            left: 5,
            // bottom: 10,
            itemWidth: 10,
            itemHeight: 10,
            borderRadius: 0,
            icon: 'circle',
            inactiveColor: utils.getGrays()['400'],
            textStyle: { color: utils.getGrays()['700'] },
            itemGap: 20
          },
          xAxis: {
            type: 'category',
            data: data["xAxis"],
            axisLine: {
              show: false
            },
            splitLine: {
              lineStyle: {
                color: utils.getGrays()['200']
              }
            },
            axisTick: {
              show: false
            },
            axisLabel: {
              color: utils.getGrays()['600'],
              formatter: value => fields[value]
            }
          },
          yAxis: {
            type: 'value',
            position: 'right',
            splitLine: {
              lineStyle: {
                color: utils.getGrays()['200']
              }
            },
            axisLine: {
              show: false
            },
            axisTick: {
              show: false
            },
            axisLabel: {
              show: true,
              color: utils.getGrays()['600'],
              margin: 15
            }
          },
          tooltip: {
            trigger: 'axis',
            padding: [7, 10],
            axisPointer: {
              type: 'none'
            },
            backgroundColor: utils.getGrays()['100'],
            borderColor: utils.getGrays()['300'],
            textStyle: { color: utils.getColors().dark },
            borderWidth: 1,
            transitionDuration: 0,
            position(pos, params, dom, rect, size) {
              return getPosition(pos, params, dom, rect, size);
            },
            formatter: tooltipFormatterCustom
          },
    
          series:data["dataset"],
    
          grid: {
            right: '50px',
            left: '0px',
            bottom: '10%',
            top: '15%'
          }
        });
        echartSetOption(chart, userOptions, getDefaultOptions);

      },
      error: function(data){
        console.log("error");
      }
    });

    const tooltipFormatter = params => {
      let tooltipItem = ``
      params.forEach(el => {
        tooltipItem = tooltipItem +`<div class='ms-1'>
          <h6 class="fs--1 text-700"><span class="fas fa-circle me-2" style="color:${
            el.color}"></span>
            ${el.seriesName} : ${el.value}
          </h6>
        </div>`
      });
      return `<div>
                <p class='mb-2 text-600'>${window
                  .dayjs(params[0].axisValue)
                  .format('MMM DD, YYYY')}</p>
                ${tooltipItem}
              </div>`;
    };
    // const getDefaultOptions = () => ({
    //   color: [
    //     utils.getColors().primary,
    //     utils.rgbaColor(utils.getColors().primary, 0.8),
    //     utils.rgbaColor(utils.getColors().primary, 0.6),
    //     utils.rgbaColor(utils.getColors().primary, 0.4),
    //     utils.rgbaColor(utils.getColors().primary, 0.2)
    //   ],
    //   legend: {
    //     data: ['Pole projet', 'Pole marketing', 'Pole dev co'],
    //     left: 5,
    //     // bottom: 10,
    //     itemWidth: 10,
    //     itemHeight: 10,
    //     borderRadius: 0,
    //     icon: 'circle',
    //     inactiveColor: utils.getGrays()['400'],
    //     textStyle: { color: utils.getGrays()['700'] },
    //     itemGap: 20
    //   },
    //   xAxis: {
    //     type: 'category',
    //     data: [0,1,2,3,4,5,6],
    //     axisLine: {
    //       show: false
    //     },
    //     splitLine: {
    //       lineStyle: {
    //         color: utils.getGrays()['200']
    //       }
    //     },
    //     axisTick: {
    //       show: false
    //     },
    //     axisLabel: {
    //       color: utils.getGrays()['600'],
    //       formatter: value => label[value]
    //     }
    //   },
    //   yAxis: {
    //     type: 'value',
    //     position: 'right',
    //     splitLine: {
    //       lineStyle: {
    //         color: utils.getGrays()['200']
    //       }
    //     },
    //     axisLine: {
    //       show: false
    //     },
    //     axisTick: {
    //       show: false
    //     },
    //     axisLabel: {
    //       show: true,
    //       color: utils.getGrays()['600'],
    //       margin: 15
    //     }
    //   },
    //   tooltip: {
    //     trigger: 'axis',
    //     padding: [7, 10],
    //     axisPointer: {
    //       type: 'none'
    //     },
    //     backgroundColor: utils.getGrays()['100'],
    //     borderColor: utils.getGrays()['300'],
    //     textStyle: { color: utils.getColors().dark },
    //     borderWidth: 1,
    //     transitionDuration: 0,
    //     position(pos, params, dom, rect, size) {
    //       return getPosition(pos, params, dom, rect, size);
    //     },
    //     formatter: tooltipFormatterCustom
    //   },

    //   series: [
    //     {
    //       name: 'Pole projet',
    //       type: 'bar',
    //       stack: 'total',
    //       data: [320, 302, 301, 334, 390, 330, 320]
    //     },
    //     {
    //       name: 'Pole marketing',
    //       type: 'bar',
    //       stack: 'total',
    //       data: [120, 132, 101, 134, 90, 230, 210]
    //     },
    //     {
    //       name: 'Pole dev co',
    //       type: 'bar',
    //       stack: 'total',
    //       data: [220, 182, 191, 234, 290, 330, 310]
    //     }
    //   ],

    //   grid: {
    //     right: '50px',
    //     left: '0px',
    //     bottom: '10%',
    //     top: '15%'
    //   }
    // });

    // echartSetOption(chart, userOptions, getDefaultOptions);
  }
};

export default trafficChannelChartInit;
