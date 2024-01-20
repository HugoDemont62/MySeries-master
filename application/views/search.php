<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->helper(['url','html']); ?>

<div class="hero bg-secondary">
 <div class="hero-body">
   <form action="<?= site_url('search/'); ?>" method="post" class="m-2">
   <div class="input-group">
     <span class="input-group-addon">Nom de la série</span>
     <input type="search" name="query" class="form-input">
     <button type="submit" class="btn btn-primary input-group-btn">Rechercher</button>
   </div>
 </form>


   <?php if (isset($serie_list)): ?>
  <div class="container">
   <div class="columns">
    <?php foreach ($serie_list as $serie): ?>
      <div class="column col-2 col-xs-12 col-sm-6 col-md-4 col-lg-3" >
        <a href="<?php echo site_url('serie/update/'.$serie->id); ?>" class="panel my-2 hover-up bg-dark">
          <div class="panel-image">
            <?php if (isset($serie->urlImage) && $serie->urlImage != NULL && strlen($serie->urlImage)>5): ?>
              <img src="<?=$serie->urlImage?>" class="img-responsive p-centered"/>
            <?php else:?>
              <img src="<?=base_url('public/img/medium_portrait.png')?>" class="img-responsive p-centered"/>
            <?php endif;?>
          </div>
          <div class="panel-body p-2">
            <div class="panel-title h6 text-center text-secondary"><?php echo $serie->nom; ?></div>
          </div>
        </a>
      </div>
    <?php endforeach; ?>
   </div>
  </div>
<?php endif;?>
 </div>
</div>
