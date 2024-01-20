<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->helper(['url','html']); ?>

<div class="panel bg-gray">
  <div class="panel-body">
   <?php foreach ($categories as $tag): ?>
     <a href="<?=site_url('category/'.$tag->nom); ?>">
       <span class="label label-rounded m-1 <?= $tag->nom == $current_cat ? "label-primary" : "label-secondary"?> ">
       <?=$tag->nom.' ('.$tag->count .') '?></span></a>

   <?php endforeach; ?>
  </div>
</div>
