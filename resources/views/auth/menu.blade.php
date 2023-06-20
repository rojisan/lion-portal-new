<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary">
  <!-- Brand Logo -->
  <a class="brand-link">
    <img src="{{ asset('images/logo.png') }}" class="brand-image">
    <span class="brand-text font-weight-light">PORTAL-SYSTEM</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="{{asset('images/profile.png')}}" alt="User Image" alt="centered image" height="598" width="398">
      </div>
      <div class="info">
        <a href="#" class="d-block">{{ session('username') }}</a>
      </div>
    </div>

    <!-- Sidebar Menu -->
    
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
          <a href="{{ url('/') }}" class="nav-link" type="submit" id="m-dashboard"><i class="nav-icon fas fa-home"></i>
            <p>
              Dashboard
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link" >
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>
              Master
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ url('client') }}" class="nav-link" type="submit" id="m-client"><i class="far fa-circle nav-icon"></i>
                <p>
                  Employee
                </p>
              </a>
            </li>
          </ul>
          <!-- <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ url('absensi') }}" class="nav-link" type="submit" id="m-Attendance"><i class="far fa-circle nav-icon"></i>
                <p>
                  Attendance
                </p>
              </a>
            </li>
          </ul> -->
        </li>
        <li class="nav-item">
          <a href="{{ url('absensi') }}" class="nav-link" type="submit" id="m-Attendance"><i class="nav-icon fas fa-calendar-check"></i>
            <p>
              Attendance
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('tiket') }}" class="nav-link" type="submit" id="m-tiket"><i class="nav-icon fas fa-file-invoice"></i>
            <p> 
              Tiketing
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link" >
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>
              HRIS
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ url('absensipayroll') }}" class="nav-link" type="submit" id="m-absensipayroll"><i class="far fa-calendar-check nav-icon"></i>
                <p>
                  Attendance Payroll
                </p>
              </a>
            </li>
          </ul>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="" class="nav-link" type="submit" id="m-Attendance"><i class="far fas fa-receipt nav-icon"></i>
                <p>
                  Payroll
                </p>
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>
