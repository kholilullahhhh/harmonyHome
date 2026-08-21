'use strict';

(function () {
  let labelColor, headingColor, borderColor;

  if (isDarkStyle) {
    labelColor = config.colors_dark.textMuted;
    headingColor = config.colors_dark.headingColor;
    borderColor = config.colors_dark.borderColor;
  } else {
    labelColor = config.colors.textMuted;
    headingColor = config.colors.headingColor;
    borderColor = config.colors.borderColor;
  }

  // Data dari Laravel (script tag application/json — aman dari XSS)
  const dataEl = document.getElementById('kostku-dashboard-data');

  if (!dataEl) {
    return;
  }

  let dashboardData;

  try {
    dashboardData = JSON.parse(dataEl.textContent);
  } catch (e) {
    console.error('Dashboard data tidak valid', e);

    return;
  }

  const fontFamily = 'Inter';

  // Status Kamar Donut Chart
  // --------------------------------------------------------------------
  const kamarChartEl = document.querySelector('#kamarStatusChart');

  if (kamarChartEl && Array.isArray(dashboardData.kamar)) {
    const kamarSeries = dashboardData.kamar.series || [];
    const hasKamarData = kamarSeries.some(v => Number(v) > 0);
    const kamarConfig = {
      chart: {
        type: 'donut',
        height: 260,
        parentHeightOffset: 0,
        toolbar: { show: false },
        fontFamily
      },
      series: hasKamarData ? kamarSeries : [1],
      labels: dashboardData.kamar.labels || [],
      colors: [
        config.colors.success,
        config.colors.warning,
        config.colors.info,
        config.colors.danger
      ],
      dataLabels: { enabled: false },
      legend: { show: false },
      stroke: {
        width: 2,
        colors: [cardColorFallback()]
      },
      plotOptions: {
        pie: {
          donut: {
            size: '72%',
            labels: {
              show: true,
              name: {
                color: labelColor,
                fontFamily
              },
              value: {
                color: headingColor,
                fontSize: '1.5rem',
                fontWeight: '600',
                fontFamily,
                formatter: val => Math.round(Number(val))
              },
              total: {
                show: true,
                showAlways: true,
                label: hasKamarData ? 'Total Kamar' : 'Belum ada data',
                fontSize: '0.8125rem',
                color: labelColor,
                fontFamily,
                formatter: w => {
                  if (!hasKamarData) return '';
                  return w.globals.seriesTotals.reduce((a, b) => a + Number(b), 0).toString();
                }
              }
            }
          }
        }
      },
      tooltip: {
        y: { formatter: val => Math.round(Number(val)) + ' kamar' }
      }
    };

    new ApexCharts(kamarChartEl, kamarConfig).render();
  }

  // Statistik Booking Bar Chart (per bulan)
  // --------------------------------------------------------------------
  const bookingChartEl = document.querySelector('#bookingChart');

  if (bookingChartEl && dashboardData.booking) {
    const booking = dashboardData.booking;
    const bookingConfig = {
      chart: {
        type: 'bar',
        height: 300,
        stacked: false,
        parentHeightOffset: 0,
        toolbar: { show: false },
        fontFamily
      },
      series: [
        { name: 'Total Booking', data: booking.total || [] },
        { name: 'Completed', data: booking.completed || [] },
        { name: 'Cancelled', data: booking.cancelled || [] }
      ],
      colors: [config.colors.primary, config.colors.success, config.colors.danger],
      plotOptions: {
        bar: {
          borderRadius: 6,
          columnWidth: '45%',
          endingShape: 'rounded'
        }
      },
      dataLabels: { enabled: false },
      legend: {
        show: true,
        position: 'top',
        horizontalAlign: 'left',
        markers: { offsetX: -4 },
        labels: { colors: labelColor },
        fontFamily
      },
      grid: {
        borderColor,
        padding: { top: -10, bottom: -15 }
      },
      states: {
        hover: { filter: { type: 'none' } },
        active: { filter: { type: 'none' } }
      },
      xaxis: {
        categories: booking.labels || [],
        axisTicks: { show: false },
        axisBorder: { show: false },
        labels: {
          style: { colors: labelColor, fontSize: '13px', fontFamily },
          rotate: -45,
          rotateAlways: (booking.labels || []).length > 8,
          hideOverlappingLabels: true
        }
      },
      yaxis: {
        labels: {
          style: { colors: labelColor, fontFamily },
          formatter: val => Math.round(Number(val)).toString()
        }
      },
      tooltip: {
        y: { formatter: val => Math.round(Number(val)).toString() }
      }
    };

    new ApexCharts(bookingChartEl, bookingConfig).render();
  }

  function cardColorFallback() {
    return isDarkStyle ? config.colors_dark.cardColor : config.colors.cardColor;
  }
})();
