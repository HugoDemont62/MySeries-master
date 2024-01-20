<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->helper(['url','html','image_cache']);?>
<div class="container hero">
   <div class="columns ">
      <div class="column col-auto p-2 m-2">
        <div class="panel bg-secondary">
          <div class="panel-image">
            <a href="<?=$personne->url?>">
              <img <?php cache_src($personne->urlImage);?> class="img-responsive p-centered">
            </a>
          </div>
            <div class="panel-title text-center bg-primary">
            <div class="h5 text-secondary"><?=$personne->nom ?></div>
            <div class="h6 text-light"><?=$personne->pays ?></div>
            <div class="text-gray h6 text-center">
                <?=$personne->naissance ? date("d/m/Y",strtotime($personne->naissance)) : ''?> -
                <?=$personne->mort ? date("d/m/Y",strtotime($personne->mort)) : ''?>
            </div>
          </div>
          </div>
        </div>
    <div class="column p-2">
      <div class="columns">
  <?php foreach ($serie as $element): ?>
    <div class="column col-auto my-2">
        <div class="panel bg-secondary ">
          <div class="columns col-gapless">
        <a href="<?php echo site_url('serie/'.$element['s_id']); ?>" class="hover-up column col-auto bg-dark">
        <div class="panel-title text-center text-secondary h5"><?=$element['s_nom']?></div>
        <div class="panel-image">
            <img <?php cache_src($element['s_image']); ?> class="img-responsive p-centered">
        </div>
        <?php if(isset($element['crew']) && count($element['crew'])>0): ?>
            <div class="panel-title p-2 text-centered bg-gray">
              <?php foreach ($element['crew'] as $crew): ?>
                  <span class="label label-rounded label-primary"><?=$crew['titre']?></span>
              <?php endforeach; ?>
            </div>
        <?php endif; ?>

      </a>
        <?php if(isset($element['character']) && count($element['character'])>0): ?>
        <?php foreach ($element['character'] as $role): ?>
          <div class="column panel-subtitle">
            <div class="panel bg-gray">
              <div class="panel-title text-center h5"><?=$role['p_nom']?></div>
          <div class="panel-image">
            <img <?php cache_src($role['p_image']); ?> class="img-responsive p-centered">
        </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif;?>
    </div>
    </div>
</div>
<?php endforeach; ?>
</div>
</div>
</div>
