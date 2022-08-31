import utils from '../../../utils';
import { echartSetOption } from '../echarts-utils';

/* -------------------------------------------------------------------------- */
/*                          Echarts Gauge Progress Chart                      */
/* -------------------------------------------------------------------------- */

const echartsGaugeMultiRingChartInit = () => {
  const $gaugeMultiRingChartEl = document.querySelector('.echart-gauge-multi-ring-chart-example');

  if ($gaugeMultiRingChartEl) {
    // Get options from data attribute
    const userOptions = utils.getData($gaugeMultiRingChartEl, 'options');
    const chart = window.echarts.init($gaugeMultiRingChartEl);

    const tooltipFormatter = params => {
      return `
      <div>
          <h6 class="fs--1 text-700 mb-0">
            <span class="fas fa-circle me-1" style='color:${params[0].color}'></span>
            ${params[0].name} : ${params[0].value}
          </h6>
      </div>
      `;
    };

    const getDefaultOptions = () => ({
      tooltip: {
        trigger: 'axis',
        padding: [7, 10],
        backgroundColor: utils.getGrays()['100'],
        borderColor: utils.getGrays()['300'],
        textStyle: { color: utils.getColors().dark },
        borderWidth: 1,
        formatter: tooltipFormatter,
        transitionDuration: 0,
        axisPointer: {
          type: 'none'
        }
      },
      series: [
        {
          type: 'gauge',
          radius: '100%',
          startAngle: 90,
          endAngle: -270,
          pointer: {
            show: false
          },
          progress: {
            show: true,
            overlap: false,
            roundCap: true,
            clip: false,
            itemStyle: {
              borderWidth: 1,
              borderColor: utils.getGrays()['500']
            }
          },
          axisLine: {
            lineStyle: {
              width: 40
            }
          },
          splitLine: {
            show: false,
            distance: 0,
            length: 10
          },
          axisTick: {
            show: false
          },
          axisLabel: {
            show: false,
            distance: 50
          },
          data: [
            {
              value: 60,
              name: 'Perfect',
              title: {
                offsetCenter: ['0%', '-50%']
              },
              detail: {
                offsetCenter: ['0%', '-35%']
              },
              itemStyle: {
                color: utils.getColor('primary')
              }
            },
            {
              value: 40,
              name: 'Good',
              title: {
                offsetCenter: ['0%', '-10%']
              },
              detail: {
                offsetCenter: ['0%', '5%']
              },
              itemStyle: {
                color: utils.getColor('success')
              }
            },
            {
              value: 20,
              name: 'Commonly',
              title: {
                offsetCenter: ['0%', '30%']
              },
              detail: {
                offsetCenter: ['0%', '45%']
              },
              itemStyle: {
                color: utils.getColor('warning')
              }
            }
          ],
          title: {
            fontSize: 14,
            color: utils.getGrays()['600']
          },
          detail: {
            width: 50,
            height: 14,
            fontSize: 14,
            color: 'auto',
            borderColor: 'auto',
            borderRadius: 20,
            borderWidth: 1,
            formatter: '{value}%'
          }
        }
      ]
    });

    echartSetOption(chart, userOptions, getDefaultOptions);
  }
};

export default echartsGaugeMultiRingChartInit;
