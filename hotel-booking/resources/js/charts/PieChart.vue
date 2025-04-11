<script setup>
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  ArcElement,
} from 'chart.js';
import { Pie } from 'vue-chartjs';
import { reactive } from 'vue';

// Register required Chart.js modules
ChartJS.register(Title, Tooltip, Legend, ArcElement);

// Props
const props = defineProps({
  chartData: Object,
});

// Chart options (reactive object)
const chartOptions = reactive({
  responsive: true,
  maintainAspectRatio: false,
});
</script>

<template>
  <Pie :data="props.chartData" :options="chartOptions" />
</template>
