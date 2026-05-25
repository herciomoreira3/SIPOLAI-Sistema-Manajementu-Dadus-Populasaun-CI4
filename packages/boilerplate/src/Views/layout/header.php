<nav
    class="main-header navbar navbar-expand navbar-<?= config('Boilerplate')->theme['navbar']['bg'] ?> navbar-<?= config('Boilerplate')->theme['navbar']['type'] ?> <?= config('Boilerplate')->theme['navbar']['type'] ? '' : 'border-bottom-0' ?>">
    <ul class="nav navbar-nav">
        <li class="nav-item">
            <a class="nav-link sidebar-toggle" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Messages Dropdown Menu -->
        <!-- Notifications Dropdown Menu -->

        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" style="position: relative; transition: all 0.3s ease;">
                <i class="fa fa-power-off text-danger" style="font-size: 1.15rem; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.15)'" onmouseout="this.style.transform='scale(1)'"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right border-0 shadow-lg" style="border-radius: 16px; overflow: hidden; padding: 0; min-width: 290px; margin-top: 10px;">
                <div style="background: linear-gradient(135deg, #09090b, #1e293b); padding: 25px 20px; text-align: center; position: relative;">
                    <div style="width: 75px; height: 75px; border-radius: 50%; border: 3px solid rgba(255,255,255,0.25); overflow: hidden; margin: 0 auto 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.25);">
                        <img src="https://cdn.jsdelivr.net/npm/admin-lte@3.0.2/dist/img/avatar.png" alt="Foto Profil Uza-dor" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <h4 style="color: #ffffff; margin: 0; font-size: 16px; font-weight: 600; letter-spacing: 0.5px;"><?= esc(user()->username) ?></h4>
                    <p style="color: rgba(255,255,255,0.7); margin: 4px 0 0; font-size: 13px; font-weight: 400;"><?= esc(user()->email) ?></p>
                </div>
                <div style="background: #ffffff; padding: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 15px; border-bottom: 1px solid #f1f5f9; margin-bottom: 15px; font-size: 13px; color: #475569;">
                        <span style="font-weight: 500;"><i class="far fa-calendar-alt text-primary mr-1"></i> Membru Hori</span>
                        <span class="font-weight-bold text-dark">
                            <?php
                            $months = [
                                'January' => 'Janeiru', 'February' => 'Fevereiru', 'March' => 'Marsu', 
                                'April' => 'Abríl', 'May' => 'Maiu', 'June' => 'Junu', 
                                'July' => 'Julu', 'August' => 'Agostu', 'September' => 'Setembru', 
                                'October' => 'Outubru', 'November' => 'Novembru', 'December' => 'Dezembru'
                            ];
                            $englishMonth = user()->created_at->format('F');
                            $tetumMonth = $months[$englishMonth] ?? $englishMonth;
                            echo user()->created_at->format('d') . ' ' . $tetumMonth . ' ' . user()->created_at->format('Y');
                            ?>
                        </span>
                    </div>
                    <a href="<?= route_to('logout') ?>" 
                       class="btn btn-danger btn-block text-white" 
                       style="border-radius: 30px; font-weight: 600; padding: 8px 20px; font-size: 14px; background: #dc2626; border: none; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2); transition: all 0.2s;"
                       onmouseover="this.style.background='#b91c1c'; this.style.transform='translateY(-1px)';"
                       onmouseout="this.style.background='#dc2626'; this.style.transform='translateY(0)';"
                    >
                        <i class="fas fa-sign-out-alt mr-1"></i> Sai husi Sistema
                    </a>
                </div>
            </div>
        </li>
    </ul>
</nav>