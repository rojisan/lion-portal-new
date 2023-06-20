<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->
      <li class="nav-item">
        <div class="navbar-search-block">
          <form class="form-inline">
            <div class="input-group input-group-sm">
              <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
              <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                  <i class="fas fa-search"></i>
                </button>
                <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
      </li>
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      <li class="nav-item">
        <div class="collapse navbar-collapse">
          <a class="btn btn-sm m-r-10 icon-profile"><i class="fas fa-user"></i></a>
          <div class="dropdown show">
            <a class="btn dropdown-toggle dropdown-profile" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="">{{ session('username') }}</span> &nbsp;</a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
                <form id="formData" name="formData" method="POST" action="{{ route('logout') }}">
                  {{ csrf_field() }}
                  <button type="submit" class="dropdown-item no-active">Logout</button>
                </form>
            </div>
          </div>
        </div>
        <!-- <form id="formData" name="formData" method="POST" action="{{ route('logout') }}">
            {{ csrf_field() }}
            <button type="submit" class="btn btn-default">Logout</button>
        </form> -->
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->