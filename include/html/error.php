
<body class="hold-transition login-page">

<div class="login-box">
  <div class="login-box-body">
    <div class="position-relative p-2 bg-gray" style="height: 180px">
      <div class="ribbon-wrapper ribbon-lg">
        <div class="ribbon bg-danger">
          ERROR
        </div>
      </div>
      Fehler <br>
      <small>Ein Fehler wurde stattgefunden.</small>
    </div>
    <a href="/" class="btn btn-info col-12" role="button"><?php echo GET_Lang::nachricht('_HOME'); ?></a>
  </div>
  <div class="col-12">
    <?php echo get_slogan(); ?>
  </div>
</div>

