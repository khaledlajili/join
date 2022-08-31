import utils from '../../utils';
import { echartSetOption } from './echarts-utils';
var echarts = require('echarts');
import $ from 'jquery';


/* -------------------------------------------------------------------------- */
/*                            Bandwidth Saved                                 */
/* -------------------------------------------------------------------------- */

const bandwidthSavedInit = () => {
  const $echartsBandwidthSaved = document.querySelector('.echart-bandwidth-saved');
  var value;
  if ($echartsBandwidthSaved) {
    const userOptions = utils.getData($echartsBandwidthSaved, 'options');
    const chart = echarts.init($echartsBandwidthSaved);

    $.ajax({
      method:"post",
      data:"quotaReussi",
      url:"/admin/statistics/data/"+ $echartsBandwidthSaved.id,
      success: function(data){
        value= data["dataset"];
        const getDefaultOptions = () => ({
          series: [
            {
              type: 'gauge',
              startAngle: 90,
              endAngle: -270,
              radius: '90%',
              pointer: {
                show: false
              },
              progress: {
                show: true,
                overlap: false,
                roundCap: true,
                clip: false,
                itemStyle: {
                  color: {
                    type: 'linear',
                    x: 0,
                    y: 0,
                    x2: 1,
                    y2: 0,
                    colorStops: [
                      {
                        offset: 0,
                        color: '#1970e2'
                      },
                      {
                        offset: 1,
                        color: '#4695ff'
                      }
                    ]
                  }
                }
              },
              axisLine: {
                lineStyle: {
                  width: 8,
                  color: [[1, utils.getColor('200')]]
                }
              },
              splitLine: {
                show: false
              },
              axisTick: {
                show: false
              },
              axisLabel: {
                show: false
              },
              data: [
                {
                  value: value,
                  detail: {
                    offsetCenter: ['7%', '4%']
                  }
                }
              ],
              detail: {
                width: 50,
                height: 14,
                fontSize: 28,
                fontWeight: 500,
                fontFamily: 'poppins',
                color: utils.getColor('500'),
                formatter: '{value}%',
                valueAnimation: true
              },
              animationDuration: 3000
            }
          ]
        });
        echartSetOption(chart, userOptions, getDefaultOptions);

      },
      error:function(data){
        console.log("error");
      }
    });


  }
};

export default bandwidthSavedInit;
