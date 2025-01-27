@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">

    <h1 class="text-center mb-4">Dashboard Overview</h1>


    <div class="row">
         <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header text-center">
                    Picking Jobs Overview
                </div>
                <div class="card-body">
                    <canvas id="salesOverview"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header text-center">
                    Sales Orders Growth
                </div>
                <div class="card-body">
                    <canvas id="userGrowth"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
       <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header text-center">
                    Stock Movements
                </div>
                <div class="card-body">
                    <canvas id="productPerformance"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header text-center">
                    Inventory Status
                </div>
                <div class="card-body" style="max-height: 273px; margin:auto;">
                    <canvas id="inventoryStatus"></canvas>
                </div>
            </div>
        </div>

    </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const salesOverviewCtx = document.getElementById('salesOverview').getContext('2d');
    const salesOverviewChart = new Chart(salesOverviewCtx, {
        type: 'line',
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            datasets: [{
                label: 'Picking Jobs',
                data: [120, 190, 300, 500, 200, 300, 450],
                borderColor: '#3498db',
                backgroundColor: 'rgba(52, 152, 219, 0.2)',
                fill: true,
            }]
        }
    });

    const userGrowthCtx = document.getElementById('userGrowth').getContext('2d');
    const userGrowthChart = new Chart(userGrowthCtx, {
        type: 'bar',
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            datasets: [{
                label: 'Sales Orders',
                data: [30, 50, 80, 120, 150, 200, 250],
                backgroundColor: '#e74c3c'
            }]
        }
    });



    const productPerformanceCtx = document.getElementById('productPerformance').getContext('2d');
    const productPerformanceChart = new Chart(productPerformanceCtx, {
        type: 'bar',
        data: {
            labels: ['Location A', 'Location B', 'Location C', 'Location D'],
            datasets: [{
                label: 'Location',
                data: [120, 200, 300, 400],
                backgroundColor: '#8e44ad'
            }]
        }
    });



    const inventoryStatusCtx = document.getElementById('inventoryStatus').getContext('2d');
    const inventoryStatusChart = new Chart(inventoryStatusCtx, {
        type: 'doughnut',
        data: {
            labels: ['In Stock', 'Out of Stock', 'Reserved'],
            datasets: [{
                data: [500, 150, 100],
                backgroundColor: ['#2ecc71', '#e74c3c', '#f1c40f']
            }]
        }
    });
</script>
@endsection
