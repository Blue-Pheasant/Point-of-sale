<script type="text/javascript">
  document.title = 'Bảng điều khiển';
</script> 

<div class="main_notification">
<div class="row">
  <div class="col-md-3">
    <div class="card card-stats card-warning">
      <div class="card-body ">
        <div class="row">
          <div class="col-5">
            <div class="icon-big text-center">
              <i class="fa fa-users"></i>
            </div>
          </div>
          <div class="col-7 d-flex align-items-center">
            <div class="numbers">
              <a href="/admin/users">
                <p class="card-category">Thành viên</p>
              </a>
              <?php
                  echo '<h4 class="card-title"> ' . $params['users'] .' </h4>';
              ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card card-stats card-success">
      <div class="card-body ">
        <div class="row">
          <div class="col-5">
            <div class="icon-big text-center">
            <i class="fas fa-chart-bar"></i>
            </div>
          </div>
          <div class="col-7 d-flex align-items-center">
            <div class="numbers">
            <a href="/admin/orders/accepted">
              <p class="card-category">Doanh thu</p>
            </a>
              <?php
                  echo '<h4 class="card-title"> ' . number_format($params['income'], 0, ',', '.') . ' VNĐ' .' </h4>';
              ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card card-stats card-danger">
      <div class="card-body">
        <div class="row">
          <div class="col-5">
            <div class="icon-big text-center">
              <i class="far fa-newspaper"></i>
            </div>
          </div>
          <div class="col-7 d-flex align-items-center">
            <div class="numbers">
            <a href="/admin/products">
              <p class="card-category">Sản phẩm</p>
            </a>
                  <?php
                    echo '<h4 class="card-title"> ' . $params['products'] .' </h4>';
                  ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card card-stats card-primary">
      <div class="card-body ">
        <div class="row">
          <div class="col-5">
            <div class="icon-big text-center">
              <i class="far fa-check-circle"></i>
            </div>
          </div>
          <div class="col-7 d-flex align-items-center">
            <div class="numbers">
            <a href="/admin/orders/accepted">
              <p class="card-category">Đã bán</p>
            </a>
                <?php
                    echo '<h4 class="card-title"> ' . $params['orders'] .' </h4>';
                ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-3">
    <div class="card card-stats card-info">
      <div class="card-body">
        <div class="row">
          <div class="col-5">
            <div class="icon-big text-center">
              <i class="fas fa-receipt"></i>
            </div>
          </div>
          <div class="col-7 d-flex align-items-center">
            <div class="numbers">
              <p class="card-category">Giá trị đơn TB</p>
              <h4 class="card-title">
                <?= e(number_format($params['averageOrderValue'], 0, ',', '.')) ?> VNĐ
              </h4>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

<div class="row mt-4">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Doanh thu 30 ngày gần nhất</h4>
      </div>
      <div class="card-body">
        <canvas id="revenueChart" height="120"></canvas>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Sản phẩm bán chạy</h4>
      </div>
      <div class="card-body">
        <canvas id="topProductsChart" height="220"></canvas>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
  (function () {
    // Analytics payloads are encoded server-side (json_encode escapes any
    // product names) and consumed only as data here — never as markup.
    const revenueByDay = <?= json_encode($params['revenueByDay'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const topProducts = <?= json_encode($params['topProducts'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

    const vnd = (value) => new Intl.NumberFormat('vi-VN').format(value) + ' VNĐ';

    const revenueCanvas = document.getElementById('revenueChart');
    if (revenueCanvas) {
      new Chart(revenueCanvas, {
        type: 'line',
        data: {
          labels: revenueByDay.map((row) => row.date),
          datasets: [{
            label: 'Doanh thu',
            data: revenueByDay.map((row) => row.revenue),
            borderColor: '#1572E8',
            backgroundColor: 'rgba(21, 114, 232, 0.15)',
            fill: true,
            tension: 0.3,
          }],
        },
        options: {
          responsive: true,
          plugins: {
            tooltip: { callbacks: { label: (ctx) => vnd(ctx.parsed.y) } },
          },
          scales: { y: { ticks: { callback: (value) => vnd(value) } } },
        },
      });
    }

    const topCanvas = document.getElementById('topProductsChart');
    if (topCanvas) {
      new Chart(topCanvas, {
        type: 'bar',
        data: {
          labels: topProducts.map((row) => row.name),
          datasets: [{
            label: 'Số lượng đã bán',
            data: topProducts.map((row) => row.quantity),
            backgroundColor: '#31CE36',
          }],
        },
        options: {
          indexAxis: 'y',
          responsive: true,
          plugins: { legend: { display: false } },
        },
      });
    }
  })();
</script>