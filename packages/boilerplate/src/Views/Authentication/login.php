<?= $this->extend('Boilerplate\Views\Authentication\index') ?>
<?= $this->section('content') ?>
<div class="card">
  <div class="card-body login-card-body">
    <p class="login-box-msg" style="font-weight: 500; font-size: 15px; color: #94a3b8;">Prenxe dadus tuir mai hodi tama ba sistema</p>
    
    <!-- Display messages in Tetum -->
    <?php if (session()->has('message')) : ?>
        <div class="alert alert-success">
            <?= session('message') ?>
        </div>
    <?php endif ?>
    <?php if (session()->has('error') || session()->has('errors')) : ?>
        <div class="alert alert-danger">
            Favor verifika fali ita-boot nia dadus! Email ka Lia-kloot (Password) keta sala.
        </div>
    <?php endif ?>

    <form action="<?= route_to('login') ?>" method="post">
      <?= csrf_field() ?>
      
      <div class="form-group position-relative mb-4">
        <label for="login" style="font-size: 13px; font-weight: 600; color: #94a3b8; margin-bottom: 8px; display: block;">Email ka Naran User</label>
        <div style="position: relative;">
          <input type="text" name="login" id="login"
            class="form-control <?= session('error.login') || session('errors.login') ? 'is-invalid' : '' ?>"
            placeholder="Prenxe Email ka Naran User..." value="<?= old('login') ?>" autocomplete="off" required>
          <i class="fas fa-envelope input-icon"></i>
        </div>
        <?php if (session('errors.login')) : ?>
            <div class="invalid-feedback mt-1 small" style="display: block; color: #fca5a5;">
                Favor hatama email ka naran ne'ebé loos!
            </div>
        <?php endif ?>
      </div>

      <div class="form-group position-relative mb-4">
        <label for="password" style="font-size: 13px; font-weight: 600; color: #94a3b8; margin-bottom: 8px; display: block;">Lia-kloot (Password)</label>
        <div style="position: relative;">
          <input type="password" name="password" id="password"
            class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>"
            placeholder="Prenxe Lia-kloot..." required>
          <i class="fas fa-lock input-icon"></i>
        </div>
        <?php if (session('errors.password')) : ?>
            <div class="invalid-feedback mt-1 small" style="display: block; color: #fca5a5;">
                Lia-kloot (Password) tenke nakonu!
            </div>
        <?php endif ?>
      </div>

      <div class="row align-items-center mb-3">
        <?php if ($config->allowRemembering) { ?>
        <div class="col-7">
          <div class="icheck-primary">
            <input type="checkbox" name="remember" id="remember" <?= old('remember') ? 'checked' : '' ?> >
            <label for="remember" style="font-size: 13px; color: #94a3b8; user-select: none; font-weight: normal !important;">
              Hanoin ha'u
            </label>
          </div>
        </div>
        <?php } ?>
        <div class="col-5">
          <button type="submit" class="btn btn-primary btn-block" style="padding: 10px 15px !important; font-size: 14px; font-weight: 600;">Tama Agora</button>
        </div>
      </div>
    </form>
  </div>
</div>
<?= $this->endSection() ?>
