import utils from '../../utils';
import { echartSetOption } from './echarts-utils';
var echarts = require('echarts');
import $ from 'jquery';


/* -------------------------------------------------------------------------- */
/*                            Bandwidth Saved                                 */
/* -------------------------------------------------------------------------- */

const FeedbackStatInit = () => {

  const $echartSatisfaction = document.querySelector('.echart-satisfaction');

  var value;

  if ($echartSatisfaction) {

    const userOptions = utils.getData($echartSatisfaction, 'options');
    
    const chart = echarts.init($echartSatisfaction);
    $.ajax({
      method:"post",
      data:"statSatisfaction",
      url:"/admin/statistics/data/"+ $echartSatisfaction.id,
      success: function(data){
        var dataset=data['dataset'];
        value= dataset['satisfaction'];
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
        const selectMenu = document.querySelector(
          "[data-target='.echart-satisfaction-type']"
        );
    
        if (selectMenu) {
          selectMenu.addEventListener('change', e => {
            const value = e.currentTarget.value;
            chart.setOption({
              series: [{ data: [
                {
                  value: dataset[value],
                  detail: {
                    offsetCenter: ['7%', '4%']
                  }
                }
              ] }],
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

export default FeedbackStatInit;
