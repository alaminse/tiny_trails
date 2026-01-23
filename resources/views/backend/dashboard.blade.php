@extends('backend.app')
@section('title', 'Dashboard')
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css"
        integrity="sha256-4MX+61mt9NVvvuPjUWdUdyfZfxSB1/Rf9WtqRHgG5S0=" crossorigin="anonymous" />
@endsection
@section('content')
    @include('backend.includes.header', ['mainTitle' => 'Dashboard', 'subTitle' => null])

    <div class="app-content">
        <div class="container-fluid">
            <!-- Info boxes -->
            <div class="row">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon text-bg-primary shadow-sm">
                            <i class="bi bi-people-fill"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Parents</span>
                            <span class="info-box-number">{{ $totalParents }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon text-bg-success shadow-sm">
                            <i class="bi bi-person-check-fill"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Active Parents</span>
                            <span class="info-box-number">{{ $activeParents }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon text-bg-warning shadow-sm">
                            <i class="bi bi-person-hearts"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Children</span>
                            <span class="info-box-number">{{ $totalChildren }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon text-bg-danger shadow-sm">
                            <i class="bi bi-person-plus-fill"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">New (30 Days)</span>
                            <span class="info-box-number">{{ $newRegistrations }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title">Monthly Parent Registrations</h5>
                        </div>
                        <div class="card-body">
                            <div id="registrations-chart"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title">Gender Distribution</h5>
                        </div>
                        <div class="card-body">
                            <div id="gender-chart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Country Stats & Recent Parents -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title">Top 5 Countries</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Country</th>
                                        <th class="text-end">Parents</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($countryStats as $stat)
                                        <tr>
                                            <td>{{ $stat->country }}</td>
                                            <td class="text-end">
                                                <span class="badge bg-primary">{{ $stat->count }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title">Recent Registrations</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Location</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentParents as $parent)
                                        <tr>
                                            <td>{{ $parent->first_name }} {{ $parent->last_name }}</td>
                                            <td>{{ $parent->email }}</td>
                                            <td>{{ $parent->city->name ?? 'N/A' }}</td>
                                            <td>{{ $parent->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js"
            integrity="sha256-+vh8GkaU7C9/wbSLIcwq82tQ2wTf44aOHA8HlBMwRI8=" crossorigin="anonymous"></script>
        <script>
            // Monthly Registrations Chart
            const registrationsData = @json($monthlyRegistrations);
            const registrations_chart_options = {
                series: [{
                    name: 'Registrations',
                    data: registrationsData.map(item => item.count)
                }],
                chart: {
                    height: 300,
                    type: 'area',
                    toolbar: {
                        show: false,
                    },
                },
                colors: ['#0d6efd'],
                dataLabels: {
                    enabled: false,
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.3,
                    }
                },
                xaxis: {
                    categories: registrationsData.map(item => item.month),
                    labels: {
                        formatter: function(value) {
                            const date = new Date(value + '-01');
                            return date.toLocaleDateString('en-US', {
                                month: 'short',
                                year: 'numeric'
                            });
                        }
                    }
                },
                tooltip: {
                    x: {
                        format: 'MMMM yyyy',
                    },
                },
            };

            const registrations_chart = new ApexCharts(
                document.querySelector('#registrations-chart'),
                registrations_chart_options,
            );
            registrations_chart.render();

            // Gender Distribution Chart
            const genderData = @json($genderStats);
            const gender_chart_options = {
                series: genderData.map(item => item.count),
                chart: {
                    type: 'donut',
                    height: 300
                },
                labels: genderData.map(item => item.gender.charAt(0).toUpperCase() + item.gender.slice(1)),
                colors: ['#0d6efd', '#d63384', '#6f42c1'],
                legend: {
                    position: 'bottom'
                },
                dataLabels: {
                    enabled: true,
                },
            };

            const gender_chart = new ApexCharts(
                document.querySelector('#gender-chart'),
                gender_chart_options
            );
            gender_chart.render();
        </script>
    @endpush
@endsection