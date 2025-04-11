<template>
    <div>
      <Line :data="chartData" :options="options" />
    </div>
  </template>
  
  <script>
  import { Line } from 'vue-chartjs';
  import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    LineElement,
    CategoryScale,
    LinearScale,
    PointElement
  } from 'chart.js';
  
  ChartJS.register(
    Title, Tooltip, Legend,
    LineElement, CategoryScale, LinearScale, PointElement
  );
  
  export default {
    name: 'LineChart',
    props: {
      chartData: Object,
    },
    components: { Line },
    data() {
      return {
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'top',
            },
          },
        },
      };
    },
  };
  </script>
  