import Chart from 'chart.js/auto';

// Make Chart available globally
window.Chart = Chart;

// Debug log to confirm Chart.js is loaded
console.log('Chart.js loaded:', typeof Chart !== 'undefined');
