@php
   $configData = Helper::appClasses();
   $user = auth()->user();
   $can = fn (string $menu, string $action = 'read') => $user?->hasPermission($menu, $action);

   $badgeBooking = [
      'pending' => 'warning', 'confirmed' => 'info', 'active' => 'primary',
      'completed' => 'success', 'cancelled' => 'secondary', 'rejected' => 'danger', 'expired' => 'dark',
   ];
   $badgeKamar = ['available' => 'success', 'reserved' => 'warning', 'occupied' => 'info', 'maintenance' => 'danger'];
   $rupiah = fn ($v) => 'Rp'.number_format((int) $v, 0, ',', '.');
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Dashboard KostKu')

@section('vendor-style')
   @vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection

@section('page-style')
   @vite(['resources/assets/vendor/scss/pages/cards-statistics.scss'])
@endsection

@section('vendor-script')
   @vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('page-script')
   @vite(['resources/assets/js/dashboards-analytics.js'])
@endsection

@section('content')
   <div class="row g-6">
      <!-- Welcome Card -->
      <div class="col-12">
         <div class="card">
            <div class="d-flex align-items-end row">
               <div class="col-md-8 order-2 order-md-1">
                  <div class="card-body">
                     <h4 class="card-title mb-3">Selamat Datang, <span class="fw-bold">{{ $user?->name }}</span> 👋</h4>
                     <p class="mb-4">Kelola seluruh operasional KostKu mulai dari kost, kamar, booking, pengguna, hingga laporan.</p>
                     <div class="d-flex gap-2 flex-wrap">
                        @if ($can('kost.index'))
                           <a href="{{ route('kost.index') }}" class="btn btn-primary"><i class="ri-building-2-line me-1"></i>Kelola Kost</a>
                        @endif
                        @if ($can('booking.index'))
                           <a href="{{ route('booking.index') }}" class="btn btn-label-primary"><i class="ri-calendar-check-line me-1"></i>Kelola Booking</a>
                        @endif
                        @if ($can('laporan.booking'))
                           <a href="{{ route('laporan.booking.index') }}" class="btn btn-label-secondary"><i class="ri-file-chart-line me-1"></i>Laporan</a>
                        @endif
                     </div>
                  </div>
               </div>
               <div class="col-md-4 text-center text-md-end order-1 order-md-2">
                  <div class="card-body pb-0 px-0 pt-2 h-100 d-flex align-items-end justify-content-center">
                     <img src="{{ asset('assets/img/illustrations/illustration-john-' . $configData['style'] . '.png') }}"
                        height="150" class="scaleX-n1-rtl" alt="Dashboard"
                        data-app-light-img="illustrations/illustration-john-light.png"
                        data-app-dark-img="illustrations/illustration-john-dark.png">
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!--/ Welcome Card -->

      <!-- KPI: Total Kost -->
      <div class="col-12 col-sm-6 col-lg-3">
         <div class="card h-100">
            <div class="card-body">
               <div class="avatar">
                  <div class="avatar-initial bg-label-primary rounded-3"><i class="ri-building-2-line ri-24px"></i></div>
               </div>
               <div class="card-info mt-4">
                  <h5 class="mb-1">{{ number_format($overview['kost']['total']) }}</h5>
                  <p class="mb-0">Total Kost</p>
               </div>
            </div>
         </div>
      </div>

      <!-- KPI: Total Kamar -->
      <div class="col-12 col-sm-6 col-lg-3">
         <div class="card h-100">
            <div class="card-body">
               <div class="avatar">
                  <div class="avatar-initial bg-label-dark rounded-3"><i class="ri-door-open-line ri-24px"></i></div>
               </div>
               <div class="card-info mt-4">
                  <h5 class="mb-1">{{ number_format($overview['kamar']['total']) }}</h5>
                  <p class="mb-0">Total Kamar</p>
               </div>
            </div>
         </div>
      </div>

      <!-- KPI: Kamar Tersedia -->
      <div class="col-12 col-sm-6 col-lg-3">
         <div class="card h-100">
            <div class="card-body">
               <div class="avatar">
                  <div class="avatar-initial bg-label-success rounded-3"><i class="ri-checkbox-circle-line ri-24px"></i></div>
               </div>
               <div class="card-info mt-4">
                  <h5 class="mb-1">{{ number_format($overview['kamar']['tersedia']) }}</h5>
                  <p class="mb-0">Kamar Tersedia</p>
               </div>
            </div>
         </div>
      </div>

      <!-- KPI: Kamar Terisi -->
      <div class="col-12 col-sm-6 col-lg-3">
         <div class="card h-100">
            <div class="card-body">
               <div class="avatar">
                  <div class="avatar-initial bg-label-info rounded-3"><i class="ri-hotel-bed-line ri-24px"></i></div>
               </div>
               <div class="card-info mt-4">
                  <h5 class="mb-1">{{ number_format($overview['kamar']['terisi']) }}</h5>
                  <p class="mb-0">Kamar Terisi</p>
               </div>
            </div>
         </div>
      </div>

      <!-- KPI: Booking Pending -->
      <div class="col-12 col-sm-6 col-lg-3">
         <div class="card h-100">
            <div class="card-body">
               <div class="d-flex justify-content-between align-items-start">
                  <div class="avatar">
                     <div class="avatar-initial bg-label-warning rounded-3"><i class="ri-time-line ri-24px"></i></div>
                  </div>
                  @if ($overview['perluTindakan']['mendekati_expiry'] > 0)
                     <span class="badge bg-label-danger rounded-pill" title="Mendekati batas waktu pembayaran">+{{ $overview['perluTindakan']['mendekati_expiry'] }} mendekati expiry</span>
                  @endif
               </div>
               <div class="card-info mt-4">
                  <h5 class="mb-1">{{ number_format($overview['booking']['pending']) }}</h5>
                  <p class="mb-0">Booking Pending</p>
               </div>
            </div>
         </div>
      </div>

      <!-- KPI: Booking Aktif -->
      <div class="col-12 col-sm-6 col-lg-3">
         <div class="card h-100">
            <div class="card-body">
               <div class="avatar">
                  <div class="avatar-initial bg-label-primary rounded-3"><i class="ri-user-follow-line ri-24px"></i></div>
               </div>
               <div class="card-info mt-4">
                  <h5 class="mb-1">{{ number_format($overview['booking']['active']) }}</h5>
                  <p class="mb-0">Booking Aktif</p>
               </div>
            </div>
         </div>
      </div>

      <!-- KPI: Total Penyewa -->
      <div class="col-12 col-sm-6 col-lg-3">
         <div class="card h-100">
            <div class="card-body">
               <div class="avatar">
                  <div class="avatar-initial bg-label-secondary rounded-3"><i class="ri-group-line ri-24px"></i></div>
               </div>
               <div class="card-info mt-4">
                  <h5 class="mb-1">{{ number_format($overview['pengguna']['penyewa']) }}</h5>
                  <p class="mb-0">Total Penyewa</p>
               </div>
            </div>
         </div>
      </div>

      <!-- KPI: Pendapatan Bulan Ini -->
      <div class="col-12 col-sm-6 col-lg-3">
         <div class="card h-100">
            <div class="card-body">
               <div class="avatar">
                  <div class="avatar-initial bg-label-success rounded-3"><i class="ri-money-dollar-circle-line ri-24px"></i></div>
               </div>
               <div class="card-info mt-4">
                  <h5 class="mb-1">{{ $rupiah($overview['pendapatan']['bulan_ini']) }}</h5>
                  <p class="mb-0">Pendapatan Bulan Ini</p>
               </div>
            </div>
         </div>
      </div>

      <!-- Status Kamar (Donut) + Statistik Booking (Bar) -->
      <div class="col-lg-4 col-12">
         <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
               <h5 class="mb-0">Status Kamar</h5>
               <span class="badge bg-label-secondary">{{ number_format($overview['kamar']['total']) }} kamar</span>
            </div>
            <div class="card-body">
               @if ($overview['kamar']['total'] > 0)
                  <div id="kamarStatusChart"></div>
                  <ul class="list-unstyled d-flex flex-wrap gap-3 justify-content-center mt-3 mb-0 small">
                     @foreach (['available' => 'success', 'reserved' => 'warning', 'occupied' => 'info', 'maintenance' => 'danger'] as $status => $warna)
                        <li class="d-flex align-items-center gap-1">
                           <span class="badge bg-label-{{ $warna }} p-1">&nbsp;</span>
                           {{ ucfirst($status) }}: <strong>{{ number_format($overview['kamar']['per_status'][$status] ?? 0) }}</strong>
                        </li>
                     @endforeach
                  </ul>
               @else
                  <div class="text-center py-5 text-muted">
                     <i class="ri-door-closed-line ri-48px mb-2 d-block"></i>
                     Belum ada kamar terdaftar.
                  </div>
               @endif
            </div>
         </div>
      </div>

      <div class="col-lg-8 col-12">
         <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
               <h5 class="mb-0">Statistik Booking</h5>
               <small class="text-muted">12 bulan terakhir</small>
            </div>
            <div class="card-body">
               @if ($overview['booking']['total'] > 0)
                  <div id="bookingChart"></div>
               @else
                  <div class="text-center py-5 text-muted">
                     <i class="ri-calendar-close-line ri-48px mb-2 d-block"></i>
                     Belum ada booking.
                  </div>
               @endif
            </div>
         </div>
      </div>

      <!-- Booking Terbaru -->
      <div class="col-12">
         <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
               <h5 class="mb-0">Booking Terbaru</h5>
               @if ($can('booking.index'))
                  <a href="{{ route('booking.index') }}" class="btn btn-sm btn-label-primary">Lihat Semua</a>
               @endif
            </div>
            <div class="table-responsive text-nowrap">
               <table class="table table-hover">
                  <thead>
                     <tr>
                        <th>Kode Booking</th>
                        <th>Penyewa / Guest</th>
                        <th>Kost</th>
                        <th>Kamar</th>
                        <th>Tipe</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        @if ($can('booking.index'))<th>Aksi</th>@endif
                     </tr>
                  </thead>
                  <tbody class="table-border-bottom-0">
                     @forelse ($overview['bookingTerbaru'] as $b)
                        <tr>
                           <td><strong>{{ $b->booking_code }}</strong></td>
                           <td>{{ $b->customerName() }}</td>
                           <td>{{ $b->kost->name ?? '-' }}</td>
                           <td>{{ $b->kamar->number ?? '-' }}</td>
                           <td>
                              <span class="badge bg-label-{{ $b->booking_type === 'guest' ? 'warning' : 'primary' }}">
                                 {{ strtoupper($b->booking_type) }}
                              </span>
                           </td>
                           <td>{{ $rupiah($b->total_amount) }}</td>
                           <td><span class="badge bg-label-{{ $badgeBooking[$b->status] ?? 'secondary' }}">{{ ucfirst($b->status) }}</span></td>
                           <td>{{ $b->created_at?->format('d M Y H:i') }}</td>
                           @if ($can('booking.index'))
                              <td>
                                 <a href="{{ route('booking.show', $b) }}" class="btn btn-sm btn-icon btn-outline-secondary" title="Detail"><i class="bx bx-show"></i></a>
                              </td>
                           @endif
                        </tr>
                     @empty
                        <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada booking.</td></tr>
                     @endforelse
                  </tbody>
               </table>
            </div>
         </div>
      </div>

      <!-- Perlu Tindakan -->
      <div class="col-md-6 col-12">
         <div class="card h-100">
            <div class="card-header">
               <h5 class="mb-0">Perlu Tindakan</h5>
            </div>
            <div class="card-body pt-4">
               <ul class="list-unstyled mb-0">
                  <li class="d-flex align-items-center mb-4">
                     <div class="avatar avatar-sm me-3">
                        <span class="avatar-initial rounded bg-label-warning"><i class="ri-time-line ri-18px"></i></span>
                     </div>
                     <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                        <div class="me-2">
                           <h6 class="mb-0">Menunggu Konfirmasi</h6>
                           <small class="text-muted">Booking pending perlu ditinjau</small>
                        </div>
                        <span class="badge bg-label-warning rounded-pill">{{ number_format($overview['perluTindakan']['pending']) }}</span>
                     </div>
                  </li>
                  <li class="d-flex align-items-center mb-4">
                     <div class="avatar avatar-sm me-3">
                        <span class="avatar-initial rounded bg-label-info"><i class="ri-login-circle-line ri-18px"></i></span>
                     </div>
                     <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                        <div class="me-2">
                           <h6 class="mb-0">Siap Check-in</h6>
                           <small class="text-muted">Booking confirmed menunggu aktivasi</small>
                        </div>
                        <span class="badge bg-label-info rounded-pill">{{ number_format($overview['perluTindakan']['menunggu_checkin']) }}</span>
                     </div>
                  </li>
                  <li class="d-flex align-items-center">
                     <div class="avatar avatar-sm me-3">
                        <span class="avatar-initial rounded bg-label-danger"><i class="ri-alarm-warning-line ri-18px"></i></span>
                     </div>
                     <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                        <div class="me-2">
                           <h6 class="mb-0">Mendekati Expiry</h6>
                           <small class="text-muted">Sisa waktu pembayaran kurang dari 4 jam</small>
                        </div>
                        <span class="badge bg-label-danger rounded-pill">{{ number_format($overview['perluTindakan']['mendekati_expiry']) }}</span>
                     </div>
                  </li>
               </ul>
               @if ($can('booking.index'))
                  <a href="{{ route('booking.index', ['status' => 'pending']) }}" class="btn btn-primary w-100 mt-2">Lihat Booking</a>
               @endif
            </div>
         </div>
      </div>

      <!-- Aktivitas Terbaru -->
      <div class="col-md-6 col-12">
         <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
               <h5 class="mb-0">Aktivitas Terbaru</h5>
               @if ($can('activity-log.index'))
                  <a href="{{ route('activity-log.index') }}" class="btn btn-sm btn-label-secondary">Log Lengkap</a>
               @endif
            </div>
            <div class="card-body pt-3">
               @forelse ($overview['aktivitas'] as $log)
                  <div class="d-flex align-items-start mb-3 {{ $loop->last ? '' : 'pb-3 border-bottom' }}">
                     <div class="avatar avatar-sm me-3 flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-{{ $log->action_color }}"><i class="ri-history-line ri-16px"></i></span>
                     </div>
                     <div class="flex-grow-1">
                        <p class="mb-0 small">
                           <strong>{{ $log->user->name ?? 'Sistem' }}</strong>
                           {{ strtolower($log->action_label) }}
                           @if ($log->description)<span class="text-muted">— {{ \Illuminate\Support\Str::limit($log->description, 60) }}</span>@endif
                        </p>
                        <small class="text-muted">{{ $log->created_at?->diffForHumans() }}</small>
                     </div>
                  </div>
               @empty
                  <div class="text-center py-4 text-muted">
                     <i class="ri-history-line ri-36px mb-2 d-block"></i>
                     Belum ada aktivitas.
                  </div>
               @endforelse
            </div>
         </div>
      </div>

      <!-- Quick Actions -->
      <div class="col-12">
         <div class="card">
            <div class="card-header">
               <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body pt-4">
               <div class="row g-3">
                  @if ($can('kost.index', 'create'))
                     <div class="col-12 col-sm-6 col-lg-3">
                        <a href="{{ route('kost.create') }}" class="d-flex align-items-center p-3 border rounded-2 h-100 text-decoration-none">
                           <span class="avatar avatar-sm me-3"><span class="avatar-initial rounded bg-label-primary"><i class="ri-add-line ri-18px"></i></span></span>
                           <span class="text-body">Tambah Kost</span>
                        </a>
                     </div>
                  @endif
                  @if ($can('kamar.index'))
                     <div class="col-12 col-sm-6 col-lg-3">
                        <a href="{{ route('kamar.index') }}" class="d-flex align-items-center p-3 border rounded-2 h-100 text-decoration-none">
                           <span class="avatar avatar-sm me-3"><span class="avatar-initial rounded bg-label-info"><i class="ri-door-open-line ri-18px"></i></span></span>
                           <span class="text-body">Kelola Kamar</span>
                        </a>
                     </div>
                  @endif
                  @if ($can('payment.index'))
                     <div class="col-12 col-sm-6 col-lg-3">
                        <a href="{{ route('payment.index') }}" class="d-flex align-items-center p-3 border rounded-2 h-100 text-decoration-none">
                           <span class="avatar avatar-sm me-3"><span class="avatar-initial rounded bg-label-success"><i class="ri-wallet-3-line ri-18px"></i></span></span>
                           <span class="text-body">Kelola Pembayaran</span>
                        </a>
                     </div>
                  @endif
                  @if ($can('laporan.pendapatan'))
                     <div class="col-12 col-sm-6 col-lg-3">
                        <a href="{{ route('laporan.pendapatan.index') }}" class="d-flex align-items-center p-3 border rounded-2 h-100 text-decoration-none">
                           <span class="avatar avatar-sm me-3"><span class="avatar-initial rounded bg-label-warning"><i class="ri-line-chart-line ri-18px"></i></span></span>
                           <span class="text-body">Laporan Pendapatan</span>
                        </a>
                     </div>
                  @endif
                  @if ($can('user.index'))
                     <div class="col-12 col-sm-6 col-lg-3">
                        <a href="{{ route('user.index') }}" class="d-flex align-items-center p-3 border rounded-2 h-100 text-decoration-none">
                           <span class="avatar avatar-sm me-3"><span class="avatar-initial rounded bg-label-secondary"><i class="ri-group-line ri-18px"></i></span></span>
                           <span class="text-body">Data Pengguna</span>
                        </a>
                     </div>
                  @endif
               </div>
            </div>
         </div>
      </div>
   </div>

   {{-- Data chart dikirim aman ke JS via JSON script tag --}}
   <script type="application/json" id="kostku-dashboard-data">{!! json_encode($chartData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
@endsection
