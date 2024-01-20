<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->helper(['url','html','image_cache']); ?>

<div class="hero bg-secondary">
 <div class="hero-body">
  <div class="container">
     <div class="columns">
     <?php foreach ($serie_list as $element): ?>
     <div class="column col-12 my-2">
       <div class="panel bg-secondary" id="<?=$element['id']?>">
         <div class="columns col-gapless">
       <a href="<?php echo site_url('serie/'.$element['id']); ?>" class="hover-up column col-auto bg-dark">
       <div class="panel-title text-center text-secondary h5"><?=$element['nom']?></div>
       <div class="panel-subtitle">
        <div class="bar">
          <?php $total=$element['total']; $reste= $element['reste']; $vu= $total-$reste;
                $progress=(100*$vu)/$total; ?>
          <div class="bar-item text-gray" role="progressbar" style="width:<?=$progress?>%;"
             aria-valuenow="<?=$vu?>" aria-valuemin="0" aria-valuemax="<?=$total?>">
           <?=$vu.'/'.$total?></div>
        </div>
      </div>
       <div class="panel-image">
           <img <?php cache_src($element['urlImage']); ?> class="img-responsive p-centered">
       </div>

     </a>
        <?php if(isset($element['episode'])):?>
       <?php for($i=0; $i<3 && $i < count($element['episode']);$i++): ?>
        <?php $episode=$element['episode'][$i];?>
         <div class="column col-auto panel-subtitle">
           <a href="<?=site_url('serie/'.$episode['idSerie'].'/'.$episode['saison'].'#'.$episode['numero']); ?>"
              class="panel hover-up">
             <div class="panel-subtitle text-center text-gray h6">
               <?='S'.$episode['saison'].'E'.$episode['numero']?></div>
             <div class="panel-title text-center h5"><?=$episode['nom']?></div>
         <div class="panel-image">
           <img <?php cache_src($episode['urlImage']); ?> class="img-responsive p-centered">
       </div>
            <div class="panel-subtitle text-center text-secondary bg-dark h6">
              <?php echo "Diffusion le " . date("d/m/Y",strtotime($episode['premiere'])) ;?>
            </div>

          </a>
          <div class="text-dark bg-secondary h6">
            <form class="form-group mx-2"
            action="<?php echo site_url('home/vu/'.$episode['id'].'#'.$episode['idSerie']); ?>"
            method="post">
  <label class="form-checkbox">
    <input type="checkbox" action="submit" onChange='submit();'>
    <i class="form-icon"></i> Episode déjà vu
  </label>
</form>
 </div>
         </div>
       <?php endfor; ?>
     <?php endif; ?>

     </div>
     </div>
     </div>
     <?php endforeach; ?>
   </div>
  </div>
 </div>
</div>
